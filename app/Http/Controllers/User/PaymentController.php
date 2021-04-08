<?php
namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\SslCommerzPaymentController;
use App\Http\Controllers\StripeController;
use App\Mail\OrderMail;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\PaymentGateway;
use App\Traits\Sms;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;


class PaymentController extends Controller
{
    use Sms;
    //display payment gateway list in payment page
    public function orderPaymentGateway($orderId)
    {
        $order = Order::with('order_details.product:id,title,slug,feature_image')
            ->where('user_id', Auth::id())
            ->where('order_id', $orderId)->first();
        if($order){
            $paymentgateways = PaymentGateway::orderBy('position', 'asc')->where('method_for', '!=', 'payment')->where('status', 1)->get();
            return view('frontend.checkout.order-payment')->with(compact('order', 'paymentgateways'));
        }
        return view('404');
    }

    // process payment gateway & redirect specific gateway
    public function orderPayment(Request $request, $orderId){

        $order = Order::with('order_details.product:id,title')->where('user_id', Auth::id())->where('order_id', $orderId)->first();
        if($order){
            $total_price = $order->total_price + $order->shipping_cost - $order->coupon_discount;
            $data = [
                'order_id' => $order->order_id,
                'total_price' => $total_price,
                'total_qty' => $order->total_qty,
                'currency' => $order->currency,
                'payment_method' => $request->payment_method
            ];
            Session::put('payment_data', $data);
        }else{
            Toastr::error('Payment failed.');
            return redirect()->back();
        }

        if($request->payment_method == 'cash-on-delivery'){
            Session::put('payment_data.status', 'success');
            //redirect payment success method
            return $this->paymentSuccess();
        }elseif($request->payment_method == 'wallet-balance'){
            if(Auth::user()->wallet_balance >= $total_price) {
                Session::put('payment_data.status', 'success');
                Session::put('payment_data.payment_status', 'paid');
                //redirect payment success method
                return $this->paymentSuccess();
            }else{
                Toastr::error('Insufficient wallet balance.');
                return redirect()->back();
            }
        }
        elseif($request->payment_method == 'sslcommerz'){
            //redirect PaypalController for payment process
            $sslcommerz = new SslCommerzPaymentController;
            return $sslcommerz->sslCommerzPayment();
        }elseif($request->payment_method == 'paypal'){
            //redirect PaypalController for payment process
            $paypal = new PaypalController;
            return $paypal->paypalPayment();
        }
        elseif($request->payment_method == 'masterCard'){
            //redirect StripeController for payment process
            Session::put('payment_data.stripeToken', $request->stripeToken);
            $stripe = new StripeController();
            return $stripe->masterCardPayment();
        }
        elseif($request->payment_method == 'manual'){
            Session::put('payment_data.payment_method', $request->manual_method_name);
            Session::put('payment_data.status', 'success');
            Session::put('payment_data.trnx_id', $request->trnx_id);
            Session::put('payment_data.payment_info', $request->payment_info);
            //redirect payment success method
            return $this->paymentSuccess();
        }else{
            Toastr::error('Please select payment method');
        }
        return back();
    }

    //payment status success then update payment status
    public function paymentSuccess(){

        $payment_data = Session::get('payment_data');

        //clear session payment data
        Session::forget('payment_data');
        if($payment_data && $payment_data['status'] == 'success') {
            $order = Order::where('user_id', Auth::id())->where('order_id', $payment_data['order_id'])->first();
            $order->payment_method = $payment_data['payment_method'];
            $order->tnx_id = (isset($payment_data['trnx_id'])) ? $payment_data['trnx_id'] : null;
            $order->order_date = now();
            $order->payment_status = (isset($payment_data['payment_status'])) ? $payment_data['payment_status'] : 'pending';
            $order->payment_info = (isset($payment_data['payment_info'])) ? $payment_data['payment_info'] : null;
            $order->save();

                //when one order multi payment work this
//                if(isset($payment_data['trnx_id'])) {
//                    OrderPayment::create([
//                        'user_id' => Auth::id(),
//                        'pay_method' => $payment_data['payment_method'],
//                        'amount' => $payment_data['total_price'],
//                        'txnid' => $payment_data['trnx_id'],
//                    ]);
//                }
                if($order && $payment_data['payment_method'] == 'wallet-balance'){
                    $order = Order::where('user_id', Auth::id())
                        ->where('order_id', $payment_data['order_id'])->first();
                    $user_id = Auth::id();
                    //minuse wallet balance;
                    $shipping_cost = ($order->shipping_cost) ? $order->shipping_cost : 0;
                    $total_price = $order->total_price + $shipping_cost - $order->coupon_discount;
                    $user = User::find($user_id);
                    $user->wallet_balance = $user->wallet_balance - $total_price;
                    $user->save();
                }
                //send mobile notify
                $customer_mobile = ($order->billing_phone) ? $order->billing_phone : $order->shipping_phone;
                $msg = 'Dear customer, Your order has been successfully placed on '.$_SERVER['SERVER_NAME'].'. Order track at '.route('orderTracking').'?order_id='.$order->order_id;
                $this->sendSms($customer_mobile, $msg);

                $admin_mobile = Config::get('siteSetting.phone');
                $admin_msg = 'You have received a new order on '.$_SERVER['SERVER_NAME'].'. Order details '.route('orderTracking').'?order_id='.$order->order_id;
                $this->sendSms($admin_mobile, $admin_msg);
                //insert notification in database
                Notification::create([
                    'type' => 'order',
                    'fromUser' => Auth::id(),
                    'toUser' => null,
                    'item_id' => $payment_data['order_id'],
                    'notify' => 'received a new order',
                ]);

            return redirect()->route('order.paymentConfirm', $payment_data['order_id']);
        }

        return redirect()->route('user.orderHistory');
    }

    //payment complete thanks page
    public function paymentConfirm($orderId){

            $order = Order::with(['order_details.product:id,title,slug,feature_image','get_area','get_city','get_state'])->where('user_id', Auth::id())
                ->where('order_id', $orderId)->first()->toArray();

            //send notification in email
            //Mail::to(Auth::user()->email)->send(new OrderMail($order));
            Toastr::success('Thanks Your order submitted successfully');

            return view('frontend.checkout.payemnt-confirmation')->with(compact('order'));

    }

}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderCancelReason;
use App\Models\OrderDetail;
use App\Models\OrderInvoice;
use App\Models\SiteSetting;
use App\Models\Transaction;
use App\Traits\Sms;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminOrderController extends Controller
{
    use Sms;
    //get all order by user id
    public function orderHistory(Request $request, $status='')
    {
        $orderCount = Order::where('payment_method', '!=', 'pending')->select('order_status', 'offer_id')->get();

        $orders = Order::with('orderCancelReason')->orderBy('order_date', 'desc')->where('payment_method', '!=', 'pending')
            ->join('users', 'orders.user_id', 'users.id');
        if($request->order_id){
            $orders->where('order_id', $request->order_id);
        }if($request->offer && $request->offer == 'offer'){
            $orders->where('offer_id', '!=', null);
        }if($request->offer && $request->offer == 'regular'){
            $orders->where('offer_id', null);
        }
        if($request->customer){
            $keyword = $request->customer;
                $orders->where(function ($query) use ($keyword) {
                $query->orWhere('orders.shipping_name', 'like', '%' . $keyword . '%');
                $query->orWhere('orders.shipping_phone', 'like', '%' . $keyword . '%');
                $query->orWhere('orders.shipping_email', 'like', '%' . $keyword . '%');
                $query->orWhere('users.name', 'like', '%' . $keyword . '%');
                $query->orWhere('users.mobile', 'like', '%' . $keyword . '%');
                $query->orWhere('users.email', 'like', '%' . $keyword . '%');
            });
        }
        if($status){
            $orders = $orders->where('order_status', $status);
        }
        if($request->from_date){
            $from_date = Carbon::parse($request->from_date)->format('Y-m-d')." 00:00:00";
            $orders = $orders->where('order_date', '>=', $from_date);
        }if($request->end_date){
            $end_date = Carbon::parse($request->end_date)->format('Y-m-d')." 23:59:59";
            $orders = $orders->where('order_date', '<=', $request->end_date);
        }
        if(!$status && $request->status && $request->status != 'all'){
            $orders = $orders->where('order_status',$request->status);
        }

        $orders = $orders->selectRaw('orders.*, users.name as customer_name,username')->paginate(15);
        return view('admin.order.orders')->with(compact('orders', 'orderCount'));
    }

    //show order details by order id
    public function showOrderDetails($orderId){

        $order = Order::with(['order_details.product:id,title,slug,feature_image','get_country', 'get_state', 'get_city', 'get_area'])
            ->where('order_id', $orderId)->first();
        if($order){
            return view('admin.order.order-details')->with(compact('order'));
        }
        return false;
    }

    //show order details by order id
    public function orderInvoice($orderId){
        $order = Order::with(['order_details.product:id,title,slug,feature_image'])
            ->where('order_id', $orderId)->first();

        if($order){
            return view('admin.order.invoice')->with(compact('order'));
        }
        return view('404');
    }

    //add 0r chanage shipping method
    public function shippingMethod(Request $request){
        $order = Order::where('order_id', $request->order_id)->first();
        if($order){
            $order->shipping_method_id = $request->shipping_method_id;
            $order->save();
            $output = array( 'status' => true,  'message'  => 'Shipping method set successful.');
        }else{
            $output = array( 'status' => false,  'message'  => 'Shipping added failed.!');
        }
        return response()->json($output);
    }
    //set product attribute size , color etc
    public function orderAttributeUpdate(Request $request){
        $order = OrderDetail::where('order_id', $request->order_id)->first();
        if($order){
            $attributes = explode(',', $request->productAttributes);

            $attributes = json_encode($attributes);
            $order->attributes = $attributes;
            $order->save();
            $output = array( 'status' => true,  'message'  => 'Product Attribute added successful.');
        }else{
            $output = array( 'status' => false,  'message'  => 'Product Attribute added failed.!');
        }
        return response()->json($output);
    }

    // change payment Status function
    public function changePaymentStatus(Request $request){
        $status = Order::where('order_id', $request->order_id)->first();
        if($status){
            $status->update(['payment_status' => $request->status]);
            $output = array( 'status' => true,  'message'  => 'Payment status '.str_replace( '-', ' ', $request->status).' successful.');
        }else{
            $output = array( 'status' => false,  'message'  => 'Payment status update failed.!');
        }
        return response()->json($output);
    }

    // add order info exm( shipping cost, comment)
    public function addedOrderInfo(Request $request){
        $order = Order::where('order_id', $request->order_id)->first();
        if($order){
            if($request->field_data) {
                $field = $request->field;
                $order->$field = ($request->field_data) ? $request->field_data : null;
                $order->save();
            }
            $output = array( 'status' => true,  'message'  => str_replace( '_', ' ', $request->field).' added successful.');
        }else{
            $output = array( 'status' => false,  'message'  => str_replace( '_', ' ', $request->field).' added failed.');
        }
        return response()->json($output);
    }

    // invoice Print By
    public function invoicePrintBy(Request $request, $order_id){
        $order = Order::where('order_id', $order_id)->first();
        if($order){
            $staff_id = Auth::guard('admin')->id();
            $invoicePrints = ($order->invoicePrints) ? ','. $order->invoicePrints : '';
            $order->invoicePrints = $staff_id .'pd'. now() . $invoicePrints;
            $order->updated_by = $staff_id;
            $order->save();
            //add or update order invoice
            $orderInvoice = OrderInvoice::where('invoice_id', $order_id)->first();
            if(!$orderInvoice){
                $orderInvoice = new OrderInvoice();
            }
            $orderInvoice->invoice_id = $request->invoice_id;
            $orderInvoice->all_orders = $request->all_orders;
            $orderInvoice->user_id = $order->user_id;
            $created_by = ($orderInvoice) ? ','. $orderInvoice->created_by : '';
            $orderInvoice->created_by = $staff_id .'pd'. now() . $created_by;
            $orderInvoice->save();
        }
        return true;
    }

    // change Order Status function
    public function changeOrderStatus(Request $request){
        $order = Order::where('order_id', $request->order_id)->first();
        $status = str_replace( '-', ' ', $request->status);
        $output = [];
        if($order && $order->order_status != $request->status && $order->order_status != 'delivered'){
            $order->update(['order_status' => $request->status]);

            foreach ($order->order_details as $orderDetails) {
                $orderDetails->shipping_status = $request->status;
                $orderDetails->save();
                if($request->status == 'delivered'){
                    //total price
                    $price = $orderDetails->price * $orderDetails->qty;
                    //get commission
                    $commission_percentage = SiteSetting::where('type', 'vendor_commission')->first()->value;
                    //calculate commission
                    $commission = ($commission_percentage && $commission_percentage > 0) ? round(($price * $commission_percentage)/100, 2) : 0;
                    //minus commission
                    $amount = $price - $commission;
                    //update seller balance
                    $seller = $orderDetails->seller;
                    $seller->balance = $seller->balance + $amount;
                    $seller->save();
                    //insert transaction
                    $transaction = new Transaction();
                    $transaction->type = 'order';
                    $transaction->item_id = $orderDetails->order_id;
                    $transaction->payment_method = $order->payment_method;
                    $transaction->amount = $amount;
                    $transaction->commission = $commission;
                    $transaction->seller_id = $orderDetails->vendor_id;
                    $transaction->status = 'paid';
                    $transaction->save();
                }
            }

            //if cancel order add wallet balance;
            if ($order->payment_status == 'paid' && $request->status == 'cancel'){
                $shipping_cost = ($order->shipping_cost) ? $order->shipping_cost : 0;
                $total = $order->total_price + $shipping_cost;
                $user = User::find($order->user_id);
                $user->wallet_balance = $user->wallet_balance + $total;
                $user->save();
                //insert transaction
                $transaction = new Transaction();
                $transaction->type = 'wallet';
                $transaction->notes = 'order '. $request->status;
                $transaction->item_id = $order->order_id;
                $transaction->payment_method = $order->payment_method;
                $transaction->transaction_details = $order->payment_info;
                $transaction->amount = $total;
                $transaction->total_amount = $user->wallet_balance + $total;
                $transaction->customer_id = $user->id;
                $transaction->created_by = Auth::guard('admin')->id();
                $transaction->status = 'paid';
                $transaction->save();
            }

            $staff_id = Auth::guard('admin')->id();
            $invoicePrints = ($order->invoicePrints) ? ','. $order->invoicePrints : '';
            $order->invoicePrints = $staff_id .'pd'. now() . $invoicePrints;
            $order->updated_by = $staff_id;
            $order->save();

            //send mobile notify
            if($order->order_status == 'delivered'){
                $msg = 'Dear customer, Your order has been '.$status.'. Thanks for ordering from '.$_SERVER['SERVER_NAME'];
            }else{
                $msg = 'Dear customer, Your order has been '.$status.'. Order track at '.route('orderTracking').'?order_id='.$order->order_id;
            }
            $customer_mobile = ($order->billing_phone) ? $order->billing_phone : $order->shipping_phone;
            $this->sendSms($customer_mobile, $msg);

            //insert notification in database
            Notification::create([
                'type' => 'orderStatus',
                'fromUser' => $staff_id,
                'toUser' => $order->user_id,
                'item_id' => $request->order_id,
                'notify' => $request->status.' your order',
            ]);

            $output = array( 'status' => true,  'message'  => 'Delivery status '.$status.' successful.');
        }else{
            $output = array( 'status' => false,  'message'  => 'Delivery status update failed.!');
        }
        return response()->json($output);
    }

    //order cancel
    public function orderCancel ($order_id)
    {
        $order = Order::with('order_details')->where('order_id', $order_id)->first();
        $output = [];
        if($order && $order->order_status != 'delivered') {
                $order->update(['order_status' => 'cancel']);
                    foreach ($order->order_details as $orderDetails) {
                        $orderDetails->shipping_status = 'cancel';
                        $orderDetails->save();
                    }

                    //if cancel order add wallet balance;
                    if ($order->payment_status == 'paid'){
                        $shipping_cost = ($order->shipping_cost) ? $order->shipping_cost : 0;
                        $total = $order->total_price + $shipping_cost;
                        $user = User::find($order->user_id);
                        $user->wallet_balance = $user->wallet_balance + $total;
                        $user->save();
                        //insert transaction
                        $transaction = new Transaction();
                        $transaction->type = 'wallet';
                        $transaction->notes = 'order cancel';
                        $transaction->item_id = $order->order_id;
                        $transaction->payment_method = $order->payment_method;
                        $transaction->transaction_details = $order->payment_info;
                        $transaction->amount = $total;
                        $transaction->total_amount = $user->wallet_balance + $total;
                        $transaction->customer_id = $user->id;
                        $transaction->created_by = Auth::guard('admin')->id();
                        $transaction->status = 'paid';
                        $transaction->save();
                    }
                    $staff_id = Auth::guard('admin')->id();
                    $invoicePrints = ($order->invoicePrints) ? ','. $order->invoicePrints : '';
                    $order->invoicePrints = $staff_id .'pd'. now() . $invoicePrints;
                    $order->updated_by = $staff_id;
                    $order->save();

                    //send mobile notify
                    $customer_mobile = ($order->billing_phone) ? $order->billing_phone : $order->shipping_phone;
                    $msg = 'Dear customer, Your order has been cancel. Order track at '.route('orderTracking').'?order_id='.$order->order_id;
                    $this->sendSms($customer_mobile, $msg);

                    //insert notification in database
                    Notification::create([
                        'type' => 'order',
                        'fromUser' => $staff_id,
                        'toUser' => $order->user_id,
                        'item_id' => $order->order_id,
                        'notify' => 'cancel your order',
                    ]);
                    $output = [
                        'status' => true,
                        'msg' => 'Order cancel successfully.'
                    ];

        }else{
            $output = [
                'status' => false,
                'msg' => 'Order cancel failed.'
            ];
        }
        return response()->json($output);
    }

    //set refund days
    public function refundConfig(){
        return view('admin.refund.returnConfigure');
    }

    //order cancel reason lists
    public function orderCancelReason()
    {
        $reasons = OrderCancelReason::where('order_id', null)->get();
        return view('admin.order.cancel-reason')->with(compact('reasons'));
    }

    //insert return reason
    public function reasonStore(Request $request)
    {
        $reason = new OrderCancelReason();
        $reason->reason = $request->reason;
        $reason->status = ($request->status) ? 1 : 0;
        $reason->user_type = ($request->user_type) ? 1 : 'customer';
        $store = $reason->save();
        Toastr::success('Order Cancel Reason Insert Success.');
        return back();
    }

    //edit reason
    public function reasonEdit($id)
    {
        $data = OrderCancelReason::find($id);
        echo view('admin.order.cancel-reason-edit')->with(compact('data'));
    }
    //update data
    public function reasonUpdate(Request $request)
    {
        $reason = OrderCancelReason::find($request->id);
        $reason->reason = $request->reason;
        $reason->status = ($request->status) ? 1 : 0;
        $store = $reason->save();
        Toastr::success('Order Cancel Reason update Success.');
        return back();

    }

    //delate reason
    public function reasonDelete($id)
    {
        $reason = OrderCancelReason::where('id', $id)->delete();

        if($reason){
            $output = [
                'status' => true,
                'msg' => 'Reason deleted successfully.'
            ];
        }else{
            $output = [
                'status' => false,
                'msg' => 'Reason cannot deleted.'
            ];
        }
        return response()->json($output);
    }

}

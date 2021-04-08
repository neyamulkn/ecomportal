<?php

namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderCancelReason;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ShippingAddress;
use App\Models\Transaction;
use App\Traits\CreateSlug;
use App\Traits\Sms;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class OrderController extends Controller
{
    use Sms;
    use CreateSlug;
    //Insert order in order table
    public function orderConfirm(Request $request)
    {
        $shipping_address = ShippingAddress::with(['get_country','get_state','get_city', 'get_area'])->find($request->confirm_shipping_address);

        if($shipping_address) {
            $user_id = Auth::id();
            //generate unique order id
            $user_id = (Auth::check() ? Auth::id() : rand(000, 999));
            $numberLen = strlen($user_id);
            $numberLen = 9 - $numberLen;
            $order_id = 'W' . $user_id . strtoupper(substr(str_shuffle("0123456789"), -$numberLen));

            $order_id = $this->uniqueOrderId('orders', 'order_id', $order_id);
            //get cart items
            $cartItems = Cart::join('products', 'carts.product_id', 'products.id')
                ->where('user_id', $user_id)
                ->selectRaw('carts.*, sum(qty*price) total_price, shipping_method, ship_region_id, shipping_cost, other_region_cost, vendor_id')
                ->groupBy('product_id');
                //check direct checkout
                if(Cookie::has('direct_checkout_product_id') || Session::get('direct_checkout_product_id')){
                    $direct_checkout_product_id = (Cookie::has('direct_checkout_product_id') ? Cookie::get('direct_checkout_product_id') :  Session::get('direct_checkout_product_id'));
                    $cartItems = $cartItems->where('product_id', $direct_checkout_product_id);
                }
                $cartItems = $cartItems->get();
            if(!count($cartItems)>0) {
                return redirect()->back();
            }
                $total_qty = array_sum(array_column($cartItems->toArray(), 'qty'));
                $total_price = array_sum(array_column($cartItems->toArray(), 'total_price'));
                $coupon_discount = null;
                if (Session::has('couponAmount')) {
                    $coupon_discount = (Session::get('couponType') == '%') ? round($total_price * Session::get('couponAmount'), 2) : Session::get('couponAmount');
                }

                //insert order in order table
                $order = new Order();
                $order->order_id = $order_id;
                $order->user_id = $user_id;
                $order->total_qty = $total_qty;
                $order->total_price = $total_price;
                $order->coupon_code = (Session::has('couponCode') ? Session::get('couponCode') : null);
                $order->coupon_discount = $coupon_discount;
                $order->shipping_method_id = ($request->shipping_method) ? $request->shipping_method : null;

                $order->billing_name = Auth::user()->name;
                $order->billing_phone = Auth::user()->mobile;
                $order->billing_email = Auth::user()->email;
                $order->billing_country = Auth::user()->country;
                $order->billing_region = Auth::user()->region;
                $order->billing_city = Auth::user()->city;
                $order->billing_area = Auth::user()->area;
                $order->billing_address = Auth::user()->address;

                $order->shipping_name = $shipping_address->name;
                $order->shipping_phone = $shipping_address->phone;
                $order->shipping_email = $shipping_address->email;
                $order->shipping_country = $shipping_address->get_country->name;
                $order->shipping_region = $shipping_address->get_state->name;
                $order->shipping_city = $shipping_address->get_city->name;
                $order->shipping_area = $shipping_address->get_area->name;
                $order->shipping_address = $shipping_address->address;
                $order->order_notes = $request->order_notes;
                $order->currency = Config::get('siteSetting.currency');
                $order->currency_sign = Config::get('siteSetting.currency_symble');
                $order->currency_value = Config::get('siteSetting.currency_symble');
                $order->order_date = now();
                $order->payment_status = 'pending';
                $order->order_status = 'pending';
                $order = $order->save();

                if ($order) {
                    // insert product details in table
                    $total_shipping_cost = 0;
                    foreach ($cartItems as $item) {
                        $shipping_cost = $item->shipping_cost;
                        //check shipping method
                        if ($item->shipping_method == 'location') {
                            if ($item->ship_region_id != $shipping_address->region) {
                                $shipping_cost = $item->other_region_cost;
                            }
                        }
                        if($shipping_cost > $total_shipping_cost) {
                            $total_shipping_cost = $shipping_cost;
                        }
                        $orderDetails = new OrderDetail();
                        $orderDetails->order_id = $order_id;
                        $orderDetails->vendor_id = $item->vendor_id;
                        $orderDetails->user_id = $user_id;
                        $orderDetails->product_id = $item->product_id;
                        $orderDetails->qty = $item->qty;
                        $orderDetails->price = $item->price;
                        $orderDetails->shipping_charge = $shipping_cost;
                        $orderDetails->coupon_discount = (Session::has('couponAmount') ? ($coupon_discount / $total_price) * ($item->price*$item->qty) : null);
                        $orderDetails->attributes = $item->attributes;
                        $orderDetails->shipping_status = 'pending';
                        $orderDetails->save();
                        //cart id for cart item delete
                        $cart_id[] = $item->id;
                    }
                    //insert total shipping cost
                    Order::where('order_id', $order_id)->update(['shipping_cost' => $total_shipping_cost]);
                    //delete cart item
                    Cart::whereIn('id', $cart_id)->delete();
                }
            Session::put('shipping_city', $shipping_address->get_city->id);
            //redirect payment method page for payment
            return redirect()->route('order.paymentGateway', $order_id);
        }else{
            Toastr::error('Please select shipping address.');
            return back();
        }
    }

    //get all order by user id
    public function orderHistory($status='')
    {
        $orders = Order::with(['order_details.product:id,title,slug,feature_image'])
            ->where('user_id', Auth::id());
            if($status){
                $orders = $orders->where('order_status', $status);
            }
            $data['orders'] = $orders->orderBy('id', 'desc')->get();

        return view('users.order-history')->with($data);
    }

    //show order details by order id
    public function orderDetails($orderId){
        $order = Order::with(['order_details.product:id,title,slug,feature_image','get_country', 'get_state', 'get_city', 'get_area'])
            ->where('user_id', Auth::id())
            ->where('order_id', $orderId)->first();

        if($order){
            return view('users.order-details')->with(compact('order'));
        }
        return view('404');
    }

    //order cancel form
    public function orderCancelForm (Request $request){
        $user_id = Auth::id();
        $data['order'] = Order::where('user_id', $user_id)->where('order_id', $request->order_id)->first();
        $data['orderCancel'] = OrderCancelReason::where('order_id', $request->order_id)->first();
        $data['cancelReasons'] = OrderCancelReason::where('order_id', null)->where('status', 1)->get();
        return view('users.order-cancel-form')->with($data);
    }
    //order cancel
    public function orderCancel (Request $request)
    {
        $user_id = Auth::id();
        $order = Order::with('order_details')
            ->where('order_status', 'pending')
            ->where('payment_method', '!=', 'pending')
            ->where('user_id', $user_id)
            ->where('order_id', $request->order_id)->first();

        if($order) {
            $orderDetails = $order->order_details->where('user_id', $user_id);
            //if specific product change
            if ($request->product_id) {
                $orderDetails = $orderDetails->where('product_id', $request->product_id);
            }
            foreach ($orderDetails as $orderDetail) {
                $orderDetail->shipping_status = 'cancel';
                $orderDetail->shipping_date = Carbon::now();
                $orderDetail->save();

                //insert cancel reason
                $orderCancel = new OrderCancelReason();
                $orderCancel->order_id = $request->order_id;
                $orderCancel->reason = $request->cancel_reason;
                $orderCancel->reason_details = $request->reason_details;
                $orderCancel->seller_id = $orderDetail->vendor_id;
                $orderCancel->user_id = $user_id;
                $orderCancel->user_type = 'customer';
                if ($request->product_id) {
                    $orderCancel->product_id = $request->product_id;
                }
                $orderCancel->status = 1;
                $orderCancel->save();
            }
            //change order status
            $order->order_status = 'cancel';
            $order->updated_at = Carbon::now();
            $order->save();
            if ($order->payment_status == 'paid'){
                //add wallet balance;
                $shipping_cost = ($order->shipping_cost) ? $order->shipping_cost : 0;
                $total = $order->total_price + $shipping_cost;
                $user = User::find($user_id);
                $user->wallet_balance = $user->wallet_balance + $total;
                $user->save();
                //insert transaction
                $transaction = new Transaction();
                $transaction->type = 'wallet';
                $transaction->notes = 'order cancel- '. $request->reason_details;
                $transaction->item_id = $order->order_id;
                $transaction->payment_method = $order->payment_method;
                $transaction->transaction_details = $order->payment_info;
                $transaction->amount = $total;
                $transaction->total_amount = $user->wallet_balance + $total;
                $transaction->customer_id = $user->id;
                $transaction->created_by = null;
                $transaction->status = 'paid';
                $transaction->save();
            }
            //send mobile notify
            $customer_mobile = ($order->billing_phone) ? $order->billing_phone : $order->shipping_phone;
            $msg = 'Dear customer, Your order has been cancel. Order track at '.route('orderTracking').'?order_id='.$order->order_id;
            $this->sendSms($customer_mobile, $msg);
            //notify
            Notification::create([
                'type' => 'order',
                'fromUser' => $user_id,
                'toUser' => $orderDetail->vendor_id,
                'item_id' => $orderDetail->product_id,
                'notify' => 'cancel order',
            ]);
            Toastr::success('Order cancel successfully.');
            return back()->with('success', 'Your order cancellation successfully done. Please check your wallet.');
        }else{
            Toastr::error('Order can\'t cancel.');
            return back()->with('error', 'Your order cancellation failed. Please try again.');
        }

    }

    public function orderTracking(Request $request){
        if($request->order_id){
            $order = Order::with(['order_details.product:id,title,slug,feature_image,childcategory_id,subcategory_id,category_id'])
                ->where('order_id', $request->order_id)->first();
            if($order) {

                $category_id = $subcategory_id = $childcategory_id = $product_id = [];
                foreach ($order->order_details as $order_detail){
                    $product_id[] = $order_detail->product->id;
                    if ($order_detail->product->childcategory_id) {
                        $childcategory_id[] = $order_detail->product->childcategory_id;
                    } elseif ($order_detail->product->subcategory_id) {
                        $subcategory_id[] = $order_detail->product->subcategory_id;
                    } else {
                        $category_id[] = $order_detail->product->category_id;
                    }
                }

//                foreach ($order->order_details as $order_detail){
//                    if ($order_detail->product->childcategory_id != null) {
//                        $related_products->where('childcategory_id', $order_detail->product->childcategory_id);
//                    } elseif ($order_detail->product->subcategory_id != null) {
//                        $related_products->where('subcategory_id', $order_detail->product->subcategory_id);
//                    } else {
//                        $related_products->where('category_id', $order_detail->product->category_id);
//                    }
//                    break;
//                }

                $related_products = Product::where('status', 'active')->whereNotIn('id', $product_id);
                if(count($childcategory_id)>0){
                    $related_products->whereIn('childcategory_id', $childcategory_id);
                }if(count($subcategory_id)>0){
                    $related_products->whereIn('subcategory_id', $subcategory_id);
                }if(count($category_id)>0){
                    $related_products->whereIn('category_id', $category_id);
                }
                $related_products = $related_products->where('status', 'active')->selectRaw('id,title,slug,feature_image,selling_price')->take(7)->get();

                return view('users.order-tracking-details')->with(compact('order','related_products'));
            }else{
                return view('users.order-tracking');
            }
        }
        return view('users.order-tracking');
    }

    public function orderTrackingDetails($order_id){
        $order = Order::with(['order_details.product:id,title,slug,feature_image'])
            ->where('order_id', $order_id)->first();
        if($order){
            return view('users.order-tracking-details')->with(compact('order'));
        }
        return view('404');
    }

}

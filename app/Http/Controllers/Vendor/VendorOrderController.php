<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderCancelReason;
use App\Models\OrderDetail;
use App\Traits\Vendor;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorOrderController extends Controller
{
    public function __construct()
    {
        if(!Auth::guard('vendor')->check() || !Auth::guard('staff')->check()){
            return redirect('/');
        }
    }

    use Vendor;
    //get all order by user id
    public function orderHistory(Request $request, $status='')
    {
        $vendor_id = Auth::guard('vendor')->id();
        $orderCount = Order::join('order_details', 'orders.order_id', 'order_details.order_id')
            ->where('payment_method', '!=', 'pending')
            ->where('order_details.vendor_id', $vendor_id)
            ->select('shipping_status')
            ->groupBy('order_details.order_id')->get();

        $orders = Order::join('order_details', 'orders.order_id', 'order_details.order_id')
            ->orderBy('order_details.id', 'desc')
            ->where('payment_method', '!=', 'pending')
            ->where('order_details.vendor_id', $vendor_id)
            ->groupBy('order_details.order_id');
        if($request->order_id){
            $orders->where('order_details.order_id', $request->order_id);
        }
        if($status){
            $orders = $orders->where('shipping_status', $status);
        }
        if($request->from_date){
            $from_date = Carbon::parse($request->from_date)->format('Y-m-d')." 00:00:00";
            $orders = $orders->where('order_details.created_at', '>=', $from_date);
        }if($request->end_date){
            $end_date = Carbon::parse($request->end_date)->format('Y-m-d')." 23:59:59";
            $orders = $orders->where('order_details.created_at', '<=', $request->end_date);
        }
        if(!$status && $request->status && $request->status != 'all'){
            $orders = $orders->where('shipping_status',$request->status);
        }
        $orders = $orders->selectRaw('order_details.*, count(qty) as quantity, sum(qty*price) as total_price, payment_method, currency_sign')->paginate(15);
        $cancelReasons = OrderCancelReason::where('order_id', null)->where('status', 1)->get();
        return view('vendors.order.orders')->with(compact('orders', 'orderCount', 'cancelReasons'));
    }

    //show order details by order id
    public function showOrderDetails($orderId){
        $vendor_id = Auth::guard('vendor')->id();
        $order = Order::with(['seller_order_details.product:id,title,slug,feature_image','get_country', 'get_state', 'get_city', 'get_area'])
            ->where('order_id', $orderId)->first();
        if($order){
            return view('vendors.order.order-details')->with(compact('order'));
        }
        return false;
    }

    //show order details by order id
    public function orderInvoice($orderId){
        $order = Order::with(['seller_order_details.product:id,title,slug,feature_image','get_country', 'get_state', 'get_city', 'get_area'])
            ->where('order_id', $orderId)->first();

        if($order){
            return view('vendors.order.invoice')->with(compact('order'));
        }
        return view('404');
    }

    //order return
    public function orderReturn ($order_id)
    {
        return view('users.order-return');
    }

    // change Order Status function
    public function changeOrderStatus(Request $request){
        $vendor_id = Auth::guard('vendor')->id();
        $order = Order::with('order_details')->where('order_id', $request->order_id)->first();
        if($order){
            $orderDetails = $order->order_details->where('vendor_id', $vendor_id);
            //if specific product change
            if($request->product_id){
                $orderDetails = $orderDetails->where('product_id', $request->product_id);
            }
            foreach($orderDetails as $orderDetail){
                $orderDetail->shipping_status = $request->status;
                $orderDetail->save();
            }
            $output = array('status' => true, 'message' => 'Status ' . str_replace('-', ' ', $request->status) . ' successful.');
            //insert notification in database
            Notification::create([
                'type' => 'order',
                'fromUser' => null,
                'toUser' => $order->user_id,
                'item_id' => $request->order_id,
                'notify' => $request->status . ' your order',
            ]);
        } else {
            $output = array('status' => false, 'message' => 'Status cannot update.!');
        }
        return response()->json($output);
    }

    //order cancel
    public function orderCancel (Request $request)
    {
        $vendor_id = Auth::guard('vendor')->id();
        $order = Order::with('order_details')->where('order_id', $request->order_id)->first();
        if($order){
            $orderDetails = $order->order_details->where('vendor_id', $vendor_id);
            //if specific product change
            if($request->product_id){
                $orderDetails = $orderDetails->where('product_id', $request->product_id);
            }
            foreach($orderDetails as $orderDetail){
                $orderDetail->shipping_status = 'cancel';
                $orderDetail->save();
                $orderCancel = new OrderCancelReason();
                $orderCancel->order_id = $request->order_id;
                $orderCancel->reason = $request->cancel_reason;
                $orderCancel->reason_details = $request->reason_details;
                $orderCancel->seller_id = $vendor_id;
                if($request->product_id) {
                    $orderCancel->product_id = $request->product_id;
                }
                $orderCancel->status = 1;
                $orderCancel->save();
            }
            Notification::create([
                'type' => 'order',
                'fromUser' => null,
                'toUser' => $order->user_id,
                'item_id' => $request->order_id,
                'notify' => 'cancel your order',
            ]);

            Toastr::success('Order cancel successfully');
        }else{
            Toastr::error('Order can\'t cancel.');
        }
        return back();
    }

}

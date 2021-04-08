<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\OrderDetail;
use App\Models\Refund;
use App\Models\RefundConversation;
use App\Models\RefundReason;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

class RefundController extends Controller
{
    //order return form
    public function orderReturn($order_id, $product_slug)
    {
        $user_id = Auth::id();
        $data['order_detail'] = OrderDetail::where('user_id', $user_id)
            ->where('order_id', $order_id)
            ->join('products', 'order_details.product_id', 'products.id')
            ->where('products.slug', $product_slug)
            ->selectRaw('order_details.*, title,slug,feature_image')
            ->first();

        if($data['order_detail']) {
            $data['checkReturn'] = Refund::with('refundConversations')
                ->where('user_id', $user_id)
                ->where('order_id', $data['order_detail']->order_id)
                ->where('product_id', $data['order_detail']->product_id)->first();
            $data['reasons'] = RefundReason::where('status', 1)->get();

            return view('users.order-return')->with($data);
        }
        return back();
    }

    //user send order return request
    public function sendReturnRequest(Request $request){
        $request->validate([
            'return_reason' => 'required',
            'return_type' => 'required',
            'explain_issue' => 'required',
        ]);

        $user_id = Auth::id();
        $order_detail = OrderDetail::where('user_id', $user_id)
            ->where('order_id', $request->order_id)
            ->where('product_id', $request->product_id)
            ->first();
        //check valid or order
        if($order_detail){
            $qty = ($request->qty <= $order_detail->qty) ? $request->qty : $order_detail->qty;
            $refund_amount = $qty * $order_detail->price;
            $refund = new Refund();
            $refund->order_id = $order_detail->order_id;
            $refund->product_id = $order_detail->product_id;
            $refund->user_id = $user_id;
            $refund->qty = $qty;
            $refund->refund_amount = $refund_amount;
            $refund->return_type = $request->return_type;
            $refund->return_reason = $request->return_reason;
            $refund->seller_id = $order_detail->vendor_id;
            $refund->refund_status = 'pending';
            $store = $refund->save();

            if($store){
                $refundConversation = new RefundConversation();
                $refundConversation->refund_id = $refund->id;
                $refundConversation->order_id = $order_detail->order_id;
                $refundConversation->product_id = $order_detail->product_id;
                $refundConversation->sender_id = $user_id;
                $refundConversation->explain_issue = $request->explain_issue;
                if ($request->hasFile('return_image')) {
                    $image = $request->file('return_image');
                    $new_image_name = rand() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('upload/images/refund_image'), $new_image_name);
                    $refundConversation->image = $new_image_name;
                }
                $refundConversation->save();
                //insert notification in database
                Notification::create([
                    'type' => 'refund',
                    'fromUser' => Auth::id(),
                    'toUser' => 0,
                    'item_id' => $refund->id,
                    'notify' => 'refund request',
                ]);
                Toastr::success('Return request send success.');
            }else{
                Toastr::error('Return request sending failed.');
            }
        }else{
            Toastr::error('Sorry something wront please try again.');
        }
        return back();
    }

    //user return request list
    public function userReturnRequestList()
    {
        $user_id = Auth::id();

        $returnRequests = Refund::join('products', 'refunds.product_id', 'products.id')
            ->join('order_details', 'refunds.product_id', 'order_details.product_id')
            ->where('refunds.user_id', $user_id)
            ->selectRaw('refunds.*, title,slug,feature_image,attributes')
            ->groupBy('refunds.id')
            ->get();
        return view('users.orderReturnRequests')->with(compact('returnRequests'));

    }

    //admin return request list
    public function adminReturnRequestList(Request $request, $status='')
    {
        $returnRequests = Refund::join('products', 'refunds.product_id', 'products.id')
            ->join('order_details', 'refunds.product_id', 'order_details.product_id')
            ->join('users', 'refunds.user_id', 'users.id')
            ->groupBy('refunds.id');

            if($status || isset($request->status)) {
                $status = ($request->status) ? $request->status : $status;
                if ($status != 'all'){
                    $returnRequests->where('refund_status', $status);
                }
            }
            $returnRequests = $returnRequests->selectRaw('refunds.*, title,slug,feature_image,attributes,name')->get();

        return view('admin.refund.returnRequest')->with(compact('returnRequests'));
    }

    //seller return request list
    public function sellerReturnRequestList()
    {
        $returnRequests = Refund::join('products', 'refunds.product_id', 'products.id')
            ->join('order_details', 'refunds.product_id', 'order_details.product_id')
            ->selectRaw('refunds.*, title,slug,feature_image,attributes')
            ->groupBy('refunds.id')
            ->get();
        return view('users.orderReturnRequests')->with(compact('returnRequests'));
    }

    //refund request details display
    public function refundRequestDetails($refund_id){
        $data['refundDetails'] = Refund::with('refundConversations')
            ->join('orders', 'refunds.order_id', 'orders.order_id')
            ->join('products', 'refunds.product_id', 'products.id')
            ->join('order_details', 'refunds.product_id', 'order_details.product_id')
            ->where('refunds.id', $refund_id)
            ->selectRaw('refunds.*,shipping_status,currency_sign, payment_status,billing_name,billing_phone,billing_email, title,slug,feature_image,attributes')->first();
        return view('admin.refund.returnRequestDetails')->with($data);
    }

    //refund request approved Or reject
    public function refundRequestApproved($refund_id, $status){
        $refund = Refund::find($refund_id);
        if($refund) {
//            if ($status == 'approved'){
//                if ($refund->seller_approval == 1) {
//                    $seller = Seller::where('user_id', $refund->seller_id)->first();
//                    if ($seller != null) {
//                        $seller->admin_to_pay -= $refund->refund_amount;
//                    }
//                    $seller->save();
//                }
//                $wallet = new Wallet;
//                $wallet->user_id = $refund->user_id;
//                $wallet->amount = $refund->refund_amount;
//                $wallet->payment_method = 'Refund';
//                $wallet->payment_details = 'Product Money Refund';
//                $wallet->save();
//                $user = User::findOrFail($refund->user_id);
//                $user->wallet_balance += $refund->refund_amount;
//                $user->save();
//            }

            $refund->admin_approval = 1;
            $refund->refund_status = $status;
            $refund->save();

            //insert notification in database
            Notification::create([
                'type' => 'refund',
                'fromUser' => null,
                'toUser' => $refund->user_id,
                'item_id' => $refund->id,
                'notify' => 'refund request '. $status,
            ]);

           Toastr::success('refund request '. $status);
        }else{
           Toastr::error('Sorry something went wrong.');
        }
        return back();
    }



}

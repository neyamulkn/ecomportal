<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\PaymentGateway;
use App\Models\PaymentSetting;
use App\Models\Transaction;
use App\Vendor;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class TransactionController extends Controller
{

    public function paymentSetting(Request $request){
        $paymentgateways = PaymentGateway::with('paymentInfo')->orderBy('position', 'asc')->where('method_for', 'payment')->where('status', 1)->get();

        return view('vendors.payment-setting')->with(compact('paymentgateways'));
    }
    public function paymentSettingUpdate(Request $request){
        $seller_id = Auth::guard('vendor')->id();
        $request->validate([
            'payment_id' => 'required',
            'acc_name' => 'required',
            'acc_no' => 'required'
        ]);

        $paymentSetting =  PaymentSetting::where('seller_id', $seller_id)->where('payment_id', $request->payment_id)->where('id', $request->id)->first();

        if(!$paymentSetting) {
            $paymentSetting = new PaymentSetting();
        }
        $paymentSetting->payment_id = $request->payment_id;
        $paymentSetting->acc_name = $request->acc_name;
        $paymentSetting->acc_no = $request->acc_no;
        $paymentSetting->bank_name = ($request->bank_name) ? $request->bank_name : null;
        $paymentSetting->branch_name = ($request->branch_name) ? $request->branch_name : null;
        $paymentSetting->routing_no = ($request->routing_no) ? $request->routing_no : null;
        $paymentSetting->seller_id = $seller_id;
        $paymentSetting->status = 'pending';
        $paymentSetting->save();
        Toastr::success('Payment Setting Update Success.');


        return back();
    }

    //seller withdraw list
    public function seller_withdraw(){
        $vendor_id = Auth::guard('vendor')->id();
        $data['allwithdraws'] = Transaction::with('paymethod_name')->where('type', 'withdraw')
            ->orderBy('id', 'desc')
            ->where('seller_id', $vendor_id)->paginate(15);

        $data['withdraw_amount'] = Transaction::where('type', 'withdraw')->where('seller_id', $vendor_id)->where('status', '!=', 'cancel')->sum('amount');

        $data['total'] = Transaction::whereIn('type', ['order'])->where('seller_id', $vendor_id)->where('status', 'paid')->sum('amount');

        $data['paymentgateways'] = PaymentSetting::where('seller_id', $vendor_id)->get();

        return view('vendors.widthdraw')->with($data);
    }
    //seller send withdraw request
    public function seller_withdraw_request(Request $request){
        $vendor_id = Auth::guard('vendor')->id();
        $request->validate([
            'payment_method' => 'required',
            'amount' => 'required'
        ]);
        $seller = Vendor::find($vendor_id);

        if($request->amount < 50) {
            Toastr::error('Minimum Withdraw Amount '. Config::get('siteSetting.currency_symble') . 50);
        }elseif($request->amount > $seller->balance){
            Toastr::error('Insufficient Your Wallet Balance.');
        }else{
            //minus seller balance
            $seller->balance = $seller->balance - $request->amount;
            $seller->save();

            //insert transaction
            $withdraw = new Transaction();
            $withdraw->type = 'withdraw';
            $withdraw->payment_method = $request->payment_method;
            $withdraw->seller_id = $vendor_id;
            $withdraw->item_id = 'w-'.rand(0000,9999);
            $withdraw->amount = $request->amount;
            $withdraw->transaction_details = $request->message;
            $withdraw->status = 'pending';
            $withdraw->save();

            Toastr::success('Withdraw Request Send Success.');

            //insert notification in database
            Notification::create([
                'type' => 'withdraw',
                'fromUser' => $vendor_id,
                'toUser' => null,
                'item_id' => $withdraw->id,
                'notify' => 'withdraw request',
            ]);
        }

        return back();
    }

    // view vendor all transaction
    public function vendor_transactions(){
        $vendor_id = Auth::guard('vendor')->id();
        $data['transactions'] = Transaction::where('seller_id', $vendor_id)->orderBy('id', 'desc')->groupBy('item_id')->selectRaw('*, sum(amount) as grand_total')->paginate(15);
        $data['total'] = Transaction::whereIn('type', ['order'])->where('seller_id', $vendor_id)->where('status', 'paid')->sum('amount');
        $data['withdraw'] = Transaction::where('type', 'withdraw')->where('seller_id', $vendor_id)->where('status', '!=', 'cancel')->sum('amount');

        return view('vendors.transactions')->with($data);
    }
    //view admin all transaction
    public function admin_transactions(){

        $data['transactions'] = Transaction::orderBy('id', 'desc')->groupBy('item_id')->selectRaw('*, sum(amount) as grand_total')->paginate(15);
        $data['total'] = Transaction::whereIn('type', ['order'])->where('status', 'paid')->sum('amount');
        $data['withdraw'] = Transaction::where('type', 'withdraw')->where('status', '!=', 'cancel')->sum('amount');

        return view('admin.vendor.transactions')->with($data);
    }


    //admin withdraw request list
    public function withdraw_request(Request $request){
        $withdraws = Transaction::join('vendors', 'transactions.seller_id', 'vendors.id')
            ->with('paymethod_name')->where('type', 'withdraw')->select('transactions.*');
        //total withdraw amount
        $data['withdraw_status'] = $withdraws->get();
        //total balance
        $data['total_balance'] = Transaction::whereIn('type', ['order'])->where('status', 'paid')->sum('amount');

        if($request->name){
            $name = $request->name;
            $withdraws->where(function ($query) use ($name) {
                $query->orWhere('shop_name', 'LIKE', '%'. $name .'%');
                $query->orWhere('vendor_name', 'LIKE', '%'. $name .'%');
            });
        }
        if($request->from_date){
            $from_date = Carbon::parse($request->from_date)->format('Y-m-d')." 00:00:00";
            $withdraws->where('transactions.created_at', '>=', $from_date);
        }if($request->end_date){
            $end_date = Carbon::parse($request->end_date)->format('Y-m-d')." 23:59:59";
            $withdraws->where('transactions.created_at', '<=', $request->end_date);
        }
        if($request->status && $request->status != 'all'){
            $withdraws->where('transactions.status',$request->status);
        }
        //all withdraw lists
        $data['allwithdraws'] =  $withdraws->orderBy('transactions.id', 'desc')->paginate(15);

        return view('admin.vendor.widthdraw_request')->with($data);
    }

    // change withdraw Status function
    public function changeWithdrawStatus(Request $request){
        $withdraw = Transaction::where('id', $request->withdraw_id)->first();
        if($withdraw && $withdraw->status != $request->status){
            $withdraw->update(['status' => $request->status]);

            if($request->status == 'cancel') {
                //minus seller balance
                $seller = $withdraw->seller;
                $seller->balance = $seller->balance + $withdraw->amount;
                $seller->save();
            }

            //insert notification in database
            Notification::create([
                'type' => 'withdraw',
                'fromUser' => null,
                'toUser' => $withdraw->seller_id,
                'item_id' => $withdraw->id,
                'notify' => $request->status.' withdraw request',
            ]);

            $output = array( 'status' => true,  'message'  => 'Withdraw status '.str_replace( '-', ' ', $request->status).' successful.');
        }else{
            $output = array( 'status' => false,  'message'  => 'Withdraw status update failed.!');
        }
        return response()->json($output);
    }
    //withdraw History
    public function withdrawHistory($user_id){
        $withdraws = Transaction::with('paymethod_name')->where('type', 'withdraw')
            ->orderBy('id', 'desc')
            ->where('seller_id', $user_id)->get();
        return view('admin.vendor.withdraw_history')->with(compact('withdraws'));
    }

}

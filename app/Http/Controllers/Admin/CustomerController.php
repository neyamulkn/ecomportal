<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Transaction;
use App\Traits\Sms;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class CustomerController extends Controller
{
    use Sms;
    public function customerList(Request $request, $status= ''){
        $customers  = User::with('orders:user_id');
        if($status){
            $customers->where('status', $status);
        }
        if(!$status && $request->status && $request->status != 'all'){
            $customers->where('status', $request->status);
        }if($request->name && $request->name != 'all'){
            $keyword = $request->name;
            $customers->where(function ($query) use ($keyword) {
                $query->orWhere('name', 'like', '%' . $keyword . '%');
                $query->orWhere('mobile', 'like', '%' . $keyword . '%');
                $query->orWhere('email', 'like', '%' . $keyword . '%');
            });
        }if($request->location && $request->location != 'all'){
            $customers->where('city', $request->location);
        }
        $customers  = $customers->orderBy('id', 'desc')->paginate(15);
        $locations = City::orderBy('name', 'asc')->get();
        return view('admin.customer.customer')->with(compact('customers', 'locations'));
    }

    public function customerProfile($username){
        $customer  = User::with('orders')->where('username', $username)->first();
        return view('admin.customer.profile')->with(compact('customer'));
    }

    public function customerSecretLogin($id)
    {

        $user = User::findOrFail(decrypt($id));

        auth()->guard('web')->login($user, true);

        Toastr::success('Customer panel login success.');
        return redirect()->route('user.dashboard');

    }

    public function delete($id){
        $user = User::find($id);
        if($user){
            $user->delete();
            $output = [
                'status' => true,
                'msg' => 'User deleted successfully.'
            ];
        }else{
            $output = [
                'status' => false,
                'msg' => 'User cannot deleted.'
            ];
        }
        return response()->json($output);
    }

    public function walletHistory(){
        $data['totalBalance'] = User::where('wallet_balance', '>', 0)->sum('wallet_balance');
        $data['allWallets'] = Transaction::with(['customer:id,name,username,mobile', 'addedBy'])->where('type', 'wallet')->orderBy('id', 'desc')->paginate(15);
        return view('admin.wallet.wallet')->with($data);
    }

    public function customerWalletInfo(Request $request){
        $customer = User::where('name', $request->customer)->orWhere('mobile', $request->customer)->orWhere('email', $request->customer)->first();
        if($customer) {
            return view('admin.wallet.customerWalletInfo')->with(compact('customer'));
        }
        return false;
    }

    public function walletRecharge(Request $request){
        $request->validate([
            'amount' => 'required',
            'transaction_details' => 'required',
        ]);

        $customer = User::find($request->customer_id);
        if($customer) {
            $old_balance = $customer->wallet_balance;
            if ($request->wallet_type == 'add') {
                $amount =  '+'.$request->amount;
                $total_amount =  $old_balance + $request->amount;
            }
            if ($request->wallet_type == 'minus') {
                $amount =  '-'.$request->amount;
                $total_amount =  $old_balance - $request->amount;
            }
            $customer->wallet_balance = $total_amount;
            $customer->save();

            //insert transaction
            $transaction = new Transaction();
            $transaction->type = 'wallet';
            $transaction->notes = $request->notes;
            $transaction->item_id = $customer->id;
            $transaction->payment_method = $request->payment_method;
            $transaction->transaction_details = $request->transaction_details;
            $transaction->amount = $amount;
            $transaction->total_amount = $total_amount;
            $transaction->customer_id = $customer->id;
            $transaction->created_by = Auth::guard('admin')->id();
            $transaction->status = 'paid';
            $transaction->save();
            Toastr::success($customer->name.'\'s wallet recharge success.');
            //send sms notify
            if($customer->mobile) {
                $customer_mobile = $customer->mobile;
                $wallet_type = ($request->wallet_type == 'minus') ? 'minus ' : 'added ';
                $msg = 'Dear customer, ' . $wallet_type . $request->amount . Config::get('siteSetting.currency_symble') . ' to your woadi wallet. Current balance ' . $total_amount . Config::get('siteSetting.currency_symble') . ' get up to 70% discount shop at '.$_SERVER['SERVER_NAME'];
                $this->sendSms($customer_mobile, $msg);
            }
        }else{
            Toastr::error('Wallet recharge failed customer not found.');
        }
        return back();
    }
}

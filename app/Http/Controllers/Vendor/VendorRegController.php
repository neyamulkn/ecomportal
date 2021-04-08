<?php

namespace App\Http\Controllers\Vendor;

use App\Models\Notification;
use App\Models\State;
use App\Traits\CreateSlug;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vendor;
use App\Models\GeneralSetting as GS;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class VendorRegController extends Controller
{
    use CreateSlug;

    public function __construct()
    {
        $this->middleware('guest:vendor', ['except' => ['logout']]);
    }

    public function registerForm() {
        $data['states'] = State::where('country_id', 18)->get();
        return view('vendors.register')->with($data);
    }

    public function register(Request $request) {

        $gs = GS::first();
        if ($gs->registration == 0) {
          Toastr::error('alert', 'Registration is closed by Admin');
          return back();
        }

        Session::put('state', $request->state);
        Session::put('city', $request->city);
        Session::put('area', $request->area);

        $validatedRequest = $request->validate([
            'shop_name' => 'required',
            'vendor_name' => 'required',
            'mobile' => 'required|min:11|numeric|regex:/(01)[0-9]/|unique:vendors',
            'password' => 'required|confirmed|min:6'
        ]);

        if($request->email){
            $request->validate([
               'email' => ['required', 'string', 'email', 'max:255', 'unique:vendors'],
            ]);
        }

        $mobile = trim($request->mobile);
        $email = trim($request->email);
        $password = trim($request['password']);

        $username = explode(' ', trim($request->shop_name))[0];
        $vendor = new Vendor;
        $vendor->shop_name = $request->shop_name;
        $vendor->slug = $this->createSlug('vendors', $request->shop_name);
        $vendor->vendor_name = $request->vendor_name;
        $vendor->username = $this->createSlug('vendors', $username, 'username');
        $vendor->email = $email;
        $vendor->mobile = $mobile;
        $vendor->country = $request->country;
        $vendor->state = $request->state;
        $vendor->city = $request->city;
        $vendor->area = ($request->area) ? $request->area : null;
        $vendor->address = $request->address;
        $vendor->email_verification_token = $gs->email_verification == 0 ? rand(1000, 9999):NULL;
        $vendor->mobile_verification_token = $gs->sms_verification == 0 ? rand(1000, 9999):NULL;

        $vendor->status = 'pending';
        $vendor->password = Hash::make($password);;
        $success = $vendor->save();

        if($success) {

            $emailOrMobile = ($request->email ? $request->email : $request->mobile);

            Cookie::queue('vendorEmailOrMobile',$mobile, time() + (86400));
            Cookie::queue('vendorPassword', $password, time() + (86400));

            //insert notification in database
            Notification::create([
                'type' => 'vendor-register',
                'fromUser' => $vendor->id,
                'toUser' => 0,
                'item_id' => $vendor->id,
                'notify' => 'register new seller',
            ]);
            Toastr::success('Registration in success.');
            return back()->with('success', $request->vendor_name. ', your information will be reviewed by Admin. We will let you know about the update (after review) through Phone\Email once it\'s been checked!');

        }else{
            Toastr::error('Registration failed try again.');
            return back()->withInput();
        }

        Toastr::error('Registration failed try again.');
        return back()->withInput();
    }
}

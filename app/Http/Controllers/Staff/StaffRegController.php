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
    public function registerForm() {
        $data['states'] = State::where('country_id', 18)->where('status', 1)->get();
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
            'mobile' => 'required|min:10|unique:vendors',
            'password' => 'required|confirmed'
        ]);
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
        $vendor->state = $request->state;
        $vendor->city = $request->city;
        $vendor->area = $request->area;
        $vendor->address = $request->address;
        $vendor->password = Hash::make($password);;
        $success = $vendor->save();

        if($success) {

            $fieldType = ($request->email ? 'email' : 'mobile');
            $emailOrMobile = ($request->email ? $request->email : $request->mobile);

            Cookie::queue('vendorEmailOrMobile',$mobile, time() + (86400));
            Cookie::queue('vendorPassword', $password, time() + (86400));

            if (Auth::guard('vendor')->attempt([$fieldType => $emailOrMobile, 'password' => $password,])) {
                //insert notification in database
                Notification::create([
                    'type' => 'vendor-register',
                    'fromUser' => Auth::guard('vendor')->id(),
                    'toUser' => 0,
                    'item_id' => Auth::guard('vendor')->id(),
                    'notify' => 'register new seller',
                ]);
                Toastr::success('Registration in success.');
                return redirect()->route('vendor.dashboard');
            }
        }else{
            Toastr::error('Registration failed try again.');
            return back()->withInput();
        }

        return back()->with('message', 'Your informations will be reviewed by Admin. We will let you know about the update (after review) through Phone\Email once it\'s been checked!');
    }
}

<?php

namespace App\Http\Controllers\Staff;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use App\Vendor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Validator;
use Session;

class StaffLoginController extends Controller
{

    public function __construct()
    {
      $this->middleware('guest:vendor', ['except' => ['logout']]);
    }

    public function loginForm() {
      return view('vendors.login');
    }

    public function login(Request $request) {


      $this->validate($request, [
            'emailOrMobile' => 'required',
            'password' => 'required',
        ]);

        $emailOrMobile = trim($request->emailOrMobile);
        $password = trim($request->password);
        //remember credentials
        Cookie::queue('vendorEmailOrMobile', $emailOrMobile, time() + (86400));
        Cookie::queue('vendorPassword', $password, time() + (86400));


        $fieldType = filter_var($request->emailOrMobile, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';

        $vendor = Vendor::where($fieldType, $emailOrMobile)->where('password', $password)->first();
       if (!empty($vendor) && ($vendor->status == 0 || $vendor->status == -1)) {
           Toastr::error('Your account is deactivated');
           return back()->with('error', 'Your account is deactivated');
       }


      elseif(Auth::guard('vendor')->attempt(array($fieldType => $emailOrMobile, 'password' => $password)))
      {
        Toastr::success('Logged in success.');
        return redirect()->intended(route('vendor.dashboard'));
      }
      else {
        Toastr::error( $fieldType. ' or password is invalid.');
        return back()->withInput();
      }
    }

    public function logout() {
      Auth::guard('vendor')->logout();
      Toastr::success('Just Logged Out!');
      return redirect('/');
    }
}

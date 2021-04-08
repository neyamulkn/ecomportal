<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\City;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\State;
use App\Vendor;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;

class VendorController extends Controller
{
    public function dashboard()
    {
        $vendor_id = Auth::guard('vendor')->id();
        $data= [];
        $data['allProducts'] = Product::where('vendor_id', $vendor_id)->count();
        $data['pendingProducts'] = Product::where('vendor_id', $vendor_id)->where('status', 'pending')->count();
        $data['outOfStock'] = Product::where('vendor_id', $vendor_id)->where('stock', '<=', 0)->count();
        $data['rejectProducts'] = Product::where('vendor_id', $vendor_id)->where('status', 'reject')->count();

        $data['popularProducts'] = Product::where('vendor_id', $vendor_id)->orderBy('views', 'desc')->take(5)->get();
        $data['recentOrders'] = $orders = Order::join('order_details', 'orders.order_id', 'order_details.order_id')
            ->where('payment_method', '!=', 'pending')
            ->groupBy('order_details.order_id')
            ->orderBy('order_details.id', 'desc')
            ->where('order_details.vendor_id', $vendor_id)
            ->selectRaw('order_details.*, count(qty) as quantity, sum(qty*price) as total_price, payment_method, currency_sign')->paginate(5);


        $data['allOrders'] = Order::join('order_details', 'orders.order_id', 'order_details.order_id')
            ->where('payment_method', '!=', 'pending')->where('vendor_id', $vendor_id)->count();
        $data['pendingOrders'] = Order::join('order_details', 'orders.order_id', 'order_details.order_id')
            ->where('payment_method', '!=', 'pending')->where('vendor_id', $vendor_id)->where('order_details.shipping_status', 'pending')->count();
        $data['completeOrders'] = Order::join('order_details', 'orders.order_id', 'order_details.order_id')
            ->where('payment_method', '!=', 'pending')->where('vendor_id', $vendor_id)->where('order_details.shipping_status', 'delivered')->count();
        $data['rejectOrders'] = Order::join('order_details', 'orders.order_id', 'order_details.order_id')
            ->where('payment_method', '!=', 'pending')->where('vendor_id', $vendor_id)->where('order_details.shipping_status', 'cancel')->count();

        return view('vendors.dashboard')->with($data);

    }

    public function profileEdit(){
        $vendor_id = Auth::guard('vendor')->id();
        $data['vendor'] = Vendor::find($vendor_id);
        $data['states'] = State::where('country_id', 18)->where('status', 1)->get();
        $data['cities'] = City::where('state_id', $data['vendor']->state )->where('status', 1)->get();
        $data['areas'] = Area::where('city_id', $data['vendor']->city )->where('status', 1)->get();
        return view('vendors.shop.profile')->with($data);
    }
    //profile update
    public function profileUpdate(Request $request){

        $vendor_id = Auth::guard('vendor')->id();
        $request->validate([
            'vendor_name' => 'required',
            'shop_name' => 'required',
            'state' => 'required',
            'city' => 'required',
            'area' => 'required',
            'mobile' => ['required','unique:vendors,mobile,'.$vendor_id],
            'email' => ['email','unique:vendors,email,'.$vendor_id],
        ]);

        $profile = Vendor::find($vendor_id);
        $profile->vendor_name = $request->vendor_name;
        $profile->shop_name = $request->shop_name;
        $profile->shop_dsc = $request->shop_dsc;
        $profile->mobile = $request->mobile;
        $profile->email = $request->email;
        $profile->state = $request->state;
        $profile->city = $request->city;
        $profile->area = $request->area;
        $profile->address= $request->address;
        $profile->holiday_mode= ($request->holiday_mode) ? 1 : null;

        $profile->save();
        Toastr::success('Profile update success');
        return back();
    }

    public function logoBanner(){
        return view('vendors.shop.logo');
    }

    public function logoBannerUpdate(Request $request){
        $vendor_id  = Auth::guard('vendor')->id();
        $profile = Vendor::find($vendor_id);
        if ($request->hasFile('logo')) {
            //delete image from folder
            $image_path = public_path('upload/vendors/logo/'. $profile->logo);
            if($profile->logo && file_exists($image_path)){
                unlink($image_path);
            }
            $image = $request->file('logo');
            $new_image_name = rand() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('upload/vendors/logo/'), $new_image_name);

            $profile->logo = $new_image_name;
        }
        if ($request->hasFile('banner')) {
            //delete image from folder
            $image_path = public_path('upload/vendors/banner/'. $profile->banner);
            if($profile->banner && file_exists($image_path)){
                unlink($image_path);
            }
            $image = $request->file('banner');
            $new_image_name = rand() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('upload/vendors/banner/'), $new_image_name);

            $profile->banner = $new_image_name;
        }
        $update = $profile->save();
        if($update){
            Toastr::success('Update success.');
        }else{
            Toastr::success('Update failed.');
        }

        return back();
    }

    //change Password
    public function passwordChange(Request $request){
        return view('vendors.shop.change-password');
    }

    //password update
    public function passwordUpdate(Request $request){
        $vendor_id  = Auth::guard('vendor')->id();
        $check = Vendor::find($vendor_id);
        if($check) {
            $this->validate($request, [
                'old_password' => 'required',
                'password' => 'required|confirmed:min:6'
            ]);

            $old_password = $check->password;
            if (Hash::check($request->old_password, $old_password)) {
                if (!Hash::check($request->password, $old_password)) {
                    $user = Vendor::find($vendor_id);
                    $user->password = Hash::make($request->password);
                    $user->save();
                    Toastr::success('Password successfully change.', 'Success');
                    return redirect()->back();
                } else {
                    Toastr::error('New password cannot be the same as old password.', 'Error');
                    return redirect()->back();
                }
            } else {
                Toastr::error('Old password not match', 'Error');
                return redirect()->back();
            }
        }else{
            Toastr::error('Sorry your password can\'t change.', 'Error');
            return redirect()->back();
        }
    }

}

<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Cart;
use App\Models\City;
use App\Models\GeneralSetting;
use App\Models\ShippingAddress;
use App\Models\ShippingMethod;
use App\Models\State;
use App\Traits\CreateSlug;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{
    use CreateSlug;
    //order checkout
    public function checkout(Request $request)
    {
        $user_id = 0;
        if(Auth::check()){
            $user_id = Auth::id();
        }else{
            $user_id = (Cookie::has('user_id') ? Cookie::get('user_id') :  Session::get('user_id'));
        }
        $data = [];
        $cartItems = Cart::with('get_product:id,shipping_cost')->where('user_id', $user_id);

        //if click direct checkout button forget product id
        if(isset($request->process)){  Cookie::queue(Cookie::forget('direct_checkout_product_id')); Session::forget('direct_checkout_product_id'); }
        //check direct checkout
        if(Cookie::has('direct_checkout_product_id') || Session::get('direct_checkout_product_id')){
            $direct_checkout_product_id = (Cookie::has('direct_checkout_product_id') ? Cookie::get('direct_checkout_product_id') :  Session::get('direct_checkout_product_id'));
            $cartItems = $cartItems->where('product_id', $direct_checkout_product_id);
        }

        $data['cartItems'] =  $cartItems->orderBy('id', 'desc')->get();

        if(count($data['cartItems'])>0){
            $data['states'] = State::where('country_id', 18)->where('status', 1)->get();
            $data['get_shipping'] = ShippingAddress::with(['get_state','get_city', 'get_area'])->where('user_id', $user_id)->get();

            if(count($data['get_shipping'])>0){
                return redirect()->route('shippingReview');
            }
            return view('frontend.checkout.checkout')->with($data);
        }else{
            Toastr::error("Your shopping cart is empty. You don\'t have any product to checkout.");
            return redirect('/');
        }
    }

    // get city by state
    public function get_city(Request $request, $id){
        $user_id = 0;
        if(Auth::check()){
            $user_id = Auth::id();
        }else{
            $user_id = (Cookie::has('user_id') ? Cookie::get('user_id') :  Session::get('user_id'));
        }
        $data = [];

        $cartItems = Cart::join('products', 'carts.product_id', 'products.id')
            ->where('user_id', $user_id)
            ->selectRaw('sum(qty*price) total_price, shipping_method, ship_region_id, shipping_cost, other_region_cost')
            ->groupBy('product_id');
            //check direct checkout
            if(Cookie::has('direct_checkout_product_id') || Session::get('direct_checkout_product_id')){
                $direct_checkout_product_id = (Cookie::has('direct_checkout_product_id') ? Cookie::get('direct_checkout_product_id') :  Session::get('direct_checkout_product_id'));
                $cartItems = $cartItems->where('product_id', $direct_checkout_product_id);
            }
            $cartItems =  $cartItems->get();

        $total_amount = array_sum(array_column($cartItems->toArray(), 'total_price'));

        //set shipping region id for shipping cost
        Session::put('ship_region_id', $id);
        $total_shipping_cost = null;
        foreach($cartItems as $item) {
             $shipping_cost = $item->shipping_cost;
             //check shipping method
             if($item->shipping_method == 'location'){
                 if ($item->ship_region_id != $id) {
                    $shipping_cost = $item->other_region_cost;
                 }
             }
             if($shipping_cost > $total_shipping_cost) {
                 $total_shipping_cost = $shipping_cost;
             }
        }

        $cities = City::where('state_id', $id)->get();
        $output = $allcity = '';
        if(count($cities)>0){
            $allcity .= '<option value="">Select city</option>';
            foreach($cities as $city){
                $allcity .='<option '. (old("city") == $city->id ? "selected" : "" ).' value="'.$city->id.'">'.$city->name.'</option>';
            }
        }
        $coupon_discount = (Session::get('couponType') == '%' ? $total_amount * Session::get('couponAmount') : Session::get('couponAmount') );
        $grandTotal = ($total_amount + $total_shipping_cost) - $coupon_discount;
        $output = array( 'status' => true, 'shipping_cost' => $total_shipping_cost, 'couponAmount' => $coupon_discount, 'grandTotal' => $grandTotal, 'allcity'  => $allcity);
        return response()->json($output);
    }
    //registration user
    public function ShippingRegister(Request $request) {

        if(!Auth::check()) {
            $gs = GeneralSetting::first();
            if ($gs->registration == 0) {
                Session::flash('alert', 'Registration is closed by Admin');
                Toastr::error('Registration is closed by Admin');
                return back();
            }

            $request->validate([
                'name' => 'required',
                'mobile' => 'required|min:11|numeric|regex:/(01)[0-9]/'. ($request->account == 'register') ? '|unique:users' : '',
                'region' => 'required',
                'city' => 'required',
                'area' => 'required',
                'address' => 'required',
            ]);

            $username = $this->createSlug('users', $request->name, 'username');
            $username = trim($username, '-');
            $password = ($request['password']) ? $request['password'] : rand(100000, 999999);
            $user = new User;
            $user->name = $request->name;
            $user->username = $username;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->region = $request->region;
            $user->city = $request->city;
            $user->area = $request->area;
            $user->address = $request->address;
            $user->password = Hash::make($password);
            $user->email_verification_token = $gs->email_verification == 1 ? rand(1000, 9999) : NULL;
            $user->mobile_verification_token = $gs->sms_verification == 1 ? rand(1000, 9999) : NULL;
            $new_user = $user->save();
            if ($new_user) {
                $user_id = (Cookie::has('user_id') ? Cookie::get('user_id') :  Session::get('user_id'));

                Auth::attempt([ 'username' => $username, 'password' => $password, ]);
                //send mobile notify
                if(Auth::user()->mobile){
                    $customer_mobile = Auth::user()->mobile;
                    $msg = 'Hello '.Auth::user()->name.', Thank you for registering with woadi.com. Woadi provide worry free online shopping.';
                    $this->sendSms($customer_mobile, $msg);
                }
                Cart::where('user_id', $user_id)->update(['user_id' => Auth::id()]);
                //check duplicate records
                $duplicateRecords = Cart::select('product_id')
                    ->where('user_id', Auth::id())
                    ->selectRaw( 'id, count("product_id") as occurences')
                    ->groupBy('product_id')
                    ->having('occurences', '>', 1)
                    ->get();
                //delete duplicate record
                foreach($duplicateRecords as $record) {
                    $record->where('id', $record->id)->delete();
                }
            }
        }

        //if shipping_billing is checked then check validation
        if(!$request->shipping_address) {
            $request->validate([
                'shipping_name' => 'required',
                'shipping_phone' => 'required',
                'shipping_region' => 'required',
                'shipping_city' => 'required',
                'shipping_area' => 'required',
                'ship_address' => 'required',
            ]);
        }

        $shipping = new ShippingAddress();
        $shipping->user_id = Auth::id();
        $shipping->name = ($request->shipping_name) ? $request->shipping_name : $request->name;
        $shipping->email = ($request->shipping_email) ? $request->shipping_email : $request->email;
        $shipping->phone = ($request->shipping_phone) ? $request->shipping_phone : $request->mobile;
        $shipping->region = ($request->shipping_region) ? $request->shipping_region : $request->region;
        $shipping->city = ($request->shipping_city) ? $request->shipping_city : $request->city;
        $shipping->area = ($request->shipping_area) ? $request->shipping_area : $request->area;
        $shipping->address = ($request->ship_address) ? $request->ship_address : $request->address;
        $store = $shipping->save();

        if($store){
            Toastr::success('Shipping address added successful.');
        }else{
            Toastr::error("Shipping address cann\'t added.");
        }
        return redirect()->route('shippingReview');
    }

    //shipping review & choose one addresss
    public function shippingReview()
    {
        $user_id = 0;
        if(Auth::check()){
            $user_id = Auth::id();
        }else{
            $user_id = (Cookie::has('user_id') ? Cookie::get('user_id') :  Session::get('user_id'));
        }
        $data = [];
        $cartItems = Cart::with('get_product:id,shipping_cost')->where('user_id', $user_id);
        //check direct checkout
        if(Cookie::has('direct_checkout_product_id') || Session::get('direct_checkout_product_id')){
            $direct_checkout_product_id = (Cookie::has('direct_checkout_product_id') ? Cookie::get('direct_checkout_product_id') :  Session::get('direct_checkout_product_id'));
            $cartItems = $cartItems->where('product_id', $direct_checkout_product_id);
        }
        $data['cartItems'] =  $cartItems->orderBy('id', 'desc')->get();

        if(count($data['cartItems'])>0){
            $data['states'] = State::where('country_id', 18)->where('status', 1)->get();
            $data['get_shipping'] = ShippingAddress::with(['get_state','get_city', 'get_area'])->where('user_id', $user_id)->get();

            if(count($data['get_shipping'])>0){
                $data['shipping_methods'] = ShippingMethod::where('status', 1)->orderBy('position', 'asc')->selectRaw('id, name, logo, duration')->get();
                return view('frontend.checkout.shipping_review')->with($data);
            }else{
                return back();
            }

        }else{
            Toastr::error("Your shopping cart is empty. You don\'t have any product to checkout.");
            return redirect('/');
        }
    }

    // get shipping address by shipping id
    public function getShippingAddress($shipping_id){

        $get_shipping = ShippingAddress::with(['get_state','get_city', 'get_area'])->where('user_id', Auth::id())->where('id', $shipping_id)->first();
        if($get_shipping) {
            //set shipping address by region id
            Session::put('ship_region_id', $get_shipping->region);
            //get shipping details by region id
           $shipping_addess =  '
                <div class="form-group" > <strong><i class="fa fa-user"></i></strong> '.$get_shipping->name.' </div>
                <div class="form-group" >  <strong><i class="fa fa-envelope"></i></strong> '.$get_shipping->email.' </div>
                <div class="form-group" > <strong><i class="fa fa-phone"></i></strong> '.$get_shipping->phone.' </div>
                <div class="form-group" > <strong><i class="fa fa-map-marker"></i></strong>'.
                        $get_shipping->address .', '.
                        $get_shipping->get_area->name .', '.
                        $get_shipping->get_city->name .', '.
                        $get_shipping->get_state->name .
               '</div>';
            $user_id = 0;
            if (Auth::check()) {
                $user_id = Auth::id();
            } else {
                $user_id = (Cookie::has('user_id') ? Cookie::get('user_id') :  Session::get('user_id'));
            }
            $cartItems = Cart::join('products', 'carts.product_id', 'products.id')
                ->where('user_id', $user_id)
                ->selectRaw('sum(qty*price) total_price, shipping_method, ship_region_id, shipping_cost, other_region_cost')
                ->groupBy('product_id');
                //check direct checkout
                if(Cookie::has('direct_checkout_product_id') || Session::get('direct_checkout_product_id')){
                    $direct_checkout_product_id = (Cookie::has('direct_checkout_product_id') ? Cookie::get('direct_checkout_product_id') :  Session::get('direct_checkout_product_id'));
                    $cartItems = $cartItems->where('product_id', $direct_checkout_product_id);
                }
                $cartItems = $cartItems->get();
            $total_amount = array_sum(array_column($cartItems->toArray(), 'total_price'));

            $total_shipping_cost = null;
            foreach ($cartItems as $item) {
                $shipping_cost = $item->shipping_cost;
                //check shipping method
                if ($item->shipping_method == 'location') {
                    if ($item->ship_region_id != $get_shipping->region) {
                        $shipping_cost = $item->other_region_cost;
                    }
                }
                if($shipping_cost > $total_shipping_cost) {
                    $total_shipping_cost = $shipping_cost;
                }
            }
            //calculate coupon discount
            $coupon_discount = round(Session::get('couponType') == '%' ? $total_amount * Session::get('couponAmount') : Session::get('couponAmount'), 2);
            $grandTotal = round(($total_amount + $total_shipping_cost) - $coupon_discount, 2);
            $output = array('status' => true, 'shipping_addess' => $shipping_addess, 'shipping_cost' => $total_shipping_cost, 'couponAmount' => $coupon_discount, 'grandTotal' => $grandTotal);
        }else{
            $output = array('status' => false);
        }
        return response()->json($output);
    }



}

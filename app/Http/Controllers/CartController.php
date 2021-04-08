<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariationDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function cartAdd(Request $request)
    {
        $product = Product::with('offer_discount')->find($request->product_id);
        $qty = 0;
        $selling_price = $product->selling_price;
        $user_id = rand(1000000000, 9999999999);
        if(Auth::check()){
            $user_id = Auth::id();
        }else{
            if(Cookie::has('user_id') || Session::get('user_id')){
                $user_id = (Cookie::has('user_id') ? Cookie::get('user_id') :  Session::get('user_id'));
            }else{
                Session::put('user_id', $user_id );
                Cookie::queue('user_id', $user_id, time() + (86400));
            }
        }
        $cart_user = Cart::where('product_id', $product->id)->where('user_id', $user_id)->first();
        if($cart_user  && !$request->quantity){
            $qty = $cart_user->qty + 1;
        }else{
            $qty = $request->quantity;
        }
        //check quantity
        if($qty > $product->stock) {
            $output = array(
                'status' => 'error',
                'msg' => 'Out of stock'
            );
            return $output;
        }

        $attributes = $request->except(['product_id', '_token', 'quantity', 'buyDirect']);
        $variations = ProductVariationDetails::where('product_id', $request->product_id)->whereIn('attributeValue_name', array_values($attributes))->get();
        if(count($variations)>0){
            $variation_price = 0;
            foreach ($variations as $variation){
                if($variation->price > $variation_price){
                    $variation_price = $variation->price;
                }
            }
            $selling_price = $variation_price;
        }
        $attributes = json_encode($attributes);

        $price = $product->selling_price;
        $discount = null;
        //check offer active/inactive
        if($product->offer_discount && $product->offer_discount->offer != null){
            $discount = $product->offer_discount->offer_discount;
            $discount_type = $product->offer_discount->discount_type;
        }else{
            $discount = $product->discount;
            $discount_type = $product->discount_type;
        }
        if($discount){
            $calculate_discount = HelperController::calculate_discount($selling_price, $discount, $discount_type );
            $price = $calculate_discount['price'];
        }else{
            $price = $selling_price;
        }

        if($cart_user){
            $data = ['qty' => (isset($request->quantity)) ? $request->quantity : $cart_user->qty+1, 'price' => $price];
            //check attributes set or not
            if($request->quantity){
                $data = array_merge(['attributes' => $attributes], $data);
            }
            $cart_user->update($data);
        }else{
            $data = [
                'user_id' => $user_id,
                'product_id' => $request->product_id,
                'title' => $product->title,
                'slug' => $product->slug,
                'image' => $product->feature_image,
                'qty' => (isset($request->quantity)) ? $request->quantity : 1,
                'price' => $price,
                'attributes' => $attributes,
                'offer_id' => (Session::has('offerId')) ? Session::get('offerId') : null,
            ];
            Cart::create($data);
        }

        $output = array(
            'status' => 'success',
            'title' => $product->title,
            'image' => $product->feature_image,
            'msg' => 'Product Added To Cart.'
        );
        return response()->json($output);
    }

    public function cartView()
    {
        Cookie::queue(Cookie::forget('direct_checkout_product_id'));
        Session::forget('direct_checkout_product_id');
        $user_id = 0;
        if(Auth::check()){
            $user_id = Auth::id();
        }else{
            $user_id = (Cookie::has('user_id') ? Cookie::get('user_id') :  Session::get('user_id'));
        }
        $cartItems = Cart::with('get_product')->where('user_id', $user_id)->orderBy('id', 'desc')->get();
        return view('frontend.carts.cart')->with(compact('cartItems'));
    }

    public function cartUpdate(Request $request)
    {
        $request->validate([
            'qty' => 'required:numeric|min:1'
        ]);

        if(Auth::check()){
            $user_id = Auth::id();
        }else{
            $user_id = (Cookie::has('user_id') ? Cookie::get('user_id') :  Session::get('user_id'));
        }
        $cart = Cart::with('get_product')->where('id', $request->id)->where('user_id', $user_id)->first();

        if($request->qty <= $cart->get_product->stock) {

            $cart->update(['qty' => $request->qty]);
            $cartItems = Cart::with('get_product')->where('user_id', $user_id);
            //check direct checkout
            if(Cookie::has('direct_checkout_product_id') || Session::has('direct_checkout_product_id')){
                $direct_checkout_product_id = (Cookie::has('direct_checkout_product_id') ? Cookie::get('direct_checkout_product_id') :  Session::get('direct_checkout_product_id'));
                $cartItems = $cartItems->where('product_id', $direct_checkout_product_id);
            }
            $cartItems = $cartItems->get();

            if($request->page == 'checkout'){
                return view('frontend.checkout.order_summery')->with(compact('cartItems'));
            }else{
                return view('frontend.carts.cart_summary')->with(compact('cartItems'));
            }

        }else{
            return response()->json(['status' => 'error', 'msg' => 'Out of stock']);
        }
    }

    public function itemRemove(Request $request, $id)
    {
        $user_id = 0;
        if(Auth::check()){
            $user_id = Auth::id();
        }else{
            $user_id = (Cookie::has('user_id') ? Cookie::get('user_id') :  Session::get('user_id'));
        }

        $cartItems = Cart::where('user_id', $user_id)->where('id', $id)->delete();
        if($cartItems){

            $cartItems = Cart::with('get_product')->where('user_id', $user_id)->get();
            if($request->page == 'checkout'){
                return view('frontend.checkout.order_summery')->with(compact('cartItems'));
            }
            return view('frontend.carts.cart_summary')->with(compact('cartItems'));

        }else{
            $output = array(
                'status' => 'error',
                'msg' => 'Cart item cannot delete.'
            );
        }
        return response()->json($output);
    }

    public function clearCart(){
        $user_id = 0;
        if(Auth::check()){
            $user_id = Auth::id();
        }else{
            $user_id = (Cookie::has('user_id') ? Cookie::get('user_id') :  Session::get('user_id'));
        }
        Cart::where('user_id', $user_id)->delete();
        //destroy coupon
        Session::forget('couponCode');
        Session::forget('couponAmount');
        return redirect()->back();
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CartButton;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class CartButtonController extends Controller
{
    //return lists
    public function index()
    {
        $cartButtons = CartButton::all();
        return view('admin.product.cartButton.cartButton')->with(compact('cartButtons'));
    }

    //insert return reason
    public function store(Request $request)
    {
        $butBtn = new CartButton();
        $butBtn->btn_name = $request->btn_name;
        $butBtn->status = ($request->status) ? 1 : 0;
        $store = $butBtn->save();
        Toastr::success('Cart Button Insert Success.');
        return back();
    }

    //edit reason
    public function edit($id)
    {
        $data = CartButton::find($id);
        echo view('admin.product.cartButton.cartButtonEdit')->with(compact('data'));
    }
    //update data
    public function update(Request $request)
    {
        $butBtn = CartButton::find($request->id);
        $butBtn->btn_name = $request->btn_name;
        $butBtn->status = ($request->status) ? 1 : 0;
        $store = $butBtn->save();
        Toastr::success('Cart Button update Success.');
        return back();

    }

    //delate reason
    public function delete($id)
    {
        $reason = CartButton::where('id', $id)->delete();

        if($reason){
            $output = [
                'status' => true,
                'msg' => 'Cart button deleted successfully.'
            ];
        }else{
            $output = [
                'status' => false,
                'msg' => 'Cart button cannot deleted.'
            ];
        }
        return response()->json($output);
    }
}

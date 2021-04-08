<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\ShippingCharge;
use App\Models\ShippingMethod;
use App\Traits\CreateSlug;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

class ShippingMethodController extends Controller
{
    use CreateSlug;
    public function shipping_method()
    {
        $shipping_methods = ShippingMethod::orderBy('position', 'asc')->get();
        $locations = City::where('status', 1)->orderBy('name', 'asc')->get();
        return view('admin.shipping.shipping_method')->with(compact('shipping_methods', 'locations'));
    }

    //insert shipping
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'cost' => 'required',
        ]);

        $data = new ShippingMethod();
        $data->location_id = ($request->location_id) ? json_encode($request->location_id) : '["all"]';
        $data->name = $request->name;
        $data->slug = $this->createSlug('shipping_methods', $request->name);
        $data->cost = $request->cost;
        $data->duration = $request->duration;
        $data->notes = $request->notes;
        $data->status = ($request->status ? 1 : 0);

        if ($request->hasFile('logo')) {
            $image = $request->file('logo');
            $new_image_name = rand() . '.' . $image->getClientOriginalExtension();
            $image_path = public_path('upload/images/shipping/' . $new_image_name);
            $image_resize = Image::make($image);
            $image_resize->resize(90, 60);
            $image_resize->save($image_path);
            $data->logo = $new_image_name;
        }

        $store = $data->save();
        if($store){
            Toastr::success('Shipping Method Create Successfully.');
        }else{
            Toastr::error('Shipping Method Cannot Create.!');
        }

        return back();
    }


    public function edit($id)
    {
        $data = ShippingMethod::find($id);
        $locations = City::where('status', 1)->orderBy('name', 'asc')->get();
        echo view('admin.shipping.edit.shipping_method')->with(compact('data', 'locations'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'cost' => 'required',
        ]);

        $data = ShippingMethod::find($request->id);
        $data->location_id = ($request->location_id) ? json_encode($request->location_id) : '["all"]';
        $data->name = $request->name;
        $data->cost = $request->cost;
        $data->duration = $request->duration;
        $data->notes = $request->notes;
        $data->status = ($request->status ? 1 : 0);

        if ($request->hasFile('logo')) {
            //delete image from folder
            $image_path = public_path('upload/images/shipping/'. $data->logo);
            if(file_exists($image_path) && $data->logo){
                unlink($image_path);
            }
            $image = $request->file('logo');
            $new_image_name = rand() . '.' . $image->getClientOriginalExtension();
            $image_path = public_path('upload/images/shipping/' . $new_image_name);
            $image_resize = Image::make($image);
            $image_resize->resize(90, 60);
            $image_resize->save($image_path);
            $data->logo = $new_image_name;
        }

        $store = $data->save();
        if($store){
            Toastr::success('Shipping Method Create Successfully.');
        }else{
            Toastr::error('Shipping Method Cannot Create.!');
        }
        return back();
    }


    public function delete($id)
    {
        $shipping = ShippingMethod::find($id);

        if($shipping){
            $image_path = public_path('upload/images/shipping/'.$shipping->logo);
            if(file_exists($image_path) && $shipping->logo){
                unlink($image_path);
            }
            $shipping->delete();
            $output = [
                'status' => true,
                'msg' => 'Shipping deleted successfully.'
            ];
        }else{
            $output = [
                'status' => false,
                'msg' => 'Shipping cannot deleted.'
            ];
        }
        return response()->json($output);
    }
}

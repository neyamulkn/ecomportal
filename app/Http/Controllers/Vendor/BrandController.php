<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Traits\CreateSlug;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

class BrandController extends Controller
{
    use CreateSlug;

    public function index()
    {
        $vendor_id = Auth::guard('vendor')->id();
        $data['get_category'] = Category::where('parent_id', '=' , null)->orderBy('orderBy', 'asc')->get();

        $data['get_data'] = Brand::where('vendor_id', $vendor_id)->orderBy('id', 'desc')->get();
        return view('vendors.brand.brand')->with($data);
    }
    // store brand
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'name' => 'required',
        ]);
        $vendor_id = Auth::guard('vendor')->id();
        $data = new Brand();
        $data->vendor_id = $vendor_id;
        $data->category_id = $request->category_id;
        $data->name = $request->name;
        $data->slug = $this->createSlug('brands', $request->name);
        $data->status = 0;

        if ($request->hasFile('phato')) {
            $image = $request->file('phato');
            $new_image_name = rand() . '.' . $image->getClientOriginalExtension();

            $image_path = public_path('upload/images/brand/thumb/' . $new_image_name);
            $image_resize = Image::make($image);
            $image_resize->resize(120, 120);
            $image_resize->save($image_path);

//            $image->move(public_path('upload/images/brand'), $new_image_name);

            $data->logo = $new_image_name;
        }

        $store = $data->save();
        if($store){
            Toastr::success('Brand Create Successfully.');
        }else{
            Toastr::error('Brand Cannot Create.!');
        }

        return back();
    }

    //edit brand
    public function edit($id)
    {
        $vendor_id = Auth::guard('vendor')->id();
        $data['get_category'] = Category::where('parent_id', '=' , null)->orderBy('name', 'asc')->get();

        $data['data'] = Brand::where('vendor_id', $vendor_id)->where('id', $id)->first();
        echo view('vendors.brand.brand-edit')->with($data);
    }

    //update brand
    public function update(Request $request, Brand $brand)
    {
        $vendor_id = Auth::guard('vendor')->id();
        $request->validate([
            'category_id' => 'required',
            'name' => 'required',
        ]);
        $data = Brand::find($request->id);
        $data->vendor_id = $vendor_id;
        $data->category_id = $request->category_id;
        $data->name = $request->name;

        if ($request->hasFile('phato')) {
            //delete image from folder
            $image_path = public_path('upload/images/brand/thumb/'. $data->logo);
            if(file_exists($image_path) && $data->logo){
                unlink($image_path);
//                unlink(public_path('upload/images/brand/'. $data->logo));
            }
            $image = $request->file('phato');
            $new_image_name = rand() . '.' . $image->getClientOriginalExtension();

            $image_path = public_path('upload/images/brand/thumb/' . $new_image_name);
            $image_resize = Image::make($image);
            $image_resize->resize(120, 120);
            $image_resize->save($image_path);

//            $image->move(public_path('upload/images/brand'), $new_image_name);

            $data->logo = $new_image_name;
        }

        $store = $data->save();
        if($store){
            Toastr::success('Brand Update Successfully.');
        }else{
            Toastr::error('Brand Cannot Update.!');
        }

        return back();
    }


    public function delete($id)
    {
        $delete = Brand::find($id);

        if($delete){
            $image_path = public_path('upload/images/brand/thumb/'. $delete->logo);
            if(file_exists($image_path) && $delete->logo){
                unlink($image_path);
//                unlink(public_path('upload/images/brand/'. $delete->logo));
            }
            $delete->delete();

            $output = [
                'status' => true,
                'msg' => 'Item deleted successfully.'
            ];
        }else{
            $output = [
                'status' => false,
                'msg' => 'Item cannot deleted.'
            ];
        }
        return response()->json($output);
    }


}

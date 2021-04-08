<?php

namespace App\Http\Controllers\Staff;

use App\Models\Brand;
use App\Models\Country;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\HomepageSection;
use App\Models\PredefinedFeature;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductFeature;
use App\Models\ProductFeatureDetail;
use App\Models\ProductImage;
use App\Models\ProductVariation;
use App\Models\ProductVariationDetails;
use App\Models\ProductVideo;
use App\Models\State;
use App\Traits\CreateSlug;
use App\Traits\Vendor;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Intervention\Image\Facades\Image;
use Mockery\Exception;

class StaffProductController extends Controller
{
    use CreateSlug;
    use Vendor;
    // get product lists function
   public function index($status='')
    {
        $products = Product::with(['get_category', 'get_subcategory']);
        if($status){
            $products->where('status', $status);
        }
        $products = $products->where('vendor_id', $this->vendor_id())
            ->orderBy('id', 'desc')->paginate(15);
        return view('vendors.product.product-lists')->with(compact('products'));
    }

    // Add new product
    public function upload()
    {
        $data['regions'] = State::orderBy('name', 'asc')->get();
        $data['brands'] = Brand::orderBy('position', 'asc')->where('status', 1)->get();
        $data['categories'] = Category::where('parent_id', '=', null)->where('status', 1)->get();
        return view('vendors.product.product')->with($data);
    }

    //store new product
    public function store(Request $request)
    {

        //dd($request->attribute);
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'category' => 'required',
            'subcategory' => 'required',
            'purchase_price' => 'required',
            'selling_price' => 'required',
            'feature_image' => 'image|mimes:jpeg,png,jpg,gif,svg'
        ]);

        //auto select purpose
        Session::put('category_id', $request->category);
        Session::put('subcategory_id', $request->subcategory);
        Session::put('childcategory_id', ($request->childcategory) ? $request->childcategory : 0);
        Session::put('ship_region_id', $request->ship_region_id);
        Session::put('shipping_cost', $request->shipping_cost);
        Session::put('other_region_cost', $request->other_region_cost);
        Session::put('shipping_time', $request->shipping_time);
        Session::put('brand', ($request->brand ? $request->brand : null));
        // Insert product
        $data = new Product();
        $data->vendor_id = ($request->vendor_id ? $request->vendor_id : $this->vendor_id());
        $data->title = $request->title;
        $data->slug = $this->createSlug('products', $request->title);
//        $data->sku = $request->sku;
        $data->summery = $request->summery;
        $data->description = $request->description;
        $data->category_id = $request->category;
        $data->subcategory_id = $request->subcategory;
        $data->childcategory_id = $request->childcategory;
        $data->brand_id = ($request->brand ? $request->brand : null);
        $data->purchase_price = $request->purchase_price;
        $data->selling_price = $request->selling_price;
        $data->discount = ($request->discount) ? $request->discount : null;
        $data->discount_type = ($request->discount_type) ? $request->discount_type : null;
        $data->stock = ($request->stock) ? $request->stock : 0;
        $data->total_stock = ($request->stock) ? $request->stock : 0;
        $data->manufacture_date = $request->manufacture_date;
        $data->expired_date = $request->expired_date;
        $data->video = ($request->product_video) ? 1 : null;
        $data->weight = $request->weight;
        $data->length = $request->length;
        $data->width = $request->width;
        $data->height = $request->height;
        $data->shipping_method = ($request->shipping_method) ? $request->shipping_method : null;
        $data->order_price_above = ($request->order_price_above) ? $request->order_price_above : null;
        $data->free_shipping = ($request->free_shipping) ? 1 : null;
        $data->shipping_cost = ($request->shipping_cost) ? $request->shipping_cost : null;

        $data->ship_region_id = ($request->ship_region_id) ? $request->ship_region_id : null;
        $data->other_region_cost = ($request->other_region_cost) ? $request->other_region_cost : null;
        $data->shipping_time = ($request->shipping_time) ? $request->shipping_time : null;
        $data->meta_title = $request->meta_title;
        $data->meta_keywords = ($request->meta_keywords) ? implode(',', $request->meta_keywords) : null;
        $data->meta_description = $request->meta_description;
        $data->status = ($request->status ? 'active' : 0);
        $data->created_by = Auth::id();
        //if feature image set
        if ($request->hasFile('feature_image')) {
            $image = $request->file('feature_image');
            $new_image_name = $this->uniqueImagePath('products', 'feature_image', $image->getClientOriginalName());

            $image_path = public_path('upload/images/product/thumb/' . $new_image_name);
            $image_resize = Image::make($image);
            $image_resize->resize(200, 200);
            $image_resize->save($image_path);

            $image->move(public_path('upload/images/product'), $new_image_name);

            $data->feature_image = $new_image_name;
        }

        $store = $data->save();

        if($store) {
            $total_qty = 0;
            //insert variation
            if ($request->attribute) {

                foreach ($request->attribute as $attribute_id => $attr_value) {
                    //insert product feature name in feature table
                    $feature = new ProductVariation();
                    $feature->product_id = $data->id;
                    $feature->attribute_id = $attribute_id;
                    $feature->attribute_name = $attr_value;
                    $feature->in_display = 1;
                    $feature->save();

                    for ($i = 0; $i < count($request->attributeValue[$attribute_id]); $i++) {
                        //check weather attribute value set
                        if (array_key_exists($i, $request->attributeValue[$attribute_id])) {
                            //insert feature attribute details in ProductFeatureDetail table
                            $quantity = (isset($request->qty[$attribute_id]) && array_key_exists($i, $request->qty[$attribute_id]) ? $request->qty[$attribute_id][$i] : 0);
                            $feature_details = new ProductVariationDetails();
                            $feature_details->product_id = $data->id;
                            $feature_details->variation_id = $feature->id;
                            $feature_details->attributeValue_name = $request->attributeValue[$attribute_id][$i];
                            $feature_details->sku = (isset($request->sku[$attribute_id]) && array_key_exists($i, $request->sku[$attribute_id]) ? $request->sku[$attribute_id][$i] : 0);
                            $feature_details->quantity = $quantity;
                            $feature_details->price = (isset($request->price[$attribute_id]) && array_key_exists($i, $request->price[$attribute_id]) ? $request->price[$attribute_id][$i] : 0);
                            $feature_details->color = (isset($request->color[$attribute_id]) && array_key_exists($i, $request->color[$attribute_id]) ? $request->color[$attribute_id][$i] : null);

                            //if attribute variant image set
                            if (isset($request->image[$attribute_id]) && array_key_exists($i, $request->image[$attribute_id])) {
                                $image = $request->image[$attribute_id][$i];
                                $new_variantimage_name = $this->uniqueImagePath('product_feature_details', 'image', $image->getClientOriginalName());

                                $image_path = public_path('upload/images/product/varriant-product/thumb/' . $new_variantimage_name);
                                $image_resize = Image::make($image);
                                $image_resize->resize(250, 200);
                                $image_resize->save($image_path);

                                $image->move(public_path('upload/images/product/varriant-product'), $new_variantimage_name);
                                $feature_details->image = $new_variantimage_name;
                            }
                            $feature_details->save();
                        }
                        //count total stock quantity
                        $total_qty += $quantity;
                    }
                }
            }
            //insert additional Feature data
            if ($request->features) {
                try {
                    foreach ($request->features as $feature_id => $feature_name) {
                        if ($request->featureValue[$feature_id]) {
                            $extraFeature = new ProductFeature();
                            $extraFeature->product_id = $data->id;
                            $extraFeature->feature_id = $feature_id;
                            $extraFeature->name = $feature_name;
                            $extraFeature->value = $request->featureValue[$feature_id];
                            $extraFeature->save();
                        }
                    }
                } catch (Exception $exception) {

                }
            }
            // gallery Image upload
            if ($request->hasFile('gallery_image')) {
                $gallery_image = $request->file('gallery_image');
                foreach ($gallery_image as $image) {
                    $new_image_name = $this->uniqueImagePath('product_images', 'image_path', $image->getClientOriginalName());
                    $image_path = public_path('upload/images/product/gallery/thumb/' . $new_image_name);
                    $image_resize = Image::make($image);
                    $image_resize->resize(200, 200);
                    $image_resize->save($image_path);
                    $image->move(public_path('upload/images/product/gallery'), $new_image_name);

                    ProductImage::create([
                        'product_id' => $data->id,
                        'image_path' => $new_image_name
                    ]);
                }
            }
            //video upload
            if (isset($request->video_provider)) {
                for ($i = 0; $i < count($request->video_provider); $i++) {
                    ProductVideo::create(['product_id' => $data->id,
                        'provider' => $request->video_provider[$i],
                        'link' => $request->video_link[$i]
                    ]);
                }
            }
            //update total quantity
            if ($total_qty != 0){
                $productStock = Product::find($data->id);
                $productStock->stock = ($total_qty != 0) ? $total_qty : $request->stock;
                $productStock->total_stock = ($total_qty != 0) ? $total_qty : $request->stock;
                $productStock->save();
            }

            Toastr::success('Product Create Successfully.');
        }else{
            Toastr::error('Product Cannot Create.!');
        }
        return back();
    }

    //edit product
    public function edit($slug)
    {
        $data['product'] = Product::with('get_variations.get_variationDetails','videos')->where('slug', $slug)->first();
        $data['regions'] = State::orderBy('name', 'asc')->get();
        $data['brands'] = Brand::orderBy('name', 'asc')->where('status', 1)->get();

        // categroy id make array for query
        $category_id = [];
        if($data['product']->category_id) {
            $category_id[] = $data['product']->category_id;
        }if($data['product']->subcategory_id) {
            $category_id[] = $data['product']->subcategory_id;
        }if($data['product']->childcategory_id) {
            $category_id[] = $data['product']->childcategory_id;
        }
        //get not use attributes
        $data['attributes'] = ProductAttribute::whereIn('product_attributes.category_id', $category_id)
            ->doesntHave('variations')->get();

        $data['features'] = PredefinedFeature::leftJoin('product_features', 'predefined_features.id', 'product_features.feature_id')
            ->whereIn('category_id', $category_id)
            ->groupBy('product_features.feature_id')
            ->selectRaw('product_features.id, predefined_features.id as feature_id, predefined_features.is_required , predefined_features.name,  product_features.value')->get();

        $data['categories'] = Category::where('parent_id', '=', null)->where('status', 1)->get();
        $data['subcategories'] = Category::where('parent_id', '=', $data['product']->category_id)->where('status', 1)->get();
        $data['childcategories'] = Category::where('subcategory_id', '=', $data['product']->subcategory_id)->where('status', 1)->get();

        //dd($data['product']);
        return view('vendors.product.product-edit')->with($data);
    }

    //update product
    public function update(Request $request, $product_id)
    {

        //dd($request->all());
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'category' => 'required',
            'subcategory' => 'required',
            'purchase_price' => 'required',
            'selling_price' => 'required',
        ]);
        // Insert product
        $data = Product::find($product_id);
        $data->vendor_id = ($request->vendor_id ? $request->vendor_id : $this->vendor_id());
        $data->title = $request->title;
//        $data->sku = $request->sku;
        $data->summery = $request->summery;
        $data->description = $request->description;
        $data->category_id = $request->category;
        $data->subcategory_id = $request->subcategory;
        $data->childcategory_id = $request->childcategory;
        $data->brand_id = ($request->brand ? $request->brand : null);
        $data->purchase_price = $request->purchase_price;
        $data->selling_price = $request->selling_price;
        $data->discount = ($request->discount) ? $request->discount : null;
        $data->discount_type = ($request->discount_type) ? $request->discount_type : null;
        $data->stock = ($request->stock) ? $request->stock : 0;
        $data->total_stock = ($request->stock) ? $request->stock : 0;
        $data->manufacture_date = $request->manufacture_date;
        $data->expired_date = $request->expired_date;
        $data->video = ($request->product_video) ? 1 : null;
        $data->weight = $request->weight;
        $data->length = $request->length;
        $data->width = $request->width;
        $data->height = $request->height;
        $data->shipping_method = ($request->shipping_method) ? $request->shipping_method : null;
        $data->order_price_above = ($request->order_price_above) ? $request->order_price_above : null;
        $data->free_shipping = ($request->free_shipping) ? 1 : null;
        $data->shipping_cost = ($request->shipping_cost) ? $request->shipping_cost : null;
        $data->ship_region_id = ($request->ship_region_id) ? $request->ship_region_id : null;
        $data->other_region_cost = ($request->other_region_cost) ? $request->other_region_cost : null;
        $data->shipping_time = ($request->shipping_time) ? $request->shipping_time : null;
        $data->meta_title = $request->meta_title;
        $data->meta_keywords = ($request->meta_keywords) ? implode(',', $request->meta_keywords) : null;
        $data->meta_description = $request->meta_description;
        $data->status = ($request->status ? 'active' : 0);
        $data->created_by = Auth::id();

        //if feature image set
        if ($request->hasFile('feature_image')) {

            $getimage_path = public_path('upload/images/product/'. $data->feature_image);
            if(file_exists($getimage_path) && $data->feature_image){
                unlink($getimage_path);
                unlink(public_path('upload/images/product/thumb/'. $data->feature_image));
            }

            $image = $request->file('feature_image');
            $new_image_name = $this->uniqueImagePath('products', 'feature_image', $image->getClientOriginalName());

            $image_path = public_path('upload/images/product/thumb/' . $new_image_name);
            $image_resize = Image::make($image);
            $image_resize->resize(200, 200);
            $image_resize->save($image_path);

            $image->move(public_path('upload/images/product'), $new_image_name);

            $data->feature_image = $new_image_name;
        }

        $update = $data->save();

        if($update){
            //update variation value
            if($request->featureUpdate){
                foreach ($request->featureUpdate as $attribute_id => $variation_id){

                    for ($i=0; $i< count($request->attributeValueUpdate[$attribute_id]); $i++){
                        //check weather attribute value set
                        if(array_key_exists($i, $request->attributeValueUpdate[$attribute_id])) {
                            //insert or update feature attribute details in ProductFeatureDetail table
                            $feature_details = ProductVariationDetails::where('attributeValue_name',  $request->attributeValueUpdate[$attribute_id][$i])
                                ->where('product_id', $product_id)->first();
                            if(!$feature_details){
                                $feature_details = new ProductVariationDetails();
                            }
                            $feature_details->product_id = $product_id;
                            $feature_details->variation_id = $variation_id;
                            $feature_details->attributeValue_name = $request->attributeValueUpdate[$attribute_id][$i];
                            $feature_details->sku = (isset($request->skuUpdate[$attribute_id]) && array_key_exists($i, $request->skuUpdate[$attribute_id]) ? $request->skuUpdate[$attribute_id][$i] : 0);
                            $feature_details->quantity = (isset($request->qtyUpdate[$attribute_id]) && array_key_exists($i, $request->qtyUpdate[$attribute_id]) ? $request->qtyUpdate[$attribute_id][$i] : 0);
                            $feature_details->price = (isset($request->priceUpdate[$attribute_id]) && array_key_exists($i, $request->priceUpdate[$attribute_id]) ? $request->priceUpdate[$attribute_id][$i] : 0);
                            $feature_details->color = (isset($request->colorUpdate[$attribute_id]) && array_key_exists($i, $request->colorUpdate[$attribute_id]) ? $request->colorUpdate[$attribute_id][$i] : null);

                            //if attribute variant image set
                            if (isset($request->imageUpdate[$attribute_id]) && array_key_exists($i, $request->imageUpdate[$attribute_id])) {
                                $image = $request->imageUpdate[$attribute_id][$i];
                                $new_variantimage_name = $this->uniqueImagePath('product_feature_details', 'image', $image->getClientOriginalName());

                                $image_path = public_path('upload/images/product/varriant-product/thumb/' . $new_variantimage_name);
                                $image_resize = Image::make($image);
                                $image_resize->resize(250, 200);
                                $image_resize->save($image_path);

                                $image->move(public_path('upload/images/product/varriant-product'), $new_variantimage_name);
                                $feature_details->image = $new_variantimage_name;
                            }
                            $feature_details->save();
                        }
                    }
                }
            }

            //insert new variation
            if($request->attribute){
                foreach ($request->attribute as $attribute_id => $attr_value){
                    //insert product feature name in feature table
                    $feature = new ProductVariation();
                    $feature->product_id = $data->id;
                    $feature->attribute_id = $attribute_id;
                    $feature->attribute_name = $attr_value;
                    $feature->in_display= 1;
                    $feature->save();

                    for ($i=0; $i< count($request->attributeValue[$attribute_id]); $i++){
                        //check weather attribute value set
                        if( $request->attributeValue[$attribute_id][$i] != null && array_key_exists($i, $request->attributeValue[$attribute_id])) {
                            //insert feature attribute details in ProductFeatureDetail table
                            $feature_details = new ProductVariationDetails();
                            $feature_details->product_id = $data->id;
                            $feature_details->variation_id = $feature->id;
                            $feature_details->attributeValue_name = $request->attributeValue[$attribute_id][$i];
                            $feature_details->sku = (isset($request->sku[$attribute_id]) && array_key_exists($i, $request->sku[$attribute_id]) ? $request->sku[$attribute_id][$i] : 0);
                            $feature_details->quantity = (isset($request->qty[$attribute_id]) && array_key_exists($i, $request->qty[$attribute_id]) ? $request->qty[$attribute_id][$i] : 0);
                            $feature_details->price = (isset($request->price[$attribute_id]) && array_key_exists($i, $request->price[$attribute_id]) ? $request->price[$attribute_id][$i] : 0);
                            $feature_details->color = (isset($request->color[$attribute_id]) && array_key_exists($i, $request->color[$attribute_id]) ? $request->color[$attribute_id][$i] : null);

                            //if attribute variant image set
                            if (isset($request->image[$attribute_id]) && array_key_exists($i, $request->image[$attribute_id])) {
                                $image = $request->image[$attribute_id][$i];
                                $new_variantimage_name = $this->uniqueImagePath('product_feature_details', 'image', $image->getClientOriginalName());

                                $image_path = public_path('upload/images/product/varriant-product/thumb/' . $new_variantimage_name);
                                $image_resize = Image::make($image);
                                $image_resize->resize(250, 200);
                                $image_resize->save($image_path);

                                $image->move(public_path('upload/images/product/varriant-product'), $new_variantimage_name);
                                $feature_details->image = $new_variantimage_name;
                            }
                            $feature_details->save();
                        }
                    }
                }
            }

            //insert or update product feature
            if($request->features){
                try {
                    foreach($request->features as $feature_id => $feature_name) {

                        $extraFeature = ProductFeature::where('product_id', $product_id)->where('feature_id', $feature_id)->first();
                        if(!$extraFeature){
                            $extraFeature = new ProductFeature();
                        }
                        $extraFeature->product_id = $product_id;
                        $extraFeature->feature_id = $feature_id;
                        $extraFeature->name = $feature_name;
                        $extraFeature->value = ($request->featureValue[$feature_id]) ? $request->featureValue[$feature_id] : null;
                        $extraFeature->save();

                    }
                }catch (Exception $exception){

                }
            }

            //video upload
            if(isset($request->video_provider)){
                for ($i=0; $i< count($request->video_provider); $i++) {
                    ProductVideo::updateOrCreate(['product_id' => $product_id,
                        'provider' => $request->video_provider[$i],
                        'link' => $request->video_link[$i]
                    ],['product_id' => $product_id,
                        'provider' => $request->video_provider[$i],
                        'link' => $request->video_link[$i]
                    ]);
                }
            }
        }

        Toastr::success('Product update Successfully.');
        return back();
    }

    // delete product
    public function delete($id)
    {
        $product = Product::find($id);
        if($product){
            $image_path = public_path('upload/images/product/'. $product->feature_image);
            if(file_exists($image_path) && $product->feature_image){
                unlink($image_path);
                unlink(public_path('upload/images/product/thumb/'. $product->phato));
            }

            $product->delete();
            $output = [
                'status' => true,
                'msg' => 'Product deleted successfully.'
            ];
        }else{
            $output = [
                'status' => false,
                'msg' => 'Product cannot deleted.'
            ];
        }
        return response()->json($output);
    }

    //get highlight popup
    public function highlight($product_id){
        $product = Product::find($product_id);
        if($product){
            return view('admin.product.hightlight')->with(compact('product'));
        }
        return false;
    }

    //add remove highlight product
    public function highlightAddRemove(Request $request){

        $section = HomepageSection::find($request->section_id);

        $products_id =  ($section->product_id) ? explode(',', $section->product_id) : [];

        if(in_array($request->product_id, $products_id)){
            //remove product id from array
            unset($products_id[array_search($request->product_id, $products_id)]);
            $output = [
                'status' => false,
                'msg' => 'Product remove successfully.'
            ];

        }else{
            //add product id in array
            array_push($products_id, $request->product_id);
            $output = [
                'status' => true,
                'msg' => 'Product added successfully.'
            ];
        }
        //update hompagesection table
        $section->update(['product_id' => implode(',', $products_id)]);

        return response()->json($output);

    }

    //insert gallery image
    public function storeGalleryImage(Request $request)
    {
        $request->validate([
            'gallery_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg'
        ]);
        // gallery Image upload
        if ($request->hasFile('gallery_image')) {
            $gallery_image = $request->file('gallery_image');
            foreach ($gallery_image as $image) {
                $new_image_name = $this->uniqueImagePath('product_images', 'image_path', $image->getClientOriginalName());
                $image_path = public_path('upload/images/product/gallery/thumb/' . $new_image_name);
                $image_resize = Image::make($image);
                $image_resize->resize(200, 200);
                $image_resize->save($image_path);
                $image->move(public_path('upload/images/product/gallery'), $new_image_name);
                ProductImage::create( [
                    'product_id' => $request->product_id,
                    'image_path' => $new_image_name
                ]);
            }

            Toastr::success('Gallery image upload successfully.');
            return back();
        }
        Toastr::error('Gallery image upload failed.');
        return back();
    }

    //display gallery image
    public function getGalleryImage($product_id){
        $product_images = ProductImage::where('product_id', $product_id)->get();

        return view('admin.product.gallery-images')->with(compact('product_images', 'product_id'));


    }

    // delete GalleryImage
    public function deleteGalleryImage($id)
    {
        $find = ProductImage::find($id);
        if($find){
            //delete image from folder
            $thumb_image_path = public_path('upload/images/product/gallery/thumb/'. $find->image_path);
            $image_path = public_path('upload/images/product/gallery/'. $find->image_path);
            if(file_exists($image_path) && $find->image_path){
                unlink($image_path);
                unlink($thumb_image_path);
            }
            $find->delete();
            $output = [
                'status' => true,
                'msg' => 'Gallery Image deleted successfully.'
            ];
        }else{
            $output = [
                'status' => false,
                'msg' => 'Gallery Image cannot deleted.'
            ];
        }
        return response()->json($output);
    }


}

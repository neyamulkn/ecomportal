<?php  
    $discount = null;
    //check offer active/inactive
    if($product->offer_discount && $product->offer_discount->offer != null){

        $selling_price = $product->selling_price;
        $discount = $product->offer_discount->offer_discount;
        $discount_type = $product->offer_discount->discount_type;
       
    }else{
        $selling_price = $product->selling_price;
        $discount = $product->discount;
        $discount_type = $product->discount_type;
    }
    if($discount){
	    $calculate_discount = App\Http\Controllers\HelperController::calculate_discount($selling_price, $discount, $discount_type );
	}
?>



<div  class="product-item-container">
	<div class="left-block ">
	    <div class="image product-image-container">
	        <a class="lt-image" href="{{ route('product_details', $product->slug) }}" >
            <img src="{{asset('upload/images/product/thumb/'. $product->feature_image)}}" class="img-1 img-responsive">
            @if($discount)
            <div class="box-label">
				<span class="label-sale">@if($discount_type == '%')-@endif{{$calculate_discount['discount']}}%</span>
			</div>
			@endif
            </a>
             @if(!Request::is('/'))
            <a title="Quickview product details" data-toggle="tooltip" class="btn-button btn-quickview quickview quickview_handler" onclick="quickview('{{$product->id}}')" href="javascript:void(0)"> <i class="fa fa-search"></i> </a>
            @endif
	    </div>
	  	<div class="box-label">
	  	</div>
	</div>
	<div class="right-block">
	   	<div class="caption">
	      	<h4><a href="{{ route('product_details', $product->slug) }}">{{Str::limit($product->title, 40)}}</a></h4>
	      	<div class="total-price clearfix" style="visibility: hidden; display: block;">
	        	<div class="price">
				    <label for="ratting5">
				       {{\App\Http\Controllers\HelperController::ratting(round($product->reviews->avg('ratting'), 1))}}
				    </label><br/>
				    
				        @if($discount)
					        <span class="price-new">{{Config::get('siteSetting.currency_symble')}}{{ $calculate_discount['price'] }}</span>
					        <span class="price-old">{{Config::get('siteSetting.currency_symble')}}{{$selling_price}}</span>
					    @else
					        <span class="price-new">{{Config::get('siteSetting.currency_symble')}}{{$selling_price}}</span>
					    @endif
				</div>
	      	</div>
	  	</div>
	  	<div class="button-group2">

	    	<button class="bt-cart addToCart" type="button" data-toggle="tooltip" title="Add to cart" onclick="addToCart({{$product->id}})" > <span>Add to cart</span></button>
	    	<button class="bt wishlist" type="button" title="Add to Wish List"  @if(Auth::check()) onclick="addToWishlist({{$product->id}})" data-toggle="tooltip" @else data-toggle="modal" data-target="#so_sociallogin" @endif><i class="fa fa-heart"></i></button>
	    	<button class="bt compare" type="button" data-toggle="tooltip" title="Compare this Product" onclick="addToCompare({{$product->id}})" ><i class="fa fa-exchange"></i></button>
	  </div>
	</div>
</div>
<div class="price price-left">
    <label for="ratting5">
       {{\App\Http\Controllers\HelperController::ratting(round($product->reviews->avg('ratting'), 1))}}
    </label><br/>
 
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

    @if($discount)
        <span class="price-new">{{Config::get('siteSetting.currency_symble')}}{{ $calculate_discount['price'] }}</span>
        <span class="price-old">{{Config::get('siteSetting.currency_symble')}}{{$selling_price}}</span>
    @else
        <span class="price-new">{{Config::get('siteSetting.currency_symble')}}{{$selling_price}}</span>
    @endif
</div>

@if($discount)
<div class="price-sale price-right">
    <span class="discount">
      @if($discount_type == '%')-@endif{{$calculate_discount['discount']}}%
    <strong>OFF</strong>
  </span>
</div>
@endif
@if(count($cartItems)>0)

<div id="content" class="col-sm-8 sticky-conent">
    <h2 class="secondary-title"> Cart Summary</h2>
    <div class="table-responsive">

        <table class="table table-bordered">
        <thead>
            <tr>
                <td class="text-center">Image</td>
                <td class="text-left">Product Name</td>
                <td class="text-right">Price</td>
                <td style="width: 15%" class="text-left">Quantity</td>
                
                <td class="text-right">Total</td>
                <td class="text-right">#</td>
            </tr>
        </thead>
        <tbody>
            <?php $total = $total_shipping_cost = 0; ?>
            @foreach($cartItems as $item)
                <?php 

                $total += $item->price*$item->qty;
                $shipping_cost = $item->get_product->shipping_cost;

                if($shipping_cost > $total_shipping_cost) {
                    $total_shipping_cost = $shipping_cost;
                 }

                ?>
            <tr id='carItem{{$item->id}}'>
                <td class="text-center"> <a href="{{route('product_details', $item->slug)}}"><img width="70" src="{{asset('upload/images/product/thumb/'.$item->image)}}" class="img-thumbnail"></a> </td>
                
                <td class="text-left attributes"><a href="{{route('product_details', $item->slug)}}">{{$item->title}}</a>
                    @if($item->attributes)<br>
                    @foreach(json_decode($item->attributes) as $key=>$value)
                    <small> {{$key}} : {{$value}} </small>
                    @endforeach
                    @endif
                </td>
                <td class="text-right">{{Config::get('siteSetting.currency_symble')}}<span class="amount">{{$item->price}}</span></td>
                <td class="text-left">
                    <div class="input-group btn-block" style="max-width: 200px;">
                    <input type="number" min="1" style="margin-right: 15px;" id="qtyTotal{{$item->id}}" onchange="cartUpdate({{$item->id}})" name="qtybutton" value="{{$item->qty}}" class="form-control">
                    <span class="input-group-btn">
                        <button style="padding: 7px;" type="button" onclick ="cartUpdate({{$item->id}})" data-toggle="tooltip" title="" class="btn btn-primary" data-original-titl
                    ="Update"><i class="fa fa-refresh"></i></button>
                    
                    </span></div>
                </td>
                
                <td class="text-right">{{Config::get('siteSetting.currency_symble')}}<span id="subtotal{{$item->id}}">{{$item->price*$item->qty}}</td>
                
                <td class="text-right"  data-toggle="tooltip"><button type="button" title="Remove Item" class="btn btn-danger" data-target="#delete" data-toggle="modal" onclick='cartDelete("{{route("cart.itemRemove", $item->id)}}")' data-original-title="Remove"><i class="fa fa-times"></i></button></td>
            </tr>
            @endforeach
        </tbody>
        </table>
    </div>

    <div class="buttons clearfix">
        <div class="pull-left"><a href="{{url('/')}}" class="btn btn-default">Back To Shopping</a></div>
        <div class="pull-right"><a onclick="return confirm('Are You Sure Clear All Cart Items.?')" class="btn btn-danger btn-sm" href="{{route('cart.clear')}}">Clear Cart</a></div>
    </div>

</div>
<div class="col-sm-4 sticky-conent">
    <h2 class="secondary-title">Cart Total</h2>
    <table class="table table-bordered">
        <tbody>
            <tr>
                <td class="text-right"><strong>Sub-Total:</strong></td>
                <td class="text-right"><span>{{Config::get('siteSetting.currency_symble')}}<span id="cartTotal">{{$total}}</span></span></td>
            </tr>
            <tr>
                <td class="text-right"><strong>Shipping Fee(+):</strong></td>
                <td class="text-right">+{{Config::get('siteSetting.currency_symble')}}{{$total_shipping_cost}}</td>
            </tr>

            <tr>
                <td class="text-right"><strong>VAT (0%):</strong></td>
                <td class="text-right">$0.00</td>
            </tr>

            <?php $coupon_discount = (Session::get('couponType') == '%' ? $total * Session::get('couponAmount') : Session::get('couponAmount') ); ?>

            <tr id="couponSection"  style="display: {{Session::has('couponAmount') ? 'table-row' : 'none'}}">
                <td class="text-right"><strong>Coupon Discount(-):</strong></td>
                <td class="text-right">-{{Config::get('siteSetting.currency_symble')}}<span id="couponAmount">{{$coupon_discount}}</span> </td>
            </tr>
          
            <tr><td colspan="2">
                <form id="couponForm" style="float: right;" method="get">
                <i>Enter your coupon code if you have one.</i>
                <div class="input-group">
                <input type="text" required name="coupon_code" id="coupon_code" value="{{Session::get('couponCode')}}" placeholder="Enter your coupon here" class="form-control">
                <span class="input-group-btn">
                        <input style="padding: 7px;" type="submit" value="Apply Coupon" id="couponBtn" data-loading-text="Loading..." class="btn btn-primary">
                    </span>
                </div>
                </form>
                </td>
            </tr>
            <tr>
                <td class="text-right"><strong>Grand Total:</strong></td>
                <td class="text-right">{{Config::get('siteSetting.currency_symble')}}<span  id="grandTotal">{{$total + $total_shipping_cost - $coupon_discount }}</td>
            </tr>
        </tbody>
    </table>

    <div><a id="checkout" style="margin-bottom:10px;width: 100%" href="{{route('checkout')}}" class="btn btn-success btn-lg">Proceed to Checkout</a></div>
</div>
@else
<div style="text-align: center;">
    <i style="font-size: 80px;" class="fa fa-shopping-cart"></i>
    <h1>Your cart is empty.</h1>
    <p>Looks line you have no items in your shopping cart.</p>
    Click here <a href="{{url('/')}}">Continue Shopping</a>
</div>
@endif

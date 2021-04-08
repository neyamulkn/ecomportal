<thead>
	<tr>
		<th class="text-left name" colspan="2">Product Name</th>
		<th class="text-center">Price</th>
		<th class="text-center quantity">Quantity</th>
		<th class="text-right total">Total</th>
	</tr>
</thead>
<tbody>
	<?php $total = $total_shipping_cost = 0; ?>
    @foreach($cartItems as $item)
        <?php 
            $total += $item->price*$item->qty;
            $shipping_cost = $item->get_product->shipping_cost;
            
             //if shipping region set
            if(Session::has('ship_region_id')){
                //check shipping method
                if($item->get_product->shipping_method == 'location'){
                    if ($item->get_product->ship_region_id !=  Session::get('ship_region_id')) {
                        $shipping_cost = $item->get_product->other_region_cost;
                    }
                }
            }
            if($shipping_cost > $total_shipping_cost) {
             	$total_shipping_cost = $shipping_cost;
         	}

        ?>
	<tr id='carItem{{$item->id}}'>
		<td class="text-left name"> <a href="{{route('product_details', $item->slug)}}"><img width="70" src="{{asset('upload/images/product/thumb/'.$item->image)}}" class="img-thumbnail"></a> </td>

        <td class="text-left attributes"><a href="{{route('product_details', $item->slug)}}">{{Str::limit($item->title, 50)}}</a>
        	@if($item->attributes)<br>
            @foreach(json_decode($item->attributes) as $key=>$value)
            <small> {{$key}} : {{$value}} </small>
            @endforeach
            @endif
        </td>
		<td class="text-center">{{Config::get('siteSetting.currency_symble')}}<span class="amount">{{$item->price}}</span></td>
		<td class="text-left quantity">
			<div class="input-group">
				<input type="text" min="1" id="qtyTotal{{$item->id}}" onchange="cartUpdate({{$item->id}})" name="qtybutton" value="{{$item->qty}}" size="1" class="form-control">
				<span class="input-group-btn">
					<span title="Remove Item" data-target="#delete" data-toggle="modal" onclick='cartDeleteConfirm("{{$item->id}}")' class="btn-delete" data-original-title="Remove"><i class="fa fa-trash-o"></i></span>
					<span data-toggle="tooltip" title="" data-product-key="317" onclick="cartUpdate({{$item->id}})" class="btn-update" data-original-title="Update"><i class="fa fa-refresh"></i></span>
				</span>
			</div>
		</td>
		<td class="text-right total">{{Config::get('siteSetting.currency_symble')}}<span id="subtotal{{$item->id}}">{{$item->price*$item->qty}}</td>
	</tr>
	@endforeach
</tbody>
<tfoot>
	<tr>
		<td colspan="4" class="text-right"><strong>Sub-Total:</strong></td>
		<td class="text-right">{{Config::get('siteSetting.currency_symble')}}<span id="cartTotal">{{$total}}</span></td>
		</tr>

		<tr>
		<td colspan="4" class="text-right"><strong>VAT (0%):</strong></td>
		<td class="text-right">$0.00</td>
		</tr>
		<tr>
		<td colspan="4" class="text-right"><strong>Shipping cost(+):</strong></td>
		<td class="text-right">+{{Config::get('siteSetting.currency_symble')}}<span id="shipping_cost">{{$total_shipping_cost}}</span></td>
		</tr>
		<?php $coupon_discount = (Session::get('couponType') == '%') ? ( $total * Session::get('couponAmount')) : Session::get('couponAmount'); ?>

		<tr id="couponSection"  style="display: {{Session::get('couponAmount') ? 'table-row' : 'none'}}">
		<td class="text-right" colspan="4"><strong>Coupon Discount(-):</strong></td>
		<td class="text-right">-{{Config::get('siteSetting.currency_symble')}}<span id="couponAmount">{{ $coupon_discount }}</span> </td>
		</tr>
		
		<tr ><td colspan="2"></td>
			<td>
                
                <form action="#" id="couponForm" method="get">
                	<div class="input-group">
	                <input type="text" required name="coupon_code" id="coupon_code" value="{{Session::get('couponCode')}}" placeholder="Enter your coupon here" class="form-control">
	                <span class="input-group-btn" style="display: block;">
	                        <input style="padding: 7px;" type="submit" value="Apply Coupon" id="couponBtn" data-loading-text="Loading..." class="btn btn-primary">
	                    </span>
	                </div>
                </form>
                </td>
            </tr>
		<tr>
		<td colspan="4" class="text-right"><strong>Total:</strong></td>
		<td class="text-right">{{Config::get('siteSetting.currency_symble')}}<span  id="grandTotal">{{ $total + $total_shipping_cost - $coupon_discount }}</td>
		</tr>
</tfoot>

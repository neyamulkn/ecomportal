<div class="row">
    <div class="col-4 col-md-2">
        <label class="text-right">Payment Status</label>
    </div>
    <div class="col-8 col-md-2">
        <select class="selectpicker" data-style="btn-sm @if($order->payment_status == 'paid') btn-success @elseif($order->payment_status == 'on-review') btn-primary @else btn-info @endif" class="form-control" id="order_status" onchange="changePaymentStatus(this.value, '{{$order->order_id}}')">
            <option  value="pending" @if($order->payment_status == 'pending') selected @endif >Pending</option>
            <option value="on-review" @if($order->payment_status == 'on-review') selected @endif >On Review</option>
            <option value="paid" @if($order->payment_status == 'paid') selected @endif >Paid</option>
        </select>
    </div>
    <div class="col-4 col-md-2">
        <label class="text-right">Delivery Status</label>
    </div>
    <div class="col-8 col-md-2">
        <select name="status" class="selectpicker" data-style="btn-sm @if($order->order_status == 'delivered') btn-success @elseif($order->order_status == 'accepted') btn-warning @elseif($order->order_status == 'cancel')  btn-danger @elseif($order->order_status == 'on-delivery') btn-primary @else btn-info @endif " id="order_status" onchange="changeOrderStatus(this.value, '{{$order->order_id}}')">
            <option value="pending" @if($order->order_status == 'pending') selected @endif>Pending</option>
            <option value="accepted" @if($order->order_status == 'accepted') selected @endif>Accepted</option>
            
            <option value="on-delivery" @if($order->order_status == 'ready-to-ship') selected @endif>Ready to ship</option>
            <option value="on-delivery" @if($order->order_status == 'on-delivery') selected @endif>On Delivery</option>
            <option value="delivered" @if($order->order_status == 'delivered') selected @endif>Delivered</option>
           <option value="cancel"  @if($order->order_status == 'cancel') selected @endif >Cancel</option>
        </select>
    </div>
</div>
   <hr/>
<div class="row">
    <div class="col-md-12">
        <div class="pull-left" style="float: left;max-width: 60%">
            <address>
                {{$order->shipping_name}}
                @if($order->shipping_email)<br>{{$order->shipping_email}}@endif
                <br>{{$order->shipping_phone}}
                <br>
                {!!
                    $order->shipping_address. ', '.
                    $order->shipping_area. ', '.
                    $order->shipping_city. ', '.
                    $order->shipping_region
                
                !!}
                @if($order->order_notes)<br><b style="font-weight: bold;">Notes: </b>{{$order->order_notes}}@endif
            </address>
        </div>
        <div class="pull-right text-right">
            <strong>Order ID:</strong> #{{$order->order_id}} <br>
            <b>Order Date:</b> {{Carbon\Carbon::parse($order->order_date)->format('M d, Y')}}<br> <b>Order Status:</b> {{ str_replace( '-', ' ', $order['order_status']) }} <br>
            <b>Payment Status:</b> {{str_replace( '-', ' ',$order->payment_status) }} <br>
            <b>Payment Method:</b> {{str_replace( '-', ' ',  $order->payment_method) }} <br>
            <b>Shipping Method:</b> @if($order->shipping_method){{ $order->shipping_method->name }} @endif<br>
          
        </div>
    </div>
    <div class="col-md-12">
        <div class="table-responsive" style="margin-top: 5px; clear: both;">
            <table class="table table-hover" style="width: 100%">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: right;">Sub Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $total = 0; ?>
                   @foreach($order->order_details as $item)
                      
                      <tr>
                        <td><img src="{{asset('upload/images/product/'.$item->product->feature_image)}}" width="48" height="38" ></td>
                        <td><a target="_blank" href="{{ route('product_details', $item->product->slug) }}"> {{Str::limit($item->product->title, 50)}} </a> <br>
                       @if($item->attributes) 
                       @foreach(json_decode($item->attributes) as $key=>$value)
                        <small> {{$key}} : {{$value}} </small>
                        @endforeach</td>
                        @endif
                        <td>{{$order->currency_sign. $item->price}}</td>
                        <td style="text-align: center;">{{$item->qty}}</td>
                        <td style="text-align: right;">{{$order->currency_sign. $item->price*$item->qty}}</td>
                        <td> <span class="label @if($item->shipping_status == 'delivered') label-success @elseif($item->shipping_status == 'accepted') label-warning @elseif($item->shipping_status == 'cancel')  label-danger @elseif($item->shipping_status == 'ready-to-ship') label-ready-ship @else label-info @endif ">{{$item->shipping_status}}</span></td>
                      </tr> 
                    @endforeach
                   
                </tbody>
                <tfoot style="text-align: right;">
                    <tr>
                        <td colspan="3"></td>
                        <td ><b>Sub-Total</b>
                        </td>
                        <td >{{$order->currency_sign . $order->total_price}}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                        <td ><b>Shipping Cost(+)</b>
                        </td>
                        <td >{{$order->currency_sign . $order->shipping_cost}}</td>
                        <td></td>
                    </tr>
                    @if($order['coupon_discount'] != null)
                    <tr>
                        <td colspan="3"></td>
                        <td ><b>Coupon Discount(-)</b>
                        </td>
                        <td >{{$order->currency_sign . $order->coupon_discount}}</td>
                        <td></td>
                    </tr>
                    @endif
                    <tr>
                        <td colspan="3"></td>
                        <td ><h3><b>Total</b></h3>
                        </td>
                        <td ><h3>{{$order->currency_sign . ($order->total_price + $order->shipping_cost - $order->coupon_discount)  }}</h3></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<div style="text-align: right;"> <a target="_blank" href="{{route('admin.orderInvoice', $order->order_id)}}" class="text-inverse" title="View Order Invoice" data-toggle="tooltip"><i class="ti-printer"></i> Print Order Invoice</a></div>
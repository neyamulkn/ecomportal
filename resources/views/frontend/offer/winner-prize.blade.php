@extends('layouts.frontend')
@section('title', 'Offer prize winner | '. Config::get('siteSetting.site_name') )

@section('metatag')
    <meta name="title" content="{{ $order->order_details[0]->product->title }} | {{$order->billing_name}}">
    <meta name="description" content="{{ $order->order_details[0]->product->title }}">
 
    <!-- Open Graph general (Facebook, Pinterest & Google+) -->
    <meta property="og:title" content="{{ $order->order_details[0]->product->title }} | {{$order->billing_name}}">
    <meta property="og:description" content="{{ $order->order_details[0]->product->title }}">
    @if($order->order_details[0]->product)
	<meta property="og:image" content="{{asset('upload/images/product/thumb/'.$order->order_details[0]->product->feature_image)}}">
	@endif
    
    <meta property="og:url" content="{{ url()->full() }}">
    <meta property="og:site_name" content="{{Config::get('siteSetting.site_name')}}">
    <meta property="og:locale" content="en">
    <meta property="og:type" content="website">
    <meta property="og:type" content="e-commerce">

    <!-- Schema.org for Google -->
    <meta itemprop="title" content="{{ $order->order_details[0]->product->title }}">
    <meta itemprop="description" content="{{ $order->order_details[0]->product->title }}">
    <meta itemprop="image" content="{{asset('upload/images/product/thumb/'.$order->order_details[0]->product->feature_image)}}">
 @endsection
@section('css')
	<style type="text/css">
		.order_success{
			text-align: center;
		}

	#prizeLoading
    {
        z-index: 999999; 
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        text-align: center;
        min-height: 200px;
        position: fixed;
        background: #000000fa url('{{ asset("upload/images/offer/kanamachi4.gif")}}') no-repeat center; 
    }   
	</style>
@endsection
@section('content')
	<!--  Start animation area -->
  
  <section >
	<!-- Main Container  -->
	<div class="main-container container" style="background: inherit;">
		<div class="row">
		
			<!--Middle Part Start-->
			<div id="content"  style="margin: 15px 0; background: #fffefef2; border-radius: 5px;" class="col-md-12">

				<div class="order_success">
					<div>
						<div><img src="https://i.pinimg.com/originals/ce/ff/f2/cefff279392c6931f4c8219c8c3134f5.gif" alt=""></div>
						<div>
							@if($order->order_details[0]->product)
							 <img src="{{asset('upload/images/product/thumb/'.$order->order_details[0]->product->feature_image)}}" class="img-1 img-responsive">
							@endif
						</div>
					<h5>{{$offer->title}} অফারে আপনি (<span style="color:#f5501f"> {{ $order->order_details[0]->product->title }} </span>) প্রোডাক্ট টি পেয়েছেন </h5>
					<p style="padding:0px;margin: 0px;">Your Order Tracking Id:<a href="{{ route('orderTracking') }}?order_id={{$order->order_id}}"> {{$order->order_id}}</a></p>
				</div>
				<h2 class="title" style="text-align: left;">Order Information</h2>

				<table class="table table-bordered table-hover">
					<thead>
						<tr>
							<td colspan="2" class="text-left">Order Details</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="width: 50%;" class="text-left"> 
								{{$order->billing_name}}<br>
								{{$order->billing_phone}}<br>
								@if($order->shipping_email){{$order->shipping_email}} <br>@endif
								{{
									$order->shipping_address. ', '.
									$order->shipping_area. ', '.
									$order->shipping_city. ', '.
									$order->shipping_region
								
								}} 
							</td>
							<td style="width: 50%;" class="text-left">
								<b>Order ID:</b> #{{$order->order_id}}
								<br>
								<b>Order Date:</b> {{Carbon\Carbon::parse($order->order_date)->format('M d, Y')}}
								<b>Shipping Status::</b> {{ $order->order_status }} <br>
								<b>Payment Status:</b> {{$order->payment_status}} <br>
								<b>Payment Method:</b> {{$order->payment_method}} <br>
							</td>
						</tr>
					</tbody>
				</table>
				
				<div class="table-responsive">
					<table class="table table-bordered table-hover">
						<thead>
							<tr>
								<td class="text-left">Product Name</td>
								<td class="text-right">Quantity</td>
								<td class="text-right">Price</td>
								<td class="text-right">Total</td>
								<td style="width: 20px;"></td>
							</tr>
						</thead>
						<tbody>
							@php $shipping_cost = 0; @endphp
							@foreach($order->order_details as $item)
                            	@php $shipping_cost += $item->shipping_charge; @endphp
							<tr>
								<td class="text-left">
									<a href="{{route('product_details', $item->product->slug)}}">{{Str::limit($item->product->title, 50)}}</a><br>
								</td>
								
								<td class="text-right">{{$item->qty}}</td>
								<td class="text-right">{{$order->currency_sign. $item->price}}</td>
								<td class="text-right">{{$order->currency_sign. $item->price*$item->qty}}</td>
								
							</tr>
							@endforeach
						</tbody>
						<tfoot>
							<tr>
								<td colspan="2"></td>
								<td class="text-right"><b>Sub-Total</b>
								</td>
								<td class="text-right">{{$order->currency_sign . $order->total_price}}</td>
								<td></td>
							</tr>
							<tr>
								<td colspan="2"></td>
								<td class="text-right"><b>Shipping Cost(+)</b>
								</td>
								<td class="text-right">@if($order->shipping_cost) {{$order->currency_sign . $order->shipping_cost}} @else Pending @endif</td>
								<td></td>
							</tr>
							
							<tr>
								<td colspan="2"></td>
								<td class="text-right"><b>Total</b>
								</td>
								<td class="text-right">{{$order->currency_sign . ($order->total_price + $order->shipping_cost)  }}</td>
								<td></td>
							</tr>
						</tfoot>
					</table>
				</div>
				
				<div class="buttons clearfix">
					<div class="pull-right"><a class="btn btn-primary" href="{{url('/')}}">Back To Shop</a>
					</div>
				</div>
			</div>
			<!--Middle Part End-->
			
		</div>
	</div>
	<!-- //Main Container -->
	</section>
@endsection

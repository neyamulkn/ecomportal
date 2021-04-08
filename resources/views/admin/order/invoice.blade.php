@extends('layouts.admin-master')
@section('title', 'Invoice ')
@section('css')
<style type="text/css">
    b, strong {
    font-weight: 700;
}
</style>
@endsection
@section('content')
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Bread crumb and right sidebar toggle -->
                <!-- ============================================================== -->
                <div class="row page-titles">
                    <div class="col-md-5 align-self-center">
                        <h4 class="text-themecolor">Invoice</h4>
                    </div>
                    <div class="col-md-7 align-self-center text-right">
                        <div class="d-flex justify-content-end align-items-center">
                            
                            <a href="{{route('admin.orderList')}}" class="btn btn-info d-none d-lg-block m-l-15"><i class="fa fa-eye"></i> Order list</a>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- End Bread crumb and right sidebar toggle -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->
                <div class="row">
                    <div class="container">
                        <div class="col-md-12">
                            <div class="card card-body printableArea">
                                <h3><b>INVOICE NO: </b> <span class="pull-right">#{{$order->order_id}}</span></h3>
                                <hr>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="pull-left" style="float: left;">
                                            <div style="width:160px; height: 55px;">
                                                <img style="height: 100%; width: 100%;" src="{{asset('upload/images/logo/'.(Config::get('siteSetting.invoice_logo') ? Config::get('siteSetting.invoice_logo'): Config::get('siteSetting.logo')))}}" title="Home" alt="Logo">
                                            </div>
                                        </div>

                                        <div class="pull-right text-right">
                                            <address>
                                            {{Config::get('siteSetting.address')}}<br/>
                                            Phone: {{Config::get('siteSetting.phone')}}<br/>
                                            Email: {{Config::get('siteSetting.email')}}
                                            </address>
                                        </div>
                                    </div>
                                </div>
                                <hr>
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
                                            @if($order->shipping_method)<b>Shipping Method:</b> {{ $order->shipping_method->name }} <br>@endif
                                          
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
                                                        <th>Quantity</th>
                                                        <th style="text-align: right;">Sub Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $total = 0; ?>
                                                   @foreach($order->order_details as $item)
                                                      
                                                      <tr>
                                                        <td><img src="{{asset('upload/images/product/'.$item->product->feature_image)}}" width="48" height="38" ></td>
                                                        <td>{{ $item->product->title }} <br>
                                                        @if($item->attributes) @foreach(json_decode($item->attributes) as $key=>$value)
                                                        <small> {{$key}} : {{$value}} </small>
                                                        @endforeach
                                                        @endif
                                                    </td>
                                                        
                                                        <td>{{$order->currency_sign. $item->price}}</td>
                                                        <td>{{$item->qty}}</td>
                                                        <td style="text-align: right;">{{$order->currency_sign. $item->price*$item->qty}}</td>
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
                            </div>

                            <div class="text-right no-print">
                                <input id="invoice_id" type="hidden" name="invoice_id" value="{{$order->order_id}}">
                                <input type="hidden" id="all_orders" name="all_orders" value="{{$order->order_id}}">
                                <button id="print" onclick="invoicePrintBy('{{$order->order_id}}')" class="btn btn-default btn-outline" type="button"> <span><i class="fa fa-print"></i> Print</span> </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- End PAge Content -->
           
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
@endsection

@section('js')
    <script src="{{asset('js/pages/jquery.PrintArea.js')}}" type="text/JavaScript"></script>
    <script>
    $(document).ready(function() {
        $("#print").click(function() {
            var mode = 'iframe'; //popup
            var close = mode == "popup";
            var options = {
                mode: mode,
                popClose: close
            };
            $("div.printableArea").printArea(options);
        });
    });

   function invoicePrintBy(order_id) {
    
            var link = '{{route("admin.invoicePrintBy", ":order_id")}}';
            link = link.replace(':order_id', order_id);
            var invoice_id = $('#invoice_id').val();
            var all_orders = $('#all_orders').val();
          
            $.ajax({
                url:link,
                method:"get",
                data:{invoice_id:invoice_id,all_orders:all_orders},
                success:function(data){
                    
                }
            });
        }
       
    </script>
@endsection
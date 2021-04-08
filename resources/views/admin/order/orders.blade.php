@extends('layouts.admin-master')
@section('title', 'Order lists')
@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('css/pages/stylish-tooltip.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{asset('assets')}}/node_modules/datatables.net-bs4/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" type="text/css"
        href="{{asset('assets')}}/node_modules/datatables.net-bs4/css/responsive.dataTables.min.css">
    <style type="text/css">
        .payment-method, .customer{
            max-width: 150px !important; font-size: 12px;text-align: center;
        }
        .bootstrap-select:not([class*=col-]):not([class*=form-control]):not(.input-group-btn){max-width: 100px;}
    </style>

    <!-- page CSS -->
    <link href="{{asset('assets')}}/node_modules/bootstrap-select/bootstrap-select.min.css" rel="stylesheet" />
    
@endsection
@section('content')
            <?php 
                $all = $pending = $offerPendingOrder = $accepted = $readyToship = $on_delivery = $delivered = $cancel = 0;
                foreach($orderCount as $order_status){
      
                    if($order_status->order_status == 'pending'){
                        if($order_status->offer_id != null){
                            $offerPendingOrder +=1 ; 
                        }else{
                            $pending +=1 ; 
                        }
                    }
                    if($order_status->order_status == 'accepted'){ $accepted +=1 ; }
                    if($order_status->order_status == 'ready-to-ship'){ $readyToship +=1 ; }
                    if($order_status->order_status == 'on-delivery'){ $on_delivery +=1 ; }
                    if($order_status->order_status == 'delivered'){ $delivered +=1 ; }
                    if($order_status->order_status == 'cancel'){ $cancel +=1 ; }
                }
                $all = $pending+$offerPendingOrder+$accepted+ $readyToship +$on_delivery+ $delivered +$cancel;

            ?>
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
                        <h4 class="text-themecolor">Total Order ({{ $all }})</h4>
                    </div>
                    <div class="col-md-7 align-self-center text-right">
                        <div class="d-flex justify-content-end align-items-center">
                            
                            <a class="btn btn-info btn-sm d-none d-lg-block m-l-15" href="{{ route('admin.orderList') }}"><i class="fa fa-eye"></i> Order lists</a>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- End Bread crumb and right sidebar toggle -->
   
                <div class="row">
                
                    <!-- Column -->
                    <div class="col-md-2">
                    <div class="card">
                        <div class="card-body" style="padding-left:3px;padding-right: 3px;">
                            <h5 class="card-title">Pending Order</h5>
                            <div class="d-flex no-block align-items-center">
                                <span class="display-5 text-info">R</span>
                                <a href="{{route('admin.orderList', 'pending')}}?offer=regular" class="link display-5 ml-auto">{{$pending}}</a>,
                                <span class="display-5 text-info">O</span>
                                <a href="{{route('admin.orderList', 'pending')}}?offer=offer" class="link display-5 ml-auto">{{$offerPendingOrder}}</a>
                            </div>
                        </div>
                    </div>
                    </div>
                    
                    <div class="col-md-2">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Accept Order</h5>
                            <div class="d-flex no-block align-items-center">
                                <span class="display-5 text-primary"><i class="fa fa-shipping-fast"></i></span>
                                <a href="{{route('admin.orderList', 'accepted')}}" class="link display-5 ml-auto">{{$accepted}}</a>
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class="col-md-2">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Ready To Ship</h5>
                            <div class="d-flex no-block align-items-center">
                                <span class="display-5 text-primary"><i class="fa fa-list-ol"></i></span>
                                <a href="{{route('admin.orderList', 'ready-to-ship')}}" class="link display-5 ml-auto">{{$readyToship}}</a>
                            </div>
                        </div>
                    </div>
                    </div>
                    <!-- Column -->
                    <div class="col-md-2">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">On Delivery</h5>
                            <div class="d-flex no-block align-items-center">
                                <span class="display-5 text-info"><i class="fa fa-shipping-fast"></i></span>
                                <a href="{{route('admin.orderList', 'on-delivery')}}" class="link display-5 ml-auto">{{$on_delivery}}</a>
                            </div>
                        </div>
                    </div>
                    </div>
                    <!-- Column -->
                    <div class="col-md-2">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Cancel</h5>
                            <div class="d-flex no-block align-items-center">
                                <span class="display-5 text-danger"><i class="fa fa-times"></i></span>
                                <a href="{{route('admin.orderList', 'cancel')}}" class="link display-5 ml-auto">{{$cancel}}</a>
                            </div>
                        </div>
                    </div>
                    </div>
                    <!-- Column -->
                    <div class="col-md-2">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Complete</h5>
                            <div class="d-flex no-block align-items-center">
                                <span class="display-5 text-success"><i class="fa fa-handshake"></i></span>
                                <a href="{{route('admin.orderList', 'delivered')}}" class="link display-5 ml-auto">{{$delivered}}</a>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card" style="margin-bottom: 2px;">

                            <form action="{{route('admin.orderList')}}" method="get">

                                <div class="form-body">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">Order Id</label>
                                                    <input name="order_id" value="{{ Request::get('order_id')}}" type="text" placeholder="W-1269345456" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Customer</label>
                                                    <input name="customer" value="{{ Request::get('customer')}}" type="text" placeholder="name or mobile or email" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">Order Status  </label>
                                                    <select name="status" class="form-control">
                                                        <option value="">Select Status</option>
                                                        <option value="pending" {{ (Request::get('status') == 'pending') ? 'selected' : ''}} >Pending</option>
                                                        <option value="accepted" {{ (Request::get('status') == 'accepted') ? 'selected' : ''}}>Accepted</option>
                                                        <option value="on-delivery" {{ (Request::get('status') == 'ready-to-ship') ? 'selected' : ''}}>Ready to ship</option>
                                                        <option value="on-delivery" {{ (Request::get('status') == 'on-delivery') ? 'selected' : ''}}>On Delivery</option>
                                                        <option value="delivered" {{ (Request::get('status') == 'delivered') ? 'selected' : ''}}>Delivered</option>
                                                        <option value="cancel" {{ (Request::get('status') == 'cancel') ? 'selected' : ''}}>Cancel</option>
                                                        <option value="all" {{ (Request::get('status') == "all") ? 'selected' : ''}}>All</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">From Date</label>
                                                    <input name="from_date" value="{{ Request::get('from_date')}}" type="date" class="form-control">
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">End Date</label>
                                                    <input name="end_date" value="{{ Request::get('end_date')}}" type="date" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    <label class="control-label">.</label>
                                                   <button type="submit" class="form-control btn btn-success"><i style="color:#fff; font-size: 20px;" class="ti-search"></i> </button>
                                                </div>
                                            </div>

                                        </div>

                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Column -->
                    <div class="col-lg-12">
                        <div class="card">
                            
                            <h3>
                                @if(Route::current()->getName() == 'order.search')
                                    Total Record: ({{count($orders)}})
                                @endif
                            </h3>
                            <div class="table-responsive">
                               <table  class="table display table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Order Date</th>
                                            <th>Customer</th>
                                            <th>Qty</th>
                                            <th>Price</th>
                                            <th>Payment Method</th>
                                            <th>Payment</th>
                                            <th>Shipping Cost</th>
                                            <th>Updated_By</th>
                                            <th>Invoice</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>


                                        @if(count($orders)>0)
                                            @foreach($orders as $order)
                                            <tr @if($order->order_status == 'cancel') style="background:#ff000026" @endif>
                                                <td>{{$order->order_id}}</td>
                                               <td>{{\Carbon\Carbon::parse($order->order_date)->format(Config::get('siteSetting.date_format'))}}
                                                <p style="font-size: 12px;margin: 0;padding: 0">{{\Carbon\Carbon::parse($order->order_date)->diffForHumans()}}</p>
                                               </td>
                                               <td>{{ $order->customer_name }}
                                                <p style="font-size: 12px;margin: 0;padding: 0">{{ $order->billing_phone }}</p></td>
                                                <td>{{$order->total_qty}}</td>
                                                <td>{{$order->currency_sign . ($order->total_price + $order->shipping_cost - $order->coupon_discount)  }}</td>

                                                <td class ="payment-method"> <span class="label label-{{($order->payment_method=='pending') ? 'danger' : 'success' }}">{{ str_replace( '-', ' ', $order->payment_method) }}</span>
                                                @if($order->payment_info)
                                                <br/><strong>Tnx_id:</strong> <span> {{$order->tnx_id}}</span><br/>
                                                <span><strong>Info:</strong> {{$order->payment_info}}</span>
                                                @endif
                                                </td>
                                                <td>
                                                    <select class="selectpicker" data-style="btn-sm @if($order->payment_status == 'paid') btn-success @elseif($order->payment_status == 'on-review') btn-primary @else btn-info @endif" id="order_status" onchange="changePaymentStatus(this.value, '{{$order->order_id}}')">
                                                        <option  value="pending" @if($order->payment_status == 'pending') selected @endif >Pending</option>
                                                        <option value="on-review" @if($order->payment_status == 'on-review') selected @endif >On Review</option>
                                                        <option value="paid" @if($order->payment_status == 'paid') selected @endif >Paid</option>
                                                    </select>
                                                 </td>
                                                <td>@if($order->shipping_cost != null) <span class="label label-success">paid</span> @else <span class="label label-danger">pending </span>@endif 
                                            </td>
                                            
                                            <td style="text-align: center;padding:0">
                                            @php 
                                            $invoice = explode(',', $order->invoicePrints);
                                            @endphp
                                            @if($order->invoicePrints && count($invoice) > 0)
                                            <span class="mytooltip tooltip-effect-2">
                                                <span class="tooltip-item">Updated</span> 
                                                <span class="tooltip-content clearfix">
                                                <span class="tooltip-text">
                                                @php
                                                for($i=0; $i < count($invoice); $i++){
                                                    $user = explode('pd', $invoice[$i]);
                                                    if(array_key_exists(0, $user)){
                                                        echo 'By '. App\Admin::find($user[0])->name;
                                                    }
                                                    if(array_key_exists(1, $user)){
                                                        echo '<p style="font-size: 10px;padding: 0;margin: 0">'. \Carbon\Carbon::parse($user[1])->format(Config::get('siteSetting.date_format') .' | '.' h:i A') . '</p>';
                                                    }
                                                }
                                                @endphp
                                                </span> 
                                                </span>
                                            </span>
                                            @else
                                                <span style="color:red">Pending </span>
                                            @endif
                                         
                                            </td>
                                            <td><a target="_blank" class="dropdown-item" href="{{route('admin.orderInvoice', $order->order_id)}}" class="text-inverse" title="View Order Invoice" data-toggle="tooltip"><i class="ti-printer"></i> Invoice</a>
                                            </td>
                                            <td> 
                                                @if($order->order_status == 'delivered')
                                                <span class="label label-success"> {{ str_replace('-', ' ', $order->order_status)}} </span>

                                                @elseif($order->order_status == 'accepted')
                                                <span class="label label-warning"> {{ str_replace('-', ' ', $order->order_status)}} </span>

                                                @elseif($order->order_status == 'ready-to-ship')
                                                <span class="label label-ready-ship"> {{ str_replace('-', ' ', $order->order_status)}} </span>

                                                @elseif($order->order_status == 'cancel')
                                                <a href="javascript:void()" class="mytooltip">
                                                    <span class="label label-danger"> {{ str_replace('-', ' ', $order->order_status)}} 
                                                    </span>
                                                    @if(count($order->orderCancelReason)>0)
                                                    
                                                        @foreach($order->orderCancelReason as $reason)
                                                        Reason <span class="tooltip-content3">{{$reason->reason}} {{$reason->reason_details}}</span>
                                                        @endforeach
                                                    
                                                    @endif
                                                </a>
                                                @elseif($order->order_status == 'on-delivery')
                                                <span class="label label-primary"> {{ str_replace('-', ' ', $order->order_status)}} </span>

                                                @else
                                                <span class="label label-info"> Pending </span>
                                                @endif
                                            </td>
                                            <td>

                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-defualt dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="ti-settings"></i>
                                                    </button>
                                                    <div class="dropdown-menu">

                                                        <a href="javascript:void(0)" class="dropdown-item" onclick="order_details('{{$order->order_id}}')" title=" View order details" data-toggle="tooltip" class="text-inverse p-r-10" >View Details</a>

                                                        <a href="javascript:void(0)" class="dropdown-item"  onclick="changeOrderStatus('accepted', '{{$order->order_id}}')" title="Accept Order" >Accepted</a>
                                                        <a href="javascript:void(0)" class="dropdown-item"  onclick="changeOrderStatus('ready-to-ship', '{{$order->order_id}}')" title="Ready To Ship " >Ready To Ship</a>
                                                        <a href="javascript:void(0)" class="dropdown-item"  onclick="changeOrderStatus('on-delivery', '{{$order->order_id}}')" title="On delivery " >On Delivery</a>
                                                        <a href="javascript:void(0)" class="dropdown-item"  onclick="changeOrderStatus('delivered', '{{$order->order_id}}')" title="Delivered order" >Delivered</a>
                                                        
                                                        <span title="Cancel Order" data-toggle="tooltip">
                                                            <button data-target="#orderCancel"  data-toggle="modal" class="dropdown-item" onclick="orderCancelPopup('{{ route("admin.orderCancel", $order->order_id) }}')"> Cancel Order</button>
                                                        </span>
                                                    </div>
                                                </div>
                                                
                                            </td>

                                            </tr>
                                           @endforeach
                                        @else <tr><td colspan="8"> <h1>No orders found.</h1></td></tr> @endif
                                    </tbody>
                                </table>
                            </div>
                        
                        </div>
                    </div>
                    <!-- Column -->
                </div>
                 <div class="row">
                   <div class="col-sm-6 col-md-6 col-lg-6 text-center">
                       {{$orders->appends(request()->query())->links()}}
                    </div>
                    <div class="col-sm-6 col-md-6 col-lg-6 text-right">Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of total {{$orders->total()}} entries ({{$orders->lastPage()}} Pages)</div>
                </div>
                <!-- ============================================================== -->
                <!-- End PAge Content -->
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <div class="modal bs-example-modal-lg" id="getOrderDetails" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header">
                        <h4 class="modal-title" id="myLargeModalLabel">Order Details</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body" id="order_details"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- ordr cancel Modal -->
        <div id="orderCancel" class="modal fade">
            <div class="modal-dialog modal-confirm">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="icon-box">
                            <i class="fa fa-times" aria-hidden="true"></i>
                        </div>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>
                    <div class="modal-body">
                        <h4 class="modal-title">Are you sure?</h4>
                        <p>Do you really want to cancel order?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                        <button type="button" value="" id="orderCancelRoute" onclick="orderCancel(this.value)" data-dismiss="modal" class="btn btn-danger">Order Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @section('js')

    <script type="text/javascript">
        function order_details(id){
            $('#order_details').html('<div class="loadingData"></div>');
            $('#getOrderDetails').modal('show');
            var  url = '{{route("admin.getOrderDetails", ":id")}}';
            url = url.replace(':id',id);
            $.ajax({
            url:url,
            method:"get",
            success:function(data){
                if(data){

                    $("#order_details").html(data);
                    $('.selectpicker').selectpicker();
                }
            }
        });
    }

    function changePaymentStatus(status, order_id) {

        if (confirm("Are you sure change payment status "+status+".?")) {

            var link = '{{route("admin.changePaymentStatus")}}';

            $.ajax({
                url:link,
                method:"get",
                data:{'status': status, 'order_id': order_id},
                success:function(data){
                    if(data){
                        toastr.success(data.message);
                    }else{
                        toastr.error(data.message);
                    }
                }

            });
        }
        return false;
    }    

    function changeOrderStatus(status, order_id) {

        if (confirm("Are you sure "+status+ " this order.?")) {

            var link = '{{route("admin.changeOrderStatus")}}';

            $.ajax({
                url:link,
                method:"get",
                data:{'status': status, 'order_id': order_id},
                success:function(data){
                    if(data.status){
                        $('#getOrderDetails').modal('hide');
                        toastr.success(data.message);
                    }else{
                        toastr.error(data.message);
                    }
                }

            });
        }
        return false;
    }

    //order cancel
    function orderCancelPopup(route) {
        document.getElementById('orderCancelRoute').value = route;
    }

    function orderCancel(route) {
        //separate id from route
        var id = route.split("/").pop();

        $.ajax({
            url:route,
            method:"get",
            success:function(data){
                if(data.status){
                    $("#ship_status"+id).html('cancel');
                    toastr.success(data.msg);
                }else{
                    toastr.error(data.msg);
                }
            }
        });
    }
    </script>

    <script src="{{asset('assets')}}/node_modules/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="{{asset('assets')}}/node_modules/datatables.net-bs4/js/dataTables.responsive.min.js"></script>
        <script>
    // responsive table
        $('#config-table').DataTable({
            responsive: true, searching: false, paging: false, info: false, ordering: false
        });
    </script>

    <!-- ============================================================== -->
    <!-- This page plugins -->
    <!-- ============================================================== -->
 
    <script src="{{asset('assets')}}/node_modules/bootstrap-select/bootstrap-select.min.js" type="text/javascript"></script>
 
    <script>
        $(function () {
      
            $('.selectpicker').selectpicker();
            
        });
    </script>
@endsection

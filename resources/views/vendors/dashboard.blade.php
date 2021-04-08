@extends('vendors.partials.vendor-master')
@section('title', 'Dashboard')
@section('css')
    <link href="{{ asset('assets/node_modules') }}/morrisjs/morris.css" rel="stylesheet">
    <!--Toaster Popup message CSS -->

    <link href="{{ asset('css') }}/pages/dashboard1.css" rel="stylesheet">
    <style type="text/css">.round{font-size:25px;}</style>
@endsection
@section('content')
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
        <!-- ============================================================== -->
    <div id="main-wrapper">
      <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid dashboard1"><br/>
                
                <div class="row">
                    
                    <!-- Column -->
                    <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Products</h5>
                            <div class="d-flex  no-block align-items-center">
                                <span class="display-5 text-purple"><i class="fa fa-cart-plus"></i></span>
                                <a href="javscript:void(0)" class="link display-5 ml-auto">{{$allProducts}}</a>
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">UnApporve Products</h5>
                            <div class="d-flex  no-block align-items-center">
                                <span class="display-5 text-info"><i class="fa fa-hourglass-half"></i></span>
                                <a href="javscript:void(0)" class="link display-5 ml-auto">{{$pendingProducts}}</a>
                            </div>
                        </div>
                    </div>
                    </div>

                    <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Out Of Stock Products</h5>
                            <div class="d-flex  no-block align-items-center">
                                <span class="display-5 text-danger"><i class="fa fa-database"></i></span>
                                <a href="javscript:void(0)" class="link display-5 ml-auto">{{$outOfStock}}</a>
                            </div>
                        </div>
                    </div>
                    </div>

                    <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Reject Products</h5>
                            <div class="d-flex  no-block align-items-center">
                                <span class="display-5 text-danger"><i class="fa fa-times"></i></span>
                                <a href="javscript:void(0)" class="link display-5 ml-auto">{{$rejectProducts}}</a>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
        
                <div class="row">
                    <!-- Column -->
                    <div class="col-lg-3 col-md-6">
                        <div class="card">
                            <div class="d-flex flex-row">
                                <div class="p-10 bg-info">
                                    <h3 class="text-white box m-b-0"><i class="fa fa-shipping-fast"></i></h3></div>
                                <div class="align-self-center m-l-20">
                                    <h3 class="m-b-0 text-info">{{$allOrders}}</h3>
                                    <h5 class="text-muted m-b-0">Total Order</h5></div>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <!-- Column -->
                    <div class="col-lg-3 col-md-6">
                        <div class="card">
                            <div class="d-flex flex-row">
                                <div class="p-10 bg-info">
                                    <h3 class="text-white box m-b-0"><i class="fa fa-hourglass-half"></i></h3></div>
                                <div class="align-self-center m-l-20">
                                    <h3 class="m-b-0  text-info">{{$pendingOrders}}</h3>
                                    <h5 class="text-muted m-b-0">Pending Orders</h5></div>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <!-- Column -->
                    <div class="col-lg-3 col-md-6">
                        <div class="card">
                            <div class="d-flex flex-row">
                                <div class="p-10 bg-success">
                                    <h3 class="text-white box m-b-0"><i class="fa fa-check-circle"></i></h3></div>
                                <div class="align-self-center m-l-20">
                                    <h3 class="m-b-0">{{$completeOrders}}</h3>
                                    <h5 class="text-muted m-b-0">Complete Orders</h5></div>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <!-- Column -->
                    <div class="col-lg-3 col-md-6">
                        <div class="card">
                            <div class="d-flex flex-row">
                                <div class="p-10 bg-primary">
                                    <h3 class="text-white box m-b-0"><i class="fa fa-times"></i></h3></div>
                                <div class="align-self-center m-l-20">
                                    <h3 class="m-b-0 text-primary">{{$rejectOrders}}</h3>
                                    <h5 class="text-muted m-b-0">Reject Orders</h5></div>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                </div>
                
                

                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Popular Product</h5>
                                <div class="table-responsive">
                                    <table class="table product-overview">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Stock</th>
                                                <th>Price</th>
                                                <th>#</th>
                                                
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(count($popularProducts)>0)
                                            @foreach($popularProducts as $product)
                                            <tr>
                                                <td><a target="_blank" href="{{ route('product_details', $product->slug) }}"> <img src="{{asset('upload/images/product/thumb/'.$product->feature_image)}}" alt="Image" width="42"> {{Str::limit($product->title, 30)}}</a> </td>
                                                 <td>{{($product->stock) ? $product->stock : 0 }}</td>
                                                <td>{{Config::get('siteSetting.currency_symble')}}{{$product->purchase_price}}</td>
                                                 <td><a  href="{{ route('product_details', $product->slug) }}" class="text-inverse p-r-10"><i class="ti-eye"></i></a> </td>
                                            </tr>
                                           @endforeach
                                        @else <tr><td colspan="8"> <h1>No products found.</h1></td></tr> @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Recent Order</h5>
                                <div class="table-responsive ">
                                    <table class="table product-overview">
                                        <thead>
                                            <tr>
                                                <th>Order</th>
                                                <th>Qty</th>
                                                <th>Price</th>
                                                <th>Status</th>
                                                <th>#</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(count($recentOrders)>0)
                                                @foreach($recentOrders as $order)
                                                <tr>
                                                    <td>#{{$order->order_id}}<br/>{{\Carbon\Carbon::parse($order->created_at)->format(Config::get('siteSetting.date_format'))}}
                                                    
                                                   </td>
                                                   <td>{{ $order->quantity }}</td>
                                                    <td>{{$order->currency_sign . ($order->total_price)  }}</td>

                                                    <td> 
                                                        @if($order->shipping_status == 'delivered')
                                                        <span class="label label-success"> {{ str_replace('-', ' ', $order->shipping_status)}} </span>@elseif($order->shipping_status == 'accepted')
                                                        <span class="label label-warning"> {{ str_replace('-', ' ', $order->shipping_status)}} </span>
                                                        @elseif($order->shipping_status == 'cancel')
                                                        <span class="label label-danger"> {{ str_replace('-', ' ', $order->shipping_status)}} </span>
                                                        @elseif($order->shipping_status == 'ready-to-ship')
                                                        <span class="label label-primary"> {{ str_replace('-', ' ', $order->shipping_status)}} </span>
                                                        @else
                                                        <span class="label label-info"> Pending </span>
                                                        @endif
                                                    </td>
                                                    <td> <a target="_blank" href="{{route('vendor.orderInvoice', $order->order_id)}}" class="text-inverse" title="View Order Invoice" data-toggle="tooltip"><i class="ti-eye"></i></a></td>

                                                </tr>
                                               @endforeach
                                            @else <tr><td colspan="8"> <h1>No orders found.</h1></td></tr> @endif
                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>  

              
                <div class="row">
                    <!-- Column -->
                    <div class="col-lg-8 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex m-b-40 align-items-center no-block">
                                    <h5 class="card-title ">YEARLY STATEMENT</h5>
                                    
                                </div>
                                <div id="morris-area-chart" style="height: 340px;"></div>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <div class="col-lg-4 col-md-12">
                        <div class="row">
                            <!-- Column -->
                            <div class="col-md-12">
                                <div class="card bg-cyan text-white">
                                    <div class="card-body ">
                                        <div class="row weather">
                                            <div class="col-6 m-t-40">
                                                <h3>&nbsp;</h3>
                                                <div class="display-4">73<sup>°F</sup></div>
                                                <p class="text-white">Dhaka, Bangladesh</p>
                                            </div>
                                            <div class="col-6 text-right">
                                                <h1 class="m-b-"><i class="wi wi-day-cloudy-high"></i></h1>
                                                <b class="text-white">SUNNEY DAY</b>
                                                <p class="op-5">{{Carbon\Carbon::parse(now())->format('M d, Y, H:m:i')}}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Column -->
                            <div class="col-md-12">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <div id="myCarouse2" class="carousel slide" data-ride="carousel">
                                            <!-- Carousel items -->
                                            <div class="carousel-inner">
                                                <div class="carousel-item active">
                                                    <h4 class="cmin-height">My Acting blown <span class="font-medium">Your Mind</span> and you also <br/>laugh at the moment</h4>
                                                    <div class="d-flex no-block">
                                                        <span><img src="{{asset('assets')}}/images/users/1.jpg" alt="user" width="50" class="img-circle"></span>
                                                        <span class="m-l-10">
                                                    <h4 class="text-white m-b-0">Govinda</h4>
                                                    <p class="text-white">Actor</p>
                                                    </span>
                                                    </div>
                                                </div>
                                                <div class="carousel-item">
                                                    <h4 class="cmin-height">My Acting blown <span class="font-medium">Your Mind</span> and you also <br/>laugh at the moment</h4>
                                                    <div class="d-flex no-block">
                                                        <span><img src="{{asset('assets')}}/images/users/2.jpg" alt="user" width="50" class="img-circle"></span>
                                                        <span class="m-l-10">
                                                    <h4 class="text-white m-b-0">Govinda</h4>
                                                    <p class="text-white">Actor</p>
                                                    </span>
                                                    </div>
                                                </div>
                                                <div class="carousel-item">
                                                    <h4 class="cmin-height">My Acting blown <span class="font-medium">Your Mind</span> and you also <br/>laugh at the moment</h4>
                                                    <div class="d-flex no-block">
                                                        <span><img src="{{asset('assets')}}/images/users/3.jpg" alt="user" width="50" class="img-circle"></span>
                                                        <span class="m-l-10">
                                                    <h4 class="text-white m-b-0">Govinda</h4>
                                                    <p class="text-white">Actor</p>
                                                    </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Column -->
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
            
           
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
       
    </div>
    <!-- ============================================================== -->
    <!-- End Wrapper -->
@endsection
@section('js')

    <!--morris JavaScript -->
    <script src="{{ asset('assets/node_modules') }}/raphael/raphael-min.js"></script>
    <script src="{{ asset('assets/node_modules') }}/morrisjs/morris.min.js"></script>
    <script src="{{ asset('assets/node_modules') }}/jquery-sparkline/jquery.sparkline.min.js"></script>
    <!-- Popup message jquery -->
    <script src="{{ asset('assets/node_modules') }}/toast-master/js/jquery.toast.js"></script>
    <!-- Chart JS -->
    <script src="{{ asset('js') }}/dashboard1.js"></script>
   
@endsection
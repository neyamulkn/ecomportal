@extends('vendors.partials.vendor-master')
@section('title', 'Withdraw History')
@section('css')
    <link rel="stylesheet" type="text/css"
        href="{{asset('assets')}}/node_modules/datatables.net-bs4/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" type="text/css"
        href="{{asset('assets')}}/node_modules/datatables.net-bs4/css/responsive.dataTables.min.css">


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
                        <h4 class="text-themecolor">Withdraw History</h4>
                    </div>
                    <div class="col-md-7 align-self-center text-right">
                        <div class="d-flex justify-content-end align-items-center">

                            <button data-toggle="modal" data-target="#withdraw_request" class="btn btn-info d-lg-block m-l-15"><i class="fa fa-plus-circle"></i> Send Withdraw Request</button>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- End Bread crumb and right sidebar toggle -->
             
                <div class="row">
                    
                    <!-- Column -->
                    <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Balance</h5>
                            <div class="d-flex  no-block align-items-center">
                                <span class="display-5 text-purple"><i class="fa fa-donate"></i></span>
                                <a href="javscript:void(0)" class="link display-5 ml-auto">{{Config::get('siteSetting.currency_symble'). $total}}</a>
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Widthraw</h5>
                            <div class="d-flex  no-block align-items-center">
                                <span class="display-5 text-info"><i class="fa fa-donate"></i></span>
                                <a href="javscript:void(0)" class="link display-5 ml-auto">{{Config::get('siteSetting.currency_symble'). $withdraw_amount}}</a>
                            </div>
                        </div>
                    </div>
                    </div>

                    <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Available Withdrawal</h5>
                            <div class="d-flex  no-block align-items-center">
                                <span class="display-5 text-success"><i class="fa fa-donate"></i></span>
                                <a href="javscript:void(0)" class="link display-5 ml-auto">{{Config::get('siteSetting.currency_symble'). ($total - $withdraw_amount)}}</a>
                            </div>
                        </div>
                    </div>
                    </div>

                    <div class="col-md-3">
                    <div class="card" data-toggle="modal" data-target="#withdraw_request">
                        <div class="card-body " style="text-align: center;cursor: pointer;">
                            
                            <div class="align-items-center">
                                <span class="display-5 text-warning"><i class="fa fa-plus-circle"></i></span>
                            </div>
                            <h5 class="card-title">Send Withdraw Request</h5>
                        </div>
                    </div>
                    </div>
                </div>


                <div class="row">
                   
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    
                                    <div class="table-responsive">
                                       <table id="config-table" class="table display table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Withdraw Date</th>
                                                    <th>Payment Method</th>
                                                    <th>Amount</th>
                                                    <th>Details</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            @if(count($allwithdraws)>0)
                                                @foreach($allwithdraws as $withdraw)
                                                <tr>
                                                   <td>{{\Carbon\Carbon::parse($withdraw->created_at)->format(Config::get('siteSetting.date_format'))}}
                                                   ({{\Carbon\Carbon::parse($withdraw->created_at)->diffForHumans()}})
                                                   </td>
                                                    <td>@if($withdraw->paymethod_name){{$withdraw->paymethod_name->acc_name}} @endif</td>
                                                   
                                                    <td> <span class="label label-info">{{Config::get('siteSetting.currency_symble'). $withdraw->amount }}</span></td>
                                                     <td>{{$withdraw->transaction_details }}</td>
                                                   
                                                    <td>@if($withdraw->status == 'paid') <span class="label label-success"> {{$withdraw->status}}</span> @elseif($withdraw->status == 'cancel') <span class="label label-danger"> {{$withdraw->status}} </span> @else <span class="label label-info"> {{$withdraw->status}} </span> @endif</td>
                                                </tr>
                                               @endforeach
                                            @else <tr><td colspan="8"> <h1>No Withdraw found.</h1></td></tr>@endif

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                </div>
                <div class="row">
                   <div class="col-sm-6 col-md-6 col-lg-6 text-center">
                       {{$allwithdraws->appends(request()->query())->links()}}
                    </div>
                    <div class="col-sm-6 col-md-6 col-lg-6 text-right">Showing {{ $allwithdraws->firstItem() }} to {{ $allwithdraws->lastItem() }} of total {{$allwithdraws->total()}} entries ({{$allwithdraws->lastPage()}} Pages)</div>
                </div>
                <!-- ============================================================== -->
                <!-- End PAge Content -->

            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
        </div>
    <!-- add Modal -->
        <div class="modal fade" id="withdraw_request" role="dialog"  tabindex="-1" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">

                  <!-- Modal content-->
                  <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Send Withdraw Request</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body form-row">
                        <div class="card-body">
                            <form action="{{route('vendor.withdraw_request')}}" method="POST" >
                                {{csrf_field()}}
                                <div class="form-body">
                                   
                                    <div class="row justify-content-md-center">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="required" for="method_name">withdraw Amount</label>
                                                <input required="" name="amount" id="amount" value="{{old('amount')}}" type="text" placeholder="Minimun withdraw {{Config::get('siteSetting.currency_symble')}}50" class="form-control">
                                                 <i style="color: red">Minimun withdraw amount {{Config::get('siteSetting.currency_symble')}}50</i>
                                                @if ($errors->has('amount'))
                                                <span class="invalid-feedback" role="alert">
                                                    {{ $errors->first('amount') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <span for="payment_method">Payment Gateway</span>
                                                <select name="payment_method" required="" class="form-control select2 m-b-10" style="width: 100%" >
                                                    <option value="">Select Method</option>
                                                 @foreach($paymentgateways as $paymentgateway)
                                                    <option @if(old('amount') == $paymentgateway->id) selected @endif value="{{$paymentgateway->id}}">{{$paymentgateway->acc_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="message">Message</label>
                                                <textarea rows="2" name="message" id="message"  type="text" style="resize: vertical;" placeholder="Write your message" class="form-control">{{old('message')}}</textarea>
                                            </div>
                                        </div>
                                     
                                        <div class="col-md-12">
                                            
                                            <div class="modal-footer">
                                                <button type="submit" name="submitType" value="add" class="btn btn-success"> <i class="fa fa-check"></i> Send Request</button>
                                                <button type="button" data-dismiss="modal" class="btn btn-inverse">Cancel</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
 
@extends('layouts.admin-master')
@section('title', 'Seller Commisstion')
@section('css')

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
                        <h4 class="text-themecolor"><a href="{{ url()->previous() }}"> <i class="fa fa-angle-left"></i> Seller Commisstion</a></h4>
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
                            <div class="card card-body">
                               
                                <div class="row justify-content-md-center">
                                    <div class="col-md-5">
                                        <form action="{{ route('vendor.commission') }}" method="post" >
                                            @csrf
                                            <div class="form-group">
                                                <label class="required" for="title">Set Seller Commission</label><br/>
                                                <input style="width: 300px" required="" name="seller_commission" id="seller_commission" value="{{ $commission[0]['value'] }}" type="text" placeholder="Example: 10" class="form-control">%
                                            </div>
                                            <button type="submit" class="btn btn-info">Update</button>
                                        </form>
                                    </div>
                                </div>
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

    </script>

        <script type="text/javascript">
        //change status by id
        function siteSettingActiveDeactive(field){
            var  url = '{{route("siteSettingActiveDeactive")}}';
            $.ajax({
                url:url,
                method:"get",
                data:{field:field},
                success:function(data){
                    if(data.status){
                        toastr.success(data.message);
                    }else{
                        toastr.error(data.message);
                    }
                }
            });
        }
    </script>  
@endsection
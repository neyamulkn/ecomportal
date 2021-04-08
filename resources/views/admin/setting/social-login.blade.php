@extends('layouts.admin-master')
@section('title', 'Social media login')

@section('css')
<link href="{{asset('css')}}/pages/tab-page.css" rel="stylesheet" type="text/css" />
@endsection
@section('content')
        <!-- Page wrapper  -->
    <!-- ============================================================== -->
    <div class="page-wrapper">
        <!-- ============================================================== -->
        <!-- Container fluid  -->
        <div class="container-fluid">
            <!-- ============================================================== -->
            <!-- Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <div class="row page-titles">
                
                <div class="col-md-12 align-self-center ">
                    <div class="d-fl ">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">General</a></li>
                            <li class="breadcrumb-item active">Setting</li>
                        </ol>
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
               
                <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="title_head"> Social media login configuration</div>
                               
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs" role="tablist">
                                    @foreach($socialLogins as $index => $socialLogin)
                                    <li class="nav-item"> <a class="nav-link @if(!Session::get('socialUpdateTab') && $index == 0) active @endif @if(Session::get('socialUpdateTab') == $socialLogin->provider) active @endif " data-toggle="tab" href="#{{$socialLogin->provider}}" role="tab"><i class="ti-settings"></i>  {{ucwords($socialLogin->provider)}} Setting</a> </li>
                                    @endforeach
                                </ul>
                                <!-- Tab panes -->
                                <div class="tab-content tabcontent-border">
                                    @foreach($socialLogins as $index => $socialLogin)
                                    <div class="tab-pane @if(!Session::get('socialUpdateTab') && $index == 0) active @endif @if(Session::get('socialUpdateTab') == $socialLogin->provider) active @endif " id="{{$socialLogin->provider}}" role="tabpanel">
                                        <div class="p-20">
                                            <form action="{{route('socialLoginSettingUpdate', $socialLogin->id)}}"  method="post" data-parsley-validate>
                                                @csrf
                                                <div class="form-body">
                                                    
                                                    <div class="">
                                                        <div class="form-group row justify-content-md-center ">
                                                            <div class="col-md-4">
                                                                <label class="col-form-label" for="client_id">{{ ucwords($socialLogin->provider) }} client id</label>
                                                                <input type="text" value="{{$socialLogin->client_id}}" placeholder="Enter client id" name="client_id" id="client_id" class="form-control" >
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="col-form-label" for="client_secret">{{ucwords($socialLogin->provider)}} client secret</label>
                                                                <input type="text" value="{{$socialLogin->client_secret}}" placeholder="Enter client secret" name="client_secret" id="client_secret" class="form-control" >
                                                            </div>
                                                            <div class="col-md-2">
                                                                <label class="col-form-label">Status</label>
                                                                <div class="custom-control custom-switch">
                                                                  <input name="status" onclick="satusActiveDeactive('social_logins', '{{$socialLogin->id}}', 'status')"  type="checkbox" {{ ($socialLogin->status == 1) ? 'checked' : ''}}  type="checkbox" class="custom-control-input" id="status{{$socialLogin->provider}}">
                                                                  <label style="padding: 5px 12px" class="custom-control-label" for="status{{$socialLogin->provider}}"></label>
                                                                </div>
                                                            </div>
                                                            
                                                        </div>
                                                            
                                                    </div><hr>
                                                    <div class="form-actions pull-right">
                                                        <button type="submit" name="socialUpdateTab" value="{{$socialLogin->provider}}" class="btn btn-success"> <i class="fa fa-save"></i> Update {{$socialLogin->provider}} setting</button>
                                                       
                                                        <button type="reset" class="btn waves-effect waves-light btn-secondary">Reset</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- End Page wrapper  -->

@endsection

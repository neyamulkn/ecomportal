@extends('layouts.admin-master')
@section('title', 'Seo Setting')

@section('css')
<link href="{{asset('assets')}}/node_modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.css" rel="stylesheet" />
<link href="{{asset('assets')}}/node_modules/dropify/dist/css/dropify.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('css')}}/pages/tab-page.css" rel="stylesheet" type="text/css" />
<style type="text/css">
    #generalSetting input, #generalSetting textarea{color: #797878!important}
    .asColorPicker_open{z-index: 9999999;border:1px solid #ccc;}
    .dropify-wrapper{  width: 300px !important; height: 150px !important; }
</style>
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
                                <div class="title_head">
                                Seo Setting
                            </div>
                               
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item"> <a class="nav-link active " data-toggle="tab" href="#seoSetting" role="tab"><span class="hidden-sm-up"><i class="ti-home"></i></span> <span class="hidden-xs-down">General Setting</span></a> </li>
                                </ul>
                                <!-- Tab panes -->
                                <div class="tab-content tabcontent-border">
                                    
                                    <div class="tab-pane active p-20" id="seoSetting" role="tabpanel">
                                        <form action="{{route('seoSetting')}}" enctype="multipart/form-data" method="post" data-parsley-validate id="seoSetting">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $setting->id }}">
                                            <div class="form-body">
                                                <div class="">
                                                        <div class="form-group row">
                                                            <label class="col-md-2 text-right col-form-label" for="title">Meta Title</label>
                                                            <div class="col-md-8">
                                                                <input type="text" value="{{$setting->title}}"  name="title" id="title" placeholder = 'Enter meta title'class="form-control" >
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-md-2 text-right col-form-label" for="description">Meta Description</label>
                                                            <div class="col-md-8">
                                                                <textarea class="form-control" name="description" id="description" rows="2" style="resize: vertical;" placeholder="Enter Meta Description">{{$setting->description}}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-md-2 text-right col-form-label">Meta Keywords</label>
                                                            <div class="col-md-8">
                                                                 <div class="tags-default">
                                                                   
                                                                    <input style="width: 100%" type="text" name="meta_keywords[]"  data-role="tagsinput" value="{{$setting->meta_keywords}}" placeholder="Enter meta keywords" />
                                                                     <p style="font-size: 12px;color: #777;font-weight: initial;">Write meta tags Separated by Comma[,]</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="form-group row">
                                                        
                                                            <label class="col-md-2 text-right col-form-label">Meta image</label>
                                                            <div class="col-md-8">
                                                                <input type="file" data-default-file="{{asset('upload/images/logo/'.$setting->meta_image)}}" class="dropify" accept="image/*" data-type='image' data-allowed-file-extensions="jpeg jpg png gif" name="meta_image" id="input-file-events">
                                                            </div>
                                                        </div>
                                                </div><hr>
                                                <div class="form-actions pull-right">
                                                    <button type="submit"  name="updateTab" value="seoSetting" class="btn btn-success"> <i class="fa fa-save"></i> Update Seo Setting</button>
                                                   
                                                    <button type="reset" class="btn waves-effect waves-light btn-secondary">Reset</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
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

@section('js')
    <script src="{{asset('assets')}}/node_modules/dropify/dist/js/dropify.min.js"></script>
    <script>
        $(document).ready(function() {
            // Basic
            $('.dropify').dropify();
        });
    </script>
    <script src="{{asset('assets')}}/node_modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>
    <script type="text/javascript">
    // Enter form submit preventDefault for tags
    $(document).on('keyup keypress', 'form input[type="text"]', function(e) {
      if(e.keyCode == 13) {
        e.preventDefault();
        return false;
      }
    });
    </script>
@endsection
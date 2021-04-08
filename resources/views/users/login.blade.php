
@extends('layouts.frontend')
@section('title', 'Login | '.Config::get('siteSetting.site_name'))
@section('css-top')
 <link href="{{asset('css/pages/login-register-lock.css')}}" rel="stylesheet">

<style type="text/css">
    @media (min-width: 1200px){
        .container {
            max-width: 1200px !important;
        }
    }
    .dropdown-toggle::after, .dropup .dropdown-toggle::after {
        content: initial !important;
    }
    .card-footer, .card-header {
        margin-bottom: 5px;
        border-bottom: 1px solid #ececec;
    }
    .loginArea{background: #fff; border-radius: 5px;margin:10px 0;padding-top: 10px;}
</style>
 @endsection
@section('content')
<div class="container">
    
    <div class="row justify-content-center">
        <div class="col-md-3 col-12"></div>
        <div class="col-md-6 col-12 loginArea">
            <div class="card">

                   <div class="card-body">

                        <form id="loginform" action="{{route('login')}}" data-parsley-validate method="post">
                            @csrf
                            <div class="card-header text-center"><h3>Sign In</h3></div>

                            <div class="form-group">
                              <label class="control-label" for="phoneOrEmail">Email or Mobile Number</label>
                              <input type="text" name="emailOrMobile" value="{{old('emailOrMobile')}}" placeholder="Enter Email or Mobile Number " id="input-email" required="" data-parsley-required-message = "Email or Mobile number is required" class="form-control">
                              @if ($errors->has('emailOrMobile'))
                                    <span class="error" role="alert">
                                        {{ $errors->first('emailOrMobile') }}
                                    </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="input-password">Password</label>
                                <input type="password" required="" name="password" value="" placeholder="Password" id="input-password" data-parsley-required-message = "Password is required" class="form-control">
                                @if ($errors->has('password'))
                                    <span class="error" role="alert">
                                       {{ $errors->first('password') }}
                                    </span>
                                @endif
                            </div>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div style=" display: flex!important;" class="d-flex no-block align-items-center">
                                        <div style="display: inline-flex;" class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="Remember"> 
                                            <label style="margin: 0 5px;" class="custom-control-label" for="Remember"> Remember me</label>
                                        </div> 
                                        <div class="ml-auto" style="margin-left: auto!important;">
                                            <a href="javascript:void(0)" id="to-recover" class="text-muted"><i class="fa fa-lock"></i> Forgot pwd?</a> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                            <div class="form-group text-center">
                                <div class="col-xs-12 p-b-20">
                                    <button class="btn btn-block btn-lg btn-info btn-rounded" type="submit"> Log In</button>
                                </div>
                            </div> 
                            <div id="column-login" style="margin:15px 0" class="col-sm-8 pull-right">
                                <div class="row">
                                    <div class="social_login pull-right" id="so_sociallogin">
                                      <a href="#" class="btn btn-social-icon btn-sm btn-facebook"><i class="fa fa-facebook fa-fw" aria-hidden="true"></i></a>
                                      <a href="#" class="btn btn-social-icon btn-sm btn-twitter"><i class="fa fa-twitter fa-fw" aria-hidden="true"></i></a>
                                      <a href="#" class="btn btn-social-icon btn-sm btn-google-plus"><i class="fa fa-google fa-fw" aria-hidden="true"></i></a>
                                      <a href="#" class="btn btn-social-icon btn-sm btn-linkdin"><i class="fa fa-linkedin fa-fw" aria-hidden="true"></i></a>
                                    </div>
                                </div>
                            </div>                            
           
                        </form>
                        <form class="form-horizontal" id="recoverform" action="index.html">
                        <div class="form-group ">
                            <div class="col-xs-12">
                                <h3>Recover Password</h3>
                                <p class="text-muted">Enter your Email and instructions will be sent to you! </p>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="col-xs-12">
                                <input class="form-control" type="text" required="" placeholder="Email"> </div>
                        </div>
                        <div class="form-group text-center m-t-20">
                            <div class="col-xs-12">
                                <button class="btn btn-primary btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">Reset</button>
                            </div>
                        </div>
                    </form>
                
                </div>
            </div>
            
            <div class="form-group m-b-0">
                <div class="col-sm-12 text-center">
                    Don't have an account? <a href="{{route('register')}}" class="text-info m-l-5"><b>Sign Up</b></a>
                </div>
            </div>  
            <div class="col-md-3 col-12"></div>     
        </div>
    </div>
</div>
@endsection

@section('js')
    <!--Custom JavaScript -->
    <script type="text/javascript">
       
        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        });
        // ============================================================== 
        // Login and Recover Password 
        // ============================================================== 
        $('#to-recover').on("click", function() {
            $("#loginform").slideUp();
            $("#recoverform").fadeIn();
        });
    </script>
@endsection

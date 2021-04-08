@yield('css-top')
<link rel="stylesheet" type="text/css" href="{{ mix('frontend/css/style.min.css') }}">
<link href="{{asset('frontend')}}/css/custom.css" rel="stylesheet">
<link href="{{asset('frontend')}}/css/headline.css" rel="stylesheet">

@yield('css')
<style type="text/css">
  .typeheader-6 .header-center{ background:{{ config('siteSetting.header_bg_color') }}; color: {{ config('siteSetting.header_text_color')}} }
  .typeheader-6 .header_custom_link .compare a {
    background: url('{{asset("frontend/image/icon/icon-compare2.png")}}') no-repeat center;
    }
    .typeheader-6 .header_custom_link .wishlist a {
        background: url('{{asset("frontend/image/icon/icon-wishlist2.png")}}') no-repeat center;
    }

    .typeheader-6 .header-cart h2.title-cart2{color:{{ config('siteSetting.header_text_color')}};}
    .typeheader-6 .header-cart .btn-shopping-cart .fa-check-circle, .header-top a, .typeheader-6 .header-cart .btn-shopping-cart .cart-total-full{color: {{ config('siteSetting.header_text_color')}} !important;}
    .typeheader-6 .header-top{background: {{ config('siteSetting.header_bg_color') }}; color: {{ config('siteSetting.header_text_color')}} }
    .dropdown-menu > li > a{color: #000 !important}
    #typeheadsection
    {
        z-index: 999999; 
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        text-align: center;
        min-height: 200px;
        position: fixed;
        background: #e6e4e4 url('{{ asset("assets/images/loading.gif")}}') no-repeat center; 
    }    

    #dataLoading
    {
        z-index: 999999; 
        width: 100%;
        height: 100%;
        top: 0%;
        left: 0%;
        text-align: center;
        display: none;
        min-height: 500px;
        position: absolute;
        background: #ffffffe0 url('{{ asset("assets/images/loading.gif")}}') no-repeat center; 
    }
    #loadingData
    {
        z-index: 999999; 
        width: 100%;
        height: 100%;
        min-height: 100px;
        display: none;
        position: fixed;
        background: url('{{ asset("assets/images/loading.gif")}}') no-repeat center; 
    }
    .loadingData-sm
    {
        z-index: 9999; 
        width: 100%;
        height: 20px;
        background: url('{{ asset("assets/images/loader.gif")}}') no-repeat center; 
    }
    #process
    {
        display: none;
        width: 100%;
        position: absolute;
        height: 100%;
        background: url('{{ asset("assets/images/process.gif")}}') no-repeat center; 
    }
    .footer_area{background: {{ config('siteSetting.footer_bg_color') }}; color: {{ config('siteSetting.footer_text_color') }} }
    .footer_area span,  .footer_area a,  .footer_area h4,  .footer_area i{color: {{ config('siteSetting.footer_text_color') }} !important; }
     .footer_area .title-footer{border-bottom:1px solid {{ config('siteSetting.footer_text_color') }} !important; }
     .footer_area li a:before{background: {{ config('siteSetting.footer_text_color') }} !important;}
    .copyright_area{ background: {{ config('siteSetting.copyright_bg_color') }} !important; color: {{ config('siteSetting.copyright_text_color') }} !important; }
</style>

@yield('perpage-css')
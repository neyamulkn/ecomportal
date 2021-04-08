@extends('layouts.frontend')
@section('title', $offer->title .' | Offer | '. Config::get('siteSetting.site_name') )
@section('metatag')
    <meta name="title" content="{{$offer->title}}">
    <meta name="description" content="{{$offer->title}}">
 
    <!-- Open Graph general (Facebook, Pinterest & Google+) -->
    <meta property="og:description" content="{{$offer->title}}">
    <meta property="og:description" content="{!!$offer->title!!}">
    <meta property="og:image" content="{{asset('upload/images/offer/thumbnail/'.$offer->thumbnail)}}">
    <meta property="og:url" content="{{ url()->full() }}">
    <meta property="og:site_name" content="{{Config::get('siteSetting.site_name')}}">
    <meta property="og:locale" content="en">
    <meta property="og:type" content="website">
    <meta property="fb:admins" content="1323213265465">
    <meta property="fb:app_id" content="13212465454">
    <meta property="og:type" content="e-commerce">

    <!-- Schema.org for Google -->

    <meta itemprop="title" content="{{$offer->title}}">
    <meta itemprop="description" content="{{$offer->title}}">
    <meta itemprop="image" content="{{asset('upload/images/offer/thumbnail/'.$offer->thumbnail)}}">

    <!-- Twitter -->
    <meta name="twitter:card" content="{{$offer->title}}">
    <meta name="twitter:title" content="{{$offer->title}}">
    <meta name="twitter:description" content="{{$offer->title}}">
    <meta name="twitter:site" content="{{url('/')}}">
    <meta name="twitter:creator" content="@Neyamul">
    <meta name="twitter:image:src" content="{{asset('upload/images/offer/thumbnail/'.$offer->thumbnail)}}">

    <!-- Twitter - Product (e-commerce) -->

@endsection
@section('css')
 <style type="text/css">
      
      .progress{background-color: #f5f5f5eb;}
      .progress-bar{background-color: #c5e3fb;color: #fc2828;}
      .common-home .label-sale{width: 100%;
      right: -75px;
      top: 8px !important;
      font-weight: 600;
      border: 1px solid red;
      color: #fffcfc;
      background: #ff3839;
      transform: rotateZ(45deg);
    }
    @-webkit-keyframes blinker {
      from {opacity: 1.0;}
      to {opacity: 0.1;}
    }
    .blink{text-decoration: blink;-webkit-animation-name: blinker;-webkit-animation-duration: 0.7s;-webkit-animation-iteration-count:infinite;-webkit-animation-timing-function:ease-in-out;-webkit-animation-direction: alternate; color: #ffbc00}
    .liveBox{ position: absolute; color: red; font-size: 20px; top: -20px; right: 15px;
        }
  .liveBtn {    width: 285px; height: 105px; transition: auto; background: #121213bd; display: inline-block;border-radius: 50%;font-weight: 800;color: #fff;transform: translate(-50%, -0%);left: 50%;position: absolute;margin-top: 10px;
  }
  .offer_area { height: 155px; background: linear-gradient(#0364c7b8, #eeefcfeb); border-top-right-radius: 75px; border-top-left-radius: 75px; border-bottom-right-radius: 75px; border-bottom-left-radius: 75px; width: 100%; text-align: center; padding-top: 12px; margin-top: 10px; margin-bottom: 60px; position: relative;
  }
  .offer-info{text-align: left;display: inline-block;padding: 10px;border-radius: 5px;margin-bottom: 10px;}
  .offer-info p{line-height: 16px;}
    .offer-left-right{margin-top: 25px !important;}
    .offer-left-right .caption{min-height: 50px;overflow: hidden;line-height: normal;text-align: center;}
    .offer-left-right .caption a{color: #da154a !important;font-weight: 600;
    font-size: 12px;}

    .offer-top-product{left: auto; left: 50%;transform: translate(-50%, -0%);position: absolute;}
    .offer-image_area{width: 100%; overflow: hidden; border-radius: 4px; padding: 5px 15px; background: #fff;}
    .offer-image_area img{width: 100%;height: 100%}
    .offer-title{ margin-top: 20px;padding: 10px 5px; color: #000;  height: 60px;overflow: hidden;}
    .offer_area p{color: #000; font-size:30px; margin-bottom: 100%}
    @media (max-width: 768px) {
       .offer-title p{font-size: 20px;}
       .offer-top-product{width: 80%;}
       .offers{background-size: inherit !important;}
       .offer_area{margin-top: 20px;margin-bottom: 65px; border-top-right-radius: 25px;
        border-top-left-radius: 25px;
        border-bottom-right-radius: 25px;
        border-bottom-left-radius: 25px;}
    }

  .count{ display: inline-flex; margin: 0 auto; text-align: center; align-items: center;
  }
  .count_d {
      position: relative;
      width: 57px;
      padding: 10px 0px;
      margin: 0px 3px;
      background-image: linear-gradient(to right, #4e0c25 ,#8e0d3c,#0a060e);
      color: #eef1f5;
      /* border: 2px solid #000; */
      border-radius: 50%;
      overflow: hidden;
  }
  .count_d:before{
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 50%;

  }
  .count_d span {
      display: block;
      text-align: center;
      font-size: 15px;
      font-weight: 800;
  }
  .count_d h2 {
      display: block;
      text-align: center;
      font-size: 8px;
      font-weight: 800;
      text-transform: uppercase;
      color: #fdfdfd;
      margin: 0;
  }
    .irotate {
        text-align: center;
        margin: 0 auto;
        display: block;
    }
    .thisis {
        display: inline-block;
        vertical-align: middle;
       
    }
    .slidem {
       
        text-align: center;
        min-width: 90px;
    }

    </style>
@endsection
@section('content')

    <section class="offers" style="padding: 10px 0;color:{{$offer->text_color}};">
     
        <div class="container" id="purchase_offer">
          <div class="offer_area" style="background:{{$offer->background_color}}">
            <div class="offer-title">
                <div class="irotate">
                  <div class="thisis slidem">
                    <p style="color: {{$offer->text_color}}">{{$offer->title}}</p>
                    <p style="color: {{$offer->text_color}}">ওয়াদিতে শপিং করুন ওয়ারী ফ্রি </p>
                  </div>
                </div>
            </div>
            @if(now() <= $offer->start_date)

              <div class="liveBtn">

                 @if(Auth::guard('admin')->check())
                  <span >Offer Started </span>
                  <a @if(Auth::check() && $offer->offer_type == 'kanamachi') href="{{ route('offer.buyOffer', $offer->slug) }}" @else data-toggle="modal" data-target="#so_sociallogin" @endif>
                     
                    <div class="blink" style="font-size: 25px; padding: 13px 0px 18px; white-space: nowrap; line-height: 1"><i class="fa fa-play-circle"></i> Shopping Now</div>
                  </a>
                  @else
                  <span class="blink">Offer Upcomming</span>
                  <div class="head" id="offerDate" data-offerDate="{{Carbon\Carbon::parse($offer->start_date)->format('m,d,Y H:i:s')}}">
                    
                    <div class="count">
                      <div class="count_d">
                      <h2>Days</h2>
                        <span id="days">00</span>
                      </div>
                      <div class="count_d">
                      <h2>HOURS</h2>
                        <span id="hour">00</span>
                      </div>
                      <div class="count_d">
                      <h2>MINUTES</h2>
                        <span id="minutes">00</span>
                      </div>
                      <div class="count_d">
                      <h2>SECONDS</h2>
                        <span id="seconds">00</span>
                      </div>
                    </div>
                  </div>
                   @endif
              </div>
          
            @elseif(now() >= $offer->start_date && now() <= $offer->end_date)
              @if($offer->offer_type == 'kanamachi')
                <a @if(Auth::check() && $offer->offer_type == 'kanamachi') href="{{ route('offer.buyOffer', $offer->slug) }}" @else data-toggle="modal" data-target="#so_sociallogin" @endif>
                  <div class="liveBtn">
                    <span class="blink"><i class="fa fa-play-circle"></i> Live Offer</span>
                    <p style="line-height: 1; font-size: 10px; margin: -5px; color: #ffbc00;">Until</p>
                    <div class="head" id="offerDate" data-offerDate="{{Carbon\Carbon::parse($offer->end_date)->format('m,d,Y H:i:s')}}">
                      
                      <div class="count">
                        <div class="count_d">
                        <h2>Days</h2>
                          <span id="days">00</span>
                        </div>
                        <div class="count_d">
                        <h2>HOURS</h2>
                          <span id="hour">00</span>
                        </div>
                        <div class="count_d">
                        <h2>MINUTES</h2>
                          <span id="minutes">00</span>
                        </div>
                        <div class="count_d">
                        <h2>SECONDS</h2>
                          <span id="seconds">00</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </a>
              @else
              <div class="liveBtn">
                    <span class="blink"><i class="fa fa-play-circle"></i> Live Offer</span>
                    <p style="line-height: 1; font-size: 10px; margin: -5px; color: #ffbc00;">Until</p>
                    <div class="head" id="offerDate" data-offerDate="{{Carbon\Carbon::parse($offer->end_date)->format('m,d,Y H:i:s')}}">
                      
                      <div class="count">
                        <div class="count_d">
                        <h2>Days</h2>
                          <span id="days">00</span>
                        </div>
                        <div class="count_d">
                        <h2>HOURS</h2>
                          <span id="hour">00</span>
                        </div>
                        <div class="count_d">
                        <h2>MINUTES</h2>
                          <span id="minutes">00</span>
                        </div>
                        <div class="count_d">
                        <h2>SECONDS</h2>
                          <span id="seconds">00</span>
                        </div>
                      </div>
                    </div>
                  </div>
        
              @endif
              
            @else
              <span class="liveBtn" style="padding: 8px 60px 23px;">Closed <br/> Offer</span>
            @endif
          </div> 
         
        </div>
           
          <div class="container">
              <div class="products-category">
                <div class="row">
                 
                 
                  <div class="col-md-{{(count($offers)>0) ? 10 : 12}} sticky-content">
                    @if($offer->notes)
                     
                        <div class="offer-info" style="width: 100%; background: #00000085;color:{{$offer->text_color}};">
                         {!! $offer->notes !!}
                        </div> 
                  
                    @endif
                    @if(count($offer_products)>0)
                    <div class="products-list grid row " id="loadProducts">
                        @if($offer->offer_type == 'kanamachi')
                        @include('frontend.offer.kanamachi-product')
                        @else
                        @include('frontend.offer.products')
                        @endif
                    </div>
                    <div class="ajax-load  text-center" id="data-loader"><img src="{{asset('frontend/image/loading.gif')}}"></div>
                     @endif
                  </div>
                 
                  @if(count($offers)>0)
                  <div class="col-md-2 sticky-content"> <a  href="{{route('offers')}}"><h3 style="background: red;color: #fff; padding: 10px 5px;text-align:center;border-radius: 5px;">More Offers</h3></a>
                    <div class="row">
                      
                      @foreach($offers as $offer)
                      <div class="col-xs-12" style="padding: 10px; border-radius: 3px; margin-bottom: 5px; background:{{$offer->background_color}}">
                        <a  href="{{route('offer.details', $offer->slug)}}">
                        <img style="width: 100%" src="{{ asset('upload/images/offer/thumbnail/'.$offer->thumbnail) }}">
                        </a>
                      </div>
                      @endforeach
                    </div>
                  </div>
                  @endif
                </div>
              </div>
          </div>
       
    </section>

 @endsection

@section('js')

<script type="text/javascript">


    $(document).ready(function(){
    
        var page = 1;
        $(window).scroll(function() {
            //check section last page
            if(page <= '{{$offer_products->lastPage()}}' ){
                page++;
                loadMoreProducts(page);
            }
        });

        function loadMoreProducts(page){
           
            $.ajax(
                {
                    url: '?page=' + page,
                    type: "get",
                    beforeSend: function()
                    {
                        $('.ajax-load').show();
                    }
                })
            .done(function(data)
            {
                $('.ajax-load').hide();
                $("#loadProducts").append(data.html);
               
                // Content slider
                $('.yt-content-slider').each(function () {
                    var $slider = $(this),
                    $panels = $slider.children('div'),
                    data = $slider.data();
                    // Remove unwanted br's
                    //$slider.children(':not(.yt-content-slide)').remove();
                    // Apply Owl Carousel
        
                    $slider.owlCarousel2({
                        responsiveClass: true,
                        mouseDrag: true,
                        video:true,
                    lazyLoad: (data.lazyload == 'yes') ? true : false,
                        autoplay: (data.autoplay == 'yes') ? true : false,
                        autoHeight: (data.autoheight == 'yes') ? true : false,
                        autoplayTimeout: data.delay * 1000,
                        smartSpeed: data.speed * 1000,
                        autoplayHoverPause: (data.hoverpause == 'yes') ? true : false,
                        center: (data.center == 'yes') ? true : false,
                        loop: (data.loop == 'yes') ? true : false,
                  dots: (data.pagination == 'yes') ? true : false,
                  nav: (data.arrows == 'yes') ? true : false,
                        dotClass: "owl2-dot",
                        dotsClass: "owl2-dots",
                  margin: data.margin,
                    navText:  ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
                        
                        responsive: {
                            0: {
                                items: data.items_column4 
                                },
                            480: {
                                items: data.items_column3
                                },
                            768: {
                                items: data.items_column2
                                },
                            992: { 
                                items: data.items_column1
                                },
                            1200: {
                                items: data.items_column0 
                                }
                        }
                    });
                });
            })
            .fail(function(jqXHR, ajaxOptions, thrownError)
            {
                $('.ajax-load').hide();
            });
        }
    });
</script>

<script type="text/javascript">

    var offerDate = $('#offerDate').attr('data-offerDate');
    var count = new Date(offerDate).getTime();
    var x = setInterval(function() {
    var now = new Date().getTime();
    var time = count - now;


    var days = Math.floor(time / (1000 * 60 * 60 * 24));
    var hours = Math.floor((time % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes = Math.floor((time % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((time % (1000 * 60)) / 1000);


    document.getElementById("days").innerHTML = days;
    document.getElementById("hour").innerHTML = hours;
    document.getElementById("minutes").innerHTML = minutes;
    document.getElementById("seconds").innerHTML = seconds;


    if (days < 0) {
      clearInterval(x);
      document.getElementById("days").innerHTML = "00";
    }
  }, 1000);

//offer title slide
    jQuery(".slidem").prepend(jQuery(".slidem > p:last").clone()); /* copy last div for the first slideup */
    jQuery.fn.slideFadeToggle  = function(speed, easing, callback) {
        return this.animate({opacity: 'toggle', height: 'toggle'}, speed, easing, callback);
    }; /* slideup fade toggle code */
    var divS = jQuery(".slidem > p"), /* get the divs to slideup */
        sDiv = jQuery(".slidem > p").length, /* get the number of divs to slideup */
        n = 0; /* starting counter */
    function slidethem() { /* slide fade function */
        jQuery( divS ).eq( n ).slideFadeToggle(1000,"swing",n=n+1); /* slide fade the div at 1000ms swing and add to counter */
        jQuery( divS ).eq( n ).show(); /* make sure the next div is displayed */
    }
    ( function slideit() { /* slide repeater */
        if( n == sDiv ) { /* check if at the last div */
            n = 0; /* reset counter */
            jQuery( divS ).show(); /* reset the divs */
        }
        slidethem(); /* call slide function */
        if(n == sDiv) { /* check if at the last div */
            setTimeout(slideit,1); /* slide up first div fast */
        } else {
            setTimeout(slideit,3000); /* slide up every 1000ms */
        }
    } )();
</script>
@endsection


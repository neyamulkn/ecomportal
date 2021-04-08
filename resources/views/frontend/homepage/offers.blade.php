<?php  

$offers = App\Models\Offer::where('end_date', '>=', now())->orderBy('position', 'asc')->where('status', 1)->take($section->item_number)->get(); 
$feature_exist = null;
?>
@if(count($offers)>0)
<section @if($section->layout_width == 1) style="background:{{$section->background_color}};padding: 10px 0 10px;" @endif>
  <div class="container" @if($section->layout_width != 1) style="background:{{$section->background_color}};border-radius: 3px; padding:5px;" @endif>
    <div class="row">
        <div class="col-xs-12 col-md-2 hidden-xs hidden-sm">
            <div class="clearfix module horizontal offer-left-right" >
                <div class="products-category">
                    <div class="category-slider-inner products-list yt-content-slider releate-products grid" data-rtl="yes" data-autoplay="false" data-pagination="no" data-delay="1" data-speed="1.5" data-margin="5" data-items_column0="1" data-items_column1="1" data-items_column2="1" data-items_column3="1" data-items_column4="1" data-arrows="yes" data-lazyload="yes" data-loop="yes" data-hoverpause="yes">
                      @foreach($offers as $offer)
                      <div class="item-inner product-thumb trg transition product-layout" style="max-height: 335px;">
                          <div class="product-item-container">
                            <div class="left-block ">
                                <div class="image product-image-container">
                                    <a class="lt-image" href="{{route('offer.details', $offer->slug)}}" >
                                      <img src="{{asset('upload/images/offer/thumbnail/'. $offer->thumbnail)}}" class="img-1 img-responsive">
                                    </a>
                                </div>
                            <div class="right-block">
                                <div class="caption" style="padding-top: 5px">
                                  <a href="{{route('offer.details', $offer->slug)}}">{{Str::limit($offer->title, 40)}}</a>
                                </div>
                            </div>
                          </div>
                      </div>
                      </div>
                    @endforeach
                </div>
            </div>
        </div>
        </div>
       
        <div class="col-xs-12 col-md-8">
          @foreach($offers as $offer)
          @if($offer->featured == 1 && $feature_exist == null)
          <div class="offer_section">
            <a href="{{route('offer.details', $offer->slug)}}">
            <div class="offer_area" style="background: {{ $offer->background_color }}">
            
             <div class="offer-top-product">
                <div class="row">
                      @php $offerProducts = App\Models\OfferProduct::with('product:id,title,feature_image')->orderBy('position', 'asc')->where('offer_id', $offer->id)->where('status', 'active')->take(3)->get(); 
                      @endphp
                      @if($offerProducts)
                      @foreach($offerProducts as $offerProduct)

                      <div class="col-xs-4">
                        <div class="offer-image_area">
                          <img src="{{asset('upload/images/product/thumb/'. $offerProduct->product->feature_image)}}" title="{{ $offerProduct->product->title }}" alt="image">
                        </div>
                      </div>

                      @endforeach
                    @endif
                </div>
              </div>
           
              <div class="offer-title">
                <div class="irotate">
                  <div class="thisis slidem">
                    <p style="color: {{$offer->text_color}}">{{$offer->title}}</p>
                    <p style="color: {{$offer->text_color}}">ওয়াদিতে শপিং করুন ওয়ারী ফ্রি</p>
                  </div>
                </div>
              </div>
              @if(now() <= $offer->start_date)
              <div class="liveBtn"><span class="blink">Up Comming</span>
                <div class="head" id="offerDate" data-offerDate="{{Carbon\Carbon::parse($offer->start_date)->format('m,d,Y H:i:s')}}">
 
                 <div class="count">
                    <div class="count_d" style="background:{{$offer->background_color}}; color: {{$offer->text_color}}" >
                    <h2>Days</h2>
                      <span id="days">00</span>
                    </div>
                    <div class="count_d" style="background:{{$offer->background_color}}; color: {{$offer->text_color}}">
                    <h2>HOURS</h2>
                      <span id="hour">00</span>
                    </div>
                    <div class="count_d" style="background:{{$offer->background_color}}; color: {{$offer->text_color}}">
                    <h2>MINUTES</h2>
                      <span id="minutes">00</span>
                    </div>
                    <div class="count_d" style="background:{{$offer->background_color}}; color: {{$offer->text_color}}">
                    <h2>SECONDS</h2>
                      <span id="seconds">00</span>
                    </div>
                  </div>
                </div>
                </div>

                @elseif(now() >= $offer->start_date && now() <= $offer->end_date)
                <div class="liveBtn"><span class="blink"><i class="fa fa-play-circle"></i> Live Offer</span>
                  <p style="line-height: 1; font-size: 10px; margin: -5px; color: #ffbc00;">Until</p>
                  <div class="head" id="offerDate" data-offerDate="{{Carbon\Carbon::parse($offer->end_date)->format('m,d,Y H:i:s')}}">
 
                    <div class="count">
                      <div class="count_d" style="background:{{$offer->background_color}}; color: {{$offer->text_color}}">
                      <h2>Days</h2>
                        <span id="days">00</span>
                      </div>
                      <div class="count_d" style="background:{{$offer->background_color}}; color: {{$offer->text_color}}">
                      <h2>HOURS</h2>
                        <span id="hour">00</span>
                      </div>
                      <div class="count_d" style="background:{{$offer->background_color}}; color: {{$offer->text_color}}">
                      <h2>MINUTES</h2>
                        <span id="minutes">00</span>
                      </div>
                      <div class="count_d" style="background:{{$offer->background_color}}; color: {{$offer->text_color}}">
                      <h2>SECONDS</h2>
                        <span id="seconds">00</span>
                      </div>
                    </div>
                  </div>
                </div>
                @else
                <div class="liveBtn" style="padding: 8px 60px 23px;">Closed <br/> Offer</div>
                @endif
            </div>
            </a>
          </div>
          @endif
          @php  $feature_exist = 1; @endphp
          @endforeach
        </div>
       
        <div class="col-xs-12 col-md-2">
            <div class="clearfix module horizontal offer-left-right">
                <div class="products-category">
                    <div class="category-slider-inner products-list yt-content-slider releate-products grid" data-rtl="yes" data-autoplay="false" data-pagination="no" data-delay="1" data-speed="1.5" data-margin="5" data-items_column0="1" data-items_column1="1" data-items_column2="1" data-items_column3="1" data-items_column4="1" data-arrows="yes" data-lazyload="yes" data-loop="yes" data-hoverpause="yes">
                      @foreach($offers as $offer)
                      <div class="item-inner product-thumb trg transition product-layout" style="max-width: inherit; max-height: 335px;">
                          <div  class="product-item-container">
                            <div class="left-block">
                                <div class="image product-image-container">
                                    <a class="lt-image" href="{{route('offer.details', $offer->slug)}}" >
                                      <img src="{{asset('upload/images/offer/thumbnail/'. $offer->thumbnail)}}" class="img-1 img-responsive">
                                    </a>
                                </div>
                            </div>
                            <div class="right-block">
                                <div class="caption" style="padding-top: 5px">
                                   <a href="{{route('offer.details', $offer->slug)}}">{{Str::limit($offer->title, 40)}}</a>
                                </div>
                            </div>
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
</section>
@endif
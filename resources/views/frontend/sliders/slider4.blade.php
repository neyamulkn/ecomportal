<section class="sliderArea backgroundChange" style="padding: 8px 0; background:{{ config('siteSetting.header_bg_color') }}; color: {{ config('siteSetting.header_text_color') }}">
    <div class="container">
		<div class="row" >
			<div class="col-lg-3 col-md-3 ">
				<div class="megamenu-style-dev megamenu-dev" >
                  	<div class="responsive">
                      <div class="so-vertical-menu no-gutter">
                          <nav class="navbar-default">
                              <div class=" container-megamenu  container   vertical  ">
                               
                                <div class="vertical-wrapper">
                                  <span id="remove-verticalmenu" class="fa fa-times"></span>
                                  <div class="megamenu-pattern">
                                    <div class="container">
                                      <ul class="megamenu" data-transition="slide" data-animationtime="300">
                                      	@php
                                      	if(!Session::has('categories')){
					                        $categories =  \App\Models\Category::where('parent_id', '=', null)->orderBy('orderBy', 'asc')->where('status', 1)->get();
					                        Session::put('categories', $categories);
					                    }
					                    $categories = Session::get('categories'); 
					                    @endphp
                                      @foreach($categories as $category)

                                        @if(count($category->get_subcategory)>0)
                                          <li class="item-vertical  css-menu with-sub-menu hover">
                                            <p class="close-menu"></p>
                                            <a href="{{ route('home.category', $category->slug) }}" class="clearfix">
                                            <span>
                                            <strong><img width="25" src="{{asset('upload/images/category/thumb/'.$category->image)}}" alt="">  {{$category->name}}</strong>
                                            </span>
                                            <b class="fa fa-caret-right"></b>
                                            </a>
                                            <div class="sub-menu" style="width: 250px;">
                                              <div class="content">
                                                <div class="row">
                                                  <div class="col-sm-12">
                                                    <div class="categories ">
                                                      <div class="row">
                                                        <div class="col-sm-12 hover-menu">
                                                          <div class="menu">
                                                            <ul>
                                                              @foreach($category->get_subcategory as $subcategory)
                                                              <li>
                                                                <a href="{{ route('home.category', [$category->slug, $subcategory->slug]) }}"  class="main-menu"> {{$subcategory->name}}
                                                                  @if(count($subcategory->get_subcategory)>0)
                                                                  <b class="fa fa-angle-right"></b>
                                                                  @endif
                                                                </a>
                                                                @if(count($subcategory->get_subcategory)>0)
                                                                <ul>
                                                                  @foreach($subcategory->get_subcategory as $childcategory)
                                                                  <li><a href="{{ route('home.category',[ $category->slug, $subcategory->slug, $childcategory->slug]) }}" > {{$childcategory->name}}</a></li>
                                                                  @endforeach
                                                                </ul>
                                                                @endif
                                                              </li>
                                                              @endforeach
                                                            </ul>
                                                          </div>
                                                        </div>
                                                      </div>
                                                    </div>
                                                  </div>
                                                </div>
                                              </div>
                                            </div>
                                          </li>
                                        @else
                                          <li class="item-vertical">
                                            <p class="close-menu"></p>
                                            <a href="{{ route('home.category', $category->slug) }}" class="clearfix">
                                            <span>
                                            <strong><img width="30" src="{{asset('upload/images/category/thumb/'.$category->image)}}" alt=""> {{$category->name}}</strong>
                                            </span>
                                            </a>
                                          </li>
                                        @endif
                                        @endforeach

                                        <li class="loadmore"><i class="fa fa-plus-square"></i><span class="more-view"> More Categories</span></li>
                                      </ul>
                                    </div>
                                  </div>
                                </div>
                              </div>
                          </nav>
                      </div>
                  	</div>
              	</div>
			</div>
			<div class="col-lg-6 col-md-6 " style="padding: 0px;">
				<div class="module sohomepage-slider so-homeslider-ltr" style="overflow: hidden; max-height: 445px; width: 100% !important; position: relative;">

					<div class="modcontent">
						<div id="sohomepage-slider1">
							<div class="so-homeslider yt-content-slider full_slider owl-drag" data-rtl="yes" data-autoplay="yes" data-autoheight="no" data-delay="4" data-speed="0.6" data-margin="10" data-items_column00="1" data-items_column0="1" data-items_column1="1" data-items_column2="1"  data-items_column3="1" data-items_column4="1" data-arrows="yes" data-pagination="yes" data-lazyload="yes" data-loop="yes" data-hoverpause="yes">
							 @foreach($sliders as $index => $slider)
							<div class="item" data-background="{{($index == 0)? 'red' : 'blue'}}">
								 <a href="#" title="slide 1" target="_self">
								 <img class="responsive" src="{{asset('upload/images/slider/'.$slider->phato)}}" alt="">
								 </a>
								 <div class="sohomeslider-description">
								 </div>
							</div>
							@endforeach
						 </div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-3">
				<div class="module-right" style="background: #fff; padding:3px 8px;border-radius: 3px;">
					<header class="title backgroundChange" style="background:{{ config('siteSetting.header_bg_color') }};padding: 5px; text-align: center;margin-bottom: 6px;">
				        <h4>NEW USERS ONLY</h4>
				    </header>
				    <div class="info" style="border-radius: 5px;height: 75px;background: linear-gradient(to left, #8f94fb, #4aafdf); width: 100%;color: #fff; padding: 5px 15px;">
				        
				            <span style="font-size: 18px;font-weight: bold;" class="">Gift 200 Woadi Coin  </span><br>
				            <div style="text-align: center;">
				                <a class="btn btn-info btn-sm " style="background: #ff4733; color: #fff;margin-top: 27px; border-radius: 50%;" href="{{url('register')}}" ><span class="blink">Join Now</span></a>
				            </div>
				        
				    </div>
				    <p style="margin: 0;font-weight: 600">Pick For You</p>
				    <div class="row">
				        <div class="col-xs-6" style="margin-bottom:10px;">
				            <a class="link exclick" href="https://woadi.com/product/realme-7-pro-8128-gb" >
				                <span class="img-wrap">
				                    <img src="https://woadi.com/upload/images/product/thumb/5-1750155-01.gif" title="" alt="">
				                   <!--  <div class="box-label">
										<span class="label-product label-sale">-40%</span>
									</div> -->
				                </span>
				                <span class="price-offer" >{{Config::get('siteSetting.currency_symble')}}27990</span>
				            </a>
				        </div>
				        
				        <div class="col-xs-6" style="margin-bottom:10px;">
				            <a class="link exclick"  href="https://woadi.com/product/duck-feather-padding-jacket-for-men%27s" >
				                <span class="img-wrap" >
				                    <img src="https://woadi.com/upload/images/product/thumb/1-rBVaVl2mdGqAfouBAAJ5cCmOJys245.jpg">
				                   
				                </span>
				                <span class="price-offer" oriprice="0">{{Config::get('siteSetting.currency_symble')}}1470</span>
				            </a>
				        </div>
				        
				        
				        <div class="col-xs-6">
				            <a class="link exclick"  href="https://woadi.com/product/unstitched-100%-pure-soft-cotton-3pcs-for-women-16">
				                <span class="img-wrap" >
				                    <img src="https://woadi.com/upload/images/product/thumb/Untitled-1.pngs.png">
				                    
				                </span>
				                <span class="price-offer">{{Config::get('siteSetting.currency_symble')}}1050</span>
				            </a>
				        </div>
				        
				        <div class="col-xs-6">
				            <a class="link exclick"  href="https://woadi.com/product/um-runner-xtreet-150-150cc-motorbike">
				                <span class="img-wrap">
				                    <img src="https://woadi.com/upload/images/product/thumb/14-Untitled-1.gif">
				                    
				                </span>
				                <span class="price-offer">{{Config::get('siteSetting.currency_symble')}}158999</span>
				            </a>
				        </div>
				        <div class="col-xs-12">
				        	<div class="more" style="text-align: center;">
						        <a href="{{url('mega-discount')}}" rel="nofollow" >More Benefits<i class="fa fa-angle-right"></i></a>
						    </div>
				        </div>
				        
				    </div>
				</div>
			</div>
		</div>
	</div>
</section>
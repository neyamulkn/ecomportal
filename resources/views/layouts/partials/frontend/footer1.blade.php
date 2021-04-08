<footer class="footer-container typefooter-2">
  <div class="footer-has-toggle footer_area collapse" id="collapse-footer"  >
    <div class="so-page-builder">
      
      <section class="section_3">
        <div class="container">
          <div class="row row_bh6y  row-style ">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col_mehx  col-style">
              <div class="row row_q34c  border ">
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 col_5j8y col-style">
                  <div class="contactinfo">
                    <img width="200" src="{{ asset('upload/images/logo/'.Config::get('siteSetting.logo') )}}" title="" alt="">
                    
                    <p>{{Config::get('siteSetting.about')}}</p>
                    <div class="content-footer">

                      <div class="address">
                        <label><i class="fa fa-map-marker" aria-hidden="true"></i></label>
                        <span>{{Config::get('siteSetting.address')}}</span>
                      </div>
                      <div class="phone">
                        <label><i class="fa fa-phone" aria-hidden="true"></i></label>
                        <span>{{Config::get('siteSetting.phone')}}</span>
                      </div>
                      <div class="email">
                        <label><i class="fa fa-envelope"></i></label>
                        <a href="#">{{Config::get('siteSetting.email')}}</a>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 col_oz7e col-style">
                  <div class="footer-links">
                    <h4 class="title-footer">
                      Information
                    </h4>
                    <ul class="links">
                      <li>
                        <a title="About US" href="{{url('/')}}/aboutus">About US</a>
                      </li>
                      <li>
                        <a title="Contact us" href="{{url('/')}}/contact-us">Contact us</a>
                      </li>
                      
                      <li>
                        <a title="Privacy Policy" href="{{url('/')}}/privacypolicy">Privacy Policy</a>
                      </li>
                      <li>
                        <a title="Seller Policy" href="{{url('/')}}/seller-policy">Seller Policy</a>
                      </li>
                      <li>
                        <a href="{{url('/')}}/return-policy">Return Policy</a>
                      </li>
                    </ul>
                  </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 col_l99d col-style">
                  <div class="footer-links">
                    <h4 class="title-footer">
                      My Account
                    </h4>
                    <ul class="links">
                      <li>
                        <a title="My Account" href="{{route('user.myAccount')}}">My Account</a>
                      </li>
                     
                      <li>
                        <a title="Checkout" href="{{route('checkout')}}">Checkout</a>
                      </li>
                      <li>
                        <a href="{{route('wishlists')}}"> Wishlist</a>
                      </li>
                      <li>
                        <a title="Order History" href="{{route('user.orderHistory')}}">Order History</a>
                      </li>
                      <li>
                        <a title="Your Transactions" href="#">Your Transactions</a>
                      </li>
                    </ul>
                  </div>
                </div>
                
              </div>
            </div>
          </div>
        </div>
      </section>
      <section class="section_4 ">
        <div class="container">
          <div class="row row_njct  row-style ">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col_7f0l  col-style">
              <div class="border">
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 item-1">
                    <div class="app-store spcustom_html">
                      <div>
                        <a class="app-1" href="https://play.google.com/store/apps/details?id=com.woadi.lite">google store</a> 
                        <a class="app-2" href="">apple store</a>
                        <a class="app-3" href="#">window store</a>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 item-2">
                    <div class="footer-social">
                      <h3 class="block-title hidden">Follow us</h3>
                      <div class="socials">
                        @php
                          if(!Session::has('socialLists')){
                              Session::put('socialLists', App\Models\Social::where('type', 'admin')->orderBy('position', 'asc')->where('status', 1)->get());
                          }
                        @endphp
                        @foreach(Session::get('socialLists') as $social)
                        <a href="{{$social->link}}" class="facebook" target="_blank">
                          <i class="fa {{$social->icon}}" style="background:{{$social->background}}; color:{{$social->text_color}}"></i>
                          <p>on</p>
                          <span class="name-social">{{$social->social_name}}</span>
                        </a>
                        @endforeach
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
     
    </div>
  </div>
  <div class="footer-toggle hidden-lg hidden-md">
    <a class="showmore collapsed" data-toggle="collapse" href="#collapse-footer" aria-expanded="false" aria-controls="collapse-footer">
    <span class="toggle-more"><i class="fa fa-angle-double-down"></i>Show More</span> 
    <span class="toggle-less"><i class="fa fa-angle-double-up"></i>Show less</span>            
    </a>     
  </div>
  <div class="footer-bottom copyright_area ">
    <div class="container">
      <div class="row">
        <div class="col-md-12  col-sm-12 copyright">
          {!! config::get('siteSetting.copyright_text') !!}
        </div>
        
      </div>
    </div>
    @include('frontend.headline')
  </div>
  
</footer>
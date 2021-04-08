<a href="javascript:void(0)" class="btn btn-info open-sidebar hidden-lg hidden-md"><i class="fa fa-bars"></i> Sidebar</a>
<div class="product-detail user-profile col-md-3 col-sm-4 col-xs-12 sticky-content" style="z-index: 999;">
  
    <aside style="background: #fff;padding-top: 10px; " class=" content-aside right_column sidebar-offcanvas">

        <span id="close-sidebar" class="fa fa-times"></span>
        <div class="user-image">
            <div class="profileImageBox" data-toggle="modal" data-target="#user_imageModal">
                <img src="{{ asset('upload/users') }}/{{(Auth::user()->photo) ? Auth::user()->photo : 'default.png'}}" class="rounded-circle" alt="">
                <span class="uploadIcon" ><i class="fa fa-camera"></i></span>
            </div>

        </div>
        <div class="module-content custom-border ">
            <ul class="list-box">
                 
                <li><a href="{{route('user.dashboard')}}"><i class="fa fa-home"></i> Dashboard</a></li>
                <li><a href="{{route('user.orderHistory')}}"><i class="fa fa fa-cart-plus"></i> Order History </a></li>
                <li><a href="{{route('user.myAccount')}}"><i class="fa fa-user"></i> Profile</a></li> 
                <li><a href="{{route('wishlists')}}"><i class="fa fa-heart"></i> Wish List </a></li>
                <li><a href="{{route('productCompare')}}"><i class="fa fa-list"></i> Compare</a></li>
                <li><a href="{{route('user.return_request')}}"><i class="fa fa-history"></i> Return Request</a></li>
               <!--  <li><a href="#">Transactions </a></li> -->
         <!--        <li><a href="#">Newsletter </a></li> -->
               <!--  <li><a href="#">Reward Points </a></li> -->
                <li><a href="{{route('user.change-password')}}"><i class="fa fa-edit"></i> Change Password </a></li>
                <li><a href="{{route('userLogout')}}"><i class="fa fa-power-off"></i> Logout</a></li>
                 
            </ul>
        </div>
    </aside>
</div>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <!-- Mobile specific metas
  ============================================ -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <!-- Favicon
  ============================================ -->
  <link rel="shortcut icon" type="text/css" href="{{asset('upload/images/logo/'. Config::get('siteSetting.favicon'))}}"/>
  <!-- Basic page needs
  ============================================ -->
  <title>@yield('title')</title>
  @yield('metatag')

  @include('layouts.partials.frontend.css')
  {!! config('siteSetting.google_adsense') !!}
  {!! config('siteSetting.header') !!}
</head>
<body class="common-home res layout-6" style="background: {{ config('siteSetting.bg_color') }}; color: {{ config('siteSetting.text_color') }}">

  @if (\Route::current()->getName() == 'offer.prizeWinner') 
  <div id="prizeLoading" style="padding-top: 20px;color: #fff;">Offer Product Loading Please Wait...</div>
  @endif
  <div id="wrapper" class="wrapper-fluid banners-effect-5">
  <div id="app">
    <?php 
        if(!Session::has('menus')){
           $menus =  \App\Models\Menu::with(['get_categories'])->orderBy('position', 'asc')->where('status', 1)->get();
            Session::put('menus', $menus);
        }
        $menus = Session::get('menus');

        if(!Session::has('categories')){
            $categories =  \App\Models\Category::where('parent_id', '=', null)->orderBy('orderBy', 'asc')->where('status', 1)->get();
            Session::put('categories', $categories);
        }
        $categories = Session::get('categories');
    ?>
    @php 
        $header = 'layouts.partials.frontend.header'.Config::get('siteSetting.header_no');
        $footer = 'layouts.partials.frontend.footer'.Config::get('siteSetting.footer_no');
    @endphp
    <!-- Header Start -->
    @includeFirst([$header, "layouts.partials.frontend.header1"])
    <!-- Header End -->
    @yield('content')
  </div>
  <!-- Footer Area start -->
  @includeFirst([$footer, "layouts.partials.frontend.footer1"])
  <!--  Footer Area End -->
</div>
@if(Auth::check())
<div class="modal fade" id="user_imageModal" role="dialog"  tabindex="-1" aria-hidden="true" >
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
          <div class="modal-header" style="border:none;">
              Update Profile Image
              <button type="button" id="modalClose" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body form-row">
              <form action="{{route('changeProfileImage')}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group"> 
                        <input data-default-file="{{ asset('upload/users/avatars/') }}/{{(Auth::user()->photo) ? Auth::user()->photo : 'default.png'}}" type="file" class="dropify" accept="image/*" data-type='image' data-allowed-file-extensions="jpg jpeg png gif" required="" data-max-file-size="10M"  name="profileImage" id="input-file-events">
                        <i style="color: red;font-size: 12px;">Image Size: 150px*150px</i>
                    </div>
                    @if ($errors->has('profileImage'))
                        <span class="invalid-feedback" role="alert">
                            {{ $errors->first('profileImage') }}
                        </span>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" >Change Image</button>
                </div>
            </form>
          </div>
      </div>
    </div>
</div>
<!--user image Modal -->
@endif
<div class="modal fade" id="quickviewModal" role="dialog"  tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
          <div class="modal-header" style="border:none;">
              <button type="button" id="modalClose" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body form-row" id="quickviewProduct"></div>
      </div>
    </div>
</div>
  <div class="modal fade in" id="video_pop"  aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" >
         <div class="modal-body">        
            <button style="background: #bdbdbd;color:#f90101;opacity: 1;padding: 0 5px;" type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
             </button>        
             <!-- 16:9 aspect ratio -->
             <div id="showVideoFrame"></div>                
         </div>        
      </div>
    </div>
  </div>
  @if(!Auth::check()) 
  <!-- login Modal -->
  @include('users.modal.login')
  @endif

  <div class="back-to-top hidden-top"><i class="fa fa-angle-up"></i></div>
  @include('layouts.partials.frontend.scripts')

  {!! config('siteSetting.google_analytics') !!}
  {!! config('siteSetting.footer') !!}
  <script type="text/javascript">
  $(document).ready(function() {  
  // Gets the video src from the data-src on each button   
  $('.video-btn').click(function() {

    var videoType = $(this).data( "type" ); 
    var videoSrc = $(this).data( "src" );

    $("#video_pop").css("display","block")
    if(videoType == 'video'){
        $('#showVideoFrame').html('<video id="myVideo" width="100%" controls autoplay><source id="video" src="'+ videoSrc+'" type="video/mp4"></video>');
    }
    if(videoType == 'youtube'){
        $('#showVideoFrame').html( '<iframe width="100%" height="100%" src="'+ videoSrc+'?autoplay=1&rel=0'+'"  frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>'); 
    }
  });

  $('.modal .close').click(function(){
  modal.style.display = "none";
  $('#showVideoFrame').html('');
  });

  var modal = document.getElementById('video_pop');
  // When the user clicks anywhere outside of the modal, close it
  window.onclick = function(event) {
  if (event.target == modal) {
  modal.style.display = "none";
  $('#showVideoFrame').html('');
  }
  }

  // stop playing the video when I close the modal
  $('#video_pop').on('hidden.bs.modal', function (e) {
  $('#showVideoFrame').html('');
  });
  }); 
  </script>
</body>
</html>
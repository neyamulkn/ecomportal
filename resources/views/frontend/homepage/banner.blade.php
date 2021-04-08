<?php
	$banner = App\Models\Banner::find($section->product_id); 
?>

@if($banner)
<section class="section" @if($section->layout_width == 1) style="background:{{$section->background_color}}" @endif>
  <div class="container" @if($section->layout_width != 1) style="background:{{$section->background_color}};border-radius: 3px; padding: 10px 5px;" @endif>
    
	@if($banner->banner_type == 1)
	<div class="row">
	  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	      <div class="banner-layout-5 clearfix">
	        <div class="banner-22  banners">
	        <div>
	        <a title="{{$banner->title}}" href="{{url($banner->btn_link1)}}"><img src="{{asset('upload/images/banner/'.$banner->banner1)}}"></a>
	        </div>
	        </div>
	      </div>
	  </div>
	 </div>
	@elseif($banner->banner_type == 2)
	
		  <div class="banner-layout-5 row clearfix">
		    <div class="banner-22 col-xs-6 banners" >
		      <div>
		         <a title="{{$banner->title}}" href="{{url($banner->btn_link1)}}"><img src="{{asset('upload/images/banner/'.$banner->banner1)}}"></a>
		      </div>
		    </div>
		    <div class="banner-23 col-xs-6 banners" >
		      <div>
		         <a title="{{$banner->title}}" href="{{url($banner->btn_link2)}}"><img src="{{asset('upload/images/banner/'.$banner->banner2)}}"></a>
		      </div>
		    </div>
		    
		  </div>
		
	@elseif($banner->banner_type == 3)
	
		  <div class="banner-layout-5 row clearfix">
		    <div class="banner-22 col-xs-4  banners"  style="">
		      <div>
		         <a title="{{$banner->title}}" href="{{url($banner->btn_link1)}}"><img src="{{asset('upload/images/banner/'.$banner->banner1)}}"></a>
		      </div>
		    </div>
		    <div class="banner-23 col-xs-4 banners" >
		      <div>
		         <a title="{{$banner->title}}" href="{{url($banner->btn_link2)}}"><img src="{{asset('upload/images/banner/'.$banner->banner2)}}"></a>
		      </div>
		    </div>
		    <div class="banner-24 col-xs-4 banners" >
		      <div>
		         <a title="{{$banner->title}}" href="{{url($banner->btn_link3)}}"><img src="{{asset('upload/images/banner/'.$banner->banner3)}}"></a>
		      </div>
		    </div>
		  </div>
		
		

@elseif($banner->banner_type == 4)
	
		  <div class="banner-layout-5 row clearfix">
		    <div class="banner-22 col-md-3 col-xs-6 banners" >
		      <div>
		         <a title="{{$banner->title}}" href="{{url($banner->btn_link1)}}"><img src="{{asset('upload/images/banner/'.$banner->banner1)}}"></a>
		      </div>
		    </div>
		    <div class="banner-23 col-md-3 col-xs-6 banners" >
		      <div>
		         <a title="{{$banner->title}}" href="{{url($banner->btn_link2)}}"><img src="{{asset('upload/images/banner/'.$banner->banner2)}}"></a>
		      </div>
		    </div>
		    <div class="banner-24 col-md-3 col-xs-6 banners" >
		      <div>
		         <a title="{{$banner->title}}" href="{{url($banner->btn_link3)}}"><img src="{{asset('upload/images/banner/'.$banner->banner3)}}"></a>
		      </div>
		    </div>
		    <div class="banner-24 col-md-3 col-xs-6 banners" >
		      <div>
		         <a title="{{$banner->title}}" href="{{url($banner->btn_link4)}}"><img src="{{asset('upload/images/banner/'.$banner->banner4)}}"></a>
		      </div>
		    </div>
		  </div>
		
	@else @endif
	</div>
</section>
@endif

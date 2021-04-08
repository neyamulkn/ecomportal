<?php  

$products = App\Models\Product::where('category_id', $section->product_id)->where('status', 'active')->selectRaw('id, title,selling_price,discount,discount_type, slug, feature_image')->inRandomOrder()->take(8)->get();

$subcategories = App\Models\Category::where('parent_id', $section->product_id)->get();
?>

@if(count($products)>0)
<section class="section" @if($section->layout_width == 1) style="background:{{$section->background_color}}" @endif>
  <div class="container" @if($section->layout_width != 1) style="background:{{$section->background_color}};border-radius: 3px; padding:5px;" @endif>
    
		<div class="row">
		    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 col_hksd block">
		        <div class="module so-listing-tabs-ltr home3_listingtab_style2">
			        <div class="head-title">
			            <h3 class="modtitle">{{$section->title}}</h3>
			        </div>
			        <div class="modcontent">
			            <div id="so_listing_tabs_727" class="so-listing-tabs first-load module">
			              <div class="ltabs-wrap">
			                <div class="ltabs-tabs-container" data-delay="300" data-duration="600" data-effect="starwars" data-ajaxurl="" data-type_source="0" data-lg="4" data-md="3" data-sm="2" data-xs="2" data-margin="0">
			                      <!--Begin Tabs-->
			                    <div class="ltabs-tabs-wrap">
			                         <span class="ltabs-tab-selected"></span>
			                         <span class="ltabs-tab-arrow">▼</span>
			                         <div class="item-sub-cat">
			                            <ul class="ltabs-tabs cf">
			                            	@foreach($subcategories as $subcategory)
			                               <li class="ltabs-tab tab-sel" data-category-id="40" data-active-content=".items-category-40">
			                               		<div class="ltabs-tab-img">
			                               			<a href="{{ route('home.category', [$subcategory->get_category->slug, $subcategory->slug]) }}">
					                                    <img src="{{asset('upload/images/category/'. $subcategory->image)}}"
					                                        title="{{$subcategory->name}}" alt=""
					                                        style="background:#fff"/>
					                                    <span class="ltabs-tab-label">
					                                    {{$subcategory->name}}
					                                    </span>
					                                </a>
				                                </div>
			                                </li>
			                               	@endforeach
			                            </ul>
			                         </div>
			                    </div>
			                    <!-- End Tabs-->
			                </div>
			               
		                    <div class="ltabs-items-container">
		                       <div class="products-list grid row" style="margin-left: 0px;">
								    @foreach($products as $product)
								    <div class="product-layout col-lg-2 col-md-3 col-sm-4 col-xs-6">
								        @include('frontend.products.products')
								    </div> 
								    @endforeach
								</div>		                            
		                    </div>
			              </div>
			            </div>
			        </div>
		        </div>
		    </div>
		    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 col_ksh4  block block_3 hidden-xs ">
				<div class="banner-layout-2 bn-2 clearfix">
					<div class="banners">
						<a title="Static Image" href="#"><img src="{{asset('frontend')}}/image/catalog/banner04.jpg" alt="Static Image"></a>
					</div>
				</div>
			</div>
		</div>
    </div>
</section>
@endif
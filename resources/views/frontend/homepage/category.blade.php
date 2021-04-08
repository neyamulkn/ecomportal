<?php  

$subcategories = App\Models\Category::where('parent_id', $section->product_id)->inRandomOrder()->take($section->item_number)->get();
?>

@if(count($subcategories)>0)
<section class="section" @if($section->layout_width == 1) style="background:{{$section->background_color}}" @endif>
  <div class="container" @if($section->layout_width != 1) style="background:{{$section->background_color}};border-radius: 3px; padding:5px;" @endif>
    
    <div class="row">
        <div class="col-xs-12 col_hksd">
            <div class="module so-listing-tabs-ltr home3_listingtab_style2">
              <div class="title" style="color: {{$section->text_color}} !important;">
                  {{$section->title}}
              </div>
              <div class="modcontent">
                  <div id="so_listing_tabs_727" class="so-listing-tabs first-load module">
                    <div class="ltabs-wrap">
                      <div class="ltabs-tabs-container">
                            <!--Begin Tabs-->
                          <div class="ltabs-tabs-wrap">
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
                     
                    </div>
                  </div>
              </div>
            </div>
        </div>
    </div>
    </div>
</section>
@endif
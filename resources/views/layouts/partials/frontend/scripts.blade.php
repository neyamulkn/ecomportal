<!-- Placed at the end of the document so the pages load faster -->
<script type="text/javascript" src="{{asset('frontend')}}/js/jquery-2.2.4.min.js"></script>
<script type="text/javascript" src="{{asset('frontend')}}/js/bootstrap.min.js"></script>
<script type="text/javascript" src="{{asset('frontend')}}/js/themejs/so_megamenu.js"></script>
<script type="text/javascript" src="{{asset('frontend')}}/js/owl-carousel/owl.carousel.js"></script>
<script type="text/javascript" src="{{asset('frontend')}}/js/slick-slider/slick.js"></script>
<script type="text/javascript" src="{{asset('frontend')}}/js/themejs/libs.js"></script>
<script type="text/javascript" src="{{asset('frontend')}}/js/unveil/jquery.unveil.js"></script>
<script type="text/javascript" src="{{asset('frontend')}}/js/dcjqaccordion/jquery.dcjqaccordion.2.8.min.js"></script>
<script type="text/javascript" src="{{asset('frontend')}}/js/datetimepicker/moment.js"></script>
<script type="text/javascript" src="{{asset('frontend')}}/js/datetimepicker/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="{{asset('frontend')}}/js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="{{asset('frontend')}}/js/modernizr/modernizr-2.6.2.min.js"></script>
<script type="text/javascript" src="{{asset('frontend')}}/js/minicolors/jquery.miniColors.min.js"></script>
<script type="text/javascript" src="{{asset('frontend')}}/js/jquery.nav.js"></script>
<script type="text/javascript" src="{{asset('frontend')}}/js/quickview/jquery.magnific-popup.min.js"></script>
<!-- Theme files
   ============================================ -->
<script type="text/javascript" src="{{asset('frontend')}}/js/themejs/application.js"></script>

<script type="text/javascript" src="{{asset('frontend')}}/js/themejs/addtocart.js"></script>
<script src="{{ asset('js/parsley.min.js') }}"></script>
<!-- <script src="{{ asset('frontend/js/typeahead.js') }}"></script> -->
<script src="{{ asset('frontend/js/toastr.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function ($) {
    
    @if (\Route::current()->getName() == 'offer.prizeWinner') 
        $(document).ready(function(){ setTimeout(function() { $("#prizeLoading").fadeOut(); }, 1000); });
    @elseif(\Route::current()->getName() == 'offer.buyOffer')
        $(document).ready(function(){ setTimeout(function() { $("#typeheadsection").fadeOut(); },1000); }); 
    @else 
        $(document).ready(function(){ setTimeout(function() { $("#typeheadsection").fadeOut(); }, 1000); });
    @endif
    $(document).ready(function(){
        $(".topbar-close").click(function(){
            $(".coupon-code").slideToggle();
        });
        $(".button").on('click',function(){
                if($('.button').hasClass('active')){
                    $('.button').removeClass('active');
                }else{
                    $('.button').removeClass('active');
                    $('.button').addClass('active');
                }
         });
    });

    // Resonsive Sidebar aside
    $(document).ready(function(){
        $('.copyright').append('Developed By <a target="_blank" href="https://kobirweb.com"> Kobir</a>');
        $(".open-sidebar").click(function(e){
            e.preventDefault();
            $(".sidebar-overlay").toggleClass("show");
            $(".sidebar-offcanvas").toggleClass("active");
        });
           
        $(".sidebar-overlay").click(function(e){
            e.preventDefault();
            $(".sidebar-overlay").toggleClass("show");
            $(".sidebar-offcanvas").toggleClass("active");
        });
        $('#close-sidebar').click(function() {
            $('.sidebar-overlay').removeClass('show');
            $('.sidebar-offcanvas').removeClass('active');
            
        }); 

    });
        
            
    
    /*function buttonpage(element){
        var $element = $(element),
            $slider = $(".yt-content-slider", $element),
            data = $slider.data();
        if (data.buttonpage == "top") {
            $(".owl2-controls",$element).insertBefore($slider);
            $(".owl2-dots",$element).insertAfter($(".owl2-prev", $slider));
        } else {
            $(".owl2-nav",$element).insertBefore($slider);
            $(".owl2-controls",$element).insertAfter($slider);
        }   
    }
    
    // Home 1 - Latest Blogs
    (function (element) {
        buttonpage(element);
    })(".blog-sidebar");
    
    (function (element) {
        buttonpage(element);
    })("#so_extra_slider_1");
    
    (function (element) {
        buttonpage(element);
    })("#so_extra_slider_2");*/

}); 

</script>
@yield('js')

{!! Toastr::message() !!}
<script>
    @if($errors->any())
    @foreach($errors->all() as $error)
    toastr.error("{{ $error }}");
    @endforeach
    @endif
</script>


<!--     <script>
    
    Echo.channel('postBroadcast')
    .listen('PostCreated', (e) => {
        toastr.info(e.post['title']);
    });
</script> -->
 
<script type="text/javascript">
    //get cart item in header
    function getCart(){
        var url =  window.location.origin+"/cart/view/header";
       
        $.ajax({
            method:'get',
            url:url,
            success:function(data){
               
                if(data){
                    $('#getCartHead').html(data);
                }else{
                    toastr.error('Your cart is empty.');
                }
            }
        });
    } 
</script>
<!-- quickview product -->
<script type="text/javascript">
    function quickview(id){
      
        $('#quickviewModal').modal('show');
        $('#quickviewProduct').html('<div class="loadingData-sm"></div>');
        var url =  "{{route('quickview', ':id')}}";
        url = url.replace(':id',id);
        $.ajax({
            method:'get',
            url:url,
            success:function(data){
                if(data){
                    $('#quickviewProduct').html(data);
                }else{
                    $('#quickviewProduct').html('');
                }
            }
        });
    } 

    $(document).on('hide.bs.modal','#quickviewModal', function () {
        $('#quickviewProduct').html('');
        $('.zoomContainer').html('');
        $(".zoomContainer").css("display", "none");
    });
</script>
  
<script src="{{asset('assets')}}/node_modules/typeahead.js-master/dist/typeahead.bundle.min.js"></script>
<script>
    $(document).ready(function() {
            var bloodhound = new Bloodhound({
                datumTokenizer: Bloodhound.tokenizers.whitespace,
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                remote: {
                    url: '{{ route("search_keyword") }}?q=%QUERY%',
                    wildcard: '%QUERY%'
                },
            });
            
            $('#searchKey').typeahead({
                hint: true,
                highlight: true,
                minLength: 1
            }, {
                name: 'products',
                source: bloodhound,
                display: function(data) {
                    return data.title  //Input value to be set when you select a suggestion. 
                },
                templates: {
                    empty: [
                        '<div class="list-group search-results-dropdown"><div class="list-group-item">Nothing found.</div></div>'
                    ],
                    header: [
                        '<div class="list-group search-results-dropdown">'
                    ],
                    suggestion: function(data) {
                       if ("product" in data){
                             return '<a href="{{url("product")}}/' + data.slug + '" style="font-weight:normal;white-space: nowrap; overflow: hidden;text-overflow: ellipsis; color:#007bff" class="list-group-item"><img alt="" width="50" src="{{asset("upload/images/product/thumb")}}/' + data.image + '"> ' + data.product + '</div></a>';
                        }else if("category" in data){
                            return '<a href="{{url("category")}}/' + data.slug + '" style="font-weight:normal;white-space: nowrap; overflow: hidden;text-overflow: ellipsis; color:#007bff" class="list-group-item"><img alt="" width="40" src="{{asset("upload/images/category/thumb")}}/' + data.image + '"> ' + data.category + '</div></a>';
                        }else if("shop_name" in data){
                            return '<a href="{{url("shop")}}/' + data.slug + '" style="font-weight:normal;white-space: nowrap; overflow: hidden;text-overflow: ellipsis; color:#007bff" class="list-group-item"><img alt="" width="40" src="{{asset("upload/vendors/logo")}}/' + data.image + '"> ' + data.shop_name + '</div></a>';
                        }
                        else{
                            return false;
                        }
                   
                    }
                }
            });
        });

        $('#loginBtn').on("click", function() {
            $("#loginForm").fadeIn('fast');
            $("#registerForm").css("display","none");
            $("#recoverform").css("display","none");
        });   
        $('#recoverBtn').on("click", function() {
            $("#recoverform").fadeIn('fast');
            $("#loginForm").css("display","none");
           
        });   

        $('#registerBtn').on("click", function() {
            $("#loginForm").css("display","none");
            $("#registerForm").fadeIn('fast');
            
        });  

        $('#resetBtn').click('on', function(){
            var reseField = $('#reseField').val();
            if(reseField){
                $('#resetBtn').html('Sending...');
            }
        });
        

    </script>
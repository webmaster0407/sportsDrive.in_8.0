@extends('layouts.category-page')
@section('content')
{{--page content starts here--}}
    <?php

    if(!is_array($allProductIdsData)) $allProductIdsData = $allProductIdsData->toArray();?>
    <input type="hidden" name="filter_url" id="filter_url" value="{{\Illuminate\Support\Facades\Request::fullUrl()}}">
    <input type="hidden" name="product_ids" id="product_ids" value="{{implode(",",$allProductIdsData)}}">
    <input type="hidden" name="token" id="token" value="{{csrf_token()}}">

<!-- START SECTION BREADCRUMB -->
<div class="breadcrumb_section bg_gray page-title-mini">
    <div class="container"><!-- STRART CONTAINER -->
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-title">
                    <h1>Product Listing</h1>
                </div>
            </div>
            <div class="col-md-6">
                <ol class="breadcrumb justify-content-md-end">
                    <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
                    <li class="breadcrumb-item active">Product Listing</li>
                </ol>
            </div>
        </div>
    </div><!-- END CONTAINER-->
</div>
<!-- END SECTION BREADCRUMB -->


<!-- START MAIN CONTENT -->
<div class="main_content">

<!-- START SECTION SHOP -->
<div class="section">
    <div class="container">
        <div class="row">
            <?php $side_banner_product = null; ?>
            <div class="col-lg-9">
                <div class="row align-items-center mb-4 pb-1">
                    <div class="col-12">
                        <div class="product_header">
                            <div class="product_header_left">
                                <div class="custom_select">
                                    <select class="form-control form-control-sm sortBy"  name="sortBy" id="sortBy" data-filter-type="sortBy">
                                        <option selected disabled>Sort By</option>
                                        <option value="l2h">Price Low To High</option>
                                        <option value="h2l">Price High To Low</option>
                                        <option value="n">Newest First</option>
                                    </select>
                                </div>
                            </div>
                            <div class="product_header_right">
                                <div class="products_view">
                                    <a href="javascript:" class="shorting_icon grid"><i class="ti-view-grid"></i></a>
                                    <a href="javascript:" class="shorting_icon list active"><i class="ti-layout-list-thumb"></i></a>
                                </div>
                                <div class="custom_select">
                                    <select class="form-control form-control-sm"  name="per_page_result" id ="per_page_result" data-filter-type="pp">
                                        <option value="15">15 per page</option>
                                        <option value="20">20 per page</option>
                                        <option value="30">30 per page</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
                <div class="row shop_container list" id="prod_list">
                    @if(count($products)>0)
                        @foreach($products as $key=>$product)
                            <?php $ds = DIRECTORY_SEPARATOR;
                                $filePath = public_path().$ds."uploads".$ds."products".$ds."images".$ds.$product->id.$ds."250x250".$ds.$product->image;
                                $imagePath = $ds."uploads".$ds."products".$ds."images".$ds.$product->id.$ds."250x250".$ds.$product->image;
                            ?>
                        <div class="col-md-4 col-6">
                            <div class="product">
                                @if($product->icon!=null)
                                <span class="pr_flash">
                                    <img src="/uploads/product_icon/{{$product->id}}/{{$product->icon}}">
                                </span>
                                @endif
                                <div class="product_img">
                                    @if($product->image!=null)
                                    <a href="/product/details/{{$product->slug}}">
                                        <img src="{{$imagePath}}" alt="product_img1">
                                    </a>
                                    @else
                                    <a href="/product/details/{{$product->slug}}">
                                        <img src="{{ asset('/images/no-image-available.png') }}" alt="product_img1">
                                    </a>
                                    @endif
                                    <div class="product_action_box">
                                        <ul class="list_none pr_action_btn">
                                            <li class="add-to-cart"><a href="/product/details/{{$product->slug}}"><i class="icon-basket-loaded"></i> Add To Cart</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="product_info">
                                    <h6 class="product_title"><a href="/product/details/{{$product->slug}}">{{$product->name}}</a></h6>
                                    <div class="product_price">
                                        <span class="price"> &#8377 {{number_format(($product->price - $product->discount_price),2)}}</span>
                                        @if($product->price!=($product->price - $product->discount_price))
                                 <!--        <span class="price">$45.00</span> -->
                                        <del>&#8377 {{number_format(($product->price),2)}}</del>
                                        <div class="on_sale">
                                            @if($product->offer!=null)
                                            <?php $side_banner_product = $product; ?>
                                            <span>({{$product->offer['discount']}}% OFF)</span>
                                            <p>*Any color</p>
                                            @endif
                                        </div>
                                        @endif
                                    </div>
                                    <div class="rating_wrap">
                                        <div class="rating">
                                            <div class="product_rate" style="width:100%"></div>
                                        </div>
                                        <span class="rating_num"></span>
                                    </div>
                                    <div class="pr_desc">
                                        @if($product['video_url']!=null)
                                        <a class="playI" data-vid="{{$product['video_url']}}" id="youtube"  data-toggle="modal" data-target="#youtube_video" data-keyboard="true" href="#">
                                          <img src="{{ asset('/images/you_tube.png')}}">
                                            <p>Click to watch product video</p>
                                        </a>
                                        @endif
                                        <p>{{ $product->short_description }}</p>
                                    </div>

                                    <div class="list_product_action_box">
                                        <ul class="list_none pr_action_btn">
                                            <li class="add-to-cart"><a href="/product/details/{{$product->slug}}"><i class="icon-basket-loaded"></i> Add To Cart</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="col-md-4 col-6">
                            <h1 style="color: #0b3e6f">Sorry! No products found for this filter range.</h1>
                        </div> 
                    @endif
                </div>
                <div class="row">
                    <div class="col-12 pagination">
                        {{ $products->links() }}

                    </div>
                </div>
            </div>
            <div class="col-lg-3 order-lg-first mt-4 pt-2 mt-lg-0 pt-lg-0">
                <div class="sidebar">
                    <div class="widget" data-filter-type="f">
                        <h5 class="widget_title">For</h5> 
                        <ul class="list_brand checkList">
                            <li>
                                <div class="custome-checkbox">
                                    <input class="for form-check-input" type="checkbox" name="select" value="m" data-name="Men" id="f_m">
                                    <label class="form-check-label" for="f_m"><span>Men</span></label>
                                </div>
                            </li>
                            <li>
                                <div class="custome-checkbox">
                                    <input class="for form-check-input" type="checkbox" name="select" value="w" data-name="Women" id="f_w">
                                    <label class="form-check-label" for="f_w"><span>Women</span></label>
                                </div>
                            </li>
                            <li>
                                <div class="custome-checkbox">
                                    <input class="for form-check-input" type="checkbox" name="select" value="g" data-name="Girls" id="f_g">
                                    <label class="form-check-label" for="f_g"><span>Girls</span></label>
                                </div>
                            </li>
                            <li>
                                <div class="custome-checkbox">
                                    <input class="for form-check-input" type="checkbox" name="select" value="b" data-name="Boys" id="f_b">
                                    <label class="form-check-label" for="f_b"><span>Boys</span></label>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="widget" data-filter-type="p">
                        <h5 class="widget_title">Price</h5>
                        <ul class="list_brand checkList">
                            <?php
                                $slot = ($productsAvg-$productsMin)/3;
                                $aboveAgvSlot = ($productsMax-$productsAvg)/2;
                            ?> 
                            @if($productsMax>0)
                            <li>
                                <div class="custome-checkbox">
                                    <input class="form-check-input filter-field" type="checkbox" name="select" id="p_1000-2999" value="{{round($productsMin)}}-{{round($productsMin+($slot*1))}}">
                                    <label class="form-check-label" for="p_1000-2999"><span>{{number_format(round($productsMin),2)}}-{{number_format(round($productsMin+($slot*1)),2)}}</span></label>
                                </div>
                            </li>
                            <li>
                                <div class="custome-checkbox">
                                    <input class="form-check-input filter-field" type="checkbox" name="select" id="p_3000-6999" value="{{round($productsMin+($slot*1))}}-{{round($productsMin+($slot*2))}}">
                                    <label class="form-check-label" for="p_3000-6999"><span>{{number_format(round($productsMin+($slot*1)),2)}}-{{number_format(round($productsMin+($slot*2)),2)}}</span></label>
                                </div>
                            </li>
                            <li>
                                <div class="custome-checkbox">
                                    <input class="form-check-input filter-field" type="checkbox" name="select" id="p_7000-9999" value="{{round($productsMin+($slot*2))}}-{{round($productsMin+($slot*3))}}">
                                    <label class="form-check-label" for="p_7000-9999"><span>{{number_format(round($productsMin+($slot*2)),2)}}-{{number_format(round($productsMin+($slot*3)),2)}}</span></label>
                                </div>
                            </li>
                            <li>
                                <div class="custome-checkbox">
                                    <input class="form-check-input filter-field" type="checkbox" name="select" id="p_10000-14999" value="{{round($productsMin+($slot*3))}}-{{round($productsMin+($slot*3)+$aboveAgvSlot)}}">
                                    <label class="form-check-label" for="p_10000-14999"><span>{{number_format(round($productsMin+($slot*3)),2)}}-{{number_format(round($productsMin+($slot*3)+$aboveAgvSlot),2)}}</span></label>
                                </div>
                            </li>
                            <li>
                                <div class="custome-checkbox">
                                    <input class="form-check-input filter-field" type="checkbox" name="select" id="p_15000-999999" value="{{round($productsMin+($slot*3)+$aboveAgvSlot)}}-{{round($productsMax)}}">
                                    <label class="form-check-label" for="p_15000-999999"><span>{{number_format(round($productsMin+($slot*3)+$aboveAgvSlot),2)}}-Above</span></label>
                                </div>
                            </li>
                            @endif
                        </ul>
                    </div>
                    <div class="widget">
                        <h5 class="widget_title">Category</h5> 
                        <ul class="list_brand checkList">
                            <li>
                                <div class="custome-checkbox">
                                    <input class="form-check-input" type="checkbox" name="select" checked disabled="true">
                                    <label class="form-check-label" for=""><span>{{$category['name']}}</span></label>
                                </div>
                            </li>
                        </ul>
                    </div>
                    @if($subCategories!=null)
                        <div class="widget"  data-filter-type="sc">
                            <h5 class="widget_title">Category List</h5> 
                            <ul class="list_brand checkList">
                                @foreach($subCategories as $subCategory)
                                <li>
                                    <div class="custome-checkbox">
                                        <input class="form-check-input categories" type="checkbox" id="sc_{{$subCategory['id']}}" name="select" value="{{$subCategory['id']}}" data-name="{{$subCategory['name']}}">
                                        <label class="form-check-label" for="sc_{{$subCategory['id']}}"><span>{{$subCategory['name']}}</span></label>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if($subSubCategories!=null)
                        <div class="widget" data-filter-type="ssc">
                            <h5 class="widget_title">Subcategory List</h5> 
                            <ul class="list_brand checkList">
                                @foreach($subSubCategories as $subSubCategory)
                                <li>
                                    <div class="custome-checkbox">
                                        <input class="form-check-input subCategories" type="checkbox" id="ssc_{{$subSubCategory['id']}}" name="select" value="{{$subSubCategory['id']}}" data-name="{{$subSubCategory['name']}}">
                                        <label class="form-check-label" for="ssc_{{$subSubCategory['id']}}"><span>{{$subSubCategory['name']}}</span></label>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if($brands!=null)
                        <div class="widget" data-filter-type="b">
                            <h5 class="widget_title">Brand List</h5>
                            <input type="text" id="searchBrand" placeholder="Enter brand" /> 
                            <ul class="list_brand checkList" id="suggesstion-box">
                                @foreach($brands as $brand)
                                <li>
                                    <div class="custome-checkbox">
                                        <input class="form-check-input filter-field-brand" type="checkbox" id="b_{{$brand['id']}}" name="select" value="{{$brand['id']}}" data-name="{{$brand['name']}}">
                                        <label class="form-check-label" for="b_{{$brand['id']}}"><span>{{$brand['name']}}</span></label>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="widget">
                        @if($side_banner_product != null)
                        <div class="shop_banner">
                            <div class="banner_img overlay_bg_20">
                                @if($side_banner_product->image!=null)
                                    <img src="{{$imagePath}}" alt="product_img1">
                                @else
                                    <img src="{{ asset('/images/no-image-available.png') }}" alt="product_img1">
                                @endif
                            </div> 
                            <div class="shop_bn_content2 text_white">
                                <h5 class="text-uppercase shop_subtitle">{{$product->name}}</h5>
                                <h3 class="text-uppercase shop_title">({{$product->offer['discount']}}% OFF)</h3>
                                <a href="#" class="btn btn-white rounded-0 btn-sm text-uppercase">Shop Now</a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END SECTION SHOP -->
{{--youtube model start here--}}
<div class="modal fade" id="youtube_video" role="dialog" tabindex='-1'>
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content youtube-modal">
            <div class="modal-header">
                <h4 class="modal-title">Watch Youtube Video</h4>
                <button type="button" id="youtube_button_close" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                {{----}}
                <iframe id="youtube111" width="100%" height="345" frameborder="0" allowfullscreen src=""></iframe>
                {{----}}
            </div>
        </div>
    </div>
</div>
{{--youtube model ends here--}}

</div>
<!-- END MAIN CONTENT -->

<script>

    var filter_ajax_url = "{{ route('filter-product-ajax') }}";

    $(document).ready(function() {



        $(document).on("click",".playI",function(e){
            var value = $(this).attr("data-vid");
            $("#youtube111").show()
            var ytpattrn=/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i
            var vid=ytpattrn.exec(value);
            var yt='https://www.youtube.com/embed/'+vid[1]
            $("#youtube111")[0].src=yt+'?autoplay=1&rel=0';
        });

        $('#youtube_button_close').click(function(){
            var frame=$("#youtube111")[0],fsrc=frame.src;
            frame.src=fsrc.replace('autoplay=1','autoplay=0')
            $('#video')[0].pause();
        });


        $(document).on("keyup", '#searchBrand', function(event) {
            var token = $('input[name=token]').val();
            var key = $(this).val();
            $.ajax({
            url: "/search-brand",
            headers: {'X-CSRF-TOKEN': token},
            data:{"key":key},
            type: "POST",
            datatype: 'JSON',
            success: function(data){
                $("#suggesstion-box").html(data);
                // $("#searchBrand").css("background","#FFF");
            }
            });
        });

        // var clickTimer;
        // $('.static-right-content > div').on('touchstart',function() {
        //     clearTimeout(clickTimer);
        //     $(".mob-menu").removeClass("show");
        //     $(".mob-menu").addClass("hide");
        //     $(".nav").addClass('hide');
        //     $(".nav").removeClass("show");

        //     $(this).addClass('tray').siblings().removeClass('tray');
        //     clickTimer=setTimeout(function(){$('.static-right-content div').removeClass('tray')},7000)
        // });
        // $('body').on('touchstart',function(e) { 
        //     var _tray=$(e.target).parents('.static-right-content').length; 
        //     if ( _tray > 0 ) {
        //         return false;
        //     } 
        //     $('.static-right-content div').removeClass('tray');
        // });
        // // script for list and grid view ends here

        // $("#empty_list").hide();
        // var clickTimer;
        // $('.static-right-content > div').on('touchstart',function(){
        //     clearTimeout(clickTimer);
        //     $(".mob-menu").removeClass("show");
        //     $(".mob-menu").addClass("hide");
        //     $(".nav").addClass('hide');
        //     $(".nav").removeClass("show");
        //     $(this).addClass('tray').siblings().removeClass('tray');
        //     clickTimer=setTimeout(function(){$('.static-right-content div').removeClass('tray')},7000)
        // });
        // $('body').on('touchstart',function(e){var _tray=$(e.target).parents('.static-right-content').length; if(_tray>0){return false}$('.static-right-content div').removeClass('tray')});

    });

</script>
<script src="{{asset('js/filters.js')}}" type="text/javascript" language="javascript"></script>
@endsection
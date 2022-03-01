@extends('layouts.home')
@section('content')
<style type="text/css">
    .pr_flash {
        background-color: transparent;
        padding: 0;
    }
    .pr_flash > img {
        width: 30% !important;
        height: auto;
    }

    .hover_effect1 {
        position: relative;
        text-align: center;
    }
    .img_over_txt {
        width: 80%;
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
    }   

    .img_over_txt  > h5 { 
        color: white;
        font-weight: bold;
    } 
    .smallName > span {
        color: white !important;
        font-size: 15px;
        font-weight: bold;
    }
</style>

<!-- START SECTION BANNER -->
<div class="banner_section full_screen staggered-animation-wrap">
    <div id="carouselExampleControls" class="carousel slide carousel-fade light_arrow carousel_style2" data-ride="carousel">
        <div class="carousel-inner">
        <?php $ds = DIRECTORY_SEPARATOR;?>
            @if(count($bannerData)>0)
                <?php $i = 1; ?>
                @foreach($bannerData as $banner)    
                    @if(file_exists(public_path().$ds."uploads"."$ds"."banners"."$ds"."1280x404"."$ds"."$banner->banner_images"))
                            @if($i == 1) 
                            <?php $i++; ?>
                                <div class="carousel-item active background_bg overlay_bg_50" data-img-src="'{{ asset('uploads/banners/1280x404').'/'.$banner->banner_images}}'">
                            @else
                                <div class="carousel-item background_bg overlay_bg_50" data-img-src="'{{ asset('uploads/banners/1280x404').'/'.$banner->banner_images}}'">
                            @endif
                                    <div class="banner_slide_content banner_content_inner">
                                    	<div class="container">
                                        	<div class="row justify-content-center">
                                                <div class="col-lg-7 col-md-10">
                                                    <div class="banner_content text-center">
                                                        <p class="text_white staggered-animation" data-animation="fadeInUp" data-animation-delay="0.4s" style="color: #fff;">{{$banner->short_text}}</p>
                                                        <h2 class="text_white staggered-animation" data-animation="fadeInDown" data-animation-delay="0.3s" style="color: #fff;">{{$banner->banner_heading}}</h2>
                                                        <a class="btn btn-white staggered-animation" href="{{$banner->banner_url}}" data-animation="fadeInUp" data-animation-delay="0.5s">Shop Now</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                    @endif
                @endforeach
            @endif
        </div>
        <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev"><i class="ion-chevron-left"></i></a>
        <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next"><i class="ion-chevron-right"></i></a>
    </div>
</div>
<!-- END SECTION BANNER -->

<!-- START SECTION BANNER --> 
<div class="section pb_20 small_pt">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="heading_s4 text-center">
                    <h2>Top Categories</h2>
                </div>
                <p class="text-center leads">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus blandit massa enim Nullam nunc varius.</p>
            </div>
        </div>
        <div class="row">
            @foreach($topCategories as $topCategory)
            <div class="col-lg-4 col-md-6">
                <div class="sale-banner mb-3 mb-md-4">
                    <a class="hover_effect1" href="/category/{{$topCategory['slug']}}?page=1">
                        <img src="/uploads/categories/426x210/{{$topCategory['image']}}" alt="{{$topCategory['name']}}" style="border-radius: 15px;">
                        <div class="img_over_txt">
                            <h5>{{strtoupper($topCategory['name'])}}</h5>
                            <div class="smallName"><span>{{strtoupper($topCategory['short_description'])}}</span></div>
                        </div>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
<!-- END SECTION BANNER -->


<!-- START SECTION SHOP -->
<div class="section small_pt">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="heading_s1 text-center">
                    <h2>FEATURED PRODUCTS</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="product_slider carousel_slider owl-carousel owl-theme dot_style1" data-loop="true" data-margin="20" data-responsive='{"0":{"items": "1"}, "481":{"items": "2"}, "768":{"items": "3"}, "991":{"items": "4"}}'>
                    @foreach($featuredProduct as $key=>$product)
                    <div class="item">
                        <div class="product_wrap">
                            @if($product->icon!=null)
                            <span class="pr_flash"><img src="/uploads/product_icon/{{$product->id}}/{{$product->icon}}"></span>
                            @endif
                            <div class="product_img">
                                <a href="/product/details/{{$product['slug']}}">
                                    @if($product->image!=null)
                                    <img src="/uploads/products/images/{{$product->id}}/250x250/{{$product->image}}" alt="product">
                                    <img class="product_hover_img" src="/uploads/products/images/{{$product->id}}/250x250/{{$product->image}}" alt="el_hover_img2">
                                    @else
                                    <img src="/images/no-image-available.png" alt="product">
                                    <img class="product_hover_img" src="/images/no-image-available.png" alt="el_hover_img2">
                                    @endif
                                </a>
                            </div>
                            <a href="/product/details/{{$product['slug']}}">
                                <div class="product_info">
                                    <h6 class="product_title" style="text-align: center; font-size: 14px;">{{$product['name']}}</h6>
                                    <div class="product_price">
                                        <div style="text-align:center;">
                                        <span class="price">&#x20b9 {{number_format($product['price'] - $product['discount_price'],2)}}</span>
                                        @if($product['price']!=($product['price'] - $product['discount_price']))
                                            <del>&#x20b9 {{number_format($product['price'],2)}}</del>
                                        @endif
                                        </div>
                                        <div class="on_sale">
                                            @if($product->offer!=null)
                                                <h6 style="text-align:center;" >{{$product->offer['name']}}</h6>
                                                <div style="text-align: center;"><span style="text-align: center">({{$product->offer['discount']}}% OFF)</span></div>
                                                <br />
                                                <h6 style="font-size: 14px; text-align: center; color: #444;">*Any color</h6>
                                            @else 
                                                <h6 >&nbsp;</h6>
                                                <p>&nbsp;</p>
                                                <br />
                                                <h6 >&nbsp;</h6>
                                            @endif
                                        </div>
                                        @if($product['video_url']!=null)
                                                <a class="playI" data-vid="{{$product['video_url']}}" id="youtube"  data-toggle="modal" data-target="#youtube_video" data-keyboard="true" href="#">
                                                <img src="{{ asset('/images/you_tube.png')}}" style="margin-left: auto; margin-right: auto; width: 30px;">
                                                </a>
                                        @else 
                                                <div style="margin-top: 30px;"></div>
                                        @endif
                                    </div>
                                    <div class="rating_wrap" style="text-align: center;">
                                        <div class="rating">
                                            <div class="product_rate" style="width:100%"></div>
                                        </div>
                                        <!-- <span class="rating_num">(+5)</span> -->
                                    </div>
                                </div>
                            </a>
                        </div>   
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END SECTION SHOP -->
@endsection


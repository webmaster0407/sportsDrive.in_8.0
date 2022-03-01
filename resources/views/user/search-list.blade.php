@extends('layouts.category-page')
@section('content')
    {{--page content starts here--}}
    <?php
    if(!is_array($allProductIdsData)) $allProductIdsData = $allProductIdsData->toArray();?>
    <input type="hidden" name="filter_url" id="filter_url" value="{{str_replace('%2C', ',',url()->full())}}">
    <input type="hidden" name="product_ids" id="product_ids" value="{{implode(',', $allProductIdsData)}}">
    <input type="hidden" name="token" id="token" value="{{csrf_token()}}">

<!-- START SECTION BREADCRUMB -->
<div class="breadcrumb_section bg_gray page-title-mini">
    <div class="container"><!-- STRART CONTAINER -->
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-title">
                    <h1>Search Result</h1>
                </div>
            </div>
            <div class="col-md-6">
                <ol class="breadcrumb justify-content-md-end">
                    <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
                    <li class="breadcrumb-item active">Search</li>
                </ol>
            </div>
        </div>
    </div><!-- END CONTAINER-->
</div>
<!-- END SECTION BREADCRUMB -->


<!-- START MAIN CONTENT -->
<div class="main_content">


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
<!--                                             <li><a href="javascript:" class="popup-ajax"><i class="icon-shuffle"></i></a></li>
                                            <li><a href="javascript:" class="popup-ajax"><i class="icon-magnifier-add"></i></a></li>
                                            <li><a href="javascript:"><i class="icon-heart"></i></a></li> -->
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
       <!--                              <div class="pr_switch_wrap">
                                        <div class="product_color_switch">
                                            <span class="active" data-color="#87554B"></span>
                                            <span data-color="#333333"></span>
                                            <span data-color="#DA323F"></span>
                                        </div>
                                    </div> -->
                                    <div class="list_product_action_box">
                                        <ul class="list_none pr_action_btn">
                                            <li class="add-to-cart"><a href="/product/details/{{$product->slug}}"><i class="icon-basket-loaded"></i> Add To Cart</a></li>
         <!--                                    <li><a href="shop-compare.html" class="popup-ajax"><i class="icon-shuffle"></i></a></li>
                                            <li><a href="shop-quick-view.html" class="popup-ajax"><i class="icon-magnifier-add"></i></a></li>
                                            <li><a href="#"><i class="icon-heart"></i></a></li> -->
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
                    @if($mainCategories!=null)
                        @foreach($mainCategories as $mainKey=>$mainCategory)
                            @if(count($mainCategory['subCategories'])>0)
                            <div class="widget filter filter-item" data-filter-type="c">
                                <h5 class="widget_title filter-heading">
                                    <a data-toggle="collapse" href="javascript:void(0)" aria-expanded="" data-id="collapse{{$mainKey}}"  class="collapsed">{{$mainCategory['name']}}
                                    </a>
                                </h5>
                                @if($mainCategory['subCategories']!=null) 
                                        <ul class="list_brand checkList category">
                                            @foreach($mainCategory['subCategories'] as $mainKey=>$subCategory)
                                                <li>
                                                    <div class="custome-checkbox">
                                                        <input class="categories form-check-input" type="checkbox" name="select" value="{{$subCategory['id']}}" data-name="{{$subCategory['name']}}" id="{{$subCategory['id']}}"   <?php if(in_array($subCategory['id'],$selectedCategories)) echo "checked";?> >
                                                        <label class="form-check-label" for="{{$subCategory['id']}}">
                                                            <span>{{$subCategory['name']}}</span>
                                                        </label>
                                                        @if($subCategory['subSubCategories']!=null)
                                                            <ul class="list_brand checkList subCategory">
                                                                @foreach($subCategory['subSubCategories'] as $subSubCategories)
                                                                    <li>
                                                                        <div class="custome-checkbox">
                                                                            <input class="categories form-check-input" type="checkbox" name="select" value="{{$subSubCategories['id']}}" data-name="{{$subSubCategories['name']}}" id="{{$subSubCategories['id']}}" <?php if(in_array($subSubCategories['id'],$selectedCategories)) echo "checked";?> >
                                                                            <label class="form-check-label" for="{{$subSubCategories['id']}}"><span>{{$subSubCategories['name']}}</span></label>
                                                                        </div>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                  
                                @endif
                            </div>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>



</div>
<!-- END MAIN CONTENT -->





<script src="{{asset("js/search-filter.js")}}" type="text/javascript" language="javascript"></script>
@endsection
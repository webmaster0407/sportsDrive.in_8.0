@extends('layouts.product-page')
@push('stylesheets1')
   
@endpush
@section('content')




<!-- START SECTION BREADCRUMB -->
<div class="breadcrumb_section bg_gray page-title-mini">
    <div class="container"><!-- STRART CONTAINER -->
        <div class="row align-items-center">
        	<div class="col-md-6">
                <div class="page-title">
            		<h1>Product Detail</h1>
                </div>
            </div>
            <div class="col-md-6">
                <ol class="breadcrumb justify-content-md-end">
                    <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
                    <li class="breadcrumb-item active">Product Detail</li>
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
	@if(Session::has('error'))
		<div class="alert alert-danger" id="errorMessage">
			{{Session::get('error')}}
		</div>
	@endif
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
	<input type="hidden" name="pid" id="pid" value="{{$product->id}}">
	
	<div class="container">
		<div class="row">
            <div class="col-lg-6 col-md-6 mb-4 mb-md-0">
              <div class="product-image">
                    <div class="product_img_box">
                    	@if(count($productConfiguration) >0)
                    		@foreach($productConfiguration as $pConfig)
                    			@if($pConfig->config_img != null)
		                        <img id="product_img" src="{{URL::asset('uploads/products/images/'.$pConfig->product_id.'/1024x1024/'.$pConfig->config_img)}}" data-zoom-image="{{URL::asset('uploads/products/images/'.$pConfig->product_id.'/1024x1024/'.$pConfig->config_img)}}" alt="product_img1" />
		                        <a href="#" class="product_img_zoom" title="Zoom">
		                            <span class="linearicons-zoom-in"></span>
		                        </a>
                            	@endif
                            @endforeach
                        @endif
                    </div>
                    <div id="pr_item_gallery" class="product_gallery_item slick_slider" data-slides-to-show="4" data-slides-to-scroll="1" data-infinite="false">
					
                    <?php $i = 0; ?>
					@if(count($productConfiguration) >0)
						@foreach($productConfiguration as $pConfig)
							@if($pConfig->config_img != null)
	                        <div class="item">
	                        	@if( $i == 1 )
	                            <a href="#" class="product_gallery_item active" data-image="{{$pConfig->config_img}}" data-zoom-image="{{URL::asset('uploads/products/images/'.$pConfig->product_id.'/1024x1024/'.$pConfig->config_img)}}" data-config="{{$pConfig->color_id}}" data-val="{{$pConfig->config_img}}">
	                            @else 
	                            <a href="#" class="product_gallery_item" data-image="{{$pConfig->config_img}}" data-zoom-image="{{URL::asset('uploads/products/images/'.$pConfig->product_id.'/1024x1024/'.$pConfig->config_img)}}" data-config="{{$pConfig->color_id}}" data-val="{{$pConfig->config_img}}">
	                            @endif
	                                <img src="{{URL::asset('uploads/products/images/'.$pConfig->product_id.'/80x85/'.$pConfig->config_img)}}" alt="product_small_img1" />
	                            </a>
	                        </div>
							@endif
						@endforeach
					@endif

                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
				@if($product->icon!=null)
				<span class="pr_flash"><img src="/uploads/product_icon/{{$product->id}}/{{$product->icon}}"></span>
				@endif
                <div class="pr_detail">
                    <div class="product_description">
                        <h4 class="product_title">{{$product->name}}</h4>
	                    <hr />
	                    <ul class="product-meta">
	                        <li>SKU: {{$product->sku}}</li>
	                        <li>Brand: {{$brandName->name}} </li>
	                    </ul>
                        <div class="product_price">
                            <span class="price"> ₹ {{number_format($finalprice,2)}}</span>
                            @if($product->price!=$finalprice)
                            	<del> ₹ {{number_format($product->price,2)}} </del>
                            @endif
                            <div class="on_sale">
                            	<?php  
                            		$offRate = ($product->price - $finalprice) * 100 / $product->price;
                            	?>
                                <span>{{ $offRate }}% Off</span>
                            </div>
                        </div>
                        <?php 
                        	$ratingAvg = $ratingAvg * 100 / 5;
                        ?>
                        <div class="rating_wrap">
                            <div class="rating">
                            	@if($totalRatings>0)
                            	<?php echo '<div class="product_rate" style="width:'.$ratingAvg.'%;"></div>' ?>
                            	@else
                            	<div class="product_rate" style="width:0%;"></div>
                            	@endif
                                <!-- <div class="product_rate" style="width: $ratingAvg %"></div> -->
                            </div>
                            <span class="rating_num">({{$totalRatings}} reviews)</span>
							@if($product->video_url!=null)
								<div class="you_tube"><img src="/images/you_tube.png" alt="you tube">
									<a id="youtube"  data-toggle="modal" data-target="#youtube_video" data-keyboard="true" href="#">Click to watch product video</a>
								</div>
							@endif
                        </div>

                        <div class="pr_desc">
                            <p>{!! nl2br(e($product->short_description)) !!}</p>
                        </div>
                        <div class="product_sort_info">
                            <ul>
                                <li><i class="linearicons-shield-check"></i> 1 Year Warranty</li>
                                <li><i class="linearicons-sync"></i> 30 Day Return Policy</li>
                                <li><i class="linearicons-bag-dollar"></i> Cash on Delivery available</li>
                            </ul>
                        </div>
                        <div class="pr_switch_wrap">
                            <span class="switch_lable">Color</span>
                            <div class="product_color_switch">
                                <span class="active" data-color="#87554B"></span>
                                <span data-color="#333333"></span>
                                <span data-color="#DA323F"></span>
                            </div>
                        </div>
                        <div class="pr_switch_wrap">
                        	@if(count($getattributesSize)>=1)
                        	<input type="hidden" name="selectedSize" id="selectedSize" value="@if(count($getattributesSize)== 1) {{$getattributesSize[0]->AttributeSize}} @endif">
                            <span class="switch_lable">Size</span>
                            <div class="product_size_switch">
                            	@foreach($getattributesSize as $size)
                            	<span class="sizeselect @if($size->quantity >0)@else outstockSize @endif" data-val="{{$size->AttributeSize}}">{{$size->name}}</span>
                            	@endforeach
                            </div>
	                        @endif
                        </div>
                    </div>
                    <hr />
                    <div class="cart_extra">
                        <div class="cart-product-quantity">
                            <div class="quantity">
                                <input type="button" value="-" class="minus">
                                <input type="text" name="quantity" value="1" title="Qty" class="qty" size="4">
                                <input type="button" value="+" class="plus">
                            </div>
                        </div>
                        <div class="cart_btn">
                            <button class="btn btn-fill-out btn-addtocart" type="button"><i class="icon-basket-loaded"></i> Add to cart</button>
                            <a class="add_compare" href="#"><i class="icon-shuffle"></i></a>
                            <a class="add_wishlist" href="#"><i class="icon-heart"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
        	<div class="col-12">
            	<div class="large_divider clearfix"></div>
            </div>
        </div>
        <div class="row">
        	<div class="col-12">
            	<div class="small_divider"></div>
            	<div class="divider"></div>
                <div class="medium_divider"></div>
            </div>
        </div>
        <div class="row">
        	<div class="col-12">
            	<div class="tab-style3">
					<ul class="nav nav-tabs" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" id="Description-tab" data-toggle="tab" href="#Description" role="tab" aria-controls="Description" aria-selected="true">Description</a>
                      	</li>
                      	<li class="nav-item">
                        	<a class="nav-link" id="Additional-info-tab" data-toggle="tab" href="#Additional-info" role="tab" aria-controls="Additional-info" aria-selected="false">Specification</a>
                      	</li>
                      	<li class="nav-item">
                        	<a class="nav-link" id="Reviews-tab" data-toggle="tab" href="#Reviews" role="tab" aria-controls="Reviews" aria-selected="false">Rating & Reviews</a>
                      	</li>
                    </ul>
                	<div class="tab-content shop_info_tab">
                      	<div class="tab-pane fade show active" id="Description" role="tabpanel" aria-labelledby="Description-tab">
                        	<p>{!! $product->description !!}</p>
                      	</div>
                      	<div class="tab-pane fade" id="Additional-info" role="tabpanel" aria-labelledby="Additional-info-tab">
                        	<div>{!! $product->product_specifications !!}</div>
                      	</div>
                      	<div class="tab-pane fade" id="Reviews" role="tabpanel" aria-labelledby="Reviews-tab">
                        	<div class="comments">
                            	<h5 class="product_tab_title">Review for  <span>Blue Dress For Woman</span></h5>
								@if( count($ratingReviews) >0)
									<div class="toppaging paginationLink">
										{{ $ratingReviews->links() }}
									</div>
	                                <ul class="list_none comment_list mt-4 review-name" id="reviewList">
	                                	@foreach($ratingReviews as $review)
	                                    <li>
	                                        <div class="comment_block">
	                                            <div class="rating_wrap">
	                                                <div class="rating">
	                                                <?php 
	                                            		$review_rating = null;
	                                            		if( !empty( $review->rating ) ) 
	                                            			$review_rating = $review->rating * 100 / 5;
	                                            		else 
	                                            			$review_rating = 0;
	                                          			 echo '<div class="product_rate" style="width:'.$review_rating.'%;"></div>'; 
	                                          		?>
	                                                </div>
	                                            </div>
	                                            <p class="customer_meta">
	                                                <span class="review_author">{{$review->name}}</span>
	                                                <span class="comment-date">{{ $review->created_at }}</span>
	                                            </p>
	                                            <div class="description">
	                                                <p>{{$review->message}}</p>
	                                            </div>
	                                        </div>
	                                    </li>
	                                    @endforeach
	                                </ul>
                                @else
									<div id="empty_list">
										<p>There are no reviews yet.</p>
									</div>
								@endif 
                        	</div>
                            <div class="review_form field_form">
                                <h5>Add a review</h5>
                            	@if($flag == false && $user != null)
                            		@if ($errors->has('rating'))
										<div class="alert alert-danger">
											{{ $errors->first('rating') }}
										</div>
									@endif
									@if ($errors->has('message'))
										<div class="alert alert-danger">
											{{ $errors->first('message') }}
										</div>
									@endif
	                                <form class="row mt-3" method="POST" name="frmReview" id="review-form" action="/product/add-review/{{$product->id}}">
										<input type="hidden" name="review_url" id="review_url" value="{{\Illuminate\Support\Facades\Request::fullUrl()}}">
										<input type="hidden" name="_token" value="{{ csrf_token() }}">
	                                    <div class="form-group col-12">
	                                    	<input type="hidden" name="rating" required id="rating" value="" />
	                                        <div class="star_rating">
	                                            <span data-value="1" class="add_review_star"><i class="far fa-star"></i></span>
	                                            <span data-value="2" class="add_review_star"><i class="far fa-star"></i></span> 
	                                            <span data-value="3" class="add_review_star"><i class="far fa-star"></i></span>
	                                            <span data-value="4" class="add_review_star"><i class="far fa-star"></i></span>
	                                            <span data-value="5" class="add_review_star"><i class="far fa-star"></i></span>
	                                        </div>
	                                    </div>
	                                    <div class="form-group col-12">
	                                        <textarea required="required" placeholder="Your review *" class="form-control" name="message" rows="4"></textarea>
	                                    </div>
	                                    <div class="form-group col-md-6">
	                                        <input required="required" placeholder="Enter Name *" class="form-control" name="name" type="text" value="@if($user != null){{$user->first_name}}@endif">
	                                    </div>
	                                    <div class="form-group col-md-6">
	                                        <input required="required" placeholder="Enter Email *" class="form-control" name="email" type="email" @if($user != null)  value="{{$user->email_address}}" readonly  @endif>
	                                    </div>
	                                   
	                                    <div class="form-group col-12">
	                                        <button type="submit" class="btn btn-fill-out" name="submit" value="Submit">Submit Review</button>
	                                    </div>
	                                </form>
								@elseif($user == null)
									<p><b>Please login to add review & rating.</b></p>
								@elseif($flag == true)
									<p><b>You are already rated this Product.</b></p>
								@else
									<p></p>
								@endif
                            </div>
                      	</div>
                	</div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END SECTION SHOP -->

</div>
<!-- END MAIN CONTENT -->

<script  type="text/javascript" src="{{asset('js/product-detail-page.js')}}"></script>
@endsection

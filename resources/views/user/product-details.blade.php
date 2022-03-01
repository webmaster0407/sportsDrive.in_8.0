@extends('layouts.product-page')
@section('content')
<style type="text/css">
	@media (max-width:  700px) {
		.slick-prev {
			left: 0 !important;
		}

		.slick-next {
			right: 0 !important;
		}
	}
</style>

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
								@break
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
	                            <a href="#" class="product_gallery_item active" data-image="{{URL::asset('uploads/products/images/'.$pConfig->product_id.'/1024x1024/'.$pConfig->config_img)}}" data-zoom-image="{{URL::asset('uploads/products/images/'.$pConfig->product_id.'/1024x1024/'.$pConfig->config_img)}}" data-config="{{$pConfig->color_id}}" data-val="{{$pConfig->config_img}}">
	                            @else 
	                            <a href="#" class="product_gallery_item" data-image="{{URL::asset('uploads/products/images/'.$pConfig->product_id.'/1024x1024/'.$pConfig->config_img)}}" data-zoom-image="{{URL::asset('uploads/products/images/'.$pConfig->product_id.'/1024x1024/'.$pConfig->config_img)}}" data-config="{{$pConfig->color_id}}" data-val="{{$pConfig->config_img}}">
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
							@if($product->offer!=null)
                            <div class="on_sale">
                            	<?php  
                            		$offRate = ($product->price - $finalprice) * 100 / $product->price;
                            	?>
                                <span>{{ $offRate }}% Off</span>
                            </div>
							@endif
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
								<div class="you_tube">
									<a id="youtube"  data-toggle="modal" data-target="#youtube_video" data-keyboard="true" href="#">
										<img src="/images/you_tube.png" alt="you tube">
										Click to watch product video
									</a>
								</div>
							@endif
                        </div>
						<div style="clear:both"></div>
                        <div class="pr_desc">
                            <p>{!! nl2br(e($product->short_description)) !!}</p>
                        </div>

                        <!-- begin add to cart form -->
						<form action="/product/add-to-cart/{{$product->id}}" name="frmAddCart" id="frmAddCart" method="post">
							<input type="hidden" name="_token" value="{{ csrf_token() }}">
							@if((count($getattributesSize)>=1) || (count($getattributesColor)>=1))
							<div class="product-attributes-wrapper">
								@if(count($getattributesColor)>=1)
		                        <div class="pr_switch_wrap">
									<input type="hidden" name="selectedColor" id="selectedColor" value="@if(count($getattributesColor)== 1) {{$getattributesColor[0]->AttributeColor}} @endif">
									<ul class="size-list color">
									@if(strcasecmp($getattributesColor[0]->name ,"No Color") != 0 )
									<span class="switch_label">Color</span>
									@endif
		                            <div class="product_size_switch">
		                            	@foreach($getattributesColor as $color)
		                            		<?php if(strcasecmp($color->name ,"No Color") != 0 ) {?>
		                            		    <span class="colorselect" data-val="{{$color->AttributeColor}}">
														@if($color->colorImage != null)
															<img src="{{URL::asset('uploads/products/images/'.$color->product_id.'/80x85/'.$color->colorImage)}}
																	" width="45" height="45" align="center">
														@endif
												</span>
		                                	<?php } ?>
		                                @endforeach
		                            </div>
		                        </div>
								@endif
								@if(count($getattributesSize)>=1)
			                        <div class="pr_switch_wrap">
			                        	<input type="hidden" name="selectedSize" id="selectedSize" value="@if(count($getattributesSize)== 1) {{$getattributesSize[0]->AttributeSize}} @endif">
			                            <span class="switch_label">Size</span>
			                            <div class="product_size_switch">
			                            	@foreach($getattributesSize as $size)
			                                <span class="sizeselect @if($size->quantity >0)@else outstockSize @endif" data-val="{{$size->AttributeSize}}">{{$size->name}}</span>
			                                @endforeach
			                            </div>
			                        </div>
								@endif
								<div class="size">
									<hr style="margin-bottom: 2px;" />
									<div style="padding: 0 20px; display: flex; justify-content: space-around;">
										@if(($product->size_chart_type =="image" && $product->size_chart_image != null) ||($product->size_chart_type =="desc" && $product->size_chart_description!=null))
											<div class="size-chart">
												<a href="#" id="myBtn"  data-toggle="modal" data-target="#size_chart" data-keyboard="true">Size Chart</a>
											</div>
										@endif
										<div class="stock-Div">
											<a href="#" id="stockBtn"  data-toggle="modal" data-target="#stock_model" data-keyboard="true">Stock Availability</a>
										</div>
									</div>
									<hr style="margin-top: 2px;" />
								</div>
							</div>
							@endif

							<div class="cart-product-quantity">
	                            <div class="quantity">
	                                <input type="button" value="-" class="minus">
	                                <input type="text" class="input-number qty" size="4" title="Qty" type="text" name="quantity" value="1" data-price="{{$finalprice}}" min="1" max="{{$product->quantity}}" value="1">
	                                <input type="button" value="+" class="plus">
	                            </div>
	                        </div>	

							{{--new chnage by sagar for offers starts--}}
							@if($offer!=null)
								<input type="hidden" name="offer_discount" id="offer_discount" value="{{$offer['discount']}}">
								<input type="hidden" name="offer_quantity" id="offer_quantity" value="{{$offer['quantity']}}">
								<input type="hidden" name="total_price" id="total_price" value="{{$finalprice}}">
								<input type="hidden" name="clicked_type" id="clicked_type" value="">

								<div class="chose-second">
									<h6>{!! $offer->short_description !!}</h6>
									<span class="any">*Any color</span>
									<div class="row">
										@foreach($productOffers as $productOffer)
											<div class="col-6 product_offer_container">
												<a href="/product/details/{{$productOffer->slug}}" target="_blank">
													<img src="{{URL::asset('uploads/products/images/'.$productOffer->id.'/80x85/'.$productOffer->image)}}" alt="config-image">
												</a>
												<div class="cart-product-quantity">
						                            <div class="quantity">
						                                <input type="button" value="-" class="minus">
						                                <input type="text"  value="0" min="0" max="10"  title="Qty" class="qty input-number" size="4"  data-price="{{$productOffer['price']-$productOffer['discount_price']}}" name="otherQuantity[{{$productOffer->id}}][quantity]">
						                                <input type="button" value="+" class="plus">
						                            </div>
						                        </div>	
											</div>
									    @endforeach
									</div>
									<h6>{!! $offer->description !!}</h6>
									<ul class="total ammount" style="display: none">
										<li>
											<span>
												{{$product->sku}}
											</span>
											<span>
												1
											</span>
											<span>
												₹ {{number_format($finalprice,2)}}
											</span>
										</li>
										{{--main products sku and price--}}
										@foreach($productOffers as $productOffer)
											<li style="display: none" class="other-sku">
												<span>{{$productOffer->sku}}</span>
												<span>1</span>
												<span>&#8377 {{number_format(($productOffer->price - $productOffer->discount_price),2)}}</span>
											</li>
										@endforeach
										<li>
											<span class="off">Less-{{$offer->discount}}%</span>
											<span></span>
											<span></span>
										</li>
										<li>
											<span>Total</span>
											<span></span>
											<span>₹ {{number_format($finalprice,2)}}</span>
										</li>
									</ul>
								</div>
							@endif
							{{--new chnage by sagar for offers ends--}}

	                        <div class="cart_btn">
	                            <button class="btn btn-fill-out btn-addtocart add-button add-cart" id="addToCartBtn" type="submit"><i class="icon-basket-loaded"></i> Add to cart</button>
	                        </div>
						</form>
						<!--.end form-->
                    </div>
                    <hr />

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
                            	<h5 class="product_tab_title">Review for  <span>{!! $product->name !!}</span></h5>
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

	{{-- size_chart model start here--}}
	<div class="modal fade" id="size_chart" role="dialog" tabindex='-1'>
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content address-modal">
				<div class="modal-header">
					<h4 class="modal-title">Size Chart</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<div style="text-align: center;" class="sizechartDiv">
						@if($product->size_chart_type =="image" && $product->size_chart_image != null)
							<img src="{{ URL::asset('uploads/sizechart/'.$product->id.'/500x500/'.$product->size_chart_image)}}" alt="sizechart"  align="center">
						@elseif($product->size_chart_type =="desc" && $product->size_chart_description!=null)

							<p>{!! $product->size_chart_description !!}</p>
						@else
							<P>Not Present !!</P>
						@endif

					</div>
				</div>
			</div>
		</div>
	</div>
	{{-- size_chart model ends here--}}

	{{--change stock_model model start here--}}
	<div class="modal fade" id="stock_model" role="dialog" tabindex='-1'>
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content address-modal">
				<div class="modal-header">
					<h4 class="modal-title">Stock Availability</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<div class="stock-available" style="text-align: center; width: 100%;">
							@if(count($pConfiguration)>0)
								<table class="stock-table" cellspacing="1" cellpadding="1" style="width: 100%;">
									<thead>
									<tr>
										<th>Color</th>
										<th>Size</th>
										<th>Status</th>
									</tr>
									</thead>
									<tbody>
									@foreach($pConfiguration as $config)
										<tr>
											<td>
												@if( isset($config->AttributeColor['name'] ))
													{{$config->AttributeColor['name']}}
												@else 
													NA 
												@endif
											</td>
											<td>
												@if( isset($config->AttributeSize['name']) )
													{{$config->AttributeSize['name']}}
												@else 
													NA 
												@endif
											</td>
											<td>
												@if($config->quantity >0 )
													<span style="color:green;"> In stock </span> 
												@else <span style="color:red;">Out of stock</span> 
												@endif
											</td>
										</tr>
									@endforeach
									</tbody>
								</table>
							@else
								<table class="stock-table" cellspacing="1" cellpadding="1">
									<thead>
									<tr><th>Product Total Quantity</th></tr>
									</thead>
									<tbody>
									<tr><td>{{$product->quantity}}</td></tr>
									</tbody>
								</table>
							@endif
					</div>
				</div>
			</div>
		</div>
	</div>
	{{--change stock_model model ends here--}}

	@if($product->video_url!=null)
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
					<?php $videoData = preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $product->video_url, $match);$youtube_id = $match[1]; ?>
					<iframe id="youtube_player" width="100%" height="345" src="https://www.youtube.com/embed/{{$youtube_id}}?rel=0" allowfullscreen></iframe>
				</div>
			</div>
		</div>
	</div>
	{{--youtube model ends here--}}
	@endif

</div>
<!-- END MAIN CONTENT -->

<script  type="text/javascript" src="{{asset('js/product-detail-page.js')}}"></script>
@endsection

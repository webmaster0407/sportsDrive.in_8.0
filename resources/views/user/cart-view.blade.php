@extends('layouts.user')
@section('content')

<!-- START SECTION BREADCRUMB -->
<div class="breadcrumb_section bg_gray page-title-mini">
    <div class="container"><!-- STRART CONTAINER -->
        <div class="row align-items-center">
          <div class="col-md-6">
                <div class="page-title">
                <h1>View Cart</h1>
                </div>
            </div>
            <div class="col-md-6">
                <ol class="breadcrumb justify-content-md-end">
                    <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
                    <li class="breadcrumb-item active">View Cart</li>
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
		<?php $cartErrors = null;?>
		<?php $totalFinalPrice = 0;?>

		@if(Session::has('errorArray'))
	        <?php $errorArray = Session::get('errorArray');?>
				@if($errorArray!=null)
					<div class="alert alert-danger">
						@foreach($errorArray as $error)
								{{$error}}<br>
						@endforeach
					</div>
				@endif
				<?php session()->forget('errorArray');?>
		@endif
		<input type="hidden" name="token" id="token" value="{{csrf_token()}}">
        <div class="row">
            <div class="col-12">
            	<div class="alert alert-danger" id="errMsg" style="display:none;"></div>
            	<div class="alert alert-success" id="successMsg" style="display:none;"></div>
            	@if(isset($cartData) && !$cartData->isEmpty())
	                <div class="table-responsive shop_cart_table">
	                	<table class="table">
	                    	<thead>
	                        	<tr>
	                            	<th class="product-thumbnail">&nbsp;</th>
	                                <th class="product-name">Product</th>
	                                <th class="product-price">Price</th>
	                                <th class="product-quantity">Quantity</th>
	                                <th class="product-subtotal">Total</th>
	                                <th class="product-update">update</th>
	                                <th class="product-remove">Remove</th>
	                            </tr>
	                        </thead>
	                        <tbody>
                        		@foreach($cartData as $cart)
		                        	<tr id="{{$cart->id}}" data-val="{{$cart->id}}">
		                            	<td class="product-thumbnail">
		                            		<a href="/product/details/{{$cart->slug}}">
		                            			@if($cart->image!=null)
		                            			<img src="/uploads/products/images/{{$cart->product_id}}/80x85/{{$cart->image}}" alt="product">
		                            			@else 
		                            				@if($cart->mainImage!=null)
		                            					<img src="/uploads/products/images/{{$cart->product_id}}/80x85/{{$cart->mainImage}}" alt="product">
		                            				@else
		                            					<img src="/images/no-image-available.png" alt="product">
		                            				@endif
		                            			@endif
		                            		</a>
		                            	</td>
		                                <td class="product-name" data-title="Product">
		                                	<a href="/product/details/{{$cart->slug}}">{{$cart->name}}</a>
		                                	<div class="other-info">
												@if(count($cart->color))
													<span><small>Color : </small></span><small>{{$cart->color[0]}}</small><br />
												@endif
												@if(count($cart->size))
													<span><small>Size : </small></span><small>{{$cart->size[0]}}</small>
												@endif
		                                	</div>
		                                </td>
										@if($cart->configPrice!= null)
											<?php 
												$originalPrice = $cart->configPrice;
												$finalPrice = $cart->configPrice-$cart->discount_price;  
											?>
										@else
			                                <?php 
			                                	$originalPrice = $cart->price;
			                                	$finalPrice = $cart->price-$cart->discount_price; 
			                                ?>
										@endif
			                            <?php 
			                            	$totalFinalPrice = $totalFinalPrice + ($finalPrice * $cart->cartQuantity); 
			                            ?>
		                                <td class="product-price" data-title="Price">
		                                	@if($originalPrice!=$finalPrice)
		                                		<del>&#8377; 
		                                			<small>
		                                				<span id="{{$cart->id}}_original_price">
		                                				{{number_format($originalPrice,2)}} 
		                                				</span>
		                                			</small>
		                                		</del>
		                                		<br />
		                                	@endif
		                                	<span class="price">&#8377;  
		                                		<span id="{{$cart->id}}_final_price">
		                                			{{number_format($finalPrice,2)}} 
		                                		</span>
											</span>
		                            	</td>
		                                <td class="product-quantity" data-title="Quantity">
		                                	<div class="quantity">
		                                		<input type="button" value="-" class="minus">
		                                		<input type="text" id="{{$cart->id}}_qty" name="quantity" value="{{$cart->cartQuantity}}" title="Qty" class="qty" size="4" min="1" max="{{$cart->configQuantity}}">
		                                		<input type="button" value="+" class="plus">
		                              		</div>
		                              	</td>
		                              	<td class="product-subtotal" data-title="Total">
		                              		<span class="price" id="pricePerProduct">&#8377; 
		                              			<span id="{{$cart->id}}_total">
		                              				{{number_format($finalPrice*$cart->cartQuantity,2)}}
		                              			</span>
		                              		</span>
		                              	</td>
		                                <td class="product-update" data-title="Update">
		                                	<a href="#">
		                                		<i class="ti-check" data-cart-id="{{$cart->id}}"></i>
		                                	</a>
		                                </td>
		                                <td class="product-remove" data-title="Remove">
		                                	<a href="#">
		                                		<i class="ti-close" data-cart-id="{{$cart->id}}"></i>
		                                	</a>
		                                </td>
		                            </tr>
	                            @endforeach
	                       	</tbody>
	                        <tfoot>
	                        	<tr>
	                            	<td colspan="7" class="px-0">
	                                	<div class="row no-gutters align-items-center">

	                                    	<div class="col-lg-6 col-md-6 mb-3 mb-md-0">
	                                    		<a href="/">
	                                    			<button class="btn btn-fill-out btn-sm" type="submit">Continue shopping</button>
	                                    		</a>
	                                    	</div>
	                                        <div class="col-lg-6 col-md-6 mb-3 mb-md-0">
	                                        	<a href="/checkout/1">
	                                        		<button class="btn btn-line-fill btn-sm" type="submit">Proceed to Checkout</button>
	                                        	</a>
	                                        </div>
	                                    </div>
	                                </td>
	                            </tr>
	                        </tfoot>
	                    </table>
                	</div>
				@else
				<div class="cart-Div empty-cart">
					<img src="/images/empty-cart.png" alt="empty-cart">
					<h3>Empty cart.</h3>
					<p>Looks like you haven't made your choice yet.</p>
				</div>
				@endif
				<div class="cart-Div empty-cart" id="empty_cart_show" style="display: none;">
					<img src="/images/empty-cart.png" alt="empty-cart">
					<h3>Empty cart.</h3>
					<p>Looks like you haven't made your choice yet.</p>
				</div>

            </div>
        </div>
        <div class="row">
            <div class="col-12">
            	<div class="medium_divider"></div>
            	<div class="divider center_icon"><i class="ti-shopping-cart-full"></i></div>
            	<div class="medium_divider"></div>
            </div>
        </div>
        @if(isset($cartData) && !$cartData->isEmpty())
        <div class="row">
            <div class="col-md-12">
            	<div class="border p-3 p-md-4" id="cart-summary">
                    <div class="heading_s1 mb-3">
                        <h6>Order Summary</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td class="cart_total_label">
                                    	Subtotal (<span id="product_count">{{count($cartData)}}</span> items)
                                    </td>
                                    <td class="cart_total_amount">
                                    	<span id="subtotal">{{number_format($totalFinalPrice,2)}}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="cart_total_label">
                                    	Discount
                                    </td>
                                    <td class="cart_total_amount">
                                    	-<span id="offer_discount">{{number_format($offersPrices['finalDiscount'],2)}}
                                    	</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="cart_total_label">
                                    	Estimated Total
                                    </td>
                                    <td class="cart_total_amount">
                                    	<strong>
	                                    	<span id="estimated_total">{{number_format($offersPrices['finalDiscountedAmount'],2)}}
	                                    	</span>
	                                    </strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
<!-- END SECTION SHOP -->


<script type="text/javascript" src="/js/checkout.js"></script>
@endsection
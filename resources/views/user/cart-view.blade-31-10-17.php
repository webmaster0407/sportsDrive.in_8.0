@extends('layouts.user')

@section('content')
@if(Session::has('error'))
    <div class="alert alert-danger">
        {{Session::get('error')}}
    </div>
@endif
@if(Session::has('success'))
    <div class="alert alert-success">
        {{Session::get('success')}}
    </div>
@endif
@if(Session::has('errors'))
    <div class="alert alert-danger">
        You have some errors below.Please check
    </div>
@endif
@if(Session::has('errorMessageArray'))
	<div class="alert alert-danger">
		<?php $cartErrors = Session::get('errorMessageArray') ?>
	</div>
@else
	<?php $cartErrors = null;?>
@endif
<div class="content">
{{--<div class="breadcrums">
	<div class="container">
		<ul>
			<li><a href="#">Home / </a></li>
			<li><a href="#" class="active">Product Listing</a></li>
		</ul>
	</div>
</div>--}}
<?php $totalFinalPrice = 0;?>
<div class="listingContent">
	<div class="container">
		<div class="cart-Div" id="all_cart_data">
			<h2>Shopping Cart</h2>
			@if(count($cartData)>0)
				<ul class="check-tabs">
					<li class="active"><a href="javascript:void(0);">View Cart</a></li>
					<li><a href="/checkout/1">Checkout Information</a></li>
					<li><a href="javascript:void(0);">Review Order</a></li>
				</ul>
			<div class="cart-List">
				<div class="checkout-table">
					<input type="hidden" name="token" id="token" value="{{csrf_token()}}">
					<div class="cart-table-Div">
					<div class="row-Div">
						<div class="cell-Div"><span>Product</span></div>
						<div class="cell-Div"><span></span></div>
						<div class="cell-Div"><span>Price</span></div>
						<div class="cell-Div"><span>Quantity</span></div>
						<div class="cell-Div"><span>Total</span></div>
						<div class="cell-Div"><span>Remove</span></div>
						</div>	@foreach($cartData as $cart)
						
						<div class="row-Div" id="{{$cart->id}}">
							<!-- display config image if null display main img else NA -->
							<div class="cell-Div"><div class="imgDiv">
									@if($cart->image!=null)
										<img src="/uploads/products/configImages/{{$cart->configuration_id}}/1024x1024/{{$cart->image}}" alt="product">
									@else
										<img src="/images/no-image-available.png" alt="product">
									@endif
								</div></div>
							<div class="cell-Div"><a href="/product/details/{{$cart->slug}}">{{$cart->name}}</a>
								<div class="other-info">
									<!-- display attribute names -->
									@if(count($cart->colorId))
										<p><span>Color:</span>{{$cart->colorId[0]}}</p>
									@endif
									@if(count($cart->sizeId))
										<p><span>Size:</span>{{$cart->sizeId[0]}}</p>
									@endif
								</div>
								</div>
								<!-- calculate price after discount & display-->
							@if($cart->configPrice!= null)
								<?php $originalPrice = $cart->configPrice;$finalPrice = $cart->configPrice-$cart->discount_price;  ?>
							@else
                                <?php $originalPrice = $cart->price;$finalPrice = $cart->price-$cart->discount_price; ?>
							@endif
                            <?php $totalFinalPrice=$totalFinalPrice+($finalPrice*$cart->cartQuantity) ?>
							<div class="cell-Div"><span class="strikeSpan">&#8377; <span id="{{$cart->id}}_original_price">{{$originalPrice}} </span></span>
								<span class="price">&#8377;  <span id="{{$cart->id}}_final_price"> {{$finalPrice}} </span></span></div>
							<div class="cell-Div"><div class="quantity-select"><span class="qnty-Input"><input id="{{$cart->id}}_qty" type="text" value="{{$cart->cartQuantity}}" name="qnty" min="1" max="{{$cart->configQuantity}}"></span></div><div class="update"><a href="#"><i class="fa fa-refresh update-qty" aria-hidden="true" data-cart-id="{{$cart->id}}"></i></a></div></div>
							<!-- change thiss on update quantity -->
							<div class="cell-Div"><span class="price" id="pricePerProduct">&#8377; <span id="{{$cart->id}}_total">{{$finalPrice*$cart->cartQuantity}}</span></span></div>
							<!-- remove this from cart on click ajax -->
							<div class="cell-Div">
								<div class="remove-link"><a href="#"><i class="fa fa-times remove-from-cart" aria-hidden="true" data-cart-id="{{$cart->id}}"></i></a></div>
							</div>
							@if($cartErrors != null && array_key_exists($cart->id,$cartErrors))
							<div class="error-msg"> <span>{{$cartErrors[$cart->id]}}</span></div>
							@endif
						</div>
					

						@endforeach

				</div>
				</div>
				<div class="proceed-btn">
					<a  href="/"> <input type="button" name="proceed" value="Continue shopping" class="proceed-B"> </a>
					<a  href="/checkout/1"> <input type="button" name="proceed" value="Proceed to Checkout" class="proceed-B"> </a>
				</div>
			
			</div>
			<div class="summary-coupon">
				<div class="order-summary">
					<h3>Order Summary</h3>
					<div class="order-S">
						<div class="ord">
								<div class="leftN">Subtotal(<span id="product_count"> {{count($cartData)}}</span> items) </div>
								<!-- sum of pricePerProduct col here -->
								<div class="rightC"><span id="subtotal">{{$totalFinalPrice}}</span></div>
						</div>
						{{--<div class="ord">
							<div class="leftN">Ship to: 411052<a href="#" class="remove-code"><i class="fa fa-times" aria-hidden="true"></i></a></div>
								
						</div>--}}
						{{--<div class="ord">
								<div class="leftN">
									<div class="pin-code">
										<h4>Estimate Tax & Shipping</h4>
										<p>Ship available only to India</p>
										<h4>Ship to the following postal code</h4>
										<input type="text" name="pin-code" class="pin-Input">
										<input type="button" name="estimate" value="Estimate" class="estimate-B">
									</div>
							</div>
						</div>--}}
						{{--<div class="ord">
							<div class="leftN">Shipping</div>
							<div class="rightC"><span>{{$shipping_charge}}</span></div>
						</div>--}}
						{{--<div class="ord">
							<div class="leftN">Tax</div>
							<div class="rightC"><span>Rs.00</span></div>
						</div>---}}
						<hr>
						<div class="ord">
							<div class="leftN">Estimated Total</div>
							<div class="rightC"><span id="estimated_total">{{$totalFinalPrice}}</span></div>
						</div>
							</div>
					
				</div>
				{{--<div class="coupon-div">
					<h3>COUPON DISCOUNT</h3>
					<form class="at_coupon_form">
						<input type="text" name="coupon_code" class="input-text" value="" placeholder="Enter your coupon code">
						<input type="button" class="sub_button" name="apply_coupon" value="Apply Coupon">
				  </form>
				</div>--}}
			</div>
			@else
				<h2 style="text-align: center;"><i class="fa fa-shopping-cart" aria-hidden="true"></i> Your Cart is Empty.</h2>
			@endif
		</div>
		<div class="cart-Div" id="empty_cart_show" style="display: none;">
			<h2>Shopping Cart</h2>
			<h2 style="text-align: center;" ><i class="fa fa-shopping-cart" aria-hidden="true"></i> Your Cart is Empty.</h2>
		</div>
	</div>
</div>
</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript" src="/carouselengine/amazingcarousel.js"></script>
<script type="text/javascript" src="/carouselengine/initcarousel-1.js"></script>
<script type="text/javascript" src="/js/checkout.js"></script>
@endsection
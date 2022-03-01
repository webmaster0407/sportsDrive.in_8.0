@extends('layouts.user')
@section('content')

<style type="text/css">
    .ship-billing-address {
        margin-bottom: 0;
        margin-top: 5px;
    }
</style>

<!-- START SECTION BREADCRUMB -->
<div class="breadcrumb_section bg_gray page-title-mini">
    <div class="container"><!-- STRART CONTAINER -->
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-title">
                    <h1>Checkout</h1>
                </div>
            </div>
            <div class="col-md-6">
                <ol class="breadcrumb justify-content-md-end">
                    <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
                    <li class="breadcrumb-item active">Checkout - Review order</li>
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
            <form name="review_order" action="/payment/request/{{base64_encode($order->id)}}" method="post">
                <div class="row">
                    <div class="col-md-8 checkout-info">
                        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                        <input type="hidden" name="order_id" id="order_id" value="{{base64_encode($order->id)}}">
                        <input type="hidden" name="applied_code" id="hidden_code" value="">

                        <div class="order_review ship-billing-address" style="margin-bottom: 30px;">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Shipping Address</h5>
                                    <div class="exist-address">
                                        <input type="hidden" name="shipping_address_id" id="shipping_address_id" value="{{$defaultShippingAddress['id']}}">
                                        <p><strong>{{$defaultShippingAddress['address_title']}}</strong></p>
                                        <p><strong>{{$defaultShippingAddress['full_name']}}</strong></p>
                                        <p>{{$defaultShippingAddress['address_line_1']}}</p>
                                        <p>{{$defaultShippingAddress['address_line_2']}} {{$defaultShippingAddress['city']}}{{$defaultShippingAddress['state']}} </p>
                                        <p>{{$defaultShippingAddress['country']}}</p>
                                        <p>{{$defaultShippingAddress['pin_code']}}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h5>Billing Address</h5>
                                    <div class="exist-address">
                                        <input type="hidden" name="billing_address_id" id="billing_address_id" value="{{$defaultBillingAddress['id']}}">
                                        <p><strong>{{$defaultBillingAddress['address_title']}}</strong></p>
                                        <p><strong>{{$defaultBillingAddress['full_name']}}</strong></p>
                                        <p>{{$defaultBillingAddress['address_line_1']}}</p>
                                        <p>{{$defaultBillingAddress['address_line_2']}} {{$defaultBillingAddress['city']}}{{$defaultBillingAddress['state']}} </p>
                                        <p>{{$defaultBillingAddress['country']}}</p>
                                        <p>{{$defaultBillingAddress['pin_code']}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="order_review" style="margin-bottom: 30px;">
                            <div class="col-12 address-section">
                                <h5>Delivery Method</h5>
                                <div class="input-left">
                                    <input type="radio" name="delivery" value="express" checked disabled>
                                    <span>
                                        Express Shipping (Delivery is usually within 2-4 working days after dispatch date)
                                    </span>
                                </div>
                                <span>Rs.{{number_format($offersPrices['totalShippingCharge'])}}</span>
                            </div>
                        </div>

                        <div class="order_review" style="margin-bottom: 20px;">
                            <div class="col-12 payment">
                                <h5>Payment Method</h5>
                                <select name="payment" style="width: 100%;">
                                    @if($data['payment']=="online")
                                        <option selected value="online">Payment with NetBanking / Credit / Debit Card</option>
                                    @else
                                        <option selected value="cod">Cash on Delivery</option>
                                    @endif
                                </select>
                                <p>You will be redirected to your external payment gateway after reviewing your order on the next step. Once your order is placed, you will return to our store to see your order confirmation.</p>
                                <span>
                                    <img src="/images/visa.png" alt="visa">
                                </span>&nbsp;&nbsp;
                                <span>
                                    <img src="/images/master_card.png" alt="master">
                                </span>
                            </div>
                        </div>

                        <div class="proceed-btn">
                            @if($data['payment']=="online")
                                <p>You will be redirected to a secure site to confirm your payment.</p>
                                <a href="/checkout/1">
                                    <button type="button" class="proceed-B btn btn-fill-out btn-sm">Back</button>
                                </a>
                                <button type="submit" class="proceed-B btn btn-fill-out btn-sm">Proceed to Payment Gateway</button>
                            @else
                                <a href="/checkout/1">
                                    <button type="button" class="proceed-B btn btn-fill-out btn-sm">Back</button>
                                </a>
                                <button type="submit" class="proceed-B btn btn-fill-out btn-sm">Place Order</button>
                            @endif
                        </div>
                    </div>
                
                
                    <div class="col-md-4">
                        <div class="order_review order-summary">
                            <h5>Order Summary</h5>
                            <div class="order-S">
                                <div class="ord">
                                    <div class="leftN">Subtotal ({{$cartCount}}) items</div>
                                    <div class="rightC"><span>{{number_format($offersPrices['finalDiscountedAmount'],2)}}</span></div>
                                </div>
                                <div class="ord">
                                     <?php $shipping = $offersPrices['totalShippingCharge']; ?>
                                    <div class="leftN">Shipping</div>
                                    <div class="rightC"><span>{{number_format($shipping,2)}}</span></div>
                                </div>
                                @if($offersPrices['finalDiscount']>0)
                                    <div class="ord" style="color: red">
                                        <div class="leftN">Discount</div>
                                        <div class="rightC"><span id="discount">-{{number_format($offersPrices['finalDiscount'],2)}}</span></div>
                                    </div>
                                @endif

                                <div class="ord" id="additional_discount_div" style="color: red;display: none">
                                    <div class="leftN">Coupon Discount</div>
                                    <div class="rightC"><span id="additional_discount"></span></div>
                                </div>

                                <div class="ord">
                                    <div class="leftN">Estimated Total</div>
                                    <div class="rightC"><span id="estimated_total">{{number_format($offersPrices['finalDiscountedAmount']+$shipping,2)}}</span></div>
                                </div>
                            </div>
                        </div>
                        <div class="order_review summary-coupon">
                            <h5>Order Summary</h5>
                            <div class="order-S">
                                <div class="ord">
                                    <div class="leftN">Subtotal ({{$cartCount}}) items</div>
                                    <div class="rightC"><span>{{number_format($offersPrices['finalDiscountedAmount'],2)}}</span></div>
                                </div>
                                <div class="ord">
                                     <?php $shipping = $offersPrices['totalShippingCharge']; ?>
                                    <div class="leftN">Shipping</div>
                                    <div class="rightC"><span>{{number_format($shipping,2)}}</span></div>
                                </div>
                                @if($offersPrices['finalDiscount']>0)
                                    <div class="ord" style="color: red">
                                        <div class="leftN">Discount</div>
                                        <div class="rightC"><span id="discount">-{{number_format($offersPrices['finalDiscount'],2)}}</span></div>
                                    </div>
                                @endif

                                <div class="ord" id="additional_discount_div" style="color: red;display: none">
                                    <div class="leftN">Coupon Discount</div>
                                    <div class="rightC"><span id="additional_discount"></span></div>
                                </div>

                                <div class="ord">
                                    <div class="leftN">Estimated Total</div>
                                    <div class="rightC"><span id="estimated_total">{{number_format($offersPrices['finalDiscountedAmount']+$shipping,2)}}</span></div>
                                </div>
                            </div>
                        </div>

                        <div class="order_review add-coupon">
                            <h5>Add Coupon Code<small style="color: red">{{" (Case Sensitive)"}}</small></h5>
                            <div class="at_coupon_form review_order_page">
                                <div class="input_cross">
                                    <input type="text" name="code" id="coupon_code" class="input-text" value="" placeholder="Enter your coupon code" style="width: 100%;">
                                    <div class="remove-link" style="display:none;" id="remove_div">
                                        <a href="#">
                                            <i class="fa fa-times remove-from-cart" id="remove_coupon" aria-hidden="true" data-cart-id="4"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <span id="coupon_message"></span>
                            </div>
                            <div style="margin-top: 10px;">
                                <button type="button" class="sub_button btn btn-fill-line btn-sm" id="apply_coupon" style="width: 100%;">Apply Coupon</button>
                            </div>
                        </div>
                        <div class="proceed-btn right-summary-btn" style="text-align: right; margin-top: 20px;">
                           <button type="submit" class="proceed-B btn btn-fill-out btn-sm">Proceed to Payment Gateway</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- END MAIN CONTENT -->

<script type="text/javascript">
    $(document).ready( function() {
        $('#apply_coupon').on('click', function() {

            $("#coupon_message").hide();
            var code = $("#coupon_code").val();
            var order_id = $("#order_id").val();
            var token = $('#token').val();
            
            if ( code != "" ) {
                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    url: "/order/apply-coupon",
                    data: {
                        "code": code,
                        "order_id":order_id,
                        "_token":token
                    },
                    success: function ( data ) {
                        if ( data['additionalDiscount'] > 0 ) {
                            $("#remove_div").show();
                            $("#apply_coupon").hide();
                            $("#additional_discount_div").show();
                            $("#additional_discount").text("-" + data['additionalDiscount'].toFixed(2));
                            $("#estimated_total").text(data['finalDiscountAmount'].toFixed(2));
                            $( "#coupon_code" ).prop( "disabled", true ); //Disable
                            
                            if ( data['status'] == 200) {
                                $("#coupon_message").show();
                                var msgHtml = "<strong style='color:green'>" + data['message'] + "</strong>"
                                $("#coupon_message").html(msgHtml);
                                $("#hidden_code").val(code);
                            } else {
                                $("#coupon_message").show();
                                var msgHtml = "<strong style='color:red'>" + data['message'] + "</strong>"
                                $("#coupon_message").html(msgHtml);
                            }
                        } else {
                            if ( data['status'] == 200) {
                                $("#coupon_message").show();
                                var msgHtml = "<strong style='color:red'>Sorry! The Coupon(" + code + ") is not applicable to your cart items.</strong>";
                                $("#coupon_message").html(msgHtml);
                            } else {
                                $("#coupon_message").show();
                                var msgHtml = "<strong style='color:red'>" + data['message'] + "</strong>";
                                $("#coupon_message").html(msgHtml);
                            }
                        }
                    }
                });
            } else {
                var message = "Please enter valid coupon code.";
            }
        });
    });
</script>

@endsection
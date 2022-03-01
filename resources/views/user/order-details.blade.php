@extends('layouts.user')
@section('content')

<style type="text/css">
    .order-date {
        margin-top: 20px;
    }
    .order-date p {
        margin: 0;
        padding: 0;
    }

    .qnty-div {
        text-align: center;
        margin-top: 30px;
    }

    .product_price {
        text-align: center;
        margin-top: 30px;
    }

    .status-Div > p {
        font-size: 20px;
        font-weight: 600;
    }

    .smallNote > p {
        font-size: 12px;
    }
    .total_am p {
        display: flex;
        justify-content: space-between;
    }

    .total_am_price {
        font-weight: 600;
    } 

    .order-list {
        margin-top: 20px;
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
                    <li class="breadcrumb-item active">Checkout - Checkout Information</li>
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
            @if(Session::has('success'))
                <div class="alert alert-success">
                  {{Session::get('success')}}
                </div>
            @endif
            @if(Session::has('error'))
                <div class="alert alert-danger">
                  {{Session::get('error')}}
                </div>
            @endif
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
            <div class="row">
                <div class="col-lg-10 col-md-10 col-sm-12 order_review" style="margin: auto">
                    <div class="order-invoice">
                        <h4 class="idTag">Order ID {{$order['userShownOrderId']}}</h4>
                        <h4 class="idTag"> 
                            @if($trackingResponse != NULL  && isset($trackingResponse['tracking_data']['track_url'])) 
                            <button type="button" class="btn btn-fill-out btn-sm" data-toggle="modal" data-target="#trackOrder">    Track Order
                            </button>
                             @endif
                         </h4>
                         <div class="request-invoice">
                            {{--<a href="#">
                                <svg fill="#c2c2c2" height="24" viewBox="0 0 24 24" width="24" class="invoice_sv"><path d="M0 0h24v24H0z" fill="none"></path><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"></path></svg><span>Request Invoice</span>
                            </a>--}}
                        </div>
                    </div>
                    <hr />     
                    <h5>Customer Details</h5>
                    <div class="order-detail-section">
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td>Customer Name:</td>
                                        <td>{{$user->first_name}} {{$user->last_name}}</td>
                                    </tr>
                                    <tr>
                                        <td>Contact No:</td>
                                        <td>{{$user->phone}}</td>
                                    </tr>
                                    <tr>
                                        <td>Email Id:</td>
                                        <td>{{$user->email_address}}</td>
                                    </tr>
                                    <tr>
                                        <td>Order Date:</td>
                                        <td>{{date("d M Y h:i:s A",strtotime($order->order_date))}}</td>
                                    </tr>
                                    <tr>
                                        <td>Total items in Cart:</td>
                                        <td>({{count($carts)}})</td>
                                    </tr>
                                    <tr>
                                        <td>Payment Id:</td>
                                        <td>@if($order->payu_payment_id != null){{$order->payu_payment_id}}@else NA @endif</td>
                                    </tr>
                                    <tr>
                                        <td>Bank Reference NO:</td>
                                        <td>@if($order->payu_bank_ref_num!= null){{$order->payu_bank_ref_num}}@else NA @endif</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <?php
                        $defaultShippingAddress = json_decode($order->shipping_address,true);
                        $defaultBillingAddress = json_decode($order->billing_address,true)
                        ?>
                        <div class="row">
                            <div class="col-md-6 table-responsive">
                                <h5>Shipping Address</h5>
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td><strong>{{$defaultShippingAddress['address_title']}}</strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{$defaultShippingAddress['full_name']}}</strong></td>
                                        </tr>
                                        <tr>
                                            <td>{{$defaultShippingAddress['address_line_1']}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{$defaultShippingAddress['address_line_2']}} {{$defaultShippingAddress['city']}} {{$defaultShippingAddress['state']}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{$defaultShippingAddress['country']}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{$defaultShippingAddress['pin_code']}}</td>
                                        </tr>
                                        <tr>
                                            <td>Bank Reference NO:</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6 table-responsive">
                                <h5>Billing Address</h5>
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td><strong>{{$defaultBillingAddress['address_title']}}</strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{$defaultBillingAddress['full_name']}}</strong></td>
                                        </tr>
                                        <tr>
                                            <td>{{$defaultBillingAddress['address_line_1']}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{$defaultBillingAddress['address_line_2']}} {{$defaultBillingAddress['city']}} {{$defaultBillingAddress['state']}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{$defaultBillingAddress['country']}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{$defaultBillingAddress['pin_code']}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="order-list row">
                        <div class="col-12">
                            <h5><span>Ordered On</span> {{date("d M Y h:i:s A",strtotime($order->order_date))}}</h5>
                            <hr />
                        </div>
                        <div class="col-md-12">
                            @foreach($carts as $cart)
                                <div class="order_details row">
                                    <div class="col-md-2 product_img_g">
                                        <a href="#">
                                            @if($cart->configuration_image != null)
                                                <img src="/uploads/products/images/{{$cart->product_id}}/80x85/{{$cart->configuration_image}}" alt="Image">
                                            @elseif($cart->image!= null)
                                                <img src="/uploads/products/images/{{$cart->product_id}}/80x85/{{$cart->image}}" alt="Image">
                                            @else
                                                <img src="/images/no-image-available.png" alt="Image">
                                            @endif
                                        </a>
                                    </div>
                                    <div class="col-md-5 product_info">
                                        <h6 class="product_title">
                                            <a href="/product/details/{{$cart->product_slug}}">{{$cart->product_name}}</a>
                                        </h6>
                                        <div class="other-info">
                                            @if($cart->color!=null)
                                                <span style="font-style: italic;"><small>Color :  {{$cart->color}}&nbsp;&nbsp; </small></span>
                                                <br>
                                            @endif
                                            @if($cart->size!=null)
                                                <span style="font-style: italic;"><small>Size :  {{$cart->size}}</small></span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-2 col-xs-6 qnty-div">
                                        <span>{{$cart->quantity}}</span>
                                    </div>
                                    <div class="col-md-3 col-sm-3 col-xs-6 product_price">
                                        <span>₹{{number_format($cart->final_price,2)}}</span>
                                        @if(count($order['status'])>0 && $order['status'][0] == $deliveredStatus[0] && empty($reviews[$cart->product_id]))
                                            <a href="#" class="rate-link" data-toggle="modal" data-target="#myModal">Rate This Products</a>
                                        @endif
                                        @if(!empty($reviews[$cart->product_id]))
                                        <div>
                                            <p>My Ratings</p>
                                            <div class="rating">
                                                <ul class="star-rating-name">
                                                <?php
                                                $i=1;
                                                for($i=1;$i<=5;$i++) {
                                                $selected = "";
                                                if(!empty($reviews[$cart->product_id]) && $i<=$reviews[$cart->product_id]) {
                                                    $selected = "selected";
                                                }
                                                ?>
                                                <li class="<?php echo $selected; ?>" >&#9733;</li>
                                                <?php }  ?>
                                                </ul>

                                                <div id="myModel" class="modal fade rate_product" role="dialog" tabindex="-1">
                                                    <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                <h4 class="modal-title">Rate this product</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form  method="POST" class="rate-review" name="frmReview" id="review-form" action="/product/add-review/{{$cart->id}}" >
                                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                                    <div class="rate-row">
                                                                        <label>Customer Name</label>
                                                                        <input type="text" name="name"  required value="@if($user != null){{$user->first_name}}@endif">
                                                                    </div>
                                                                    <div class="rate-row">
                                                                        <label>Email Id</label>
                                                                        <input type="email" required name="email" @if($user != null)  value="{{$user->email_address}}" readonly  @endif>
                                                                    </div>
                                                                    <div class="rate-row">
                                                                        <label>Your Rating</label>
                                                                        <div class="page-wrap">
                                                                            <div class="rating" id="enterRating">
                                                                                <input type="hidden" name="rating" required id="rating" value="" />
                                                                                <ul  onmouseout="resetRating();">
                                                                                    <?php
                                                                                    for($i=1;$i<=5;$i++) {
                                                                                    $selected = "";
                                                                                    ?>
                                                                                    <li class="<?php echo $selected; ?>" onmouseover="highlightStar(this);" onmouseout="removeHighlight();" onClick="addRating(this);" >&#9733;</li>
                                                                                    <?php }  ?>
                                                                                    </ul>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="rate-row">
                                                                        <label>Message</label>
                                                                        <textarea name="message"></textarea>
                                                                    </div>

                                                                    <div class="rate-row btn-row">
                                                                        <input type="submit" value="Submit" name="submit" class="confirm-btn" >
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                @if($trackingResponse != NULL && isset($trackingResponse['tracking_data']['track_url']))
                                                    <div id="trackOrder" class="modal fade " role="dialog" tabindex="-1">
                                                    <div class="modal-fullscreen" >
                                                    <!-- Modal content-->
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal" style="color: black">Close</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <iframe src="{{$trackingResponse['tracking_data']['track_url']}}"></iframe>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="col-12 order-date">
                            <div class="row">
                                <div class="col-md-6 status-Div">
                                    @if($order['payment_status'] != 9  && $order['payment_status'] != 11)
                                        <p>{{"Order Pending"}}</p>
                                    @else
                                        <p>@if(count($order['status'])>0)@if($order['status'][0] == "Pending"){{"Order Received"}} @else {{$order['status'][0]}} @endif @else Not Available @endif</p>
                                    @endif
                                    <div class="smallNote">
                                        <p>@if($order['payment_status'] != 9  && $order['payment_status'] != 11){{"We have received your order but your payment is in Pending from Bank. Once we receive the payment, We will process the order."}} @else {{$order['note']}} @endif</p>
                                    </div>
                                </div>
                                <div class="col-md-6 total_am">
                                    <p>
                                        <span>Subtotal : </span> 
                                        <span class="total_am_price">₹{{number_format($order->sub_total+$order->discount+$order->coupon_discount,2)}}</span>
                                    </p>
                                    <p>
                                        <span>Shipping Charges</span> 
                                        <span class="total_am_price">₹{{number_format($order->total_shipping_amount,2)}}</span>
                                    </p>
                                    @if($order->discount>0)
                                        <p style="color: red">
                                            <span style="color: red">Offer Discount:</span>
                                            <span class="total_am_price">- ₹{{number_format($order->discount,2)}}</span>
                                        </p>
                                    @endif
                                    @if($order->coupon_discount>0)
                                        <p style="color: red">
                                            <span style="color: red">Coupon Discount({{$order->coupon_code}}):</span>
                                            <span class="total_am_price">- ₹{{ number_format($order->coupon_discount,2)}}</span>
                                        </p>
                                    @endif
                                    <hr>
                                    <p>
                                        <span>Order Total</span> 
                                        <span class="total_am_price">₹{{number_format($order->sub_total+$order->total_shipping_amount,2)}}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
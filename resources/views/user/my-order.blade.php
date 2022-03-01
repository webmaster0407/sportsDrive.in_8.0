@extends('layouts.user')
<style type="text/css">
    .pagination>li>span{
        padding: 0px !important;
    }

    .order-btn {
        margin: 5px 10px;
        padding-top: 5px;
        padding-bottom: 10px;
    }
    .sep-line {
        margin: 0;
    }
    .product_img_g img {
        padding: 15px;
    }
    .product_info {
        text-align: center;
    }

    .qnty-div {
        text-align: center;
        margin-top: 30px;
    }

    .product_price {
        text-align: center;
        margin-top: 30px;
    }
    .order-date > div {
        padding: 0 30px;
    }
    .order-date p {
        padding: 0;
        margin: 0;
    }
    .total-am p {
        text-align: right;
    }
    .total_am_parag {
        display: flex;
        flex-wrap: nowrap;
        justify-content: space-between;
    }
    .total_detail_price {
        font-size: 16px;
        font-weight: bold;
    }
</style>

@section('content')

<!-- START SECTION BREADCRUMB -->
<div class="breadcrumb_section bg_gray page-title-mini">
    <div class="container"><!-- STRART CONTAINER -->
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-title">
                    <h1>OrderList</h1>
                </div>
            </div>
            <div class="col-md-6">
                <ol class="breadcrumb justify-content-md-end">
                    <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
                    <li class="breadcrumb-item active">OrderList</li>
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
            @if(count($orders)>0)
            <div class="col-lg-8 col-md-10 col-sm-12" style="margin: auto;">
                @foreach($orders as $order)
                    <div class="product">
                        <div class="order_num">
                            <div class="left_order_id">
                                <a href="/order/details/{{base64_encode($order['id'])}}">
                                    <button type="button" class="btn btn-fill-out btn-sm order-btn">
                                        {{$order['userShownOrderId']}}
                                    </button>
                                </a>
                                <hr class="sep-line" />
                            </div>
                            @if($deliveredStatus[0] == $order['order_status'] || $shippedStatus[0] == $order['order_status'] )
                                <div class="right-track">
                                   <a target="_blank" href="{{$order['trackURL']}}">
                                        <svg width="12" height="12" viewBox="0 0 9 12" class="location_img">
                                            <path fill="#2874f0" class="location_img" d="M4.2 5.7c-.828 0-1.5-.672-1.5-1.5 0-.398.158-.78.44-1.06.28-.282.662-.44 1.06-.44.828 0 1.5.672 1.5 1.5 0 .398-.158.78-.44 1.06-.28.282-.662.44-1.06.44zm0-5.7C1.88 0 0 1.88 0 4.2 0 7.35 4.2 12 4.2 12s4.2-4.65 4.2-7.8C8.4 1.88 6.52 0 4.2 0z" fill-rule="evenodd"></path>
                                        </svg>
                                        Track Order
                                    </a>
                                </div>
                            @endif
                        </div>

                        @foreach($order['cart'] as $key=>$cart)
                            <div class="row">
                                <div class="col-md-2 product_img_g">
                                    @if($cart->configuration_image!=null)
                                        @if($cart->configuration_id!=null)
                                            <a href="/product/details/{{$cart->product_slug}}">
                                                <img src="/uploads/products/images/{{$cart->product_id}}/80x85/{{$cart->configuration_image}}" alt="image">
                                            </a>
                                        @else
                                            <a href="/product/details/{{$cart->product_slug}}">
                                                <img src="/uploads/products/images/{{$cart->product_id}}/80x85/{{$cart->configuration_image}}" alt="product">
                                            </a>
                                        @endif
                                    @else
                                        <a href="/product/details/{{$cart->product_slug}}">
                                            <img src="/images/no-image-available.png" alt="product">
                                        </a>
                                    @endif
                                </div>
                                <div class="col-md-5 product_info">
                                    <h6 class="product_title">
                                        <a href="/product/details/{{$cart->product_slug}}">{{$cart->product_name}}</a>
                                    </h6>
                                    <div class="other-info">
                                        @if($cart->color!=null)
                                            <span style="font-style: italic;"><small>Color :  {{$cart->color}}&nbsp;&nbsp; </small></span>
                                            <br />
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
                                </div>
                            </div>
                        @endforeach
                        <div class="row order-date">
                            <div class="col-md-6">
                                <p><span>Ordered On</span> <span style="font-size: 20px;"><b>{{date("D, M, d 'y",strtotime($order['created_at']))}}</b></span></p>
                                <div class="status-Div">
                                    @if($order['payment_status'] != 9 && $order['payment_status'] != 11)
                                        <p style="font-weight: 20px; font-weight: 600;">
                                            {{"Order Pending"}}
                                        </p>
                                    @else
                                        <p style="font-weight: 20px; font-weight: 600;">
                                            @if(count($order['status'])>0)
                                                @if($order['status'][0] == "Pending")
                                                    {{"Order Received"}} 
                                                @else 
                                                    {{$order['status'][0]}} 
                                                @endif 
                                            @else 
                                                Not Available 
                                            @endif
                                        </p>
                                    @endif
                                    <div class="smallNote">
                                        <p>
                                            <small>
                                            @if($order['payment_status'] != 9 && $order['payment_status'] != 11){{"We have received your order but your payment is in Pending from Bank. Once we receive the payment, We will process the order."}} @else {{$order['note']}} @endif
                                            </small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="total-am">
                                    <p class="total_am_parag">
                                        <span >Subtotal : </span>
                                        <span class="total_detail_price"> ₹{{number_format($order->sub_total+$order->discount+$order->coupon_discount,2)}} </span>
                                    </p>
                                    <p class="total_am_parag">
                                        <span >
                                            Shipping Charges : 
                                        </span> 
                                        <span class="total_detail_price">₹{{number_format($order->total_shipping_amount,2)}}</span>
                                    </p>
                                    @if($order->discount>0)
                                        <p class="total_am_parag" style="color: red">
                                            <span style="color: red" >
                                                Offer Discount : 
                                            </span>
                                            <span class="total_detail_price">- ₹{{ number_format($order->discount,2)}}</span>
                                        </p>
                                    @endif
                                    @if($order->coupon_discount > 0)
                                        <p class="total_am_parag" style="color: red">
                                            <span  style="color: red">
                                                Coupon Discount({{$order->coupon_code}}) : </span>
                                            <span class="total_detail_price">- ₹{{ number_format($order->coupon_discount,2)}}</span>
                                        </p>
                                    @endif
                                    <hr>
                                    <p class="total_am_parag">
                                        <span >Order Total : </span> 
                                        <span class="total_detail_price">₹{{number_format($order->sub_total+$order->total_shipping_amount,2)}}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="page-number">
                    {{ $orders->links() }}
                </div>
            </div>
            @else
            <div class="col-lg-8 col-md-10 col-sm-12">
                <div class="cart-Div empty-cart">
                    <img src="/images/no-order.png" alt="empty-order">
                    <h3>Empty Orders.</h3>
                    <p>Looks like you haven't made your choice yet.</p>
                </div>
            </div>
            @endif
            {{--<!--Modal start here-->
            <div class="modal fade order-cancel-modal" id="myModal" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Cancellation Request</h4>
                        </div>
                        <div class="modal-body">
                            <div class="item-details">
                                <table>
                                    <thead>
                                    <tr>
                                        <td>Item Details</td>
                                        <td>Qty.</td>
                                        <td>Subtoal</td>
                                    </tr>
                                    </thead>
                                    <tbody id="CancelOrder">
                                    </tbody>
                                </table>
                                <form class="cancel-form" id="frmCancelOrder" action="/order/cancel" method="post" name="frmCancelOrder">
                                   <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                   <input type="hidden" name="order_id" id="order_id" value="">
                                    <div class="f-row">
                                        <label>Reason for cancellation<span>*</span></label>
                                        <select name="reason">
                                            <option selected disabled>Select Reason</option>
                                            <option value="The delivery is delayed">The delivery is delayed</option>
                                            <option value="Shopping cost is too much">Shopping cost is too much</option>
                                            <option value="Need to change shipping address">Need to change shipping address</option>
                                        </select>
                                    </div>
                                    <div class="f-row">
                                        <label>Comments</label>
                                        <textarea name="comments"></textarea>
                                    </div>
                                    <div class="f-row less-row">
                                        <p><span>Note:</span> There will be no refund as the order is purchased using Cash-On-Delivery</p>
                                    </div>
                                    <div class="f-row less-row right-btn">
                                        <input type="submit" name="cancellation" value="Confirm" class="confirm-btn">
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <!--Modal End here-->
            <!--Modal start here-->
            <div class="modal fade order-cancel-modal" id="myModal1" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Return Order Request</h4>
                        </div>
                        <div class="modal-body">
                            <div class="item-details">
                                <table>
                                    <thead>
                                    <tr>
                                        <td>Item Details</td>
                                        <td>Qty.</td>
                                        <td>Subtoal</td>
                                    </tr>
                                    </thead>
                                    <tbody id="ReturnOrder">
                                    </tbody>
                                </table>
                                <form class="cancel-form" id="frmReturnOrder" action="/order/return" method="post" name="frmReturnOrder">
                                   <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                   <input type="hidden" name="order_id1" id="order_id1" value="">
                                    <div class="f-row">
                                        <label>Reason for Return Order<span>*</span></label>
                                        <select name="reason">
                                            <option selected disabled>Select Reason</option>
                                            <option value="Product damaged, but shipping box OK">Product damaged, but shipping box OK</option>
                                            <option value="Wrong item was sent">Wrong item was sent</option>
                                            <option value="Performance or quality not adequate">Performance or quality not adequate</option>
                                        </select>
                                    </div>
                                    <div class="f-row">
                                        <label>Comments</label>
                                        <textarea name="comments"></textarea>
                                    </div>
                                    <div class="f-row less-row">
                                        <p><span>Note:</span> There will be no refund as the order is purchased using Cash-On-Delivery</p>
                                    </div>
                                    <div class="f-row less-row right-btn">
                                        <input type="submit" name="returnorder" value="Confirm" class="confirm-btn">
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>

                </div>
            </div>
            <!--Modal End here-->--}}
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('.product').hover(function(e) {
            e.preventDefault();
        });
    })
</script>
<script src="{{asset('js/order.js')}}" type="text/javascript" language="javascript"></script>
@endsection
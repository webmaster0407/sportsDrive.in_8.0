@extends('layouts.user')
@section('content')
    <div class="main_content">
        <div class="container" id="allContent">

            <div class="thank-u">
                <h3>Thank You For Your Purchase</h3>
                <p>Your order ( Order ID: {{$userShownOrderId}} ) has been placed successfully.</p>
               {{-- <input type="button" name="print_this" value="Print This" class="print_b" onclick="print()">--}}
            </div>
            <div class="container">
                <div class="cart-Div">
                    <?php
                    $defaultShippingAddress = json_decode($order->shipping_address,true);
                    $defaultBillingAddress = json_decode($order->billing_address,true)
                    ?>
                    <div class="cart-List">
                        <div class="final-address">
                            <div class="address-left">
                                <h3>Shipping Address:</h3>
                                <p><strong>{{$defaultShippingAddress['address_title']}}</strong></p>
                                <p><strong>{{$defaultShippingAddress['full_name']}}</strong></p>
                                <p>{{$defaultShippingAddress['address_line_1']}}</p>
                                <p>{{$defaultShippingAddress['address_line_2']}} {{$defaultShippingAddress['city']}}{{$defaultShippingAddress['state']}} </p>
                                <p>{{$defaultShippingAddress['country']}}</p>
                                <p>{{$defaultShippingAddress['pin_code']}}</p>
                            </div>
                            <div class="address-left">
                                <h3>Billing Address:</h3>
                                <p><strong>{{$defaultBillingAddress['address_title']}}</strong></p>
                                <p><strong>{{$defaultBillingAddress['full_name']}}</strong></p>
                                <p>{{$defaultBillingAddress['address_line_1']}}</p>
                                <p>{{$defaultBillingAddress['address_line_2']}} {{$defaultBillingAddress['city']}}{{$defaultBillingAddress['state']}} </p>
                                <p>{{$defaultBillingAddress['country']}}</p>
                                <p>{{$defaultBillingAddress['pin_code']}}</p>
                            </div>
                        </div>
                        <div class="cart-Table success-table">
                            <table cellspacing="1" cellpadding="0">
                                <tbody>
                                <tr>
                                    <th>Products</th>
                                    <th></th>
                                    <th>Price &#8377;</th>
                                    <th>Quantity</th>
                                    <th>Total Amount</th>
                                </tr>
                                @foreach($carts as $cart)
                                <tr>
                                    <td><div class="imgDiv">
                                        @if($cart->configuration_image!=null)
                                            @if($cart->configuration_id!=null)
                                                    <a href="#"><img src="/uploads/products/images/{{$cart->product_id}}/{{$cart->colorId}}/80x85/{{$cart->configuration_image}}" alt="image"></a>
                                            @else
                                                    <a href="#"><img src="/uploads/products/images/{{$cart->product_id}}/80x85/{{$cart->configuration_image}}" alt="product"></a>
                                            @endif
                                        @else
                                                <img src="/images/no-image-available.png" alt="product">
                                        @endif
                                        </div></td>
                                    <td><a href="/product/details/{{$cart->product_slug}}">{{$cart->product_name}}</a>
                                        <div class="other-info">
                                           @if($cart->color!=null) <p><span>Color:</span>{{$cart->color}}</p> @endif
                                           @if($cart->size!=null) <p><span>Size:</span>{{$cart->size}}</p> @endif
                                        </div>
                                    </td>
                                    <td><span class="price">{{number_format($cart->price_per_qty - $cart->discount_per_qty,2)}}</span></td>
                                    <td><span>{{$cart->quantity}}</span></td>
                                    <td><span class="price"> {{number_format($cart->final_price,2)}}</span></td>
                                </tr>
                               @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="summary-coupon">
                        <div class="order-summary order-history">
                            <h3>Order History</h3>
                            <div class="order-S">
                                <div class="ord">
                                    <div class="leftN ">Order Confirmation</div>
                                    <div class="rightC total"><span>{{$userShownOrderId}}</span></div>
                                </div>
                                <div class="ord">
                                    <div class="leftN">Order Date</div>
                                    <div class="rightC"><span>{{date("d M Y",strtotime($order->order_date))}}</span></div>
                                </div>
                            </div>

                             <div class="order-S">
                                    <h4>Payment Info</h4>
                                     <div class="ord">
                                        <div class="leftN">Payment Method</div>
                                        <div class="rightC">
                                            <span>@if($order->payu_payment_mode != null)
                                                      @if($order->payu_payment_mode == "CC")
                                                      Credit Card
                                                      @elseif($order->payu_payment_mode == "DC")
                                                       Debit Card
                                                      @elseif($order->payu_payment_mode == "NB")
                                                       NetBanking
                                                      @elseif($order->payu_payment_mode == "CASH")
                                                       Cash Card
                                                      @elseif($order->payu_payment_mode == "EMI")
                                                       EMI
                                                      @elseif($order->payu_payment_mode == "IVR")
                                                       IVR
                                                       @elseif($order->payu_payment_mode == "COD")
                                                       Cash On Delivery
                                                      @endif
                                                 @else
                                                    {{$order->payu_payment_mode}}
                                                 @endif
                                            </span></div>
                                    </div>
                                    <div class="ord">
                                        <div class="leftN">Payment Id</div>
                                        <div class="rightC"><span>@if($order->payu_payment_id != null){{$order->payu_payment_id}}@else NA @endif</span></div>
                                    </div>
                                    <div class="ord">
                                        <div class="leftN">Bank Reference NO</div>
                                        <div class="rightC"><span>@if($order->payu_bank_ref_num!= null){{$order->payu_bank_ref_num}}@else NA @endif</span></div>
                                    </div>
                                   
                                </div>
                            

                            <div class="order-S">
                                <h4>Order Summary</h4>
                                <div class="ord">
                                    <div class="leftN">Total</div>
                                    <div class="rightC"><span>{{number_format($order->sub_total+$order->discount+$order->coupon_discount)}}</span></div>
                                </div>
                                @if($order->discount>0)
                                    <div class="ord" style="color: red">
                                        <div class="leftN">Discount</div>
                                        <div class="rightC"><span>-{{number_format($order->discount)}}</span></div>
                                    </div>
                                @endif
                                @if($order->coupon_discount>0)
                                    <div class="ord" style="color: red">
                                        <div class="leftN">Coupon Discount({{$order->coupon_code}})</div>
                                        <div class="rightC"><span>-{{number_format($order->coupon_discount)}}</span></div>
                                    </div>
                                @endif
                                <div class="ord">
                                    <div class="leftN">SubTotal</div>
                                    <div class="rightC" id="purchase_subtotal"><span>{{number_format($order->sub_total)}}</span></div>
                                </div>
                                <div class="ord">
                                    <div class="leftN">Shipping Charges</div>
                                    <div class="rightC"><span>{{number_format($order->total_shipping_amount,2)}}</span></div>
                                </div>
                            </div>
                            <div class="order-S">
                                <div class="ord">
                                    <div class="leftN total">Total</div>
                                    <div class="rightC total"><span>{{number_format($order->sub_total + $order->total_shipping_amount,2)}}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function print() {
            var mywindow = window.open('', 'PRINT', 'height=600,width=900');
            mywindow.document.write('<html><head><title>' + document.title  + '</title>');
            mywindow.document.write('<link rel="stylesheet" href="/css/style.css" type="text/css" />');
            mywindow.document.write('</head><body >');
            mywindow.document.write('<h1>' + document.title  + '</h1>');
            mywindow.document.write(document.getElementById("allContent").innerHTML);
            mywindow.document.write('</body></html>');
            mywindow.document.close(); // necessary for IE >= 10
            mywindow.focus(); // necessary for IE >= 10*/
            mywindow.print();
            mywindow.close();
            return true;
        }
    </script>
    <script type="text/javascript" src="/js/checkout.js"></script>
@endsection
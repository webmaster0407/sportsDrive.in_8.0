@extends('layouts.user')
@section('content')

<style type="text/css">
    .change-address > button {
        width: 100%;
    }
    .edit-add > a > button {
        width: 100%;
        margin-bottom: 10px;
        padding-top: 5px;
        padding-bottom: 5px;
    }
    #default_shipping_address {
        margin-top: 45px;
    }
    #default_shipping_address p {
        margin-bottom: 0;
        margin-top: 5px;
    }

    #default_billing_address p {
        margin-bottom: 0;
        margin-top: 5px;
    }

    .add-edit-change-btns {
        margin-bottom: 20px;
    }

    .address-List > div {
        color: #333;
        margin: 5px 0;
    }
    .address-List > div  p {
        margin-top: 5px;
        margin-bottom: 0;
    } 

    .modal_select_btn {
        margin-right: 20px;
        margin-bottom: 20px;
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
            @if(Session::has('errors'))
                <div class="alert alert-danger">
                    @if ($errors->has('billing_address_id'))
                        {{"Please add/select billing address."}}<br>
                    @endif
                    @if ($errors->has('shipping_address_id'))
                        {{"Please add/select shipping address."}}<br>
                    @endif
                    @if ($errors->has('delivery'))
                        {{"Please select delivery method."}}<br>
                    @endif
                    @if ($errors->has('payment'))
                        {{"Please select payment method."}}<br>
                    @endif
                </div>
            @endif
            <div class="row">
                <div class="col-md-8 checkout-info">
                    <form name="checkout" action="/checkout/2" method="post">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" id="std_del_ch" name="std_del_ch" value="{{ number_format($standardCharges,2)  }}">
                        <input type="hidden" id="ex_del_ch" name="ex_del_ch" value="{{ number_format($expressCharges,2) }}">
                        
                        <div class="order_review" style="margin-bottom: 30px;">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="addressType">Shipping Address</h5>
                                    <hr />
                                    <div id="error"></div>
                                    @if(isset($allShippingAddresses) && !$allShippingAddresses->isEmpty())
                                        @if( !isset($defaultShippingAddress) )
                                            <?php $defaultShippingAddress = $allShippingAddresses[0]?>
                                        @endif
                                        <div id="default_shipping_address">
                                            <input type="hidden" class="form-control" name="shipping_address_id" id="shipping_address_id" value="{{$defaultShippingAddress['id']}}">
                                            <p><strong>{{$defaultShippingAddress['address_title']}}</strong></p>
                                            <p><strong>{{$defaultShippingAddress['full_name']}}</strong></p>
                                            <p>{{$defaultShippingAddress['address_line_1']}}</p>
                                            <p>{{$defaultShippingAddress['address_line_2']}} {{$defaultShippingAddress['city']}}{{$defaultShippingAddress['state']}} </p>
                                            <p>{{$defaultShippingAddress['country']}}</p>
                                            <p>{{$defaultShippingAddress['pin_code']}}</p>
                                        </div>
                                        <div class="row add-edit-change-btns">
                                            <div class="col-12">
                                                <div class="edit-add">
                                                    <a href="/checkout/edit-address/{{$defaultShippingAddress['id']}}">
                                                        <button type="button" class="btn btn-fill-line btn-sm">Edit</button>
                                                    </a>
                                                    <a href="/checkout/add-shipping-address">
                                                        <button  type="button" class="btn btn-fill-line btn-sm">Add New Address</button>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-12 change-address">
                                                <button  type="button" class="change_new btn btn-fill-out btn-sm" data-toggle="modal" data-target="#shipping_model"  data-keyboard="true">Change Address</button>
                                            </div>
                                        </div>
                                    @else
                                        
                                        <div id="default_shipping_address"></div>
                                        
                                        <div class="edit-add">
                                            <a href="/checkout/add-shipping-address">
                                                <button  type="button" class="btn btn-fill-out btn-sm">Add New Address</button>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <h5 class="addressType">Billing Address</h5>
                                    <hr />
                                    <div class="chek-form same_as_check">
                                        <div class="custome-checkbox">
                                            <input class="form-check-input" type="checkbox" name="same" value="Y" id="same_as_ship">
                                            <label class="form-check-label label_info" for="same_as_ship">
                                                <span>Same as Shipping Address</span>
                                            </label>
                                        </div>
                                    </div>
                                    @if(isset($allBillingAddresses) && !$allBillingAddresses->isEmpty())
                                        @if( !isset($defaultBillingAddress)) 
                                            <?php $defaultBillingAddress = $allBillingAddresses[0]?>
                                        @endif
                                        <div id="default_billing_address">
                                            <input type="hidden"  type="button" name="billing_address_id" id="billing_address_id" value="{{$defaultBillingAddress['id']}}">
                                            <p><strong>{{$defaultBillingAddress['address_title']}}</strong></p>
                                            <p><strong>{{$defaultBillingAddress['full_name']}}</strong></p>
                                            <p>{{$defaultBillingAddress['address_line_1']}}</p>
                                            <p>{{$defaultBillingAddress['address_line_2']}} {{$defaultBillingAddress['city']}}{{$defaultBillingAddress['state']}} </p>
                                            <p>{{$defaultBillingAddress['country']}}</p>
                                            <p>{{$defaultBillingAddress['pin_code']}}</p>
                                        </div>
                                        <div class="row add-edit-change-btns">
                                            <div class="col-12">
                                                <div class="edit-add">
                                                    <a href="/checkout/edit-address/{{$defaultBillingAddress['id']}}">
                                                        <button  type="button" class="btn btn-fill-line btn-sm">Edit</button>
                                                    </a>
                                                    <a href="/checkout/add-billing-address">
                                                        <button  type="button" class="btn btn-fill-line btn-sm">Add New Address</button>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-12 change-address">
                                                <button  type="button" class="change_new btn btn-fill-out btn-sm" data-toggle="modal" data-target="#billing_model"  data-keyboard="true">Change Address</button>
                                            </div>
                                        </div>



                                    @else
                                        <div id="default_billing_address"></div>
                                        <div class="edit-add">
                                            <a href="/checkout/add-billing-address">
                                                <button  type="button" class="btn btn-fill-out btn-sm">Add New Address</button>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="order_review" style="margin-bottom: 30px;">
                            <div class="col-12 address-section">
                                <h5>Delivery Method</h5>
                                <div class="input-left">
                                    <input class="delivery_type" type="radio" name="delivery" value="express" checked disabled>
                                    <span>
                                        Express Shipping (Delivery is usually within 2-4 working days after dispatch date)
                                    </span>
                                </div>
                                <span>Rs.{{number_format($offersPrices['totalShippingCharge'],2)}}</span>
                            </div>
                        </div>
                        <div class="order_review" style="margin-bottom: 20px;">
                            <div class="col-12 payment">
                                <h5>Payment Method</h5>
                                <select name="payment" style="width: 100%;">
                                    <option value="online">Payment with NetBanking / Credit / Debit Card</option>
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
                        <div style="text-align: right;">
                            <button class="btn btn-fill-out btn-sm" id="Continue" type="submit">Continue</button>
                        </div>
                    </form>

                </div>
                <div class="col-md-4">
                    <div class="order_review">
                        <div class="heading_s1">
                            <h5>ORDER SUMMARY</h5>
                        </div>
                        <div class="table-responsive order_table">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td>Subtotal ({{$cartCount}}) items</td>
                                        <td>{{$subtotal}}</td>
                                    </tr>
                                    <?php $shipping = $offersPrices['totalShippingCharge']; ?>
                                    <tr>
                                        <td>Shipping Charge</td>
                                        <td>{{$shipping}}</td>
                                    </tr>
                                    @if($offersPrices['finalDiscount']>0)
                                    <tr>
                                        <td>Discount</td>
                                        <td>-{{number_format($offersPrices['finalDiscount'],2)}}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td>Estimated Total</span></td>
                                        <td>{{number_format($offersPrices['finalDiscountedAmount']+$shipping,2)}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END MAIN CONTENT -->


    {{--change shipping address model start here--}}
    <div class="modal fade" id="shipping_model" role="dialog" tabindex='-1'>
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content address-modal">
                <div class="modal-header">
                    <h4 class="modal-title">Address List</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="address-List row">
                        @foreach($allShippingAddresses as $shippingAddress)
                            <div class="col-md-6">
                                <div class="same-as">
                                    <input type="radio" name="same" value="{{$shippingAddress->id}}" id="{{$shippingAddress->id}}" class="select_shipping">
                                    <label for="{{$shippingAddress->id}}">Select Address</label>
                                </div>
                                <div id="all_shipping_{{$shippingAddress->id}}">
                                    <input type="hidden" name="shipping_address_id" id="shipping_address_id" value="{{$shippingAddress['id']}}">
                                    <p><strong>{{$shippingAddress['address_title']}}</strong></p>
                                    <p><strong>{{$shippingAddress['full_name']}}</strong></p>
                                    <p>{{$shippingAddress['address_line_1']}}</p>
                                    <p>{{$shippingAddress['address_line_2']}} {{$shippingAddress['city']}}{{$shippingAddress['state']}} </p>
                                    <p>{{$shippingAddress['country']}}</p>
                                    <p>{{$shippingAddress['pin_code']}}</p>
                                </div>
                                <hr />
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="select-btn" style="text-align: right;">
                    <button type="button" class="btn btn-fill-line btn-sm modal_select_btn" data-dismiss="modal">Select</button>
                </div>
            </div>
        </div>
    </div>
    {{--change shipping address model ends here--}}

    {{--change billing address model start here--}}

    <div class="modal fade" id="billing_model" role="dialog" tabindex='-1'>
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content address-modal">
                <div class="modal-header">
                    <h4 class="modal-title">Address List</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="address-List row">
                        @foreach($allBillingAddresses as $billingAddress)
                            <div class="col-md-6">
                                <div class="same-as">
                                    <input type="radio" name="same" class="select_billing" id="{{$billingAddress->id}}" value="{{$billingAddress->id}}">
                                    <label for="{{$billingAddress->id}}">Select Address</label>
                                </div>
                                <div id="all_billing_{{$billingAddress->id}}">
                                    <input type="hidden" name="billing_address_id" id="shipping_address_id" value="{{$billingAddress['id']}}">
                                    <p><strong>{{$billingAddress['address_title']}}</strong></p>
                                    <p><strong>{{$billingAddress['full_name']}}</strong></p>
                                    <p>{{$billingAddress['address_line_1']}}</p>
                                    <p>{{$billingAddress['address_line_2']}} {{$billingAddress['city']}}{{$billingAddress['state']}} </p>
                                    <p>{{$billingAddress['country']}}</p>
                                    <p>{{$billingAddress['pin_code']}}</p>
                                </div>
                                <hr />
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="select-btn" style="text-align: right;"> 
                    <button type="button" class="btn btn-fill-line btn-sm modal_select_btn" data-dismiss="modal">Select</button>
                </div>
            </div>
        </div>
    </div>
    {{--change billing address model ends here--}}

</div>
<!-- END MAIN CONTENT -->

    <script type="text/javascript" src="/js/checkout.js"></script>
@endsection
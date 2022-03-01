@extends('layouts.user')
@section('content')

<style type="text/css">
    .order-name {
        display: flex;
        justify-content: space-between;
    }

    .order-name span {
        font-weight: 500;
        font-size: 16px;
    }
    .top-fail p {
        font-size: 25px;
    }
    .transaction-details h5, .transaction-details h6, .transaction-details h4, {
        color: #9f9494;
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
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-md-10 col-sm-12 order_review" style="margin: auto;">
                <div class="top-fail">
                    @if($order->payment_custom_message!=null)
                    <p>{{$order->payment_custom_message}}</p>
                    @else
                        <p>Sorry! Your order has been failed. Please ty again.</p>
                    @endif
                </div>
                <div class="transaction-details">
                    <h5>Payment Details</h5>
                    <div class="row">
                        <div class="col-md-6" style="margin: auto;">
                            <div class="order-name">
                                <h6>Order ID : </h6>
                                <span>{{$userShownOrderId}}</span>
                            </div>
                            <div class="order-name">
                                <h6>Payment ID:</h6>
                                <span>{{$order->payu_payment_id}}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="back_home" style="text-align: right;">
                    <a href="{{ route('index') }}">
                        <button type="button" class="btn btn-fill-out btn-sm">Back To Home</button>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
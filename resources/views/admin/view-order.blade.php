@extends('layouts.admin') 
@push('stylesheets')
  <!-- DataTables -->
  <link rel="stylesheet" href="../../plugins/datatables/dataTables.bootstrap.css">
@endpush
@push('scripts')
<script src="{{asset("plugins/jQuery/jquery-2.2.3.min.js")}}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
@endpush
@section('content')
<section class="content-header">
    <h1>
        Order Details
    </h1>
    <ol class="breadcrumb">
        <li><a href="home"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="/administrator/list-orders">List Orders</a></li>
        <li class="active"> View Orders</li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-body">
                                    
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
                    @if(Session::has('errors'))
                    <div class="alert alert-danger">
                        {{"You have some errors below.Please check"}}
                        <br>
                        <ul>
                            @foreach ($errors->all() as $message)
                                <div>
                                    <br>
                                    <li>{{$message}}</li>
                                </div>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                   <div class="col-sm-6">
                    <h4>Customer Name : <b>{{$data->first_name}} {{$data->last_name}}</b></h4>
                    <h4>Order No. : <b>{{$userShownOrderId}}</b></h4>
                   </div>
                   <!-- if order status is not failed & if genarated invoice then print -->
                   @if($data->invoice_id == null)
                   <div  class="col-sm-3" style="float: right;"><button type="button" class="btn btn-block btn-success" id="genarate Invoice" onclick="window.location='{{ url("/administrator/generate-invoice/".$data->id) }}'"><i class="fa fa-mail-forward"></i> Generate Invoice</button></div>
                   @else
                   <div  class="col-sm-3" style="float: right;"><button type="button" class="btn btn-block btn-success" id="print" onclick="return window.print()">  <i class="fa fa-print"></i> Print Invoice </button></div>
                 
                   @endif
                 
                </div>
            </div>
        </div>
    </div>
    <div class="row">
            <div class="col-md-6">
              <div class="box box-solid">
                <div class="box-header with-border">
                  <i class="fa fa-home"></i>

                  <h3 class="box-title">Shipping Address</h3>
                </div>
                <!-- /.box-header -->
                <?php
                $defaultShippingAddress = json_decode($data->shipping_address,true);
                $defaultBillingAddress = json_decode($data->billing_address,true)
                ?>
                <div class="box-body">
                  <div>
                    
                    <a href="/administrator/update-order-shipping-address/{{$data->id}}"><p><strong>{{$defaultShippingAddress['address_title']}}</strong></p></a>
                    <p><strong>{{$defaultShippingAddress['full_name']}}</strong></p>
                    <p>{{$defaultShippingAddress['address_line_1']}}</p>
                    <p>{{$defaultShippingAddress['address_line_2']}} {{$defaultShippingAddress['city']}} {{$defaultShippingAddress['state']}} </p>
                    <p>{{$defaultShippingAddress['country']}}</p>
                    <p>{{$defaultShippingAddress['pin_code']}}</p>
                    
                  </div>
                </div>
                <!-- /.box-body -->
              </div>
              <!-- /.box -->
            </div>
            <!-- ./col -->
            <div class="col-md-6">
              <div class="box box-solid">
                <div class="box-header with-border">
                  <i class="fa fa-home"></i>

                  <h3 class="box-title">Billing Address</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body clearfix">
                  <div >
                      <a href="/administrator/update-order-billing-address/{{$data->id}}"><p><strong>{{$defaultBillingAddress['address_title']}}</strong></p></a>
                      <p><strong>{{$defaultBillingAddress['full_name']}}</strong></p>
                      <p>{{$defaultBillingAddress['address_line_1']}}</p>
                      <p>{{$defaultBillingAddress['address_line_2']}} {{$defaultBillingAddress['city']}} {{$defaultBillingAddress['state']}} </p>
                      <p>{{$defaultBillingAddress['country']}}</p>
                      <p>{{$defaultBillingAddress['pin_code']}}</p>
                  </div>
                </div>
                <!-- /.box-body -->
              </div>
              <!-- /.box -->
            </div>
            <!-- ./col -->
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box box-solid">
              <div class="box-header with-border">
                <i class="fa fa-shopping-cart"></i>
              <h3 class="box-title"> Carts Details</h3>
              </div>
              <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                  <tr>
                    <th>ID</th>
                    <th>Product</th>
                    <th>Details</th>
                    <th>Price &#8377;</th>
                    <th>Quantity</th>
                    <th>Total Amount &#8377;</th>
                  </tr>
                  <?php $i=1;?>
                  @foreach($carts as $cart)
                  <tr>
                    <td>{{$i}}</td>
                    <td>
                      @if($cart->configuration_image != null)
                        <img src="/uploads/products/images/{{$cart->product_id}}/80x85/{{$cart->configuration_image}}" alt="config-product">
                      @elseif($cart->image!= null)
                        <img src="/uploads/products/images/{{$cart->product_id}}/80x85/{{$cart->image}}" alt="product">
                      @else
                         <img src="/images/no-image-available.png" alt="product" height="85" width="80">
                      @endif

                    </td>
                    <td><a href="/administrator/edit-products/{{$cart->product_id}}">{{$cart->name}}</a>
                        <div>
                           @if($cart->color!=null) <p><span><b>Color: </b></span>{{$cart->color}}</p> @endif
                           @if($cart->size!=null) <p><span><b>Size: </b></span>{{$cart->size}}</p> @endif
                        </div>
                    </td>
                    <td><span class="price">&#8377;{{number_format($cart->price_per_qty - $cart->discount_per_qty,2)}}</span></td>
                    <td><span>{{$cart->quantity}}</span></td>
                    <td><span class="price"> &#8377;{{number_format($cart->final_price,2)}}</span></td>
                  </tr> 
                  <?php $i++; ?>  
                  @endforeach
                  <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><b>SubTotal :</b></td>
                    <td><b>&#8377;{{number_format($data->sub_total+$data->discount+$data->coupon_discount,2)}}</b></td>
                  </tr>
                   <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><b>Shipping Charges :</b></td>
                    <td><b>&#8377;{{number_format($data->total_shipping_amount,2)}}</b></td>
                  </tr>
                    <tr style="color:red;">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>Offer Discount :</b></td>
                        <td><b>&#8377;{{number_format($data->discount,2)}}</b></td>
                    </tr>
                    <tr style="color:red;">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>Coupon Discount({{$data->coupon_code}}) :</b></td>
                        <td><b>&#8377;{{number_format($data->coupon_discount,2)}}</b></td>
                    </tr>
                   <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><b>Total :</b></td>
                    <td><b>&#8377;{{number_format($data->sub_total + $data->total_shipping_amount,2)}}</b></td>
                  </tr>
                 
                </table>
              </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
          <form method="POST" name="frmOrderStatus" id="frmOrderStatus" action="/administrator/change-order-status">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="order_id" value="{{$data->id}}" />
            <input type="hidden" name="order_status_prev" value="{{$data->order_status}}" />
            <input type="hidden" name="payment_status_prev" value="{{$data->payment_status}}" />
            <input type="hidden" name="ready_to_ship_status_id" id="ready_to_ship_status_id" value="{{$readyToShipStatus->status_id}}" />
            <input type="hidden" name="shipped_status_id" id="shipped_status_id" value="{{$shippedStatus->status_id}}" />
            <input type="hidden" name="delivered_status_id" id="delivered_status_id" value="{{$deliveredStatus->status_id}}" />
            <input type="hidden" name="cancelled_status_id" id="cancelled_status_id" value="{{$cancelledStatus->status_id}}" />
            <input type="hidden" name="refund_status_id" id="refund_status_id" value="{{$orderRefundStatus->status_id}}" />

          <div class="box box-solid">
            
            <div class="box-body">
              <div class="col-md-6">
                <h3>Payment Information</h3>
                <dl class="dl-horizontal">
                      <dt>Payment Method</dt>
                      <dd>@if($data->payu_payment_mode!=null){{$data->payu_payment_mode}}@else NA @endif</dd>
                      <dt>Payment Id</dt>
                      <dd>@if($data->payu_payment_id != null){{$data->payu_payment_id}}@else NA @endif</dd>
                      <dt>Card Category</dt>
                      <dd>@if($data->payu_card_category!= null){{$data->payu_card_category}}@else NA @endif</dd>
                      <dt>Net Amount Debit</dt>
                      <dd>@if($data->payu_net_amount_debit!= null){{$data->payu_net_amount_debit}}@else NA @endif</dd>
                      <dt>Type</dt>
                      <dd>@if($data->payu_pg_type!= null){{$data->payu_pg_type}}@else NA @endif</dd>
                      <dt>Bank Reference NO</dt>
                      <dd>@if($data->payu_bank_ref_num!= null){{$data->payu_bank_ref_num}}@else NA @endif</dd>
                      <dt>User Agent</dt>
                      <dd>@if($data->user_agent!= null){{$data->user_agent}}@else NA @endif</dd>
                      <dt>Ip Address</dt>
                      <dd>@if($data->ip_address!= null){{$data->ip_address}}@else NA @endif</dd>
                      <dt>Message</dt>
                      <dd>@if($data->payment_custom_message!= null){{$data->payment_custom_message}}@else NA @endif</dd>
                    <br>
                    <dt>Payment Status</dt>
                    <dd>
                      <select class="form-control" name="payment_status" id="payment_status" >
                        <option value="0">Select Status</option>
                        @if($data->payment_status == 9 ||$data->payment_status == 11)
                        <option value="{{$data->payment_status}}" selected>{{$data->payment_status_name['status']}}</option>
                        @endif
                        @if($allPaymentStatus!= null)
                        @foreach($allPaymentStatus as $pStatus)
                            <option value="{{$pStatus->status_id}}" @if($data->payment_status == $pStatus->status_id)selected @endif >{{$pStatus->status}}</option>
                        @endforeach
                        @endif                          
                      </select>
                    </dd>
                </dl>
              </div>
              <div class="col-md-6">
                <h3>Order Information</h3>
                 <dl class="dl-horizontal">
                <dt>Order Date</dt>
                <dd>{{date("d M Y H:i:s",strtotime($data->order_date))}}</dd>
                
                <br>
                <dt>Order Status</dt>
                <dd>
                  <select class="form-control order_status" name="order_status" id="order_status" value="" >
                    <option value="0">Select Status</option>
                    @if($data->order_status >0)
                    <option value="{{$data->order_status}}" selected>@if($data->order_status_name['status'] == "Pending"){{"Order Received"}} @else {{$data->order_status_name['status']}} @endif</option>
                    @endif
                    @if($nextStatus!= null)
                        <option value="{{$nextStatus->status_id}}">{{$nextStatus->status}}</option>
                    @endif
                    @if($data->order_status == 1)
                        <option value="{{$cancelledStatus->status_id}}">Order Cancelled</option>
                    @endif
                  </select><br><br>
                </dd>

                 <div id="ready_to_ship_div" <?php if ($data->order_status  < $readyToShipStatus->status_id) echo "style='display:none'";?> >
                     <dt>Length</dt><dd><input class="form-control" type="text" name="length" id="ready_to_ship_length_text_box" value="{{$data->length}}"></dd><br><br>
                     <dt>Breadth</dt><dd><input class="form-control" type="text" name="breadth" id="ready_to_ship_breadth_text_box" value="{{$data->breadth}}"></dd><br><br>
                     <dt>Height</dt><dd><input class="form-control" type="text" name="height" id="ready_to_ship_height_text_box" value="{{$data->height}}"></dd><br><br>
                     <dt>Weight</dt><dd><input class="form-control" type="text" name="weight" id="ready_to_ship_weight_text_box" value="{{$data->weight}}"></dd><br><br>
                 </div>

                 <div id="courier_div" <?php if ($data->order_status  < $shippedStatus->status_id || $data->order_status == $orderRefundStatus->status_id) echo "style='display:none'";?> >
                     <dt>Courier Tracking Number : </dt>
                     <dd><input class="form-control" type="text" name="courier_text_box" id="courier_text_box" value="{{$data->tracking_number}}"></dd><br><br>
                 </div>
                 <div id="delivered_to_div" <?php if ($data->order_status  != $deliveredStatus->status_id) echo "style='display:none'";?> >
                     <dt>Delivered To</dt>
                     <dd><input class="form-control" type="text" name="delivered_to" id="delivered_to" value="{{$data->delivered_to}}"></dd><br><br>
                 </div>
                 <div id="delivery_date_div" <?php if ($data->order_status  != $deliveredStatus->status_id) echo "style='display:none'";?> >
                     <dt>Delivery Date</dt>
                     <dd><input class="form-control" type="text" name="new_delivery_date" id="new_delivery_date" value="{{$data->new_delivery_date}}"></dd><br><br>
                 </div>
                 <div id="cancellation_reason_div" <?php if ($data->order_status  != $cancelledStatus->status_id && $data->order_status != $orderRefundStatus->status_id) echo "style='display:none'";?> >
                     <dt>Cancellation Reason</dt>
                     <dd><input class="form-control" type="text" name="cancellation_reason" id="cancellation_reason" value="{{$data->cancellation_reason}}"></dd><br><br>
                 </div>
                 <div id="refund_amount_div" <?php if ($data->order_status  != $orderRefundStatus->status_id) echo "style='display:none'";?> >
                     <dt>Refund Amount</dt>
                     <dd><input class="form-control" type="text" name="refund_amount" id="refund_amount" value="{{$data->refund_amount}}"></dd><br><br>
                 </div>
                 <div id="refund_bank_id_div" <?php if ($data->order_status  != $orderRefundStatus->status_id) echo "style='display:none'";?> >
                     <dt>Bank Refund Id Number</dt>
                     <dd><input class="form-control" type="text" name="refund_bank_id" id="refund_bank_id" value="{{$data->refund_bank_id}}"></dd><br><br>
                 </div>
                 <div id="bank_reference_div" <?php if ($data->order_status  != $orderRefundStatus->status_id) echo "style='display:none'";?> >
                     <dt>Bank Reference Number</dt>
                     <dd><input class="form-control" type="text" name="refund_bank_ref" id="refund_bank_ref" value="{{$data->refund_bank_ref}}"></dd><br><br>
                 </div>
                @if($data->is_return == 'Y') 
                  <dt>Order Return Status</dt>
                  <dd>
                    <select class="form-control" name="return_status" id="return_status">
                      <option value="0">Select Status</option>
                        @if($allReturnStatus!= null)
                        @foreach($allReturnStatus as $rStatus)
                            <option value="{{$rStatus->status_id}}" @if($data->return_status == $rStatus->status_id)selected @endif>{{$rStatus->status}}</option>
                        @endforeach
                        @endif
                    </select>
                  </dd>
                @endif
              </dl>
              </div>
            </div>
            <div class="box-footer">
              
              <div class="col-sm-12" align="center">
                    <button class="btn btn-primary" type="submit" >Submit</button>
                    <button class="btn btn-danger" type="button" onclick="window.location='{{ url("/administrator/list-orders") }}'">Cancel</button>
              </div>
            </div>
           
          </div>
          </form>
        </div> 
    </div>

</section>
@endsection
@push('scripts')

    <script
  src="https://code.jquery.com/jquery-1.12.4.js"
  integrity="sha256-Qw82+bXyGq6MydymqBxNPYTaUXXq7c8v3CwiYwLLNXU="
  crossorigin="anonymous"></script>

    <script>
        $(document).ready(function(){
            $("#order_status").change(function(){
                var ready_to_ship_id = $("#ready_to_ship_status_id").val();
                var shipped_status_id = $("#shipped_status_id").val();
                var delivered_status_id = $("#delivered_status_id").val();
                var cancelled_status_id = $("#cancelled_status_id").val();
                var refund_status_id = $("#refund_status_id").val();
                var order_status = $("#order_status option:selected").val();

                if(order_status == ready_to_ship_id){
                    $("#ready_to_ship_div").show();
                }else if (shipped_status_id == order_status) {
                    $("#courier_div").show();
                }else if(order_status == delivered_status_id){
                    $("#courier_div").show();
                    $("#delivered_to_div").show();
                    $("#delivery_date_div").show();
                }else if(order_status == cancelled_status_id){
                    $("#cancellation_reason_div").show();
                    $("#ready_to_ship_div").hide();
                }else if(order_status == refund_status_id){
                    $("#cancellation_reason_div").show();
                    $("#refund_amount_div").show();
                    $("#refund_bank_id_div").show();
                    $("#bank_reference_div").show();
                }else if(order_status<shipped_status_id){
                    $("#courier_div").hide();
                    $("#delivered_to_div").hide();
                    $("#delivery_date_div").hide();
                }
            });
        });
    </script>

@endpush

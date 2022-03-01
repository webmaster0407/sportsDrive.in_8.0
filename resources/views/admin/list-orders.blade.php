@extends('layouts.admin')
@push('stylesheets')
<link src="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
@endpush
@push('scripts')
<script type="text/javascript" src="{{ asset("/js/setCommon.js")}}"></script>
<script src="{{ asset("plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
@endpush
@section('content')
<section class="content-header">
    <h1>
       List Orders
    </h1>
    <ol class="breadcrumb">
        <li><a href="home"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">List Orders</li>
    </ol>
</section>
<section class="content">
  <div class="row">
      <div class="col-md-12">
        <div class="box box-solid">
          <div class="box-header with-border">
            <h3 class="box-title">Search Orders By Filter</h3>
            
              <button class="btn btn-sm btn-primary " type="button"  onclick="window.location='/administrator/list-orders'" style="float: right;"><i class="fa fa-arrow-circle-left"></i> All Orders</button>
            
          </div>
          <div class="box-body">
              <div class="row">
                <form name="searchOrder" id="searchOrder" method="post"
                @if($customer!= null) action="/administrator/list-orders/{{$customer->id}}
                 @else action="/administrator/list-orders" @endif">
                  <input type="hidden" name="_token" value="{{ csrf_token() }}">
                  <div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label" for="status">Order status :</label>
                        <select id="byOrderStatus" name="byOrderStatus" value="" placeholder="Status" class="form-control">
                        <option value="0">Select status</option>
                        @if($allOrderStatus !== null)
                        @foreach($allOrderStatus as $oStatus)
                            @if (isset($oStatus))
                            <option value="{{$oStatus->status_id}}" @if($byOrderStatus == $oStatus->status_id) selected @endif >
                              {{$oStatus->status}}
                            </option>
                            @endif
                        @endforeach
                        @endif
                        </select>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label" for="status">Payment status :</label>
                        <select id="byPaymentStatus" name="byPaymentStatus" value="" placeholder="Status" class="form-control">
                          <option value="0">Select status</option>
                          @if($allPaymentStatus !== null)
                            @foreach($allPaymentStatus as $pStatus)
                              @if (isset($pStatus))
                                <option value="{{$pStatus->status_id}}" @if($byPaymentStatus == $pStatus->status_id) selected @endif >
                                  {{$pStatus->status}}
                                </option>
                              @endif
                           @endforeach
                          @endif
                        </select>
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                        <label>Date range :</label>
                        <div class="input-group">
                            <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                            </div>
                        <input type="text" name="dateRange" class="form-control pull-right" id="reservation">
                        </div>
                    </div>
                  </div>
                  <div class="col-sm-2" ><button class="btn btn-success btn-sm" type="submit" name="search" value="search" style="margin-top: 25px;">Search</button></div>   
                </form>
              </div>
          </div>
        </div>
      </div>
  </div>
  <form method="POST" id="frmOrders" name="frmOrders" action="change-status-Orders">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
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
                <div class="box">
                  <input type="hidden" name="_token" value="{{ csrf_token() }}">
                  <div class="box-header">
                    <div class="col-md-9 col-sm-9">
                      @if($customer!== null)
                      <h4>Orders Of customer <b>{{$customer->first_name}} {{$customer->last_name}}</b></h4>
                      @endif
                    </div>  
                  </div>
                  <!-- /.box-header -->
                  <div class="box-body">
                    <table id="OrdersTable" class="table table-bordered table-striped">
                      <thead>
                      <tr>
                        <th>SrNo.</th>
                        <th>Order No</th>
                        <th>Customer Name</th>
                        <th>Order Status</th>
                        <th>Payment Status</th>
                        <th>Order Amount</th>
                        <th>Ordered On</th>
                      </tr>
                      </thead>
                      <tbody>
                      @if ( $orders !== null )
                        <?php $i = 1;  ?>
                        @foreach ($orders as $Orders)
                          <tr >
                            <td>{{$i}}</td>
                            <td> <a href="/administrator/view-Orders/{{$Orders->id}}">{{$userShownOrderId[$Orders->id]}}</a></td>
                            <td>
                              @if($customer!== null)
                                {{$customer->first_name}} {{$customer->last_name}}
                              @else
                                {{$Orders->first_name}} {{$Orders->last_name}}
                              @endif
                            </td>
                            <td 
                              @if( isset($orderStatus[$Orders->id]) && ($orderStatus[$Orders->id]['status']=="Failed") )  
                                style="color: red" 
                              @endif
                            >
                              @if( isset($orderStatus[$Orders->id]) && $orderStatus[$Orders->id]['status'] == "Pending") 
                                {{"Order Received"}}
                              @elseif( isset($orderStatus[$Orders->id]) )
                                {{$orderStatus[$Orders->id]['status']}} 
                              @endif
                            </td>
                            <td @if(isset($paymentStatus[$Orders->id]) && $paymentStatus[$Orders->id]['slug']=="failed") style="color: red" @endif>
                              @if ( isset($paymentStatus[$Orders->id]) )
                                {{$paymentStatus[$Orders->id]['status']}}
                              @endif
                            </td>
                            <td>
                              {{number_format($Orders->total,2)}}
                            </td>
                            <td>
                              <?php echo  date('d-M-Y H:i:s',strtotime($Orders->order_date)) ?>
                            </td>
                          </tr>
                        <?php $i++; ?>
                        @endforeach
                      @endif
                      </tbody>
                    </table>
                  </div>
                  <!-- /.box-body -->
                </div>

            </div>
        </div>
    </div>
  </form>
</section>

<script>
$("#checkAll").change(function () {
    $("input:checkbox").prop('checked', $(this).prop("checked"));
});
</script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script type="text/javascript">
  $('#reservation').daterangepicker({
      startDate: moment().add(-30, 'day'),
      endDate: moment().add(0, 'day'),
    locale: {
            format: 'DD/MM/YYYY'
        } 
  });
  $( '#reservation').datepicker({ defaultDate: new Date()-30});
  // $('#reservation').daterangepicker({ timePicker: true, timePickerIncrement: 30, format: 'DD/MM/YYYY h:mm A' })
</script>

<script>
    $(function () {
        $('#OrdersTable').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "scrollX": true,
            "columnDefs": [ { "orderable": false },{ "sType": "title-string", "aTargets": [] }  ],
            "order": [[ 6, 'desc' ]]
        });
    });
</script>
@endsection

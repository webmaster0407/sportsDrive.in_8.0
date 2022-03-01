@extends('layouts.admin')

@push('scripts')
    <script type="text/javascript" src="{{ asset("/js/setCommon.js")}}"></script>
    <script src="{{ asset("plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>

@endpush

@section('content')
    <script language="javascript" type="text/javascript" src="{{ asset("/js/setCommon.js")}}"></script>
    <form method="POST" id="frmCustomer" name="frmCustomer" action="change-status-partner">
        <section class="content-header">
            <h1>
                List Partner
            </h1>
            <ol class="breadcrumb">
                <li><a href="home"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li class="active">List Partner</li>
            </ol>
        </section>
        <section class="content">
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
                            <input type="hidden" name="partner_id" id="partner_id" value="{{$id}}">
                            <div class="box-header">
                                <div class="col-md-2 col-sm-2"><h4>Sales Window : </h4></div>
                                <div class="col-md-4 col-sm-4">
                                     <select name="sales_window" class="form-control" id="sales_window">
                                    @foreach($salesWindows as $salesWindow)
                                        <option @if($slot_id == $salesWindow->id) {{"selected"}} @endif value="{{$salesWindow->id}}">{{date("F j, Y   ",strtotime($salesWindow->start_date)) }} - {{date("F j, Y",strtotime($salesWindow->end_date))}}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <table id="customerTable" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Order No.</th>
                                        <th>Customer Name</th>
                                        <th>Product Name</th>
                                        <th>City</th>
                                        <th>Date Purchased</th>
                                        <th>Quantity Purchased</th>
                                        <th>Price / Unit WithOut GST</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $i = 1;  ?>
                                    @foreach ($currentSalesOrders as $key=>$currentSalesOrder)
                                        <tr>
                                            <td><a target="_blank" href="/administrator/view-Orders/{{$currentSalesOrder['order_id']}}"> {{$currentSalesOrder['order_id']}}</a></td>
                                            <td>{{$currentSalesOrder['first_name']}} {{$currentSalesOrder['last_name']}} </td>
                                            <td>{{$currentSalesOrder['product_name']}}</td>
                                            <td>{{json_decode($currentSalesOrder['shipping_address'])->city}}</td>
                                            <td>{{date("F j, Y   ",strtotime($currentSalesOrder['order_date']))}} </td>
                                            <td>{{$currentSalesOrder["quantity"]}}</td>
                                            <td>{{$currentSalesOrder["price_without_gst"]}}</td>
                                        </tr>
                                        <?php $i++; ?>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.box-body -->
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </form>
    <script>
        $("#checkAll").change(function () {
            $("input:checkbox").prop('checked', $(this).prop("checked"));
        });
    </script>
    <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>

    <script>
        $(function () {
            $('#customerTable').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "scrollX": true,
                "columnDefs": [ { "targets": [0], "orderable": false },{ "sType": "title-string", "aTargets": [5] }  ],
                "order": [[ 1, 'asc' ]]
            });
        });
        $("#sales_window").change(function () {
            var slotId = $(this).val();
            var partnerId = $("#partner_id").val();
            var APP_URL = {!! json_encode(url('/')) !!}
            var url = APP_URL+"/administrator/partner-sales-details/"+partnerId+"/"+slotId
           window.location = url;
        });
    </script>
@endsection

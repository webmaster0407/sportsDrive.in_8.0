@extends('layouts.admin')

@push('scripts')
<script type="text/javascript" src="{{ asset("/js/setCommon.js")}}"></script>
<script src="{{ asset("plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>

@endpush

@section('content')
<script language="javascript" type="text/javascript" src="{{ asset("/js/setCommon.js")}}"></script>
<form method="POST" id="frmCustomer" name="frmCustomer" action="change-status-customer">
<section class="content-header">
    <h1>
       List Customer
    </h1>
    <ol class="breadcrumb">
        <li><a href="home"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">List Customer</li>
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
                @php
                    $msgData = null;
                    $msgData= session("msgData");
                @endphp
                @if($msgData != null)
                        <div class="alert alert-success">
                        {{$msgData['successCount']." "." customers has been successfully uploaded"}}
                    </div>
                @endif
                @if($msgData != null)
                    <div class="alert alert-danger">
                        @php $errorMsg = $msgData['errorCount']." "."customers has not been uploaded as they don't have valid email address OR They are already uploaded"; @endphp
                        {{$errorMsg}}
                    </div>
                @endif
                @if($msgData != null)
                    <div class="alert alert-danger">
                        {{$msgData['noEmail']." "."customers has not been uploaded as they don't have email address."}}
                    </div>
                @endif
                <div class="box">
                  <input type="hidden" name="_token" value="{{ csrf_token() }}">
                  <div class="box-header">
                    <div class="col-md-6 col-sm-6">
                      <p>
                            @if((count($data)) != 0)
                            <button class="btn btn-success btn-sm" type="button" name="active" value="Activate" onClick="JavaScript:CallOperation(this.value,'frmCustomer','chk[]');">Active</button>

                            <button class="btn btn-success btn-sm" type="button" name="de-active" value="De-Activate" onClick="JavaScript:CallOperation(this.value,'frmCustomer','chk[]');">Deactive</button>

                            <button class="btn btn-danger btn-sm" type="button" name="delete" value="Delete" onClick="JavaScript:CallOperation(this.value,'frmCustomer','chk[]');">Delete</button>
                            @endif
                            <input type="hidden" name="operationFlag" value="">
                      </p>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <button class="btn btn-sm btn-primary " type="button" name="add" value="Add" onClick="window.location='/administrator/add-customer'" style="float: right; margin-left: 10px;"> Add Customer</button>

                        <button class="btn btn-sm btn-primary " type="button" name="add" value="Add" onClick="window.location='/administrator/download-customer-data'" style="float: right; margin-left: 10px;">Download customer data</button>

                        <button class="btn btn-sm btn-primary " type="button" name="add" value="Add" onClick="window.location='/administrator/upload-customer-csv'" style="float: right;">Upload Customer Excel</button>
                    </div>
                  </div>
                  <!-- /.box-header -->
                  <div class="box-body">
                    <table id="customerTable" class="table table-bordered table-striped">
                      <thead>
                      <tr>
                        <td><input type="checkbox" id="checkAll" name="chkAll" value="checkbox" onClick="JavaScript:CheckAll('frmCustomer', 'chkAll', 'chk[]');"></td>
                        <th>SrNo.</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Orders</th>
                        <th>Login</th>
                        <th>IS Active</th>
                        <th>Addresses</th>
                        <th>Registered On</th>
                       
                      </tr>
                      </thead>
                      <tbody>
                      <?php $i = 1;  ?>
                        @foreach ($data as $customer)
                          <tr>
                            <td><div class="i-checks"><label><input type="checkbox" name="chk[]" value="{{$customer->id}}" class="text" /></label></div></td>
                            <td>{{$i}}</td>
                            <td> <a href="/administrator/edit-customer/{{$customer->id}}">{{$customer->first_name}} {{$customer->last_name}}</a></td>
                            <td> {{$customer->email_address}}</td>
                            <td><a href="/administrator/list-orders/{{$customer->id}}">{{$orderCount[$customer->id]}}</a></td>
                            <td><a target="_blank" href="/administrator/auto-login-customer/{{base64_encode($customer->id)}}">{{"Login"}}</a></td>
                              <td class="center">@if($customer->is_active=='Y') <img src="{{{ URL::asset('img/tick.png')}}}" /> @else <img src="{{{ URL::asset('img/cross.png')}}}"/> @endif</td>
                              <td><a href="/administrator/view-address/{{$customer->id}}">View({{$addressCount[$customer->id]}})</a></td>
                            <td><?php echo  date('d-M-Y',strtotime($customer->created_at)) ?></td>

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
</script>
@endsection

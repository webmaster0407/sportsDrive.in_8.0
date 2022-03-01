@extends('layouts.admin')

@push('scripts')
<script type="text/javascript" src="{{ asset("/js/setCommon.js")}}"></script>
<script src="{{ asset("plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>

@endpush

@section('content')
<script language="javascript" type="text/javascript" src="{{ asset("/js/setCommon.js")}}"></script>
<form method="POST" id="frmNewsletter" name="frmNewsletter" action="change-status-newsletter">
<section class="content-header">
    <h1>
       List Newsletter
    </h1>
    <ol class="breadcrumb">
        <li><a href="home"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">List Newsletter</li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <!-- <div class="box-header with-border">
                    <h3 class="box-title">List Newsletter</h3>
                </div> -->
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

                    <!-- <h3 class="box-title">All CMS Pages</h3> -->
                    <div class="col-md-9 col-sm-9">
                      <p>
                            @if((count($data)) != 0)
                            <button class="btn btn-success btn-sm" type="button" name="active" value="Activate" onClick="JavaScript:CallOperation(this.value,'frmNewsletter','chk[]');">Active</button>
                            <button class="btn btn-success btn-sm" type="button" name="de-active" value="De-Activate" onClick="JavaScript:CallOperation(this.value,'frmNewsletter','chk[]');">Deactive</button>

                            <button class="btn btn-danger btn-sm" type="button" name="delete" value="Delete" onClick="JavaScript:CallOperation(this.value,'frmNewsletter','chk[]');">Delete</button>

                            <button class="btn btn-success btn-sm" type="button" name="customers" value="Customers" onClick="JavaScript:CallOperation(this.value,'frmNewsletter','chk[]');">Send Newsletter to Customers</button>
                            <button class="btn btn-success btn-sm" type="button" name="subscribers" value="Subscribers" onClick="JavaScript:CallOperation(this.value,'frmNewsletter','chk[]');">Send Newsletter to Subscribers</button>
                            <div class="hr-line-dashed"></div>
                            <button class="btn btn-success btn-sm" type="button" name="customerandsubscribers" value="Customerandsubscribers" onClick="JavaScript:CallOperation(this.value,'frmNewsletter','chk[]');">Send Newsletter to Customers & Subscribers</button>

                            @endif
                            <input type="hidden" name="operationFlag" value="">
                      </p>
                    </div>
                    <div class="col-md-3 col-sm-3">
                    <button class="btn btn-sm btn-primary " type="button" name="add" value="Add" onClick="window.location='/administrator/add-newsletter'" style="float: right;"><i class='fa fa-plus-circle'></i> Add Newsletter</button>
                    </div>
                  </div>
                  <!-- /.box-header -->
                  <div class="box-body">
                    <table id="newsletterTable" class="table table-bordered table-striped">
                      <thead>
                      <tr>
                        <td><input type="checkbox" id="checkAll" name="chkAll" value="checkbox" onClick="JavaScript:CheckAll('frmNewsletter', 'chkAll', 'chk[]');"></td>
                        <th>SrNo.</th>
                        <th>Title</th>
                        <th>Subject</th>
                        <th>IS Active</th>
                        <th>Added On</th>
                       
                      </tr>
                      </thead>
                      <tbody>
                      <?php $i = 1;  ?>
                        @foreach ($data as $newsletter)
                          <tr>
                            <td><div class="i-checks"><label><input type="checkbox" name="chk[]" value="{{$newsletter->id}}" class="text" /></label></div></td>
                            <td>{{$i}}</td>
                            <td> <a href="/administrator/edit-newsletter/{{$newsletter->id}}">{{$newsletter->newsletter_name}}</a></td>
                            <td> {{$newsletter->newsletter_subject}}</td>
                            <td class="center">@if($newsletter->is_active=='Y') <img src="{{{ URL::asset('img/tick.png')}}}" /> @else <img src="{{{ URL::asset('img/cross.png')}}}"/> @endif</td>
                            <td><?php echo  date('d-M-Y',strtotime($newsletter->created_at)) ?>
                             </td>
                           
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
        $('#newsletterTable').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "scrollX": true,
            "columnDefs": [ { "targets": [0], "orderable": false },{ "sType": "title-string", "aTargets": [4] }  ],
            "order": [[ 1, 'asc' ]]
        });
    });
</script>
@endsection

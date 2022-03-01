@extends('layouts.admin')

@push('scripts')
<script type="text/javascript" src="{{ asset("/js/setCommon.js")}}"></script>
<script src="{{ asset("plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
@endpush

@section('content')
<form method="POST" id="frmCmsPages" name="frmCmsPages" action="/administrator/change-status-cms">
<input type="hidden" name="parent_id" value="{{$data->parent_id}}">
<section class="content-header">
    <h1>
       CMS Pages 
       <small> (@if($ParentData != null) Sub pages of {{$ParentData->page_title}} @else  Main Pages  @endif)</small>
    </h1>
    <ol class="breadcrumb">
         @include('admin.includes.breacrumb')
       
    </ol>
   
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <!-- <div class="box-header with-border"> -->
                    <!-- <h3 class="box-title">List CMS Pages Level {{$data->level_id}}</h3> -->
                <!-- </div> -->
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
                            <button class="btn btn-success btn-sm" type="button" name="active" value="Activate" onClick="JavaScript:CallOperation(this.value,'frmCmsPages','chk[]');">Active</button>
                            <button class="btn btn-success btn-sm" type="button" name="de-active" value="De-Activate" onClick="JavaScript:CallOperation(this.value,'frmCmsPages','chk[]');">Deactive</button>
                            <button class="btn btn-success btn-sm" type="button" name="setheader" value="Set As Header" onClick="JavaScript:CallOperation(this.value,'frmCmsPages','chk[]');">Set as Menu</button>
                            <button class="btn btn-success btn-sm" type="button" name="unsetheader" value="Unset As Header" onClick="JavaScript:CallOperation(this.value,'frmCmsPages','chk[]');">Unset from Menu</button>

                            <button class="btn btn-success btn-sm" type="button" name="setfooter" value="Set As Footer" onClick="JavaScript:CallOperation(this.value,'frmCmsPages','chk[]');">Set as Footer</button>
                            <button class="btn btn-success btn-sm" type="button" name="unsetfooter" value="Unset As Footer" onClick="JavaScript:CallOperation(this.value,'frmCmsPages','chk[]');">Unset from Footer</button>

                            <button class="btn btn-success btn-sm" type="button" name="setfeature" value="Set As Featured" onClick="JavaScript:CallOperation(this.value,'frmCmsPages','chk[]');">Set as Featured</button>

                            <div class="hr-line-dashed"></div>

                            <button class="btn btn-success btn-sm" type="button" name="unsetfeature" value="Unset As Featured" onClick="JavaScript:CallOperation(this.value,'frmCmsPages','chk[]');">Unset from Featured</button>

                            <button class="btn btn-danger btn-sm" type="button" name="delete" value="Delete" onClick="JavaScript:CallOperation(this.value,'frmCmsPages','chk[]');">Delete</button>
                            @endif
                            <input type="hidden" name="operationFlag" value="">
                      </p>
                    </div>
                    <div class="col-md-3 col-sm-3">
                      <button class="btn btn-sm btn-primary" type="button" name="add" value="Add" onClick="window.location='/administrator/add-pages/{{$data->parent_id}}'" style="float: right;"><i class='fa fa-plus-circle'></i> Add CMS Pages</button>
                    </div>

                  </div>
                  <!-- /.box-header -->
                  <div class="box-body">

                    <table id="cmsTable" class="table table-bordered table-striped">
                      <thead>
                      <tr>
                        <td><input type="checkbox" id="checkAll" name="chkAll" value="checkbox" onClick="JavaScript:CheckAll('frmCmsPages', 'chkAll', 'chk[]');"></td>
                        <th>SrNo.</th>
                        <th>Page Title</th>
                        <!--<th>SubPages</th>-->
                        <th>IS Active</th>
                        <th>IS Menu</th>
                        <th>IS Featured <br><small>(Max 3)</small></th>
                        <th>IS Footer</th>
                        <th>Added On</th>
                        <th>Order</th>
                      </tr>
                      </thead>
                      <tbody>
                      <?php $i = 1;  ?>
                        @foreach ($data as $cmspage)
                          <tr>
                            <td><div class="i-checks"><label><input type="checkbox" name="chk[]" value="{{$cmspage->page_id}}" class="text"/></label></div></td>
                            <td>{{$i}}</td>
                            <td> <a href="/administrator/edit-page/{{$cmspage->page_id}}">{{$cmspage->page_title}}</a></td>

                            <td class="center">@if($cmspage->is_active=='Y') <img src="{{{ URL::asset('img/tick.png')}}}" /> @else <img src="{{{ URL::asset('img/cross.png')}}}"/> @endif</td>
                            <td class="center">@if($cmspage->is_header=='Y') <img src="{{{ URL::asset('img/tick.png')}}}" /> @else <img src="{{{ URL::asset('img/cross.png')}}}"/> @endif</td>
                            <td class="center">@if($cmspage->is_featured=='Y') <img src="{{{ URL::asset('img/tick.png')}}}" /> @else <img src="{{{ URL::asset('img/cross.png')}}}"/> @endif</td>
                            <td class="center">@if($cmspage->is_footer=='Y') <img src="{{{ URL::asset('img/tick.png')}}}" /> @else <img src="{{{ URL::asset('img/cross.png')}}}"/> @endif</td>

                            <td><?php echo  date('d-M-Y',strtotime($cmspage->created_at)) ?>
                             </td>
                            <td class="center"> 
                                @if($i!=1)
                                <a href="/administrator/pages/order-up/{{base64_encode($cmspage->page_id)}}/{{$cmspage->sort_order}}" class="success"><i class="fa fa-arrow-up"></i></a>
                                @endif
                                &nbsp;
                                @if($i!=count($data))
                                <a href="/administrator/pages/order-down/{{base64_encode($cmspage->page_id)}}/{{$cmspage->sort_order}}" class="success "><i class="fa fa-arrow-down"></i></a>
                                @endif
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
<script src="{{ asset("plugins/datatables/jquery.dataTables.min.js") }}"></script>
<script src="{{ asset("plugins/datatables/dataTables.bootstrap.min.js") }}"></script>
<script>
$(function () {
  $('#cmsTable').DataTable({
    "paging": true,
    "lengthChange": true,
    "searching": true,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "scrollX": true,
    "columnDefs": [ { "targets": [0], "orderable": false },{ "sType": "title-string", "aTargets": [3,4,5,6] }  ],
    "order": [[ 1, 'asc' ]]
  });
});
</script>
@endsection

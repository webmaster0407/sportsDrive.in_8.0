@extends('layouts.admin')

@push('scripts')
<script type="text/javascript" src="{{ asset("/js/setCommon.js")}}"></script>
<script src="{{ asset("plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
@endpush

@section('content')
<form method="POST" id="frmCategories" name="frmCategories" action="/administrator/change-status-category">
<input type="hidden" name="parent_id" value="{{$data->parent_id}}">
<input type="hidden" name="level_id" value="{{$data->level_id}}">

<section class="content-header">
    <h1>
       Categories  
       <small> (@if($ParentData != null) Sub categories of {{$ParentData->name}} @else  Main Categories  @endif)</small>
    </h1>
    <ol class="breadcrumb">
        @include('admin.includes.breacrumb-category')
    </ol>
   
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <!-- <div class="box-header with-border"> -->
                    <!-- <h3 class="box-title">List Category Pages Level {{$data->level_id}}</h3> -->
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
                    <!-- <h3 class="box-title">All Category Pages</h3> -->
                    <div class="col-md-9 col-sm-9">
                      <p>
                            @if((count($data)) != 0)
                            <button class="btn btn-success btn-sm" type="button" name="active" value="Activate" onClick="JavaScript:CallOperation(this.value,'frmCategories','chk[]');">Active</button>
                            <button class="btn btn-success btn-sm" type="button" name="de-active" value="De-Activate" onClick="JavaScript:CallOperation(this.value,'frmCategories','chk[]');">Deactive</button>
                            <button class="btn btn-success btn-sm" type="button" name="setheader" value="Set As Header" onClick="JavaScript:CallOperation(this.value,'frmCategories','chk[]');">Set as Header</button>
                            <button class="btn btn-success btn-sm" type="button" name="unsetheader" value="Unset As Header" onClick="JavaScript:CallOperation(this.value,'frmCategories','chk[]');">Unset from Header</button>

                            <button class="btn btn-success btn-sm" type="button" name="settop" value="Set As Top" onClick="JavaScript:CallOperation(this.value,'frmCategories','chk[]');">Set as Top</button>
                            <button class="btn btn-success btn-sm" type="button" name="unsettop" value="Unset As Top" onClick="JavaScript:CallOperation(this.value,'frmCategories','chk[]');">Unset from Top</button>

                            <button class="btn btn-success btn-sm" type="button" name="setbottom" value="Set As Bottom" onClick="JavaScript:CallOperation(this.value,'frmCategories','chk[]');">Set as Bottom</button>
                             <div class="hr-line-dashed"></div>
                            <button class="btn btn-success btn-sm" type="button" name="unsetbottom" value="Unset As Bottom" onClick="JavaScript:CallOperation(this.value,'frmCategories','chk[]');">Unset from Bottom</button>

                            <button class="btn btn-danger btn-sm" type="button" name="delete" value="Delete" onClick="JavaScript:CallOperation(this.value,'frmCategories','chk[]');">Delete</button>
                            @endif
                            <input type="hidden" name="operationFlag" value="">
                      </p>
                    </div>
                    <div class="col-md-3 col-sm-3">
                      <button class="btn btn-sm btn-primary" type="button" name="add" value="Add" onClick="window.location='/administrator/add-categories/{{$data->parent_id}}'" style="float: right;"><i class='fa fa-plus-circle'></i> Add Category</button>
                    </div>

                  </div>
                  <!-- /.box-header -->
                  <div class="box-body">

                    <table id="categoryTable" class="table table-bordered table-striped">
                      <thead>
                      <tr>
                        <td><input type="checkbox" id="checkAll" name="chkAll" value="checkbox" onClick="JavaScript:CheckAll('frmCategories', 'chkAll', 'chk[]');"></td>
                        <th>Sr No.</th>
                        <th>Category Id</th>
                        <th>Name</th>
                        <th>Sub Categories</th>
                        <th>Products Count</th>
                        <th>IS Active</th>
                        <th>IS Header</th>
                        <th>IS Top <br><small>(Max 6)</small></th>
                        <th>IS Bottom <br><small>(Max 6)</small></th>
                        <th>Added On</th>
                        <th>Order</th>

                      </tr>
                      </thead>
                      <tbody>
                       
                      <?php $i = 1;  ?>
                        @foreach ($data as $category)
                          <tr>
                            <td><div class="i-checks"><label><input type="checkbox" name="chk[]" value="{{$category->id}}" class="text"/></label></div></td>
                            <td>{{$i}}</td>
                              <td>{{$category->id}}</td>
                            <td> <a href="/administrator/edit-category/{{$category->id}}">{{$category->name}}</a></td>
                               <?php $lid = $category->level_id +1 ;?>
                            @if($lid < 3)
                              <td><a href="/administrator/list-categories/{{$category->id}}/{{$lid}}">View ({{$count[$category->id]}})</a></td>
                            @else
                              <td>NA</td>
                            @endif
                              <td>{{$category->productCount}}</td>
                            <td class="center">@if($category->is_active=='Y') <img src="{{{ URL::asset('img/tick.png')}}}" /> @else <img src="{{{ URL::asset('img/cross.png')}}}"/> @endif</td>
                            <td class="center">@if($category->is_header=='Y') <img src="{{{ URL::asset('img/tick.png')}}}" /> @else <img src="{{{ URL::asset('img/cross.png')}}}"/> @endif</td>
                            <td class="center">@if($category->is_top=='Y') <img src="{{{ URL::asset('img/tick.png')}}}" /> @else <img src="{{{ URL::asset('img/cross.png')}}}"/> @endif</td>
                            <td class="center">@if($category->is_bottom=='Y') <img src="{{{ URL::asset('img/tick.png')}}}" /> @else <img src="{{{ URL::asset('img/cross.png')}}}"/> @endif</td>
                            

                            <td><?php echo  date('d-M-Y',strtotime($category->created_at)) ?>
                             </td>
                            <td class="center"> 
                                @if($i!=1)
                                <a href="/administrator/categories/order-up/{{base64_encode($category->id)}}/{{$category->sort_order}}/{{$data->level_id}}/{{$data->parent_id}}" class="success"><i class="fa fa-arrow-up"></i></a>
                                @endif
                                &nbsp;
                                @if($i!=count($data))
                                <a href="/administrator/categories/order-down/{{base64_encode($category->id)}}/{{$category->sort_order}}/{{$data->level_id}}/{{$data->parent_id}}" class="success "><i class="fa fa-arrow-down"></i></a>
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
  $('#categoryTable').DataTable({
    "paging": true,
    "lengthChange": true,
    "searching": true,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "scrollX": true,
    "columnDefs": [ { "targets": [0], "orderable": false },{ "sType": "title-string", "aTargets": [5,6,7,8] }  ],
    "order": [[ 1, 'asc' ]]
  });
});
</script>
@endsection

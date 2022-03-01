@extends('layouts.admin')

@push('scripts')
<script type="text/javascript" src="{{ asset("/js/setCommon.js")}}"></script>
<script src="{{ asset("plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>

<!-- for  highslide  -->
<script type="text/javascript" src="{{asset('../highslide/highslide-with-gallery.js')}}"></script>
<link rel="stylesheet" type="text/css" href="{{asset('../highslide/highslide.css')}}" />
<script type="text/javascript">
hs.graphicsDir = '../../highslide/graphics/';
hs.align = 'center';
hs.transitions = ['expand', 'crossfade'];
hs.outlineType = 'rounded-white';
hs.fadeInOut = true;
//hs.dimmingOpacity = 0.75;

// Add the controlbar
hs.addSlideshow({
    //slideshowGroup: 'group1',
    interval: 5000,
    repeat: false,
    useControls: true,
    fixedControls: 'fit',
    overlayOptions: {
        opacity: .75,
        position: 'bottom center',
        hideOnMouseOut: true
    }
});
</script>
@endpush

@section('content')
<script language="javascript" type="text/javascript" src="{{ asset("/js/setCommon.js")}}"></script>
<form method="POST" id="frmBrands" name="frmBrands" action="change-status-brand">
<section class="content-header">
    <h1>
       List Brands
    </h1>
    <ol class="breadcrumb">
        <li><a href="home"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">List Brands</li>
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
                  <div class="box-header">
                    <div class="col-md-9 col-sm-9">
                      <p>
                            @if((count($data)) != 0)
                            <button class="btn btn-success btn-sm" type="button" name="active" value="Activate" onClick="JavaScript:CallOperation(this.value,'frmBrands','chk[]');">Active</button>
                            <button class="btn btn-success btn-sm" type="button" name="de-active" value="De-Activate" onClick="JavaScript:CallOperation(this.value,'frmBrands','chk[]');">Deactive</button>

                           <!--  <button class="btn btn-danger btn-sm" type="button" name="delete" value="Delete" onClick="JavaScript:CallOperation(this.value,'frmBrands','chk[]');">Delete</button> -->
                            @endif
                            <input type="hidden" name="operationFlag" value="">
                      </p>
                    </div>
                    <div class="col-md-3 col-sm-3">
                    <button class="btn btn-sm btn-primary " type="button" name="add" value="Add" onClick="window.location='/administrator/add-brand'" style="float: right;"><i class='fa fa-plus-circle'></i> Add Brands</button>
                    </div>
                  </div>
                  <!-- /.box-header -->
                  <div class="box-body">
                    <table id="bannerTable" class="table table-bordered table-striped">
                      <thead>
                      <tr>
                        <td><input type="checkbox" id="checkAll" name="chkAll" value="checkbox" onClick="JavaScript:CheckAll('frmBrands', 'chkAll', 'chk[]');"></td>
                        <th>SrNo.</th>
                        <th>Brand </th>
                        <th>Image</th>
                        <th>IS Active</th>
                        <th>Added On</th>
                        <th>Order</th>
                      </tr>
                      </thead>
                      <tbody>
                      <?php $i = 1;  ?>
                        @foreach ($data as $brand)
                          <tr>
                            <td><div class="i-checks"><label><input type="checkbox" name="chk[]" value="{{$brand->id}}" class="text" /></label></div></td>
                            <td>{{$i}}</td>
                            <td> <a href="/administrator/edit-brand/{{$brand->id}}">{{$brand->name}}</a></td>
                            <td>
                              @if($brand->image != null || $brand->image != '')
                              <a href="{{{ URL::asset('uploads/brand/'.$brand->image)}}}" class="highslide" onclick="return hs.expand(this)"  rel="thumbnail" title="{{$brand->name}}">
                                 <img src="{{{ URL::asset('uploads/brand/'.$brand->image)}}}" class="img-thumbnail" height="30" width="60"/>
                              </a>
                              @else
                                  <img src="{{{ URL::asset('images/no_image_available.png')}}}" class="img-thumbnail" height="40" width="60"/>
                              @endif
                            </td>
                            <td class="center">@if($brand->is_active=='Y') <img src="{{{ URL::asset('img/tick.png')}}}" /> @else <img src="{{{ URL::asset('img/cross.png')}}}"/> @endif</td>
                            <td><?php echo  date('d-M-Y',strtotime($brand->created_at)) ?>
                             </td>
                            <td class="center"> 
                                @if($i!=1)
                                <a href="/administrator/brand/order-up/{{base64_encode($brand->id)}}/{{$brand->sort_order}}" class="success"><i class="fa fa-arrow-up"></i></a>
                                @endif
                                &nbsp;
                                @if($i!=count($data))
                                <a href="/administrator/brand/order-down/{{base64_encode($brand->id)}}/{{$brand->sort_order}}" class="success "><i class="fa fa-arrow-down"></i></a>
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
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>

<script>
    $(function () {
        $('#bannerTable').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "scrollX": true,
            "columnDefs": [ { "targets": [0,3], "orderable": false },{ "sType": "title-string", "aTargets": [4] }  ],
            "order": [[ 1, 'asc' ]]
        });
    });
</script>
@endsection

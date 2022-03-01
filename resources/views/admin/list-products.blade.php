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
<form method="POST" id="frmProducts" name="frmProducts" action="change-status-products">
<section class="content-header">
    <h1>
       List Products
    </h1>
    <ol class="breadcrumb">
        <li><a href="home"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">List Products</li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <!-- <div class="box-header with-border">
                    <h3 class="box-title">List Products</h3>
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
                            <button class="btn btn-success btn-sm" type="button" name="active" value="Activate" onClick="JavaScript:CallOperation(this.value,'frmProducts','chk[]');">Active</button>
                            <button class="btn btn-success btn-sm" type="button" name="de-active" value="De-Activate" onClick="JavaScript:CallOperation(this.value,'frmProducts','chk[]');">Deactive</button>

                            <button class="btn btn-danger btn-sm" type="button" name="delete" value="Delete" onClick="JavaScript:CallOperation(this.value,'frmProducts','chk[]');">Delete</button>
                            <button class="btn btn-success btn-sm" type="button" name="setfeature" value="Set As Featured" onClick="JavaScript:CallOperation(this.value,'frmProducts','chk[]');">Set as Featured</button>
                            <button class="btn btn-success btn-sm" type="button" name="unsetfeature" value="Unset As Featured" onClick="JavaScript:CallOperation(this.value,'frmProducts','chk[]');">Unset from Featured</button>

                            <button class="btn btn-success btn-sm" type="button" name="setfeature" value="Set As New" onClick="JavaScript:CallOperation(this.value,'frmProducts','chk[]');">Set as New</button>
                            <button class="btn btn-success btn-sm" type="button" name="unsetfeature" value="Unset As New" onClick="JavaScript:CallOperation(this.value,'frmProducts','chk[]');">Unset from New</button>

                            @endif
                            <input type="hidden" name="operationFlag" value="">
                      </p>
                    </div>
                    <div class="col-md-3 col-sm-3">
                    <button class="btn btn-sm btn-primary " type="button" name="add" value="Add" onClick="window.location='/administrator/add-products'"><i class='fa fa-plus-circle'></i> Add Products</button>&nbsp;&nbsp; &nbsp;&nbsp;
                    <button class="btn btn-sm btn-primary " type="button" name="upload-excel" value="Upload Excel" onClick="window.location='/administrator/upload-excel'" style="float: right;"><i class='fa fa-plus-circle'></i>Upload Excel</button>
                    </div>
                  
                  </div>
                  <!-- /.box-header -->
                  <div class="box-body">
                    <table id="productTable" class="table table-bordered table-striped">
                      <thead>
                      <tr>
                        <td><input type="checkbox" id="checkAll" name="chkAll" value="checkbox" onClick="JavaScript:CheckAll('frmProducts', 'chkAll', 'chk[]');"></td>
                        <th>SrNo.</th>
                        <th>Name</th>
                        <th>SKU</th>
                        <th>Image</th>
                        <th>IS Active</th>
                        <th>Is Featured</th>
                        <th>IS New</th>
                       
                        <th>Is Completed</th>
                        <th>Added On</th>
                        <th>Order</th>

                      </tr>
                      </thead>
                      <tbody>
                      <?php $i = 1;  ?>
                        @foreach ($data as $products)
                          <tr>
                            <td><div class="i-checks"><label><input type="checkbox" name="chk[]" value="{{$products->id}}" class="text" /></label></div></td>
                            <td>{{$i}}</td>
                            <td> <a href="/administrator/edit-products/{{$products->id}}">{{$products->name}}</a></td>
                            <td>{{$products->sku}}</td>
                            <td>
                              @if($products->image != null || $products->image != '')
                              <a href="{{{ URL::asset('uploads/products/images/'.$products->id.'/'.$products->image)}}}" class="highslide" onclick="return hs.expand(this)"  rel="thumbnail" title="{{$products->name}}">
                                 <img src="{{{ URL::asset('uploads/products/images/'.$products->id.'/'.$products->image)}}}" class="img-thumbnail" width="60"/>
                              </a>
                              @else
                                  <img src="{{{ URL::asset('images/no_image_available.png')}}}" class="img-thumbnail" width="60"/>
                              @endif
                            </td>
                            <td class="center">@if($products->is_active=='Y') <img src="{{{ URL::asset('img/tick.png')}}}" /> @else <img src="{{{ URL::asset('img/cross.png')}}}"/> @endif</td>

                            
                            <td class="center">@if($products->is_featured=='Y') <img src="{{{ URL::asset('img/tick.png')}}}" /> @else <img src="{{{ URL::asset('img/cross.png')}}}"/> @endif</td>
                            <td class="center">@if($products->is_new=='Y') <img src="{{{ URL::asset('img/tick.png')}}}" /> @else <img src="{{{ URL::asset('img/cross.png')}}}"/> @endif</td>
                            
                            <td class="center">@if($products->is_completed=='Y') <img src="{{{ URL::asset('img/tick.png')}}}" /> @else <img src="{{{ URL::asset('img/cross.png')}}}"/> @endif</td>
                           
                            <td><?php echo  date('d-M-Y',strtotime($products->created_at)) ?>
                             </td>
                            <td class="center"> 
                                @if($i!=1)
                                <a href="/administrator/products/order-up/{{base64_encode($products->id)}}/{{$products->sort_order}}" class="success"><i class="fa fa-arrow-up"></i></a>
                                @endif
                                &nbsp;
                                @if($i!=count($data))
                                <a href="/administrator/products/order-down/{{base64_encode($products->id)}}/{{$products->sort_order}}" class="success "><i class="fa fa-arrow-down"></i></a>
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
        $('#productTable').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "scrollX": true,
            "columnDefs": [ { "targets": [0,4], "orderable": false },{ "sType": "title-string", "aTargets": [5,6,7,8,9,10] }  ],
            "order": [[ 1, 'asc' ]]
        });
    });
</script>
@endsection

@extends('layouts.admin')
@push('stylesheets')
  <!-- DataTables -->
  <link rel="stylesheet" href="../../plugins/datatables/dataTables.bootstrap.css">
  <link href="{{{ URL::asset('css/select2.min.css')}}}" rel="stylesheet">
@endpush
@push('scripts')
<script src="{{ asset("plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
@endpush
@section('content')
<section class="content-header">
    <h1>
        Products
    </h1>
    <ol class="breadcrumb">
        <li><a href="home"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="/administrator/list-products">List Products</a></li>
        <li class="active"> @if($data->mode == 'edit')Edit @else Add @endif Products</li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"> @if($data->mode == 'edit')Edit @else Add @endif  Products</h3>
                </div>

                <div class="box">
                  <div class="row">
                    <div class="col-md-12">
                      <!-- Custom Tabs -->
                      <div class="nav-tabs-custom" style="margin-bottom:0px;">
                        <ul class="nav nav-tabs">
                          <li class="active"><a href="#">Basic Details</a></li>
                          @if($data->mode == 'edit')
                          <li><a href="/administrator/edit-products/step2/{{$data->id}}">Product Information</a></li>
                           @if($data->completed_step >=2)
                          <li><a href="/administrator/edit-products/step3/{{$data->id}}">Configuration</a></li>
                           @endif
                          @endif
                          
                        </ul>
                        <div class="tab-content">
                          <div class="tab-pane active" id="tab_1">
                            <!-- add form here -->
                            <form class="form-horizontal" method="POST" enctype="multipart/form-data" name="frmProducts" id="frmProducts"
                                   @if($data->mode == 'edit')
                                   action="/administrator/update-productsdata"
                                   @else
                                   action="/administrator/add-productsdata"
                                   @endif
                                   >
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
                                 </div>
                                 @endif

                                 <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                 <input type="hidden" name="product_id" id="pID" value="{{$data->id}}" />

                                <div class="row">
                                    <label class="col-sm-2 control-label">Assign Categories</label>
                                    <div class="col-md-9 box-select">
                                        <div class="box box-default collapsed-box">
                                            <div class="box-header with-border">
                                                <h3 class="box-title">Select Categories</h3>
                                                <div class="box-tools pull-right">
                                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                                                    </button>
                                                </div>
                                                <!-- /.box-tools -->
                                            </div>
                                            <!-- /.box-header -->
                                            <div class="box-body">
                                                @foreach($mainCategories as $mainCategory)
                                                    <input type="checkbox" name="mainCategories[]" value="{{$mainCategory->id}}" <?php if(in_array($mainCategory->id,$selectedMainCategories)) echo "checked"; ?>><strong>{{$mainCategory->name}}</strong>
                                                    @if($mainCategory['subCategories']!=null)
                                                        @foreach($mainCategory['subCategories'] as $subCategory)
                                                          <div class="cat-1"><input type="checkbox" name="subCategories[]" value="{{$subCategory->id}}" <?php if(in_array($subCategory->id,$selectedSubCategories)) echo "checked"; ?>> <span style="font-size: 17px !important;">{{$subCategory->name}}</span></div>
                                                            @if($subCategory['subSubCategories']!=null)
                                                                @foreach($subCategory['subSubCategories'] as $subSubCategories)
                                                                   <div class="cat-2"><input type="checkbox" name="subSubCategories[]" value="{{$subSubCategories->id}}" <?php if(in_array($subSubCategories->id,$selectedSubSubCategories)) echo "checked"; ?>><span style="font-size: 16px !important;">{{$subSubCategories->name}}</span></div>
                                                                @endforeach
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                @endforeach
                                            </div>
                                            <!-- /.box-body -->
                                        </div>
                                    </div>

                                </div>



                                <div class="hr-line-dashed"></div>
                                  <div class="form-group {{ $errors->has('brand') ? ' has-error' : '' }}">
                                         <label class="col-sm-2 control-label">Select Brand&nbsp;<span class="label-mandatory">*</span></label>
                                         <div class="col-sm-9">
                                             <select class="form-control" name="brand" id="brand" >
                                                 <option value="0">----select Brand----</option>
                                                 @foreach($selectBrand as $brand)
                                                 <option value="{{$brand->id}}" @if($brand->id == $data->brand_id)selected @endif>{{$brand->name}} </option>
                                                 @endforeach
                                             </select>
                                             @if ($errors->has('brand'))
                                             <span class="help-block">
                                                 <strong>{{ $errors->first('brand') }}</strong>
                                             </span>
                                             @endif
                                         </div>
                                     </div>
                               
                               <!-- vishakha -->
                               <div class="hr-line-dashed"></div>
                                <!-- <div class="col-sm-3"> -->
                                     <div class="form-group {{ $errors->has('productUsedFor') ? ' has-error' : '' }}">
                                         <label class="col-sm-2 control-label">Product Used For&nbsp;<span class="label-mandatory">*</span></label>
                                         <div class="col-sm-9">
                                      
                                             <select class="form-control select2" multiple="multiple"  required name="productUsedFor[]" id="productUsedFor" >
                                                 <option value="g" @if( ($selectedProductUsedFor != null) && (in_array("g",$selectedProductUsedFor) ) ) selected @endif>Girls</option>
                                                 <option value="b" @if( ($selectedProductUsedFor != null) && (in_array("b",$selectedProductUsedFor) ) ) selected @endif>Boys</option>
                                                 <option value="m" @if( ($selectedProductUsedFor != null) && (in_array("m",$selectedProductUsedFor) ) )selected @endif>Men</option>
                                                 <option value="w" @if( ($selectedProductUsedFor != null) && (in_array("w",$selectedProductUsedFor) ) ) selected @endif>Women</option>
                                              
                                             </select>

                                             @if ($errors->has('productUsedFor'))
                                             <span class="help-block">
                                                 <strong>{{ $errors->first('productUsedFor') }}</strong>
                                             </span>
                                             @endif
                                         </div>
                                     </div>
                                   <!-- </div> -->
                                <!-- end    -->

                                 <div class="hr-line-dashed"></div>
                                 <div class="form-group" {{ $errors->has('sku') ? ' has-error' : '' }}>
                                     <label class="col-sm-2 control-label">Sku<span class="label-mandatory">*</span></label>
                                     <div class="col-sm-9">
                                         <input type="text" class="form-control" name="sku" id="sku"  value="<?php if(old('sku')!=null) echo old('sku');
                                             else echo $data->sku;?>"  @if($data->mode == 'edit') readonly @endif>
                                         @if ($errors->has('sku'))
                                         <span class="help-block">
                                             <strong>{{ $errors->first('sku') }}</strong>
                                         </span>
                                         @endif
                                     </div>
                                 </div>
                                 <div class="hr-line-dashed"></div>

                                 <div class="form-group" {{ $errors->has('name') ? ' has-error' : '' }}>
                                     <label class="col-sm-2 control-label">Name<span class="label-mandatory">*</span></label>
                                     <div class="col-sm-9">
                                         <input type="text" class="form-control" name="name" id="name"  value="<?php if(old('name')!=null) echo old('name');
                                             else echo $data->name;?>">
                                         @if ($errors->has('name'))
                                         <span class="help-block">
                                             <strong>{{ $errors->first('name') }}</strong>
                                         </span>
                                         @endif
                                     </div>
                                 </div>
                                 <div class="hr-line-dashed"></div>

                                  <div class="form-group {{ $errors->has('meta_title') ? ' has-error' : '' }}">
                              <label class="col-sm-2 control-label">Meta Title &nbsp;<span class="label-mandatory">*</span></label>
                              <div class="col-sm-9">
                                  <textarea class="form-control" name="meta_title" id="meta_title" rows="3">@if(old('meta_title')!=null){{old('meta_title')}} @else{{$data->meta_title}}@endif</textarea>
                                  @if ($errors->has('meta_title'))
                                  <span class="help-block">
                                      <strong>{{ $errors->first('meta_title') }}</strong>
                                  </span>
                                  @endif
                              </div>
                          </div>


                          <div class="hr-line-dashed"></div>

						  <div class="form-group {{ $errors->has('meta_keyword') ? ' has-error' : '' }}">
                              <label class="col-sm-2 control-label">Meta keyword &nbsp;<span class="label-mandatory">*</span></label>
                              <div class="col-sm-9">
                                  <textarea class="form-control" name="meta_keyword" id="meta_keyword" rows="3">@if(old('meta_keyword')!=null){{old('meta_keyword')}} @else{{$data->meta_keyword}}@endif</textarea>
                                  @if ($errors->has('meta_keyword'))
                                  <span class="help-block">
                                      <strong>{{ $errors->first('meta_keyword') }}</strong>
                                  </span>
                                  @endif
                              </div>
                          </div>
						  <div class="hr-line-dashed"></div>
                                 
                                <div class="form-group"{{ $errors->has('meta_description') ? ' has-error' : '' }}>
                                    <label class="col-sm-2 control-label">Meta Description<span class="label-mandatory">*</span></label>
                                    <div class="col-sm-9">
                                        <textarea class="form-control" id="meta_description" name="meta_description" rows="3">@if(old('meta_description')!=null){{old('meta_description')}}@else{{$data->meta_description}}@endif</textarea>
                                        @if ($errors->has('meta_description'))
                                           <span class="help-block">
                                               <strong>{{ $errors->first('meta_description') }}</strong>
                                           </span>
                                        @endif
                                    </div>
                                </div>
                                 <div class="hr-line-dashed"></div>
                                 <div class="form-group"{{ $errors->has('short_description') ? ' has-error' : '' }}>
                                     <label class="col-sm-2 control-label">Short Description<span class="label-mandatory">*</span></label>
                                     <div class="col-sm-9">
                                         <textarea class="form-control" id="short_description" name="short_description" rows="3">@if(old('short_description')!=null){{old('short_description')}}@else{{$data->short_description}}@endif</textarea>
                                         @if ($errors->has('short_description'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('short_description') }}</strong>
                                            </span>
                                         @endif
                                     </div>
                                 </div>
                                  <div class="hr-line-dashed"></div>
                                  <div class="form-group"{{ $errors->has('product_specifications') ? ' has-error' : '' }}>
                                      <label class="col-sm-2 control-label">Product Specifications<span class="label-mandatory">*</span></label>
                                      <div class="col-sm-9">
                                          <textarea class="form-control" id="product_specifications" name="product_specifications" rows="3">@if(old('product_specifications')!=null){{old('product_specifications')}}@else{{$data->product_specifications}}@endif</textarea>
                                          @if ($errors->has('product_specifications'))
                                             <span class="help-block">
                                                 <strong>{{ $errors->first('product_specifications') }}</strong>
                                             </span>
                                          @endif
                                      </div>
                                  </div>
                                   <div class="hr-line-dashed"></div>  


                                 <div class="form-group"{{ $errors->has('description') ? ' has-error' : '' }}>
                                     <label class="col-sm-2 control-label">Description</label>
                                     <div class="col-sm-9">
                                         <textarea id="description" name="description" rows="10" cols="80">@if(old('description')!=null){{old('description')}}@else{{$data->description}}@endif</textarea>
                                         @if ($errors->has('description'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('description') }}</strong>
                                            </span>
                                         @endif
                                     </div>
                                 </div>
                                 <div class="hr-line-dashed"></div>


                                 <div class="box-footer">
                                   <label class="col-sm-5 control-label"></label>
                                   <div class="col-sm-6">
                                         <button class="btn btn-primary" type="submit">Submit & Next</button>
                                         <button class="btn btn-danger" type="button" onclick="window.location='{{ url("/administrator/list-products") }}'">Cancel</button>
                                   </div>
                                 </div>

                             </form>   

                          </div>
                          <!-- /.tab-pane -->
                        </div>
                        <!-- /.tab-content -->
                      </div>
                      <!-- nav-tabs-custom -->
                    </div>
                    <!-- /.col -->
                  </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection
@push('scripts')
<script
  src="https://code.jquery.com/jquery-1.12.4.js"
  integrity="sha256-Qw82+bXyGq6MydymqBxNPYTaUXXq7c8v3CwiYwLLNXU="
  crossorigin="anonymous"></script>
<!-- Select2 -->
    <script src="{{{ URL::asset('js/select2.full.min.js')}}}"></script>
    <script type="text/javascript">
     
      $(document).ready(function() {
           $(".select2").select2();
      });

    </script>
<!-- CK Editor -->
<script src="https://cdn.ckeditor.com/4.5.7/full-all/ckeditor.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script>
  $(function () {
    CKEDITOR.replace('description');
  });

  $(function () {
      CKEDITOR.replace('product_specifications');
  });
</script>
<script type="text/javascript">


    function getSubCategories(el){ 
         var token = $('input[name=_token]').val();
         var catID = el;
         console.log(catID);
          $.ajax({
    
        url: "/administrator/get-sub-Categories",
                headers: {'X-CSRF-TOKEN': token},
                data: {"catID":catID},
                type: 'POST',
                datatype: 'JSON',
                success: function (resp) {
                   console.log(resp);
                  // $('#sOption').remove();
                   $('#subCategory').html(resp);
                    //$('#subCategory').html(resp);
                }
        });
    }
     function getSubSubCategories(el){ 
         var token = $('input[name=_token]').val();
         var catID = el;
         console.log(catID);
          $.ajax({
    
        url: "/administrator/get-sub-Categories",
                headers: {'X-CSRF-TOKEN': token},
                data: {"catID":catID},
                type: 'POST',
                datatype: 'JSON',
                success: function (resp) {
                   console.log(resp);
                 //  $('#ssOption').after(resp);
                    $('#subSubCategory').html(resp);
                }
        });
    }
    
    
     // $('.select2-selection__choice__remove').click(function(){
     //    //alert($(this).parent().attr('title'));
     //     var token = $('input[name=_token]').val();
     //    var cName = $(this).parent().attr('title');
     //    var pId = $('#pID').val();

     //    var r = confirm("Do you really want to delete this Category ?");
     //    if (r == false) {
     //      return false;
     //    } 
     //    console.log(r);
     //    $.ajax({
        
     //    url: "/administrator/remove-Categories",
     //            headers: {'X-CSRF-TOKEN': token},
     //            data: {"cName":cName,"pId":pID},
     //            type: 'POST',
     //            datatype: 'JSON',
     //            success: function (resp) {
     //               console.log(resp);
                   
     //            }
     //    });
     // });

</script>
@endpush

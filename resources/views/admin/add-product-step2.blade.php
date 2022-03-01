@extends('layouts.admin')
@push('stylesheets')
<link href="{{{ URL::asset('css/dropzone/basic.css')}}}" rel="stylesheet">
<link href="{{{ URL::asset('css/dropzone/dropzone.css')}}}" rel="stylesheet">
@endpush
@push('scripts')
<style>
	.product-list{width:98%;margin:10px auto;float: none;}
	.product-list .imgWrapGallery{display:inline-block;margin-right:5px;text-align: center;}
	.product-list .imgWrapGallery .checkbox label{padding-left:0px;}
	.product-list .imgWrapGallery .img-thumbnail{width:100%;}
</style>
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
                      <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                         <li><a @if($data->mode == 'add') href="/administrator/add-products"  @else href="/administrator/edit-products/{{$data->id}}" @endif>Basic Details</a></li>
                          <li class="active"><a href="#">Product Information</a></li>
                          @if($data->completed_step >= 2)
                          <li><a href="/administrator/edit-products/step3/{{$data->id}}">Configuration</a></li>
                          @endif
                        </ul>
                        <div class="tab-content">
                          <div class="tab-pane active" id="tab_1">
                            <!-- add form here -->
                            <form class="form-horizontal" method="POST" enctype="multipart/form-data" name="frmProducts" id="frmProducts" action="/administrator/update-productsdata/step2">
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
                                 <input type="hidden" name="product_id" value="{{$data->id}}" />

                                                                 
                                 <div class="hr-line-dashed"></div> 
                                  <div class="form-group {{ $errors->has('size_chart_type') ? ' has-error' : '' }}">
                                      <label class="col-sm-3 control-label"> Size Chart Type <span class="label-mandatory">*</span> <span class="label-mandatory"></span></label>
                                      <div class="col-sm-6">
                                            <input type="radio" class="required" name="size_chart_type" id="size_chart_type_image"  value="image" onclick="displaytoggle(this)" <?php if($data->size_chart_type=='image') echo "checked";?> >
                                             Image &nbsp;&nbsp;&nbsp;
                                            <input type="radio" class="required" name="size_chart_type" id="size_chart_type_desc"  value="desc"  onclick="displaytoggle(this)" <?php if($data->size_chart_type=='desc') echo "checked";?> > Description
                                            @if ($errors->has('size_chart_type'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('size_chart_type') }}</strong>
                                            </span>
                                            @endif
                                      </div>
                                  </div>
                                   <div class="hr-line-dashed"></div>

                                  <div class="form-group" id="sc_image" {{ $errors->has('size_chart_image') ? ' has-error' : '' }} @if($data->size_chart_type=='image') style="display:block;" @else style="display:none;" @endif>
                                      <label class="col-sm-3 control-label">Size chart image&nbsp;<span class="label-mandatory">*</span></label>
                                      <div class="col-sm-6">
                                          <input type="file" class="form-control" name="size_chart_image" id="size_chart_image" value="<?php if(old('size_chart_image')!=null) echo old('size_chart_image');
                                              else echo $data->size_chart_image;?>" >
                                          @if($data->size_chart_image !='')
                                          <img src="{{ URL::asset('uploads/sizechart/'.$data->id.'/'.$data->size_chart_image)}}" class="img-thumbnail" width="100" />
                                          <input type="hidden" name="old_image"  id="old_image" value="<?php if(old('size_chart_image')!=null) echo old('size_chart_image');
                                              else echo $data->size_chart_image;?>"/>
                                          @endif

                                          @if ($errors->has('size_chart_image'))
                                          <span class="help-block">
                                              <strong>{{ $errors->first('size_chart_image') }}</strong>
                                          </span>
                                          @endif
                                          
                                      </div>
                                  </div>
                           
                                  <div class="form-group {{ $errors->has('size_chart_description') ? ' has-error' : '' }}"  id="sc_desc" @if($data->size_chart_type=='desc') style="display:block;" @else style="display:none;" @endif>
                                      <label class="col-sm-3 control-label">Size chart description</label>
                                      <div class="col-sm-8">
                                          <textarea id="size_chart_description" name="size_chart_description" rows="10" cols="80">  @if(old('size_chart_description')!=null){{old('size_chart_description')}}@else{{$data->size_chart_description}}@endif
                                              </textarea>
                                          @if ($errors->has('size_chart_description'))
                                             <span class="help-block">
                                                 <strong>{{ $errors->first('size_chart_description') }}</strong>
                                             </span>
                                          @endif
                                      </div>
                                  </div>
                                 
                                 <div class="hr-line-dashed"></div>
                                 <div class="form-group" {{ $errors->has('price') ? ' has-error' : '' }}>
                                     <label class="col-sm-3 control-label">Main Price<span class="label-mandatory">*</span></label>
                                     <div class="col-sm-6">
                                         <input type="text" class="form-control" name="price" id="price"  value="<?php if(old('price')!=null) echo old('price');
                                             else echo $data->price;?>">
                                         @if ($errors->has('price'))
                                         <span class="help-block">
                                             <strong>{{ $errors->first('price') }}</strong>
                                         </span>
                                         @endif
                                     </div>
                                 </div>
                                 <div class="hr-line-dashed"></div>

                                 <div class="form-group" {{ $errors->has('gst') ? ' has-error' : '' }}>
                                     <label class="col-sm-3 control-label">GST(In %): <span class="label-mandatory">*</span></label>
                                     <div class="col-sm-6">
                                         <input type="text" class="form-control" name="gst" id="gst"  value="<?php if(old('gst')!=null) echo old('gst');  else echo $data->gst;?>">
                                         @if ($errors->has('gst'))
                                             <span class="help-block">
                                         <strong>{{ $errors->first('gst') }}</strong>
                                     </span>
                                         @endif
                                     </div>
                                 </div>
                                 <div class="hr-line-dashed"></div>


                                 <div class="form-group" {{ $errors->has('hsn') ? ' has-error' : '' }}>
                                     <label class="col-sm-3 control-label">HSN : <span class="label-mandatory">*</span></label>
                                     <div class="col-sm-6">
                                         <input type="text" class="form-control" name="hsn" id="hsn"  value="<?php if(old('hsn')!=null) echo old('hsn');  else echo $data->hsn;?>">
                                         @if ($errors->has('hsn'))
                                             <span class="help-block">
                                                 <strong>{{ $errors->first('hsn') }}</strong>
                                             </span>
                                         @endif
                                     </div>
                                 </div>
                                 <div class="hr-line-dashed"></div>


                                 <div class="form-group" {{ $errors->has('image') ? ' has-error' : '' }}>
                                     <label class="col-sm-3 control-label">Main Image&nbsp;</label>
                                     <div class="col-sm-6">
                                         <input type="file" class="form-control" name="image" id="image" value="<?php if(old('image')!=null) echo old('image');
                                             else echo $data->image;?>" >
                                         @if($data->image !='')
                                         <img src="{{ URL::asset('uploads/products/images/'.$data->id.'/'.$data->image)}}" class="img-thumbnail" width="100" />
                                         <input type="hidden" name="old_image"  id="old_image" value="<?php if(old('image')!=null) echo old('image');
                                             else echo $data->image;?>"/>
                                         @endif

                                         @if ($errors->has('image'))
                                         <span class="help-block">
                                             <strong>{{ $errors->first('image') }}</strong>
                                         </span>
                                         @endif
                                         
                                     </div>
                                 </div>
                                  
                                 <!-- vishakha -->
                                 <div class="form-group" {{ $errors->has('pr_icon') ? ' has-error' : '' }}>
                                     <label class="col-sm-3 control-label">Product Icon &nbsp;</label>

                                     <div class="col-sm-6">
                                         <input type="file" class="form-control" name="pr_icon" id="pr_icon" value="<?php if(old('pr_icon')!=null) echo old('pr_icon');
                                             else echo $data->icon;?>" >
                                         @if($data->icon !='')
                                         <img src="{{ URL::asset('uploads/product_icon/'.$data->id.'/65x60/'.$data->icon)}}" class="img-thumbnail"  width="100" />
                                         <input type="hidden" name="old_icon"  id="old_icon" value="<?php if(old('pr_icon')!=null) echo old('pr_icon');
                                             else echo $data->icon;?>"/>
                                         @endif

                                         @if ($errors->has('pr_icon'))
                                         <span class="help-block">
                                             <strong>{{ $errors->first('pr_icon') }}</strong>
                                         </span>
                                         @endif
                                         
                                     </div>
                                 </div>
                                 <!-- end -->
                                 <div class="hr-line-dashed"></div>
                                  
                                  <div class="form-group" {{ $errors->has('video_url') ? ' has-error' : '' }}>
                                     <label class="col-sm-3 control-label">Video url</label>
                                     <div class="col-sm-6">
                                         <input type="text" class="form-control" name="video_url" id="video_url"  value="<?php if(old('video_url')!=null) echo old('video_url');
                                             else echo $data->video_url;?>">
                                         @if ($errors->has('video_url'))
                                         <span class="help-block">
                                             <strong>{{ $errors->first('video_url') }}</strong>
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
<!-- CK Editor -->
<script src="https://cdn.ckeditor.com/4.5.7/full-all/ckeditor.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script>
  $(function () {
    CKEDITOR.replace('size_chart_description');
  });
</script>
<script type="text/javascript">
  function displaytoggle(el){
     console.log(el.value);
    if(el.value == "image"){
      $('#sc_desc').css({
                    'display':'none',
                });
      $('#sc_image').css({
                    'display':'block',
                });
    }
    if(el.value == "desc"){
      $('#sc_desc').css({
                    'display':'block',
                });
      $('#sc_image').css({
                    'display':'none',
                });
    }
   
  }
</script>
<script src="{{{ URL::asset('js/dropzone.js')}}}"></script>

<script type="text/javascript">
Dropzone.options.dropzoneForm = {
paramName: "file", // The name that will be used to transfer the file
        maxFilesize: 10, // MB
        dictDefaultMessage: "<strong>Drop files here or click to upload. </strong></br>",

        init: function() {
        this.on("success", function(file, responseText) {

        if (responseText.flag == 'success'){
          $("#productSlaveIMG").prepend(responseText.appeendImg);
        
        }
        });
        }
};

</script>
@endpush

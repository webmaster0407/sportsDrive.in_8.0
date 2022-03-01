@extends('layouts.admin')
@push('stylesheets')
<link rel="stylesheet" href="{{ URL::asset('plugins/datatables/dataTables.bootstrap.css')}}">
  <link href="{{ URL::asset('css/select2.min.css')}}" rel="stylesheet">
  <link href="{{{ URL::asset('css/dropzone/basic.css')}}}" rel="stylesheet">
<link href="{{{ URL::asset('css/dropzone/dropzone.css')}}}" rel="stylesheet">
<style type="text/css">
  .vertical-scroll-modal{
    border-top:none; max-height: 351px; overflow-x: hidden;overflow-y: scroll;
  }
</style>
<style type="text/css">
  .selectStyle{
      border:solid 2px black;
  }
</style>
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
                      <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                          <li><a @if($data->mode == 'add') href="/administrator/add-products"  @else href="/administrator/edit-products/{{$data->id}}" @endif>Basic Details</a></li>
                          <li><a href="/administrator/edit-products/step2/{{$data->id}}" >Product Information</a></li>
                          <li class="active"><a href="#">Configuration</a></li>
                          
                        </ul>
                        <div class="tab-content">
                          <div class="tab-pane active" id="tab_1">
                           <!-- add form here -->
                            <form class="form-horizontal" method="POST" enctype="multipart/form-data" name="frmProducts" id="frmProducts" action="/administrator/update-productsdata/step3">
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

                         
                                 <div class="form-group" {{ $errors->has('quantity') ? ' has-error' : '' }}>
                                     <label class="col-sm-3 control-label">Stock Levels <span class="label-mandatory">*</span></label>
                                     <div class="col-sm-8">
                                         <input type="text" class="form-control" name="quantity" id="quantity"  value="<?php if(old('quantity')!=null) echo old('quantity');
                                             else echo $data->quantity;?>">
                                         @if ($errors->has('quantity'))
                                         <span class="help-block">
                                             <strong>{{ $errors->first('quantity') }}</strong>
                                         </span>
                                         @endif
                                     </div>
                                 </div>
                                 <div class="hr-line-dashed"></div>

                                  <!-- add configuration   -->
                                  <div class="form-group  {{ $errors->has('attribute_group') ? ' has-error' : '' }}">
                                      <label class="col-sm-3 control-label">Attribute Groups &nbsp;<span class="label-mandatory">*</span></label>
                                      <div class="col-sm-8">
                                          <select class="select2 form-control" multiple="multiple" name="attribute_group[]" id="attribute_group" onchange ="get_atribute('{{$data->mode}}','{{$data->id}}')">
                                             

                                              @foreach ($attributeGroups as $attributeGroup)
                                              <option @if($data->config_group == "Both" ||$data->config_group == $attributeGroup->name ) Selected @endif value="{{$attributeGroup->id}}" >{{$attributeGroup->name}}</option>
                                              @endforeach
                                          </select>
                                          
                                          @if ($errors->has('attribute_group'))
                                          <span class="help-block">
                                              <strong>{{ $errors->first('attribute_group') }}</strong>
                                          </span>
                                          @endif
                                      </div>
                                       
                                      
                                  </div>
                                  <div class="form-group" {{ $errors->has('configQuantity') ? ' has-error' : '' }}>
                                      <label class="col-sm-3 control-label">How Many Configurations want to add ? <span class="label-mandatory">*</span></label>
                                      <div class="col-sm-3">
                                          <input type="text" class="form-control" name="configQuantity" id="configQuantity"  value="">
                                          @if ($errors->has('configQuantity'))
                                          <span class="help-block">
                                              <strong>{{ $errors->first('configQuantity') }}</strong>
                                          </span>
                                          @endif
                                      </div>
                                      <div class="attributeInnerDiv  col-sm-5">
                                          <button type="button" class="btn btn-w-m btn-success btn-sm pull-right" onclick="get_atribute('{{$data->mode}}','{{$data->id}}')">Generate Configurations</button>
                                      </div>
                                      
                                  </div>
                                  <div class="hr-line-dashed"></div>
                                  
                                  <div class="form-group "> 
                                    <label class="col-sm-3 control-label"></label>
                                    <span class="help-block">
                                      <small>(Note:To add images with size configuration only select color as "No Color".)</small>
                                  </span>
                                  <!-- <div class="col-sm-3">
                                    <button class="btn btn-danger btn-sm" type="button" name="delete" value="Delete" onClick="JavaScript:CallOperation(this.value,'frmProducts','chk[]');">Delete</button>
                                   <input type="hidden" name="operationFlag" value="">
                                  </div> -->
                                   
                                </div>
                                   <!-- here displayed config table -->
                                  <div class="form-group " id="oldAttributeTable" >
                                  </div>
                                 
                                  <div class="hr-line-dashed"></div>
                                 <div class="form-group" {{ $errors->has('discount_price') ? ' has-error' : '' }}>
                                     <label class="col-sm-3 control-label">Discount Amount <span class="label-mandatory">*</span></label>
                                     <div class="col-sm-8">
                                         <input type="text" class="form-control" name="discount_price" id="discount_price"  value="<?php if(old('discount_price')!=null) echo old('discount_price');
                                             else echo $data->discount_price;?>">
                                         @if ($errors->has('discount_price'))
                                         <span class="help-block">
                                             <strong>{{ $errors->first('discount_price') }}</strong>
                                         </span>
                                         @endif
                                     </div>
                                 </div>
                                 <div class="hr-line-dashed"></div>
                                 

                                 <div class="box-footer">
                                   <label class="col-sm-5 control-label"></label>
                                   <div class="col-sm-6">
                                         <button class="btn btn-primary" onclick="validateFunction()" type="submit">Submit</button>
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
    <div class="modal fade" id="imagemodal" role="dialog">
       <div class="modal-dialog">
         <!-- Modal content-->
         <div class="modal-content">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
           <div class="modal-header">
             <button type="button" class="close" data-dismiss="modal">&times;</button>
             <div class="row">
               <div class="col-sm-6"><h3 class="modal-title">Product Configuration Images </h3></div>
               <div class="col-sm-6"><div class="callout callout-warning">
                <p>Best fit images would be of resolution 1024x1024.</p>
              </div></div>
             </div>
           </div>
           <div class="modal-body">
            <!-- previous images & uploaded images here -->
            <div class="box vertical-scroll-modal">
             <div class="box-body" id="modalData">
             </div>
           </div>
           <div class="box" style="border-top:none;">
              <div class="box-header with-border">
                  <h3 class="box-title"><b>Upload Images</b></h3>
              </div>
              <div class="box-body">
                 <div class="dropzone-box">
                     <p>
                         <strong>Drag and drop a file onto it</strong>
                     </p>
                     <form  class="dropzone" id="dropzoneForm" action="/administrator/upload-images" method="post" enctype="multipart/form-data">
                         <input type="hidden" name="_token" value="{{ csrf_token() }}">
                         <input type="hidden" name="config_id" id="config_id" value="" />
                         <input type="hidden" name="color_id" id="color_id" value="" />
                          <input type="hidden" name="product_id" value="{{$data->id}}" />
                         <div class="fallback">
                               <input name="configImages" accept="image/*" type="file" multiple />
                         </div>
                     </form>
                 </div>
              </div>
           </div>
           </div>
           <div class="modal-footer">
             <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
    function get_atribute(mode, prodID) {
      
        var attributeGrp = $("#attribute_group").val();
        var token = $('input[name=_token]').val();
        var countNew = $('#configQuantity').val();
        $.ajax({
        url: "/administrator/get-attribute",
                headers: {'X-CSRF-TOKEN': token},
                data: {"attributeGrp": attributeGrp, "key": "attribute", "mode":mode, "prodID":prodID,"countNew":countNew},
                type: 'POST',
                datatype: 'JSON',
                success: function (resp) {
                $("#oldAttributeTable").html(resp);
                  
                }
        });
    }
  </script>
  
  <script type="text/javascript">
    function hideconfig(){
      $('#oldAttributeTable .newConfig').remove();
    }

    function removeConfigDiv(el) {
      console.log(el);
       $(el).closest( "tr" ).remove();
    }
    function deleteFromDB(el){
       var rId = $(el).closest( "tr" ).attr('id');
      
      var token = $('input[name=_token]').val();
      var r = confirm("Do you really want to delete this Configuration ?");
        if (r == false) {
          return false;
        } 
       $.ajax({
       url: "/administrator/delete-attribute",
               headers: {'X-CSRF-TOKEN': token},
               data: {"rId":rId},
               type: 'POST',
               datatype: 'JSON',
               success: function (resp) {
                
                $(resp).remove();
               }
       });
    }
    
  </script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>

<!-- DROPZONE -->
<script src="{{ URL::asset('js/dropzone.js')}}"></script>

<script type="text/javascript">
Dropzone.options.dropzoneForm = {
paramName: "file", // The name that will be used to transfer the file
        maxFilesize: 10, // MB
        dictDefaultMessage: "<strong>Drop files here or click to upload. </strong></br>",
    parallelUploads: 1,
        init: function() {
          this.on("success", function(file, responseText) {
            if (responseText.flag == 'success'){
              $("#productIMG").append(responseText.appeendImg);

            }
          });
        }
};

</script>
<script type="text/javascript">
    function addImages(configId,colorId){
      var token = $('input[name=_token]').val();
       var pid = $('input[name=product_id]').val();
       $.ajax({
       url: "/administrator/add-images/"+configId,
               headers: {'X-CSRF-TOKEN': token},
               type: 'POST',
               data:{'colorId':colorId,'pid':pid},
               success: function (resp) {
                $('#modalData').html(resp);
                $('#color_id').val(colorId);
                $('#imagemodal').modal('show');
               }
       });
    }
    function deleteImages(imageID){
        var token = $('input[name=_token]').val();
         var pid = $('input[name=product_id]').val();
        var r = confirm("Do you really want to delete this image ?");
        if (r == false) {
          return false;
        } 
        $.ajax({
            url: "/administrator/delete-image/"+imageID,
            headers: {'X-CSRF-TOKEN': token},
            type: 'GET',
            success: function (resp) {
                console.log(resp);
                 $("#" + imageID).remove();
                
            }

        });
    }
    function setImages(imageID,colorId){
      var token = $('input[name=_token]').val();
       var pid = $('input[name=product_id]').val();

        var r = confirm("Do you want to set this image as main configuration image?");
        if (r == false) {
          return false;
        } 
        var checkIcon = "<label>Set as Main <i class='fa fa-check'></i></label>";
        var withoutCheckicon = "<label>Set as Main</label>";
        $.ajax({
            url: "/administrator/set-image",
            headers: {'X-CSRF-TOKEN': token},
            type: 'POST',
            data:{'colorId':colorId,'pid':pid,'imageID':imageID},
            success: function (resp) {
                $("#" + imageID+" img").addClass('selectStyle');
                $("#" + imageID).siblings().children('img').removeClass('selectStyle');
                $("#" + imageID).find('.setMainConfig label').replaceWith(checkIcon);
                $("#" + imageID).siblings().find('.setMainConfig label').replaceWith(withoutCheckicon);              
            }

        });
    }
    function validateFunction (){
    
      event.preventDefault();
      // var mo=$('.select2-selection__choice');
    var vr=$('.variationdiv'),r=[];
      var duplicate=false,whr=[];
    
    var rr=vr.get().reverse();
    $.each(rr,function(){
        var item=$(this),elem=$('[name^="AttributeColor"],[name^="AttributeSize"]', item),str=[];
        elem.each(function(){
          str.push(this.value)
        })
        var _str=str.join('-');
        if(r.includes(_str)){duplicate=true;whr.push(item[0].id); item.addClass('duplicate').delay('3000').queue(function(){$(this).removeClass('duplicate');$(this).dequeue()})}else{
            r.push(_str);
        }
    });
      if(duplicate){
          
          errormsg ="Some rows has duplicate values!<br/>"
            
          $(".newConfig").prepend("<div id='errorprz' class='alert alert-danger'>"+errormsg+"</div>");
      }else{
        $('#frmProducts').submit();
      }
    }
</script>
<!-- <script type="text/javascript">
  $(document).on("change",".AttributeColor", function(){
    var colorid =$(this).val();
    var configId = $(this).closest('tr').attr('id');
     var htm = "<input type='button' id='addImage-114' name='addImage' value='Add/View Images' onclick='addImages("+configId+","+colorid+")'>";
     $("#addImage-"+configId).replaceWith(htm);
  });
</script> -->
<script type="text/javascript">
     
    $(document).ready(function() {
           $(".select2").select2();
           var prodID = $('input[name=product_id]').val();
           get_atribute('edit',prodID);

    });

  </script>
  <script type="text/javascript">
    $(document).on("change","#checkAll", function(){   
       $("input:checkbox").prop('checked', $(this).prop("checked")); 

          var r = confirm("Do you really want to delete all Configuration ?");
          if (r == false) {
            return false;
          } 

        $("input:checkbox").each(function(i){                         
          var rId = [];
          rId[i] = $(this).closest("tr").attr('id');
          if(rId[i] != "undefined"){
            var token = $('input[name=_token]').val();
              $.ajax({
              url: "/administrator/delete-attribute",
                      headers: {'X-CSRF-TOKEN': token},
                      data: {"rId":rId[i]},
                      type: 'POST',
                      datatype: 'JSON',
                      success: function (resp) {
                       $(resp).remove();
                       $('#oldAttributeTable .newConfig').remove();
                      }
              });
          }
         
        });
        
    });
  </script>
@endpush

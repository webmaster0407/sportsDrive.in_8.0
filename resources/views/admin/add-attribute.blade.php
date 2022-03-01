@extends('layouts.admin')
@push('stylesheets')
  <!-- DataTables -->
  <link rel="stylesheet" href="../../plugins/datatables/dataTables.bootstrap.css">
@endpush
@push('scripts')
<script src="{{ asset("plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
@endpush
@section('content')
<section class="content-header">
    <h1>
        Attributes
    </h1>
    <ol class="breadcrumb">
        <li><a href="home"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="/administrator/list-attributes">List Attributes</a></li>
        <li class="active"> @if($data->mode == 'edit')Edit @else Add @endif Attributes</li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"> @if($data->mode == 'edit')Edit @else Add @endif  Attributes</h3>
                </div>

                <div class="box">
                  <div class="box-header with-border">
                    <h3 class="box-title"></h3>
                  </div>
                  <!-- form start -->

                   <form class="form-horizontal" method="POST" enctype="multipart/form-data" name="frmAttributes" id="frmAttributes"
                          @if($data->mode == 'edit')
                          action="/administrator/update-attributesdata"
                          @else
                          action="/administrator/add-attributesdata"
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
                        <input type="hidden" name="attribute_id" value="{{$data->id}}" />
                        <?php $tempGrpType=""; ?>
                        <div class="form-group {{ $errors->has('group_id') ? ' has-error' : '' }}">
                            <label class="col-sm-3 control-label">Attribute Group&nbsp;<span class="label-mandatory">*</span></label>
                            <div class="col-sm-6" >
                                <select class="select2_demo_3 form-control" name="group_id" id="group_id" @if($data->mode=="edit") disabled @endif onchange = "display(this.options[this.selectedIndex].getAttribute('is_type'))">
                                    <option value="">Select Attribute Group</option>
                                    @foreach ($groupList as $grouplist)
                                    <option is_type="<?php echo $grouplist->type; ?>" <?php if($grouplist->id==$data->group_id) {echo 'selected'; $tempGrpType=$grouplist->type;} ?> value="{{$grouplist->id}}">{{$grouplist->name}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('group_id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('group_id') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                            <label class="col-sm-3 control-label">Name&nbsp;<span class="label-mandatory">*</span></label>
                            <div class="col-sm-6">
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

                       
                        <div class="form-group" id="sel_color"{{ $errors->has('hex_color') ? ' has-error' : '' }}">
                            <label class="col-sm-3 control-label">Select Color&nbsp;<span class="label-mandatory">*</span></label>
                            <div class="col-sm-6">
                                <input name = "hex_color" id="hex_color" class="jscolor" value="{{$data->hex_color}}">
                                @if ($errors->has('hex_color'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('hex_color') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                      
                        <div class="box-footer">
                          <label class="col-sm-3 control-label"></label>
                          <div class="col-sm-6">
                                <button class="btn btn-primary" type="submit">Submit</button>
                                <button class="btn btn-danger" type="button" onclick="window.location='{{ url("/administrator/list-attributes") }}'">Cancel</button>
                          </div>
                        </div>

                    </form>


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
<script src="{{asset("js/jscolor.min.js")}}" type="text/javascript" language="
javascript"></script>
<!-- vishakha -->
<script>
$(document).ready(function(){  
  <?php if($data->mode !== 'edit') { ?>
    $("#sel_color").hide();
  <?php } ?>
});
function display(el)
{ 
     if(el=='size')
     {
        
     document.getElementById("sel_color").style.display = "none"; 
       $("#hex_color").prop('disabled',true);
     }
     else
     {
        document.getElementById("sel_color").style.display = ""; 
        
        $("#hex_color").prop('disabled',false);
     }
}
</script>
<!-- end -->
@endpush

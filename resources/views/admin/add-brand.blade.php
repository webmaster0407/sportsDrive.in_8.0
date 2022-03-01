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
        Brands
    </h1>
    <ol class="breadcrumb">
        <li><a href="home"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="/administrator/list-brand">List Brands</a></li>
        <li class="active"> @if($data->mode == 'edit')Edit @else Add @endif Brands</li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"> @if($data->mode == 'edit')Edit @else Add @endif  Brands</h3>
                </div>
                <div class="box">
                  <div class="box-header with-border">
                    <h3 class="box-title"></h3>
                  </div>
                  <!-- form start -->
                   <form class="form-horizontal" method="POST" enctype="multipart/form-data" name="frmBrands" id="frmBrands"
                          @if($data->mode == 'edit')
                          action="/administrator/update-branddata"
                          @else
                          action="/administrator/add-branddata"
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
                        <input type="hidden" name="id" value="{{$data->id}}" />
                        <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                            <label class="col-sm-3 control-label">Name &nbsp;<span class="label-mandatory">*</span></label>
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
                        <div class="form-group {{ $errors->has('image') ? ' has-error' : '' }}">
                            <label class="col-sm-3 control-label">Image&nbsp;</label>
                            <div class="col-sm-6">
                                <input type="file" class="form-control" name="image" id="image" value="<?php if(old('image')!=null) echo old('image');
                                    else echo $data->image;?>" >
                                @if($data->image !=''||$data->image !=null)
                                <img src="{{ URL::asset('uploads/brand/'.$data->image)}}" class="img-thumbnail" width="100" />
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
                        <div class="hr-line-dashed"></div>
                       <div class="form-group {{ $errors->has('meta_title') ? ' has-error' : '' }}">
                              <label class="col-sm-3 control-label">Meta Title &nbsp;<span class="label-mandatory">*</span></label>
                              <div class="col-sm-6">
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
                              <label class="col-sm-3 control-label">Meta keyword &nbsp;<span class="label-mandatory">*</span></label>
                              <div class="col-sm-6">
                                  <textarea class="form-control" name="meta_keyword" id="meta_keyword" rows="3">@if(old('meta_keyword')!=null){{old('meta_keyword')}} @else{{$data->meta_keyword}}@endif</textarea>
                                  @if ($errors->has('meta_keyword'))
                                  <span class="help-block">
                                      <strong>{{ $errors->first('meta_keyword') }}</strong>
                                  </span>
                                  @endif
                              </div>
                          </div>                    
                        <div class="hr-line-dashed"></div>
                        <div class="form-group {{ $errors->has('meta_desc') ? ' has-error' : '' }}">
                            <label class="col-sm-3 control-label">Meta Description <span class="label-mandatory">*</span></label>
                            <div class="col-sm-6">
                                <textarea  class="form-control" id="meta_desc" name="meta_desc" rows="3">@if(old('meta_desc')!=null){{old('meta_desc')}}@else{{$data->meta_desc}}@endif</textarea>
                                @if ($errors->has('meta_desc'))
                                   <span class="help-block">
                                       <strong>{{ $errors->first('meta_desc') }}</strong>
                                   </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group {{ $errors->has('short_desc') ? ' has-error' : '' }}">
                            <label class="col-sm-3 control-label">Short Description </label>
                            <div class="col-sm-8">
                                <textarea id="short_desc" name="short_desc" rows="10" cols="80">  @if(old('short_desc')!=null){{old('short_desc')}}@else{{$data->short_desc}}@endif
                                    </textarea>
                                @if ($errors->has('short_desc'))
                                   <span class="help-block">
                                       <strong>{{ $errors->first('short_desc') }}</strong>
                                   </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="box-footer">
                          <label class="col-sm-3 control-label"></label>
                          <div class="col-sm-6">
                                <button class="btn btn-primary" type="submit">Submit</button>
                                <button class="btn btn-danger" type="button" onclick="window.location='{{ url("/administrator/list-brand") }}'">Cancel</button>
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
<!-- CK Editor -->
<script src="https://cdn.ckeditor.com/4.5.7/full-all/ckeditor.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script>
  $(function () {
    CKEDITOR.replace('short_desc');
  });
</script>
@endpush

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
      Categories
    </h1>
    <ol class="breadcrumb">
        <li><a href="home"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="/administrator/list-categories/{{$data->parent_id}}/{{$data->level_id}}}">List Categories</a></li>
        <li class="active"> @if($data->mode == 'edit')Edit @else Add @endif Categories</li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"> @if($data->mode == 'edit')Edit @else Add @endif  Categories</h3>
                </div>


                <div class="box">
                  <div class="box-header with-border">
                    <h3 class="box-title"></h3>
                  </div>
                  <!-- form start -->

                  <form  class="form-horizontal" enctype="multipart/form-data" method="POST" name="frmsitesetting" id="frmsitesetting"
                            @if($data->mode == 'edit')
                            action="/administrator/update-categorydata"
                            @else
                            action="/administrator/add-categorydata"
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
                          <input type="hidden" name="category_id" value="{{$data->id}}" />
                          <input type="hidden" name="sort_order" value="{{$data->sort_order}}" />
                          <input type="hidden" name="parent_id" value="{{$data->parent_id}}">
                          <input type="hidden" name="level_id" value="{{$data->level_id}}">

                          <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                              <label class="col-sm-3 control-label">Name&nbsp;<span class="label-mandatory">*</span></label>
                              <div class="col-sm-6">
                                  <input type="text" class="form-control" name="name" id="name" required="" value="<?php if(old('name')!=null) echo old('name');
                                      else echo $data->name;?>" minlength="3" maxlength="50">
                                  @if ($errors->has('name'))
                                  <span class="help-block">
                                      <strong>{{ $errors->first('name') }}</strong>
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
                              <label class="col-sm-3 control-label">Meta Description &nbsp;<span class="label-mandatory">*</span></label>
                              <div class="col-sm-6">
                                  <textarea class="form-control" name="meta_desc" id="meta_desc" rows="3">@if(old('meta_desc')!=null){{old('meta_desc')}} @else{{$data->meta_desc}}@endif</textarea>
                                  @if ($errors->has('meta_desc'))
                                  <span class="help-block">
                                      <strong>{{ $errors->first('meta_desc') }}</strong>
                                  </span>
                                  @endif
                              </div>
                          </div>

                          <div class="hr-line-dashed"></div>

                          <div class="form-group" {{ $errors->has('image') ? ' has-error' : '' }}>
                              <label class="col-sm-3 control-label">Image&nbsp;</label>
                              <div class="col-sm-6">
                                  <input type="file" class="form-control" name="image" id="image" value="<?php if(old('image')!=null) echo old('image');
                                      else echo $data->image;?>" >
                                  @if($data->image !='')
                                  <img src="{{{ URL::asset('uploads/categories/'.$data->image)}}}" class="img-thumbnail" width="100" />
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

                          <div class="form-group {{ $errors->has('short_description') ? ' has-error' : '' }}">
                              <label class="col-sm-3 control-label">Short Description</label>
                              <div class="col-sm-6">
                                  <textarea class="form-control" name="short_description" id="short_description" rows="3">@if(old('short_description')!=null){{old('short_description')}} @else{{$data->short_description}}@endif</textarea>
                                  @if ($errors->has('short_description'))
                                  <span class="help-block">
                                      <strong>{{ $errors->first('short_description') }}</strong>
                                  </span>
                                  @endif
                              </div>
                          </div>

                          <div class="hr-line-dashed"></div>
                          <div class="form-group {{ $errors->has('description') ? ' has-error' : '' }}">
                              <label class="col-sm-3 control-label">Description</label>
                              <div class="col-sm-8">
                                            <textarea id="description" name="description" rows="10" cols="80">@if(old('description')!=null){{old('description')}} @else{{$data->description}}@endif
                                            </textarea>
                                            @if ($errors->has('description'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('description') }}</strong>
                                            </span>
                                            @endif
                              </div>
                          </div>
                          <div class="hr-line-dashed"></div>

                          <div class="box-footer">
                            <label class="col-sm-3 control-label"></label>
                            <div class="col-sm-6">
                                    <button class="btn btn-primary" type="submit">Submit</button>
                                    <button class="btn btn-danger" type="button" onclick="window.location='{{ url("/administrator/list-categories/$data->parent_id/$data->level_id")}}'">Cancel</button>
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
<!-- <script src="{{asset("js/ckeditor.js")}}"></script> -->

<script src="https://cdn.ckeditor.com/4.5.7/full-all/ckeditor.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script>
  $(function () {
    CKEDITOR.replace('description');
  });
</script>
@endpush

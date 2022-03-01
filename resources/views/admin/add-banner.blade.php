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
        Banners
    </h1>
    <ol class="breadcrumb">
        <li><a href="home"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="/administrator/list-banners">List Banners</a></li>
        <li class="active"> @if($data->mode == 'edit')Edit @else Add @endif Banners</li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"> @if($data->mode == 'edit')Edit @else Add @endif  Banners</h3>
                </div>

                <div class="box">
                  <div class="box-header with-border">
                    <h3 class="box-title"></h3>
                  </div>
                  <!-- form start -->

                   <form class="form-horizontal" method="POST" enctype="multipart/form-data" name="frmBanners" id="frmBanners"
                          @if($data->mode == 'edit')
                          action="/administrator/update-bannersdata"
                          @else
                          action="/administrator/add-bannersdata"
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
                        <input type="hidden" name="banners_id" value="{{$data->banner_id}}" />
                        <div class="form-group {{ $errors->has('banners_heading') ? ' has-error' : '' }}">
                            <label class="col-sm-3 control-label">Banner Heading&nbsp;<span class="label-mandatory">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="banners_heading" id="banners_heading"  value="<?php if(old('banners_heading')!=null) echo old('banners_heading');
                                    else echo $data->banner_heading;?>">
                                @if ($errors->has('banners_heading'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('banners_heading') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group" {{ $errors->has('banners_image') ? ' has-error' : '' }}>
                            <label class="col-sm-3 control-label">Banners Image&nbsp;<span class="label-mandatory">*</span></label>
                            <div class="col-sm-6">
                                <input type="file" class="form-control" name="banners_image" id="banners_image" value="<?php if(old('banners_image')!=null) echo old('banners_image');
                                    else echo $data->banner_images;?>" >
                                @if($data->banner_images !='')
                                <img src="{{{ URL::asset('uploads/banners/'.$data->banner_images)}}}" class="img-thumbnail" width="100" />
                                <input type="hidden" name="old_image"  id="old_image" value="<?php if(old('banners_image')!=null) echo old('banners_image');
                                    else echo $data->banner_images;?>"/>
                                @endif

                                @if ($errors->has('banners_image'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('banners_image') }}</strong>
                                </span>
                                @endif
                                <br/>
                               
                                 <small style="color: green;">NOTE: Image size should be <b>(1280x730)</b> pixels & should not be greater than <b>2MB</b>.</small>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group {{ $errors->has('banners_url') ? 'has-error' : '' }}">
                            <label class="col-sm-3 control-label">Banners URL&nbsp;<span class="label-mandatory">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="banners_url" id="banners_url" value="<?php if(old('banners_url')!=null) echo old('banners_url');
                                    else echo $data->banner_url;?>">
                                @if ($errors->has('banners_url'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('banners_url') }}</strong>
                                </span>
                                @endif
                                <br/>
                                 <small style="color: green;"><b>Ex:</b> https://www.google.com</small>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group {{ $errors->has('short_text') ? 'has-error' : '' }}">
                            <label class="col-sm-3 control-label">Short text&nbsp;</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="short_text" id="short_text" value="<?php if(old('short_text')!=null) echo old('short_text');
                                    else echo $data->short_text;?>">
                                @if ($errors->has('short_text'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('short_text') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>


                        <div class="hr-line-dashed"></div>

                        <div class="form-group {{ $errors->has('bannner_description') ? ' has-error' : '' }}">
                            <label class="col-sm-3 control-label">Banners Description</label>
                            <div class="col-sm-8">
                                <textarea id="bannner_description" name="bannner_description" rows="10" cols="80">  @if(old('bannner_description')!=null){{old('bannner_description')}}@else{{$data->banner_text}}@endif
                                    </textarea>
                                @if ($errors->has('bannner_description'))
                                   <span class="help-block">
                                       <strong>{{ $errors->first('bannner_description') }}</strong>
                                   </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>


                        <div class="box-footer">
                          <label class="col-sm-3 control-label"></label>
                          <div class="col-sm-6">
                                <button class="btn btn-primary" type="submit">Submit</button>
                                <button class="btn btn-danger" type="button" onclick="window.location='{{ url("/administrator/list-banners") }}'">Cancel</button>
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
    CKEDITOR.replace('bannner_description');
  });
</script>
@endpush

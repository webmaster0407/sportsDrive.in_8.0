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
        Newsletter
    </h1>
    <ol class="breadcrumb">
        <li><a href="home"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="/administrator/list-newsletter">List Newsletter</a></li>
        <li class="active"> @if($data->mode == 'edit')Edit @else Add @endif Newsletter</li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"> @if($data->mode == 'edit')Edit @else Add @endif  Newsletter</h3>
                </div>

                <div class="box">
                  <div class="box-header with-border">
                    <h3 class="box-title"></h3>
                  </div>
                  <!-- form start -->

                   <form class="form-horizontal" method="POST" enctype="multipart/form-data" name="frmNewsletter" id="frmNewsletter"
                          @if($data->mode == 'edit')
                          action="/administrator/update-newsletterdata"
                          @else
                          action="/administrator/add-newsletterdata"
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
                        <input type="hidden" name="newsletter_id" value="{{$data->id}}" />
                        <div class="form-group {{ $errors->has('newsletter_name') ? ' has-error' : '' }}">
                            <label class="col-sm-3 control-label">Name<span class="label-mandatory">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="newsletter_name" id="newsletter_name"  value="<?php if(old('newsletter_name')!=null) echo old('newsletter_name');
                                    else echo $data->newsletter_name;?>">
                                @if ($errors->has('newsletter_name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('newsletter_name') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        

                        <div class="form-group {{ $errors->has('newsletter_subject') ? 'has-error' : '' }}">
                            <label class="col-sm-3 control-label">Subject<span class="label-mandatory">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="newsletter_subject" id="newsletter_subject" value="<?php if(old('newsletter_subject')!=null) echo old('newsletter_subject');
                                    else echo $data->newsletter_subject;?>">
                                @if ($errors->has('newsletter_subject'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('newsletter_subject') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group {{ $errors->has('newsletter_desc') ? ' has-error' : '' }}">
                            <label class="col-sm-3 control-label">Description<span class="label-mandatory">*</span></label>
                            <div class="col-sm-8">
                                <textarea id="newsletter_desc" name="newsletter_desc" rows="10" cols="80">  @if(old('newsletter_desc')!=null){{old('newsletter_desc')}}@else{{$data->newsletter_desc}}@endif
                                    </textarea>
                                @if ($errors->has('newsletter_desc'))
                                   <span class="help-block">
                                       <strong>{{ $errors->first('newsletter_desc') }}</strong>
                                   </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>


                        <div class="box-footer">
                          <label class="col-sm-3 control-label"></label>
                          <div class="col-sm-6">
                                <button class="btn btn-primary" type="submit">Submit</button>
                                <button class="btn btn-danger" type="button" onclick="window.location='{{ url("/administrator/list-newsletter") }}'">Cancel</button>
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
    CKEDITOR.replace('newsletter_desc');
  });
</script>
@endpush

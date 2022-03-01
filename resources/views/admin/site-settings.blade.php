@extends('layouts.admin')
@push('scripts')
<script src="{{ asset("plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
@endpush
@section('content')
<section class="content-header">
    <h1>
        Site Setting
    </h1>
    <ol class="breadcrumb">
        <li><a href="home"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Site Setting</li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Update Site Setting</h3>
                </div>
                  <!-- form start -->
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
                <form class="form-horizontal" name="frmSiteSetting" id="frmSiteSetting" method="POST" action="update-site-settings">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="box-body">
                            <div class="form-group" {{ $errors->has('admin_email') ? ' has-error' : '' }}>
                                <label for="admin_email" class="col-sm-3 control-label">Email<span class="text-red"> * </span></label>

                                <div class="col-sm-6">
                                    <input type="email" class="form-control" id="admin_email" name="admin_email" placeholder="Enter email" value="{{$data['admin_email']}}">
                                </div>
                                @if ($errors->has('admin_email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('admin_email') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group" {{ $errors->has('site_heading') ? ' has-error' : '' }}>
                                <label for="site_heading" class="col-sm-3 control-label">Site Heading<span class="text-red"> * </span></label>

                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="site_heading" name="site_heading" placeholder="Enter email" value="{{$data['site_heading']}}">
                                </div>
                                 @if ($errors->has('site_heading'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('site_heading') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group" {{ $errors->has('telephone') ? ' has-error' : '' }}>
                                <label for="telephone" class="col-sm-3 control-label">Visit Telephone No<span class="text-red"> * </span></label>

                                <div class="col-sm-6">
                                    <input type="tel" class="form-control" id="telephone" name="telephone" placeholder="Enter Telephone" value="{{$data['telephone']}}">
                                </div>
                                 @if ($errors->has('telephone'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('telephone') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group"{{ $errors->has('address') ? ' has-error' : '' }}>
                                <label for="address" class="col-sm-3 control-label">Visit Address<span class="text-red"> * </span></label>
                                <div class="col-sm-6">
                                    <textarea class="form-control" id="address" name="address" rows="4">{{$data['address']}}</textarea>
                                </div>
                                @if ($errors->has('address'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('address') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group" {{ $errors->has('facebook_url') ? ' has-error' : '' }}>
                                <label for="facebook_url" class="col-sm-3 control-label">Facebook Url<span class="text-red"> * </span></label>

                                <div class="col-sm-6">
                                    <input type="url" class="form-control" id="facebook_url" name="facebook_url" placeholder="Enter facebook url" value="{{$data['facebook_url']}}">
                                </div>
                                 @if ($errors->has('facebook_url'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('facebook_url') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group" {{ $errors->has('twitter_url') ? ' has-error' : '' }}>
                                <label for="twitter_url" class="col-sm-3 control-label">Twitter Url<span class="text-red"> * </span></label>

                                <div class="col-sm-6">
                                    <input type="url" class="form-control" id="twitter_url" name="twitter_url" placeholder="Enter twitter url" value="{{$data['twitter_url']}}">
                                </div>
                                 @if ($errors->has('twitter_url'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('twitter_url') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group"{{ $errors->has('googleplus_url') ? ' has-error' : '' }}>
                                <label for="googleplus_url" class="col-sm-3 control-label">Google plus Url<span class="text-red"> * </span></label>

                                <div class="col-sm-6">
                                    <input type="url" class="form-control" id="googleplus_url" name="googleplus_url" placeholder="Enter googleplus url" value="{{$data['googleplus_url']}}">
                                </div>
                                 @if ($errors->has('googleplus_url'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('googleplus_url') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group"{{ $errors->has('instagram_url') ? ' has-error' : '' }}>
                                <label for="instagram_url" class="col-sm-3 control-label">Instagram Url<span class="text-red"> * </span></label>

                                <div class="col-sm-6">
                                    <input type="url" class="form-control" id="instagram_url" name="instagram_url" placeholder="Enter instagram url" value="{{$data['instagram_url']}}">
                                </div>
                                 @if ($errors->has('instagram_url'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('instagram_url') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group"{{ $errors->has('youtube_url') ? ' has-error' : '' }}>
                                <label for="youtube_url" class="col-sm-3 control-label">You-tube Url<span class="text-red"> * </span></label>

                                <div class="col-sm-6">
                                    <input type="url" class="form-control" id="youtube_url" name="youtube_url" placeholder="Enter youtube url" value="{{$data['youtube_url']}}">
                                </div>
                                 @if ($errors->has('youtube_url'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('youtube_url') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                       <!--  contact_telephone
                        contact_address -->

                         <div class="box-body">
                            <div class="form-group" {{ $errors->has('contact_telephone') ? ' has-error' : '' }}>
                                <label for="contact_telephone" class="col-sm-3 control-label">Contact Telephone No<span class="text-red"> * </span></label>

                                <div class="col-sm-6">
                                    <input type="tel" class="form-control" id="contact_telephone" name="contact_telephone" placeholder="Enter Telephone" value="{{$data['contact_telephone']}}">
                                </div>
                                 @if ($errors->has('contact_telephone'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('contact_telephone') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group"{{ $errors->has('contact_address') ? ' has-error' : '' }}>
                                <label for="contact_address" class="col-sm-3 control-label">Contact Address<span class="text-red"> * </span></label>
                                <div class="col-sm-6">
                                    <textarea class="form-control" id="contact_address" name="contact_address" rows="4">{{$data['contact_address']}}</textarea>
                                </div>
                                @if ($errors->has('contact_address'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('contact_address') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                       
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <div class="col-sm-3">&nbsp;</div>
                            <div class="col-sm-6">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <button class="btn btn-danger" type="button" onclick="window.location='{{ url("/administrator/home") }}'">Cancel</button>
                            </div>
                        </div>
          
                </form>
            </div>
        </div>
    </div>
</section>
<!-- CK Editor -->
<script src="https://cdn.ckeditor.com/4.5.7/standard/ckeditor.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script>
  $(function () {
    CKEDITOR.replace('address');
  });

  $(function () {
      CKEDITOR.replace('contact_address');
  });
</script>
@endsection
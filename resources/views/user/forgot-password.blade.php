@extends('layouts.user')
@section('content')


<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/pages/auth/forgot-pwd.css') }}">

<!-- START SECTION BREADCRUMB -->
<div class="breadcrumb_section bg_gray page-title-mini">
    <div class="container"><!-- STRART CONTAINER -->
        <div class="row align-items-center">
          <div class="col-md-6">
                <div class="page-title">
                <h1>Forgot Password</h1>
                </div>
            </div>
            <div class="col-md-6">
                <ol class="breadcrumb justify-content-md-end">
                    <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
                    <li class="breadcrumb-item active">Forgot Password</li>
                </ol>
            </div>
        </div>
    </div><!-- END CONTAINER-->
</div>
<!-- END SECTION BREADCRUMB -->


<div class="content">
  <div class="listingContent">
    <div class="container">

      <!-- START MAIN CONTENT -->
      <div class="main_content">
          <div  class="row mt-3">
              <div class="col-lg-6 col-md-8 col-sm-12 form-container">
                  <form class="row mt-3" action="forgot-password" method="post" name="frmLogin" id="frmLogin">
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
                      <div class="col-12 form-group">
                          <label for="email_address">
                              Email Address
                              <span>*</span>
                          </label>
                          <input class="form-control" type="email" name="email_address" id="email_address" value="{{ old('email_address') }}" required>
                          @if ($errors->has('email_address'))
                              <div class="alert alert-danger">
                                  {{ $errors->first('email_address') }}
                              </div>
                          @endif

                          @if(Session::has('error'))
                              <div class="alert alert-danger">
                                  {{Session::get('error')}}
                              </div>
                          @endif
                      </div>

                      <div class="form-group col-12">
                          <button class="btn btn-fill-out form-control" type="submit">Submit</button>
                      </div>
                      <div class="form-group col-12 register-forgot">
                          <a href="{{  url('/login') }}">Login Here</a>
                      </div>
                  </form>
              </div>
          </div>
      </div>
      <!-- END MAIN CONTENT -->

    </div>
  </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
  $('#confirmBox').delay(10000).fadeOut();
}); 
</script>
@endsection
@extends('layouts.user')
@section('content')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/pages/auth/reset-pwd.css') }}">

  <!-- START SECTION BREADCRUMB -->
  <div class="breadcrumb_section bg_gray page-title-mini">
      <div class="container"><!-- STRART CONTAINER -->
          <div class="row align-items-center">
            <div class="col-md-6">
                  <div class="page-title">
                  <h1>Change Password</h1>
                  </div>
              </div>
              <div class="col-md-6">
                  <ol class="breadcrumb justify-content-md-end">
                      <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
                      <li class="breadcrumb-item active">Change Password</li>
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
                  <div class="row">
                      @if(Session::has('error'))
                          <div class="alert alert-danger">
                              {{Session::get('error')}}
                          </div>
                          <br>
                          <br>
                      @endif
                      @if(Session::has('success'))
                          <div class="alert alert-success">
                              {{Session::get('success')}}
                          </div>
                      @endif
                  </div>
                  <form class="row mt-3" action="/change-password" method="post" name="frmLogin" id="frmLogin">
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
                      <div class="col-12 form-group">
                          <label for="password">
                              Old Password
                              <span>*</span>
                          </label>
                          <input class="form-control" type="password" name="oldPassword" id="oldPassword" value="{{ old('oldPassword') }}" required>
                          @if ($errors->has('oldPassword'))
                            <div class="alert alert-danger">
                                {{ $errors->first('oldPassword') }}
                            </div>
                          @endif
                      </div>
                      <div class="col-12 form-group">
                          <label for="password">
                              New Password
                              <span>*</span>
                          </label>
                          <input class="form-control" type="password" name="password" id="password" value="{{ old('password') }}" required>
                          @if ($errors->has('password'))
                            <div class="alert alert-danger">
                                {{ $errors->first('password') }}
                            </div>
                          @endif
                      </div>
                      <div class="col-12 form-group">
                          <label for="password">
                              Confirm Password
                              <span>*</span>
                          </label>
                          <input class="form-control" type="password" name="confirmPassword" id="confirmPassword" value="{{ old('confirmPassword') }}" required>
                          @if ($errors->has('confirmPassword'))
                            <div class="alert alert-danger">
                                {{ $errors->first('confirmPassword') }}
                            </div>
                          @endif
                      </div>

                      @if ($errors->has('email_address'))
                          <div class="col-12 alert alert-danger">
                              {{ $errors->first('email_address') }}
                          </div>
                      @endif

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
@endsection
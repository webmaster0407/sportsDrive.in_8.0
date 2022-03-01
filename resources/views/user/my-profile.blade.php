@extends('layouts.user')
@section('content')

<!-- START SECTION BREADCRUMB -->
<div class="breadcrumb_section bg_gray page-title-mini">
  <div class="container"><!-- STRART CONTAINER -->
      <div class="row align-items-center">
        <div class="col-md-6">
              <div class="page-title">
              <h1>My Profile</h1>
              </div>
          </div>
          <div class="col-md-6">
              <ol class="breadcrumb justify-content-md-end">
                  <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
                  <li class="breadcrumb-item active">Profile</li>
              </ol>
          </div>
      </div>
  </div><!-- END CONTAINER-->
</div>
<!-- END SECTION BREADCRUMB -->


<!-- START MAIN CONTENT -->
<div class="main_content">
    <div class="container">
        @if(Session::has('error'))
            <div class="alert alert-danger address-fail">
                {{Session::get('error')}}
            </div>
        @endif
        @if(Session::has('success'))
            <div class="alert alert-success address-success">
                {{Session::get('success')}}
            </div>
        @endif
        <div  class="row mt-3">
            <div class="col-lg-6 col-md-8 col-sm-12 form-container" style="margin: auto;">
                <form class="editForm row mt-3" id="frmRegister" action="/my-profile" method="post" name="frmRegister">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="col-12 form-group">
                        <p class="note-msg"></p>
                    </div>

                    <div class="col-12 form-group">
                        <label for="first_name">
                            First Name
                            <span>*</span>
                        </label>
                        <input class="form-control" type="text" name="first_name" id="first_name" value="{{$user->first_name}}" required minlength="3">
                        @if ($errors->has('first_name'))
                        <div class="alert alert-danger">
                            {{ $errors->first('first_name') }}
                        </div>
                        @endif
                    </div>

                    <div class="col-12 form-group">
                        <label for="last_name">
                            Last Name
                            <span>*</span>
                        </label>
                        <input class="form-control" type="text" name="last_name" id="last_name" value="{{$user->last_name}}" required minlength="3">
                        @if ($errors->has('last_name'))
                        <div class="alert alert-danger">
                            {{ $errors->first('last_name') }}
                        </div>
                        @endif
                    </div>          

                    <div class="col-12 form-group">
                        <label for="email_address">
                            Email
                            <span>*</span>
                        </label>
                        <input class="form-control" type="email" name="email_address" id="email_address" value="{{$user->email_address}}" readonly>
                        @if ($errors->has('email_address'))
                        <div class="alert alert-danger">
                            {{ $errors->first('email_address') }}
                        </div>
                        @endif
                    </div>   

                    <div class="col-12 form-group">
                        <label for="phone">
                            Mobile No.
                            <span>*</span>
                        </label>
                        <input class="form-control" type="text" name="phone" id="phone" value="{{$user->phone}}" readonly>
                        @if ($errors->has('phone'))
                        <div class="alert alert-danger">
                            {{ $errors->first('phone') }}
                        </div>
                        @endif
                    </div>

                    <div class="col-12 form-group">
                        <button class="btn btn-fill-out" type="submit">Submit</button>
                        <button class="btn btn-fill-line" id="cancelBtn">Cancel</button>
                    </div>   

                </form>
            </div>
        </div>
    </div>
</div>
<!-- END MAIN CONTENT -->
<script type="text/javascript">
    $(document).ready(function() {
        $('#cancelBtn').on('click', function(e) {
            e.preventDefault();

            window.location = "{{  url('/login') }}";
        })
    });
</script>

@endsection
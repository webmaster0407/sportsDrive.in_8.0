@extends('layouts.user')
@section('content')
    <script language="Javascript" type="text/javascript">
      function allowAlphaNumericSpace(e) {
        var code = ('charCode' in e) ? e.charCode : e.keyCode;
        if (!(code == 32) && // space
                !(code > 47 && code < 58) && // numeric (0-9)
                !(code > 64 && code < 91) && // upper alpha (A-Z)
                !(code >= 44 && code <= 45) && // - and ,
                !(code > 96 && code < 123)) { // lower alpha (a-z)
          alert("Only Alphabets, Numbers , hyphen(-)  and commas(,) are allowed in address fields");
          return false;
        }
      }
  </script>

<!-- START SECTION BREADCRUMB -->
<div class="breadcrumb_section bg_gray page-title-mini">
    <div class="container"><!-- STRART CONTAINER -->
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-title">
                    <h1>Add adress</h1>
                </div>
            </div>
            <div class="col-md-6">
                <ol class="breadcrumb justify-content-md-end">
                    <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
                    <li class="breadcrumb-item active">Add address</li>
                </ol>
            </div>
        </div>
    </div><!-- END CONTAINER-->
</div>
<!-- END SECTION BREADCRUMB -->


<!-- START MAIN CONTENT -->
<div class="main_content">
        <div class="container">
            <div  class="row mt-3">
                <div class="col-lg-6 col-md-8 col-sm-12 form-container" style="margin: auto;">
                    <form class="row mt-3" id="frmAddAddress" action="/address/add" method="post" name="frmAddAddress">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="is_shipping" value="{{ $data['is_shipping'] }}">
                        <input type="hidden" name="is_billing" value="{{ $data['is_billing'] }}">
                        <input type="hidden" name="route" value="{{ $data['route'] }}">

                        <div class="col-12 form-group">
                            <label for="full_name">
                                Full Name
                                <span>*</span>
                            </label>
                            <input class="form-control" type="text" name="full_name" id="full_name" value="{{ old('full_name') }}">
                            @if ($errors->has('full_name'))
                                <div class="alert alert-danger">
                                    {{ $errors->first('full_name') }}
                                </div>
                            @endif
                        </div>

                        <div class="col-12 form-group">
                            <label for="address_title">
                                Address Title
                                <span>*</span>
                            </label>
                            <select name="address_title" id="address_title">
                                <option <?php if(old('address_title')=="Home") echo "selected";?> value="Home">Home</option>
                                <option  <?php if(old('address_title')=="Office") echo "selected";?> value="Office">Office</option>
                                <option  <?php if(old('address_title')=="Other") echo "selected";?> value="Other">Other</option>
                            </select>
                            @if ($errors->has('address_title'))
                                <div class="alert alert-danger">
                                    {{ $errors->first('address_title') }}
                                </div>
                            @endif
                        </div>


                        <div class="col-12 form-group">
                            <label for="address_line_1">
                                Address Line 1
                                <span>*</span>
                            </label>
                            <input class="form-control" type="text" name="address_line_1" id="address_line_1" value="{{ old('address_line_1') }}" onkeypress="return allowAlphaNumericSpace(event);">
                            @if ($errors->has('address_line_1'))
                                <div class="alert alert-danger">
                                    {{ $errors->first('address_line_1') }}
                                </div>
                            @endif
                        </div>

                        <div class="col-12 form-group">
                            <label for="address_line_2">
                                Address Line 2
                                <span>*</span>
                            </label>
                            <input class="form-control" type="text" name="address_line_2" id="address_line_2" value="{{ old('address_line_2') }}" onkeypress="return allowAlphaNumericSpace(event);">
                            @if ($errors->has('address_line_2'))
                                <div class="alert alert-danger">
                                    {{ $errors->first('address_line_2') }}
                                </div>
                            @endif
                        </div>     

                        <div class="col-12 form-group">
                            <label for="city">
                                City
                                <span>*</span>
                            </label>
                            <input class="form-control" type="text" name="city" id="city" value="{{ old('city') }}" onkeypress="return allowAlphaNumericSpace(event);">
                            @if ($errors->has('city'))
                                <div class="alert alert-danger">
                                    {{ $errors->first('city') }}
                                </div>
                            @endif
                        </div>   

                        <div class="col-12 form-group">
                            <label for="state">
                                State
                                <span>*</span>
                            </label>
                            <input class="form-control" type="text" name="state" id="state" value="{{ old('state') }}" onkeypress="return allowAlphaNumericSpace(event);">
                            @if ($errors->has('state'))
                                <div class="alert alert-danger">
                                    {{ $errors->first('state') }}
                                </div>
                            @endif
                        </div>   

                        <div class="col-12 form-group">
                            <label for="country">
                                Country
                                <span>*</span>
                            </label>
                            <input class="form-control" type="text" name="country" id="country" value="{{ old('country') }}" onkeypress="return allowAlphaNumericSpace(event);">
                            @if ($errors->has('country'))
                                <div class="alert alert-danger">
                                    {{ $errors->first('country') }}
                                </div>
                            @endif
                        </div>  

                        <div class="col-12 form-group">
                            <label for="pin_code">
                                Pin Code
                                <span>*</span>
                            </label>
                            <input class="form-control" type="number" name="pin_code" id="pin_code" value="{{ old('pin_code') }}" onkeypress="return allowAlphaNumericSpace(event);">
                            @if ($errors->has('pin_code'))
                                <div class="alert alert-danger">
                                    {{ $errors->first('pin_code') }}
                                </div>
                            @endif
                        </div>  

                        <div class="col-12 form-group">
                            <label for="contact_no">
                               Contact No
                                <span>*</span>
                            </label>
                            <input class="form-control" type="number" name="contact_no" id="contact_no" value="{{ old('contact_no') }}" onkeypress="return allowAlphaNumericSpace(event);">
                            @if ($errors->has('contact_no'))
                                <div class="alert alert-danger">
                                    {{ $errors->first('contact_no') }}
                                </div>
                            @endif
                        </div>  

                        <div class="col-12 form-group">
                          <div class="row">
                            <div class="col-md-6">
                              <button type="submit" class="btn btn-fill-out form-control">Submit</button>
                            </div>
                            <div class="col-md-6">
                              <a href="{{  url('/login') }}">
                                <button type="button" class="btn btn-fill-line form-control">Back To Login</button>
                              </a>
                            </div>
                          </div>
                        </div>

                    </form>
                </div>
            </div>
    </div>
</div>
<!-- END MAIN CONTENT -->

@endsection
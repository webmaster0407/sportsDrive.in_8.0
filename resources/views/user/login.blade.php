


<!-- START MAIN CONTENT -->
<div class="main_content">

    <div  class="row mt-3">
        <div class="col-lg-6 col-md-8 col-sm-12 form-container">
            <div class="successAlert">
                @if(Session::has('success'))
                    <div class="confirmBox" id="confirmBox">
                        <div class="message"> 
                          {{Session::get('success')}}
                        </div>
                    </div>
                @endif
                <form class="row mt-3" id="login" action="login" method="post" name="frmLogin">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="col-12">
                        {!! $cmsPage['description']!!}
                    </div>
                    <div class="col-12 form-group">
                        <label for="email_address">
                            Email Address
                            <span>*</span>
                        </label>
                        <input class="form-control" type="email" name="email_address" id="email_address" value="{{ old('email_address') }}">
                        @if ($errors->has('email_address'))
                            <div class="alert alert-danger">
                                {{ $errors->first('email_address') }}
                            </div>
                        @endif
                    </div>

                    <div class="col-12 form-group">
                        <label for="password">
                            Password
                            <span>*</span>
                        </label>
                        <input class="form-control" type="password" name="password" id="password">
                        @if ($errors->has('password'))
                            <div class="alert alert-danger">
                                {{ $errors->first('password') }}
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
                        <a href="{{  url('/register') }}">Don't have an account?</a>
                        <a href="{{  url('/forgot-password') }}">Forgot Password?</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
<!-- END MAIN CONTENT -->

<script type="text/javascript">
$(document).ready(function() {
    $('#confirmBox').delay(500).fadeOut();
});
</script>



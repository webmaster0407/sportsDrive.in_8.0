
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
                <form class="row mt-3" id="frmRegister" action="#" method="post" name="frmRegister">
                    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                    <div class="col-12 paraRow">
                        {!! $cmsPage['description']!!}
                    </div>

                    <div class="col-12 form-group">
                        <label for="first_name">
                            First Name
                            <span>*</span>
                        </label>
                        <input class="form-control" type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required minlength="3">
                        <div class="alert alert-danger" style="display: none" id="fname_val"></div>
                    </div>
                    

                    <div class="col-12 form-group">
                        <label for="last_name">
                            Last Name
                            <span>*</span>
                        </label>
                        <input class="form-control" type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required minlength="3">
                        <div class="alert alert-danger" style="display: none" id="lname_val"></div>
                    </div>
                    

                    <div class="col-12 form-group">
                        <label for="last_name">
                            MOBILE number : 
                            <span>*</span>
                        </label>
                        <input class="form-control" type="text" name="phone" id="phone" value="{{ old('phone') }}" required minlength="10" maxlength="10">
                        <div class="alert alert-danger" style="display: none" id="phone_val"></div>
                    </div>
                    


                    <div class="col-12 form-group">
                        <label for="email_address">
                            Email Address
                            <span>*</span>
                        </label>
                        <input class="form-control" type="email" name="email_address" id="email_address" value="{{ old('email_address') }}" required minlength="3">
                        <div class="alert alert-danger" style="display: none" id="email_val"></div>
                    </div>
                    

                    <div class="col-12 form-group">
                        <label for="password">
                            Password
                            <span>*</span>
                        </label>
                        <input class="form-control" type="password" name="password" id="password"  minlength="5" required>
                        <div class="alert alert-danger" style="display: none" id="pass_val"></div>
                    </div>
                    

                    <div class="col-12 form-group">
                        <label for="password">
                            Re-Enter Password
                            <span>*</span>
                        </label>
                        <input class="form-control" type="password" name="re_password" id="re_password"  minlength="5" required>
                        <div class="alert alert-danger" style="display: none" id="cpass_val"></div>
                        <div class="alert alert-danger" style="display: none" id="pAndCPass_val"></div>
                    </div>



                    <div class="form-group col-12">
                        <button class="btn btn-fill-out form-control" type="submit" id="submit">Submit</button>
                    </div>
                    <div class="form-group col-12 register-forgot">
                        <a href="{{  url('/login') }}">Back To Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
<!-- END MAIN CONTENT -->

<!-- Register model starts -->

<!-- Modal -->
<div class="modal fade" id="register_model" role="dialog"  tabindex='-1'>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" align="center">Please Enter the OTP here</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Mobile OTP:</label>
                            <br/>
                            <input type="text" name="otp" value="" id="mobile_otp" class="form-control">
                            <small>Your 3 digit OTP has been sent to your mobile number. Please check your SMS</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email OTP:</label>
                            <br/>
                            <input type="text" name="otp" value="" id="email_otp" class="form-control">
                            <small>Your 3 digit OTP has been sent to your email ID. Please check your email</small>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <input type="button"  class="btn btn-primary" id="submit_otp" name="submit_otp" value="Verify OTP">
            </div>
        </div>
    </div>
</div>
<!-- Register model ends -->

<!-- Added notification msg library by Akshay Patil on 27th Nov 2018-->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<!-- Added notification msg library-->

<script type="text/javascript">
$(document).ready(function() {
   
    // $(window).load(function() {
    //     
    // });
    $('#confirmBox').delay(10000).fadeOut();

    $("#submit").on('click', function (e) {
        e.preventDefault();
        var first_name = document.forms["frmRegister"]["first_name"];
        var last_name = document.forms["frmRegister"]["last_name"];
        var email_address = document.forms["frmRegister"]["email_address"];
        var phone = document.forms["frmRegister"]["phone"];
        var password = document.forms["frmRegister"]["password"];
        var re_password = document.forms["frmRegister"]["re_password"];
        $(".alert").hide();
        re_passwordFlag = 0;
        var flag = 0;
        if (first_name.value == "")
        {
            $("#fname_val").text("First Name is required").show();
            flag = 1;
        }
        if (last_name.value == "")
        {
            $("#lname_val").text("Last Name is required").show();
            flag = 1;
        }
        if (email_address.value == "")
        {
            $("#email_val").text("Email is required").show();
            flag = 1;
        }else if(email_address.value != ""){
            var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            if(!re.test(email_address.value)){
                $("#email_val").text("Please enter proper email address").show();
                flag = 1;
            }
        }
        if (phone.value == "")
        {
            $("#phone_val").text("Enter your 10 digit mobile number").show();
            $("#phone_msg").hide();
            flag = 1;
        }else if(phone.value != ""){
            var phoneNum = phone.value.replace(/[^\d]/g, '');
            if(phoneNum.length < 6 || phoneNum.length > 11) {
                $("#phone_val").text("Please enter valid phone number").show();
                flag = 1;
            }
        }
        if (password.value == "" || password.value.length < 6 || password.value.length > 20)
        {
            $("#pass_val").text("Password is required and should be minimum 6 characters and Max 20 characters.").show();
            flag = 1;
        }
        if (re_password.value == "" || re_password.value.length < 6 || re_password.value.length > 20)
        {
            $("#cpass_val").text("Confirm Password is required and should be minimum 6 characters and Max 20 characters.").show();
            flag = 1;
            re_passwordFlag = 1;
        }

        if (re_password.value != password.value)
        {
            if(re_passwordFlag != 1)
                $("#pAndCPass_val").text("Password and Confirm Password should be same").show();
            flag = 1;
        }
        if(flag == "1"){
            window.location.hash = "frmRegister";
            return false;
        }
        var token = $('meta[name="csrf_token"]').attr('content');
        $.ajax({
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': token
            },
            url: "/send-otp",
            data: { 
                "mobile":phone.value,
                "email_address":email_address.value,
                "first_name":first_name.value,
                "last_name":last_name.value
            },
            success: function (data) {
                if(data.status == "200"){
                   $("#submit_otp").click();
                }else{
                    if (data.message)
                        iziToast.show({
                            icon: 'fa fa-check',
                            backgroundColor: '#ffb3b3',
                            title: 'Error!',
                            pauseOnHover: false,
                            position: 'topCenter', // bottomRight, bottomLeft, topRight, topLeft, topCenter, bottomCenter
                            message: data.message,
                        });
                    else
                        iziToast.show({
                            icon: 'fa fa-check',
                            backgroundColor: '#ff8080',
                            title: 'Error!',
                            pauseOnHover: false,
                            position: 'topCenter', // bottomRight, bottomLeft, topRight, topLeft, topCenter, bottomCenter
                            message: "Something went wrong while processing your request! Please try again.",
                        });
                    return false;
                }
            }
        });
        return false;
    });

    $("#submit_otp").click(function () {
        var values = {};
        $.each($('#frmRegister').serializeArray(), function(i, field) {
            values[field.name] = field.value;
        });
        mobile_otp = $("#mobile_otp").val();
        email_otp = $("#email_otp").val();
        otp_field = mobile_otp+""+email_otp;
        var token = $('meta[name="csrf_token"]').attr('content');
        $.ajax({
            type: "POST",
            headers: {'X-CSRF-TOKEN': token},
            url: "/register",
            data: {"phone":values.phone,"email_address":values.email_address,"first_name":values.first_name,"last_name":values.last_name,"password":values.password,"re-password":values.re_password,"otp":otp_field},
            success: function (data) {
              console.log(data);
               if(data.status == 200){
                   iziToast.show({
                       icon: 'fa fa-check',
                       backgroundColor: '#b3cf7a',
                       title: 'Success!',
                       pauseOnHover: false,
                       position: 'topCenter', // bottomRight, bottomLeft, topRight, topLeft, topCenter, bottomCenter
                       message: 'You are registered and logged In successfully! Please wait while we are redirecting you.',
                       onClosing: function(){
                           window.location.replace("/");
                       }
                   });
               }else{
                   iziToast.show({
                       icon: 'fa fa-check',
                       backgroundColor: '#ff8080',
                       title: 'Error!',
                       pauseOnHover: false,
                       position: 'topCenter', // bottomRight, bottomLeft, topRight, topLeft, topCenter, bottomCenter
                       message: data.msg,
                   });
               }
            }
        });
    });
});

 
</script>

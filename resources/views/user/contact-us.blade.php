
<div class="content">
  <div class="container">
    <div class="loginMiddle">

      <div class="middleCard">
        <h1 class="title">{{$cmsPage['page_title']}}</h1>
        @if(Session::has('success'))
            <div class="confirmBox" id="confirmBox">
           <div class="message"> {{Session::get('success')}}</div>
          </div>
        @endif
        @if(Session::has('error'))
            <div class="alert alert-danger">
           {{Session::get('error')}}
          </div>
        @endif
        <form class="login" id="frmContact" action="contact-us" method="post" name="frmContact">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <div class="row paraRow">
            {!! $cmsPage['description']!!}
          </div>
          <div class="row">
            <label>First Name<span>*</span></label>
            <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}">
          <div class="bar"></div>
          @if ($errors->has('first_name'))
            <div class="alert alert-danger">
                {{ $errors->first('first_name') }}
            </div>
          @endif
          </div>
          <div class="row">
            <label>Last Name<span>*</span></label>
            <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}">
          <div class="bar"></div>
          @if ($errors->has('last_name'))
            <div class="alert alert-danger">
                {{ $errors->first('last_name') }}
            </div>
        @endif
          </div>
          <div class="row">
          <label>Email<span>*</span></label>
            <input type="email" name="email_address" id="email_address" value="{{ old('email_address') }}">
            <div class="bar"></div>
            @if ($errors->has('email_address'))
            <div class="alert alert-danger">
                {{ $errors->first('email_address') }}
            </div>
        @endif
          </div>
          <div class="row">
          <label>Phone no<span>*</span></label>
            <input type="text" name="phone" id="phone" value="{{ old('phone') }}">
            <div class="bar"></div>
            @if ($errors->has('phone'))
            <div class="alert alert-danger">
                {{ $errors->first('phone') }}
            </div>
        @endif
          </div>
         <div class="row">
          <label>Message</label>
            <textarea class="form-control" id="message" name="message" style="margin: 0px; width: 361px; height: 74px;"></textarea>
            @if ($errors->has('message'))
            <div class="alert alert-danger">
                {{ $errors->first('message') }}
            </div>
        @endif
          </div>
          <div class="row subBtn">
            <button type="submit"><span>Submit</span></button> 
          </div>
         <!--  <div class="row  btnRow">
            <a href="{{  url('/login') }}">Back To Login</a>
          </div> -->
          
        </form>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
$(window).load(function() {
       $('#confirmBox').delay(10000).fadeOut();
    }); 
</script>

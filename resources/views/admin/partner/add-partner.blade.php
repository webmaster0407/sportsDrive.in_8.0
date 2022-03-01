@extends('layouts.admin')
@push('stylesheets')
  <!-- DataTables -->
  <link rel="stylesheet" href="../../plugins/datatables/dataTables.bootstrap.css">
@endpush
@push('scripts')
<script src="{{asset("plugins/jQuery/jquery-2.2.3.min.js")}}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
@endpush
@section('content')
<section class="content-header">
    <h1>
        Partner
    </h1>
    <ol class="breadcrumb">
        <li><a href="home"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="/administrator/list-customer">List Partner</a></li>
        <li class="active"> @if($data->mode == 'edit')Edit @else Add @endif Partner</li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"> @if($data->mode == 'edit')Edit @else Add @endif  Customer</h3>
                </div>

                <div class="box">
                  <div class="box-header with-border">
                    <h3 class="box-title"></h3>
                  </div>
                  <!-- form start -->

                   <form class="form-horizontal" method="POST" enctype="multipart/form-data" name="frmCustomer" id="frmCustomer"
                          @if($data->mode == 'edit')
                          action="/administrator/update-partner-data"
                          @else
                          action="/administrator/add-partner-data"
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
                        <input type="hidden" name="customer_id" value="{{$data->id}}" />
                        <div class="form-group {{ $errors->has('first_name') ? ' has-error' : '' }}">
                            <label class="col-sm-3 control-label">First Name<span class="label-mandatory">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="first_name" required id="first_name"  value="<?php if(old('first_name')!=null) echo old('first_name');
                                    else echo $data->first_name;?>">
                                @if ($errors->has('first_name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('first_name') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group {{ $errors->has('last_name') ? ' has-error' : '' }}">
                            <label class="col-sm-3 control-label">Last Name<span class="label-mandatory">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="last_name" id="last_name" required value="<?php if(old('last_name')!=null) echo old('last_name');
                                    else echo $data->last_name;?>">
                                @if ($errors->has('last_name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('last_name') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>                        

                        <div class="form-group {{ $errors->has('email_address') ? 'has-error' : '' }}">
                            <label class="col-sm-3 control-label">Email<span class="label-mandatory">*</span></label>
                            <div class="col-sm-6">
                                <input type="email" class="form-control" name="email_address" id="email_address" required value="<?php if(old('email_address')!=null) echo old('email_address');
                                    else echo $data->email_address;?>">
                                @if ($errors->has('email_address'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email_address') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                            <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                                <label class="col-sm-3 control-label">Password<span class="label-mandatory">*</span></label>
                                <div class="col-sm-6">
                                    <input type="password" class="form-control" name="password" id="password" required value="<?php if(old('password')!=null) echo old('password');  else echo $data->password;?>">
                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                        <div class="form-group {{ $errors->has('phone') ? ' has-error' : '' }}">
                            <label class="col-sm-3 control-label">Phone<span class="label-mandatory">*</span></label></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="phone" id="phone"  required value="<?php if(old('phone')!=null) echo old('phone');
                                    elseif($data->phone != null) echo $data->phone;?>">
                                @if ($errors->has('phone'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('phone') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                            <div class="form-group {{ $errors->has('address') ? ' has-error' : '' }}">
                                <label class="col-sm-3 control-label">Address<span class="label-mandatory">*</span></label>
                                <div class="col-sm-6">
                                    <textarea class="form-control" name="address" id="address" required><?php if(old('address')!=null) echo old('address');  elseif($data->address != null) echo $data->address;?></textarea>
                                    @if ($errors->has('address'))
                                        <span class="help-block">
                                    <strong>{{ $errors->first('address') }}</strong>
                                </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group {{ $errors->has('city') ? ' has-error' : '' }}">
                                <label class="col-sm-3 control-label">City<span class="label-mandatory">*</span></label></label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="city" id="city" required value="<?php if(old('city')!=null) echo old('city');  elseif($data->city != null) echo $data->city;?>">
                                    @if ($errors->has('city'))
                                        <span class="help-block">
                                    <strong>{{ $errors->first('city') }}</strong>
                                </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>



                            <div class="form-group {{ $errors->has('instagram:') ? ' has-error' : '' }}">
                                <label class="col-sm-3 control-label">Instagram<span class="label-mandatory">*</span></label></label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" required name="instagram" id="instagram"  value="<?php if(old('instagram')!=null) echo old('instagram');  elseif($data->instagram != null) echo $data->instagram;?>">
                                    @if ($errors->has('instagram'))
                                        <span class="help-block">
                                    <strong>{{ $errors->first('instagram') }}</strong>
                                </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>


                            <div class="form-group {{ $errors->has('facebook:') ? ' has-error' : '' }}">
                                <label class="col-sm-3 control-label">Facebook:<span class="label-mandatory">*</span></label></label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="facebook" required id="facebook"  value="<?php if(old('facebook')!=null) echo old('facebook');  elseif($data->facebook != null) echo $data->facebook;?>">
                                    @if ($errors->has('facebook'))
                                        <span class="help-block">
                                    <strong>{{ $errors->first('facebook') }}</strong>
                                </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>


                            <div class="form-group {{ $errors->has('linkedin:') ? ' has-error' : '' }}">
                                <label class="col-sm-3 control-label">Linkedin:<span class="label-mandatory">*</span></label></label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="linkedin" required id="linkedin"  value="<?php if(old('linkedin')!=null) echo old('linkedin');  elseif($data->linkedin != null) echo $data->linkedin;?>">
                                    @if ($errors->has('linkedin'))
                                        <span class="help-block">
                                    <strong>{{ $errors->first('linkedin') }}</strong>
                                </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group {{ $errors->has('pan_no:') ? ' has-error' : '' }}">
                                <label class="col-sm-3 control-label">Pan No:<span class="label-mandatory">*</span></label></label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="pan_no" required id="pan_no"  value="<?php if(old('pan_no')!=null) echo old('pan_no');  elseif($data->pan_no != null) echo $data->pan_no;?>">
                                    @if ($errors->has('pan_no'))
                                        <span class="help-block">
                                    <strong>{{ $errors->first('pan_no') }}</strong>
                                </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group {{ $errors->has('commission_type:') ? ' has-error' : '' }}">
                                <label class="col-sm-3 control-label">Commission Type:<span class="label-mandatory">*</span></label>
                                <div class="col-sm-6">
                                    <input type="radio" class="radioButtons" name="commission_type" required id="commission_type_flat" value="flat" @if(old('commission_type') == "flat" || $data->commission_type == "flat"){{"checked"}}@endif>Flat Commission
                                    <input type="radio"   class="radioButtons" name="commission_type" required id="commission_type_per" @if(old('commission_type') != "flat" && $data->commission_type != "flat"){{"checked"}}@endif value="per">Percentage Based Commission
                                    @if ($errors->has('commission_type'))
                                        <span class="help-block">
                                    <strong>{{ $errors->first('commission_type') }}</strong>
                                </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>


                            <div id="flat_commission_div" class="form-group {{ $errors->has('flat_comission:') ? ' has-error' : '' }}">
                                <label class="col-sm-3 control-label">Flat Commission:<span class="label-mandatory">*</span></label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="flat_comission" id="flat_comission"  value="<?php if(old('flat_comission')!=null) echo old('flat_comission');  elseif($data->flat_comission != null) echo $data->flat_comission;?>">
                                    @if ($errors->has('flat_comission'))
                                        <span class="help-block">
                                    <strong>{{ $errors->first('flat_comission') }}</strong>
                                </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group {{ $errors->has('coupons') ? ' has-error' : '' }}">
                                <label class="col-sm-3 control-label">Partner Coupons</label>
                                <div class="col-sm-6">
                                    <select multiple class="form-control select2" name="coupons[]" id="coupons" required>
                                        @foreach($data->coupons as $coupon)
                                            <option @if(in_array($coupon->id,$partnerCoupons)){{"selected"}}@endif value="{{$coupon->id}}">{{$coupon->name}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('coupons'))
                                        <span class="help-block">
                                    <strong>{{ $errors->first('coupons') }}</strong>
                                </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="box-footer">
                          <label class="col-sm-3 control-label"></label>
                          <div class="col-sm-6">
                                <button class="btn btn-primary" type="submit">Submit</button>
                                <button class="btn btn-danger" type="button" onclick="window.location='{{ url("/administrator/list-partner") }}'">Cancel</button>
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
  <script>
      $(document).ready(function() {
          if($("#commission_type_flat")[0].checked){
              $("#flat_commission_div").show()
          }else{
              $("#flat_comission").val("");
              $("#flat_commission_div").hide()
          }
          $('.radioButtons').click(function(){
              if($("#commission_type_flat")[0].checked){
                  $("#flat_commission_div").show()
              }else{
                  $("#flat_commission_div").hide()
                  $("#flat_comission").val("");              }
          });
      });

  </script>
@endpush

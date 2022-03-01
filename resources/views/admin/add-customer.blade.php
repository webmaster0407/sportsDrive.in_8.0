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
        Customer
    </h1>
    <ol class="breadcrumb">
        <li><a href="home"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="/administrator/list-customer">List Customer</a></li>
        <li class="active"> @if($data->mode == 'edit')Edit @else Add @endif Customer</li>
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
                          action="/administrator/update-customerdata"
                          @else
                          action="/administrator/add-customerdata"
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
                                <input type="text" class="form-control" name="first_name" id="first_name"  value="<?php if(old('first_name')!=null) echo old('first_name');
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
                                <input type="text" class="form-control" name="last_name" id="last_name"  value="<?php if(old('last_name')!=null) echo old('last_name');
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
                                <input type="email" class="form-control" name="email_address" id="email_address" value="<?php if(old('email_address')!=null) echo old('email_address');
                                    else echo $data->email_address;?>">
                                @if ($errors->has('email_address'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email_address') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group {{ $errors->has('phone') ? ' has-error' : '' }}">
                            <label class="col-sm-3 control-label">Phone</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="phone" id="phone"  value="<?php if(old('phone')!=null) echo old('phone');
                                    elseif($data->phone != null) echo $data->phone;?>">
                                @if ($errors->has('phone'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('phone') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>


                            <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                                <label class="col-sm-3 control-label">Password</label>
                                <div class="col-sm-6">
                                    <input type="password" class="form-control" name="password" id="password"  value="<?php if(old('password') != null) echo old('password');  elseif($data->password != null) echo "notchanged!";?>">
                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>


                            <div class="box-footer">
                          <label class="col-sm-3 control-label"></label>
                          <div class="col-sm-6">
                                <button class="btn btn-primary" type="submit">Submit</button>
                                <button class="btn btn-danger" type="button" onclick="window.location='{{ url("/administrator/list-customer") }}'">Cancel</button>
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
  
@endpush

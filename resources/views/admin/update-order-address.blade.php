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
            Orders
        </h1>
        <ol class="breadcrumb">
            <li class="active">Edit Customer Order Address </li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Edit Customer Order Address </h3>
                    </div>

                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title"></h3>
                        </div>
                        <!-- form start -->
                        <form class="form-horizontal" method="POST" enctype="multipart/form-data" name="frmCustomer" id="frmCustomer" action="/administrator/update-order-address/{{$order_id}}">
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
                            <input type="hidden" name="id" value="{{$addresses->id}}" />
                            <input type="hidden" name="address_type" value="{{$addressType}}" />
                            <div class="form-group {{ $errors->has('address_title') ? ' has-error' : '' }}">
                                <label class="col-sm-3 control-label">Address Title<span class="label-mandatory">*</span></label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="address_title" id="address_title"  value="<?php if(old('address_title')!=null) echo old('address_title');
                                    else echo $addresses->address_title;?>">
                                    @if ($errors->has('address_title'))
                                        <span class="help-block">
                                    <strong>{{ $errors->first('address_title') }}</strong>
                                </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group {{ $errors->has('full_name') ? ' has-error' : '' }}">
                                <label class="col-sm-3 control-label">Full Name<span class="label-mandatory">*</span></label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="full_name" id="full_name"  value="<?php if(old('full_name')!=null) echo old('full_name');
                                    else echo $addresses->full_name;?>">
                                    @if ($errors->has('full_name'))
                                        <span class="help-block">
                                    <strong>{{ $errors->first('full_name') }}</strong>
                                </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group {{ $errors->has('address_line_1') ? 'has-error' : '' }}">
                                <label class="col-sm-3 control-label">address_line_1<span class="label-mandatory">*</span></label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="address_line_1" id="address_line_1" value="<?php if(old('address_line_1')!=null) echo old('address_line_1');
                                    else echo $addresses->address_line_1;?>">
                                    @if ($errors->has('address_line_1'))
                                        <span class="help-block">
                                    <strong>{{ $errors->first('address_line_1') }}</strong>
                                </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group {{ $errors->has('address_line_2') ? ' has-error' : '' }}">
                                <label class="col-sm-3 control-label">address_line_2</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="address_line_2" id="address_line_2"  value="<?php if(old('address_line_2')!=null) echo old('address_line_2');
                                    elseif($addresses->address_line_2 != null) echo $addresses->address_line_2;?>">
                                    @if ($errors->has('address_line_2'))
                                        <span class="help-block">
                                    <strong>{{ $errors->first('address_line_2') }}</strong>
                                </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group {{ $errors->has('city') ? ' has-error' : '' }}">
                                <label class="col-sm-3 control-label">city</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="city" id="city"  value="<?php if(old('city')!=null) echo old('city');
                                    elseif($addresses->city != null) echo $addresses->city;?>">
                                    @if ($errors->has('city'))
                                        <span class="help-block">
                                    <strong>{{ $errors->first('city') }}</strong>
                                </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group {{ $errors->has('state') ? ' has-error' : '' }}">
                                <label class="col-sm-3 control-label">state</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="state" id="state"  value="<?php if(old('state')!=null) echo old('state');
                                    elseif($addresses->state != null) echo $addresses->state;?>">
                                    @if ($errors->has('state'))
                                        <span class="help-block">
                                    <strong>{{ $errors->first('state') }}</strong>
                                </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>


                            <div class="form-group {{ $errors->has('country') ? ' has-error' : '' }}">
                                <label class="col-sm-3 control-label">country</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="country" id="country"  value="<?php if(old('state')!=null) echo old('state');
                                    elseif($addresses->country != null) echo $addresses->country;?>">
                                    @if ($errors->has('country'))
                                        <span class="help-block">
                                    <strong>{{ $errors->first('country') }}</strong>
                                </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group {{ $errors->has('pin_code') ? ' has-error' : '' }}">
                                <label class="col-sm-3 control-label">PinCode</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="pin_code" id="pin_code"  value="<?php if(old('pin_code')!=null) echo old('pin_code');
                                    elseif($addresses->pin_code != null) echo $addresses->pin_code;?>">
                                    @if ($errors->has('pin_code'))
                                        <span class="help-block">
                                    <strong>{{ $errors->first('pin_code') }}</strong>
                                </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>


                            <div class="form-group {{ $errors->has('contact_no') ? ' has-error' : '' }}">
                                <label class="col-sm-3 control-label">contact_no</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="contact_no" id="contact_no"  value="<?php if(old('contact_no')!=null) echo old('contact_no');  elseif($addresses->contact_no != null) echo $addresses->contact_no;?>">
                                    @if ($errors->has('contact_no'))
                                        <span class="help-block"><strong>{{ $errors->first('contact_no') }}</strong></span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>


                            <div class="form-group {{ $errors->has('update_to_customer_master') ? ' has-error' : '' }}">
                                <label class="col-sm-3 control-label">Update To Customer Master</label>
                                <div class="col-sm-6">
                                    <input type="checkbox"  name="update_to_customer_master" id="update_to_customer_master"  >
                                    @if ($errors->has('update_to_customer_master'))
                                        <span class="help-block"><strong>{{ $errors->first('update_to_customer_master') }}</strong></span>
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

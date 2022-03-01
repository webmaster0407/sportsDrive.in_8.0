@extends('layouts.partner')
@push('scripts')
    <script src="{{ asset("plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
@endpush
@section('content')
    <section class="content-header">
        <h1>
            Change Password
        </h1>
        <ol class="breadcrumb">
            <li><a href="/admin"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Change Password</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <!-- <h3 class="box-title">Change Password</h3> -->
                    </div>
                    <!-- form start -->
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
                    <form class="form-horizontal" name="frmChangePassword" id="frmChangePassword" method="POST" action="change-password">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="old_password" class="col-sm-3 control-label">Old Password<span class="text-red"> * </span></label>

                                <div class="col-sm-6">
                                    <input type="password" class="form-control" id="old_password" name="old_password" placeholder="Old Password" value="<?php if(old('old_password')!=null) echo old('old_password');?>">
                                    @if ($errors->has('old_password'))
                                        <div class="alert alert-danger">
                                            {{ $errors->first('old_password') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="password" class="col-sm-3 control-label">New Password <span class="text-red"> * </span></label>

                                <div class="col-sm-6">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="New Password" value="<?php if(old('new_password')!=null) echo old('new_password');?>">
                                    @if ($errors->has('password'))
                                        <div class="alert alert-danger">
                                            {{ $errors->first('password') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="password_confirmation" class="col-sm-3 control-label">Confirm Password  <span class="text-red"> * </span></label>

                                <div class="col-sm-6">
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" value="<?php if(old('password_confirmation')!=null) echo old('password_confirmation');?>">
                                    @if ($errors->has('password_confirmation'))
                                        <div class="alert alert-danger">
                                            {{ $errors->first('password_confirmation') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <div class="col-sm-3">&nbsp;</div>
                            <div class="col-sm-6">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <button class="btn btn-danger" type="button" onclick="window.location='{{ url("/administrator/home") }}'">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
@extends('layouts.admin')
@push('stylesheets')
    <!-- DataTables -->
    <link rel="stylesheet" href="../../plugins/datatables/dataTables.bootstrap.css">
    <link href="{{ URL::asset('css/select2.min.css')}}" rel="stylesheet">
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
                              @if($data->mode == 'edit') action="/administrator/update-customers-groups-data"  @else action="/administrator/add-customers-groups-data" @endif >
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
                            <input type="hidden" name="customer_group_id" value="{{$data->id}}" />
                            <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                                <label class="col-sm-3 control-label">Name<span class="label-mandatory">*</span></label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="name" id="name"  required value="<?php if(old('name')!= null) echo old('name');  else echo $data->name;?>">
                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                                <label class="col-sm-3 control-label">Name<span class="label-mandatory">*</span></label>
                                <div class="col-sm-6">
                                    <select name="customers[]"  class="form-control select2" multiple required>
                                        @foreach($customers as $customer)
                                            <option value="{{$customer->id}}" @php if(array_key_exists($customer->id,$selectedCustomers)) echo "selected" @endphp> {{ $customer->first_name }} {{ $customer->last_name }} ({{$customer->email_address}})</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="box-footer">
                                <label class="col-sm-3 control-label"></label>
                                <div class="col-sm-6">
                                    <button class="btn btn-primary" type="submit">Submit</button>
                                    <button class="btn btn-danger" type="button" onclick="window.location='{{ url("/administrator/list-customer-groups") }}'">Cancel</button>
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

    <!-- Select2 -->
    <script src="{{{ URL::asset('js/select2.full.min.js')}}}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $(".select2").select2();
        });

    </script>
@endpush

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
            Slots
        </h1>
        <ol class="breadcrumb">
            <li><a href="home"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="/administrator/list-slots">List Slots</a></li>
            <li class="active"> @if($data->mode == 'edit')Edit @else Add @endif Slots</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"> @if($data->mode == 'edit')Edit @else Add @endif  Slots</h3>
                    </div>

                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title"></h3>
                        </div>
                        <!-- form start -->
                        <form class="form-horizontal" method="POST" enctype="multipart/form-data" name="frmCustomer" id="frmCustomer"
                              @if($data->mode == 'edit') action="/administrator/update-slot/{{$data->id}}"  @else action="/administrator/add-slot" @endif >
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
                                <div class="alert alert-danger">
                                    {{"While create/Edit slots, Please take care that you will not overlap between any dates"}}
                                </div>

                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="form-group {{ $errors->has('start_date') ? ' has-error' : '' }}">
                                <label class="col-sm-3 control-label">Start Date<span class="label-mandatory">*</span></label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control datepicker" autocomplete="off" readonly name="start_date" id="start_date"  required value="<?php if(old('start_date')!= null) echo old('start_date');  else echo $data->start_date;?>">
                                    @if ($errors->has('start_date'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('start_date') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group {{ $errors->has('end_date') ? ' has-error' : '' }}">
                                <label class="col-sm-3 control-label">End Date<span class="label-mandatory">*</span></label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control datepicker" name="end_date" readonly id="end_date"  required value="<?php if(old('end_date')!= null) echo old('end_date');  else echo $data->end_date;?>">
                                    @if ($errors->has('end_date'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('end_date') }}</strong>
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
    <!-- Bootstrap Date-Picker Plugin -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css"/>
    <script>
        $(document).ready(function(){
            var options= {
                format: 'yyyy-mm-dd',
                todayHighlight: true,
                autoclose: true,
            };
            $(".datepicker").datepicker(options);
        })
    </script>
@endpush

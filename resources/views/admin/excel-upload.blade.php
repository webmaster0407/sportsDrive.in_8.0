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
           Upload Excel
        </h1>
        <ol class="breadcrumb">
            <li><a href="home"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="/administrator/list-products">List Products</a></li>
            <li class="active"> Upload Excel </li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"> Upload Excel </h3>
                    </div>

                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title"></h3>
                        </div>
                        <!-- form start -->
                        <form class="form-horizontal" method="POST" enctype="multipart/form-data" name="frmSubscriber" id="frmSubscriber" action="/administrator/import-excel">
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
                            <div class="form-group {{ $errors->has('excel') ? ' has-error' : '' }}">
                                <label class="col-sm-3 control-label">Upload File<span class="label-mandatory">*</span></label>
                                <div class="col-sm-6">
                                    <input type="file" class="form-control" name="excel" id="excel">
                                    @if ($errors->has('excel'))
                                        <span class="help-block">
                                    <strong>{{ $errors->first('excel') }}</strong>
                                </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="box-footer">
                                <label class="col-sm-3 control-label"></label>
                                <div class="col-sm-6">
                                    <button class="btn btn-primary" type="submit">Submit</button>
                                    <button class="btn btn-danger" type="button" onclick="window.location='{{ url("/administrator/list-products") }}'">Cancel</button>
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

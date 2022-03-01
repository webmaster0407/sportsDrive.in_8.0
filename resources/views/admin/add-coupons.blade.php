@extends('layouts.admin')
@push('stylesheets')
    <!-- DataTables -->
    <link rel="stylesheet" href="../../plugins/datatables/dataTables.bootstrap.css">
    <link href="{{ URL::asset('css/select2.min.css')}}" rel="stylesheet">
@endpush
@push('scripts')
    <script src="{{ asset("plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
@endpush
@section('content')
    <section class="content-header">
        <h1>
            Coupons
        </h1>
        <ol class="breadcrumb">
            <li><a href="home"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="/administrator/list-coupons">List Coupons</a></li>
            <li class="active"> @if($data->mode == 'edit')Edit @else Add @endif Coupons</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"> @if($data->mode == 'edit')Edit @else Add @endif  Coupons</h3>
                    </div>
                    <div class="box">
                        <div class="row">
                            <div class="col-md-12">
                                <!-- Custom Tabs -->
                                <div class="nav-tabs-custom" style="margin-bottom:0px;">
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="tab_1">
                                            <!-- add form here -->
                                            <form class="form-horizontal" method="post" enctype="multipart/form-data" name="frmProducts" id="frmProducts" action="/administrator/add-coupon-data/{{$data->id}}">
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
                                                <div class="form-group" {{ $errors->has('name') ? ' has-error' : '' }}>
                                                    <label class="col-sm-2 control-label">Name<span class="label-mandatory">*</span>:</label>
                                                    <div class="col-sm-7">
                                                        <input type="text" class="form-control" name="name" id="name" required value="<?php if(old('name')!=null) echo old('name'); else echo $data->name;?>">
                                                        @if ($errors->has('name'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('name') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="hr-line-dashed"></div>

                                                <div class="form-group {{ $errors->has('code') ? ' has-error' : '' }}">
                                                    <label class="col-sm-2 control-label">Code &nbsp;<span class="label-mandatory">*</span>:</label>
                                                    <div class="col-sm-7">
                                                        <input type="text" class="form-control" name="code" id="code" required value="<?php if(old('code')!=null) echo old('code'); else echo $data->code;?>">
                                                        @if ($errors->has('code'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('code') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                
 <div class="hr-line-dashed"></div>

                                                <div class="form-group" {{ $errors->has('discount') ? ' has-error' : '' }}>
                                                    <label class="col-sm-2 control-label">Discount<span class="label-mandatory">*</span>:<small>(In %)</small></label>
                                                    <div class="col-sm-7">
                                                        <input type="text" class="form-control" name="discount" id="discount"  value="<?php if(old('discount')!=null) echo old('discount'); else echo $data->discount;?>">
                                                            @if ($errors->has('discount'))
                                                                <span class="help-block">
                                                                <strong>{{ $errors->first('discount') }}</strong>
                                                            </span>
                                                            @endif
                                                    </div>
                                                </div>
 
                                                <div class="hr-line-dashed"></div>
                                               
                                                                                                
                                                @if($products!=null)
                                                    <div class="form-group" {{ $errors->has('products') ? ' has-error' : '' }}>
                                                        <label class="col-sm-2 control-label">Assign Products<span class="label-mandatory">*</span>:</label>
                                                        <div class="col-sm-7">
                                                            <select name="products[]" class="select2 col-sm-12" multiple>
                                                                @foreach($products as $product)
                                                                    <option value="{{$product['id']}}" <?php if(in_array($product['id'],$productIds)) echo "selected" ?>>{{$product['name']}}{{"(".$product['attributeColor'].")"}} </option>
                                                                @endforeach
                                                            </select>
                                                            @if ($errors->has('products'))
                                                                <span class="help-block">
                                                                    <strong>{{ $errors->first('products') }}</strong>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="hr-line-dashed"></div>
                                                @endif
                                                
                                                <div class="form-group"{{ $errors->has('short_description') ? ' has-error' : '' }}>
                                                    <label class="col-sm-2 control-label">Short Description<span class="label-mandatory"></span>:</label>
                                                    <div class="col-sm-7">
                                                        <textarea class="form-control" id="short_description" name="short_description" required rows="3">@if(old('short_description')!=null){{old('short_description')}}@else{{$data->short_description}}@endif</textarea>
                                                        @if ($errors->has('short_description'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('short_description') }}</strong>
                                                             </span>
                                                        @endif
                                                    </div>
                                                </div>
                                              
                                               
                                                <div class="hr-line-dashed"></div>
                                               
                                                <div class="hr-line-dashed"></div>
                                                <div class="box-footer">
                                                    <label class="col-sm-3 control-label"></label>
                                                    <div class="col-sm-4">
                                                        <button class="btn btn-danger" type="button" onclick="window.location='{{ url("/administrator/list-coupons") }}'">Cancel</button>
                                                        <button class="btn btn-primary" type="submit">Submit</button>
                                                    </div>
                                                </div>

                                            </form>

                                        </div>
                                        <!-- /.tab-pane -->
                                    </div>
                                    <!-- /.tab-content -->
                                </div>
                                <!-- nav-tabs-custom -->
                            </div>
                            <!-- /.col -->
                        </div>
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
    <!-- CK Editor -->
    <script src="https://cdn.ckeditor.com/4.5.7/full-all/ckeditor.js"></script>
    <!-- Bootstrap WYSIHTML5 -->
    <script>
        $(function () {           
            CKEDITOR.replace('short_description');
        });
    </script>
@endpush

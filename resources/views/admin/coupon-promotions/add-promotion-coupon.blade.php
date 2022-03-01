@extends('layouts.admin')
@push('stylesheets')
    <!-- DataTables -->
    <link rel="stylesheet" href="../../plugins/datatables/dataTables.bootstrap.css">
    <link href="{{ URL::asset('css/select2.min.css')}}" rel="stylesheet">
    <link src="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
@endpush
@push('scripts')
    <script src="{{ asset("plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
@endpush
@section('content')
    <style>.select2 {width: 610px !important;}</style>
    <section class="content-header">
        <h1>
            Coupons
        </h1>
        <ol class="breadcrumb">
            <li><a href="home"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="/administrator/list-coupons">List Promotional Coupons</a></li>
            <li class="active"> @if($data->mode == 'edit')Edit @else Add @endif Promotional Coupons</li>
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
                                            <form class="form-horizontal" method="post" enctype="multipart/form-data" name="frmProducts" id="frmProducts" action="/administrator/add-coupon-data-promotions/{{$data->id}}">
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
                                                    <div class="form-group" {{ $errors->has('email_title') ? ' has-error' : '' }}>
                                                        <label class="col-sm-3 control-label">Email Title<span class="label-mandatory">*</span>:</label>
                                                        <div class="col-sm-7">
                                                            <input type="text" class="form-control" name="email_title" id="email_title" required value="<?php if(old('email_title')!=null) echo old('email_title'); else echo $data->email_title;?>">
                                                            @if ($errors->has('email_title'))
                                                                <span class="help-block">
                                                                <strong>{{ $errors->first('email_title') }}</strong>
                                                            </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="hr-line-dashed"></div>
                                                    <div class="form-group" {{ $errors->has('banner_image') ? ' has-error' : '' }}>
                                                        <label class="col-sm-3 control-label">Banner Image<span class="label-mandatory">*</span>:</label>
                                                        <div class="col-sm-7">
                                                            <input accept="image/png, image/jpeg" type="file" class="form-control" name="banner_image" id="banner_image" required value="<?php if(old('banner_image')!=null) echo old('banner_image'); else echo $data->banner_image;?>" maxlength="25">
                                                            @if ($errors->has('banner_image'))
                                                                <span class="help-block">
                                                                    <strong>{{ $errors->first('banner_image') }}</strong>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="hr-line-dashed"></div>

                                                    <div class="form-group" {{ $errors->has('name') ? ' has-error' : '' }}>
                                                    <label class="col-sm-3 control-label">Name<span class="label-mandatory">*</span>:</label>
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
                                                <div class="form-group" {{ $errors->has('discount') ? ' has-error' : '' }}>
                                                    <label class="col-sm-3 control-label">Discount<span class="label-mandatory">*</span>:<small>(In %)</small></label>
                                                    <div class="col-sm-7">
                                                        <input type="text" class="form-control" onkeypress='return event.charCode >= 48 && event.charCode <= 57' name="discount" id="discount"  value="<?php if(old('discount')!=null) echo old('discount'); else echo $data->discount;?>">
                                                        @if ($errors->has('discount'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('discount') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="hr-line-dashed"></div>
                                                <div class="form-group" {{ $errors->has('coupons_for') ? ' has-error' : '' }}>
                                                    <label class="col-sm-3 control-label">Select customers type to creator coupon<span class="label-mandatory">*</span>:</label>
                                                    <div class="col-sm-7">
                                                        <select class="form-control order_status" name="coupons_for" id="coupons_for">
                                                            <option value="0" selected>Select Customers Type</option>
                                                            <option value="1" >For All Customers</option>
                                                            <option value="2" >For Customers who have placed the order</option>
                                                            <option value="3" >Customer Groups</option>
                                                            <option value="4" >Selected Customers</option>
                                                            <option value="5" >Selected Cities Customers</option>
                                                        </select>
                                                        @if ($errors->has('coupons_for'))
                                                            <span class="help-block">
                                                            <strong>{{ $errors->first('coupons_for') }}</strong>
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="form-group customers_class" {{ $errors->has('all_customers') ? ' has-error' : '' }}  id="all_customers" style="display: none">
                                                    <label class="col-sm-3 control-label">Select customers<span class="label-mandatory">*</span>:</label>
                                                    <div class="col-sm-7">
                                                        <select class="form-control order_status select2 customers_class" name="all_customers[]" multiple id="all_customers_select">
                                                            @foreach( $customers as $customer)
                                                                <option value="{{$customer->id}}" >{{$customer->first_name}} {{$customer->last_name}}({{$customer->email_address}})</option>
                                                            @endforeach
                                                        </select>
                                                        @if ($errors->has('all_customers'))
                                                            <span class="help-block">
                                                        <strong>{{ $errors->first('all_customers') }}</strong>
                                                    </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="form-group customers_class" {{ $errors->has('customers_group') ? ' has-error' : '' }} id="customers_group" style="display: none">
                                                    <label class="col-sm-3 control-label">Select Customers Group<span class="label-mandatory">*</span>:</label>
                                                    <div class="col-sm-7">
                                                        <select class="form-control order_status select2" name="customers_group[]" id="customers_group_select" multiple>
                                                            @foreach($customersGroup as $customerGroup)
                                                                <option value="{{$customerGroup->id}}" >{{$customerGroup->name}}</option>
                                                            @endforeach
                                                        </select>
                                                        @if ($errors->has('customers_group'))
                                                            <span class="help-block">
                                                            <strong>{{ $errors->first('customers_group') }}</strong>
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="form-group customers_class" {{ $errors->has('customers_cities') ? ' has-error' : '' }} id="customers_cities_div" style="display: none">
                                                    <label class="col-sm-3 control-label">Select Customers Cities<span class="label-mandatory">*</span>:</label>
                                                    <div class="col-sm-7">
                                                        <select class="form-control order_status select2" name="customers_cities[]" id="customers_cities" multiple>
                                                            @foreach($customers_cities as $city)
                                                                <option value="{{$city}}" >{{$city}}</option>
                                                            @endforeach
                                                        </select>
                                                        @if ($errors->has('customers_cities'))
                                                            <span class="help-block">
                                                        <strong>{{ $errors->first('customers_cities') }}</strong>
                                                    </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="hr-line-dashed"></div>
                                                    <div class="form-group" {{ $errors->has('valid_till') ? ' has-error' : '' }}>
                                                        <label class="col-sm-3 control-label">Valid From to Valid Till<span class="label-mandatory">*</span>:</label>
                                                        <div class="col-sm-7">
                                                            <input type="text" name="valid_till" class="form-control pull-right hasDatepicker" id="valid_till">
                                                            @if ($errors->has('valid_till'))
                                                                <span class="help-block">
                                                                    <strong>{{ $errors->first('valid_till') }}</strong>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="hr-line-dashed"></div>
                                                <div class="form-group"{{ $errors->has('short_description') ? ' has-error' : '' }}>
                                                    <label class="col-sm-3 control-label">Short Description<span class="label-mandatory"></span>:</label>
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
                                                        <button class="btn btn-danger" type="button" onclick="window.location='{{ url("/administrator/list-coupon-promotions") }}'">Cancel</button>
                                                        <button class="btn btn-primary" type="submit" id="submit_button">Submit</button>
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
    <script
            src="https://code.jquery.com/jquery-1.12.4.js"
            integrity="sha256-Qw82+bXyGq6MydymqBxNPYTaUXXq7c8v3CwiYwLLNXU="
            crossorigin="anonymous"></script>
    <!-- Select2 -->
    <script src="{{{ URL::asset('js/select2.full.min.js')}}}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $(".select2").select2();
            $("#coupons_for").change(function(){
                $(".customers_class").hide();
                if(this.value == 4){
                    $("#all_customers").show();
                }else if(this.value == 3){
                    $("#customers_group").show();
                }else if(this.value == 5){
                    $("#customers_cities_div").show();
                }

                $(".select2").css("width","485px !important");
            });
        });

        $("#submit_button").click(function (event) {
            event.preventDefault();//
            var coupon_for = $("#coupons_for").val();
            if($("#name").val() == ""){
                alert("Please enter name")
            }else if($("#discount").val() == ""){
                alert("Please enter discount")
            }else if($("#email_title").val() == ""){
                alert("Please enter Email Title")
            }else if(coupon_for  == 0){
                alert("Please select proper customer type to create a coupon");
            }else if( coupon_for  == 3 && $("#customers_group_select").val() == null){
                alert("Please select customer group to create a coupon");
            }else if( coupon_for  == 4 && $("#all_customers_select").val() == null){
                alert("Please select customers to create a coupon");
            }else if( coupon_for  == 5 && $("#customers_cities").val() == null){
                alert("Please select customers to create a coupon");
            }else{
                $("#frmProducts").submit();
            }
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
    <script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
    <script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
    <script type="text/javascript">
        $('#valid_till').daterangepicker({
            startDate: moment().add(+1, 'day'),
            endDate: moment().add(+30, 'day'),
            locale: {
                format: 'DD/MM/YYYY'
            }
        });
    </script>
@endsection
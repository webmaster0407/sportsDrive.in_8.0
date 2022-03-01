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
            Product Promotions
        </h1>
        <ol class="breadcrumb">
            <li><a href="home"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="/administrator/list-coupons">List Product Promotions</a></li>
            <li class="active"> @if($data->mode == 'edit')Edit @else Add @endif Promotional Coupons</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"> @if($data->mode == 'edit')Edit @else Add @endif  Product Promotions</h3>
                    </div>
                    <div class="box">
                        <div class="row">
                            <div class="col-md-12">
                                <!-- Custom Tabs -->
                                <div class="nav-tabs-custom" style="margin-bottom:0px;">
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="tab_1">
                                            <!-- add form here -->
                                            <form class="form-horizontal" method="post" enctype="multipart/form-data" name="frmProducts" id="frmProducts" action="/administrator/add-product-data-promotions/{{$data->id}}">
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
                                                    <div class="form-group" {{ $errors->has('banner_image') ? ' has-error' : '' }}>
                                                        <label class="col-sm-2 control-label">Banner Image<span class="label-mandatory">*</span>:</label>
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
                                                <div class="form-group" {{ $errors->has('promotion_type') ? ' has-error' : '' }}>
                                                    <label class="col-sm-2 control-label">Select Promotion Type<span class="label-mandatory">*</span>:</label>
                                                    <div class="col-sm-7">
                                                        <select class="form-control order_status" name="promotion_type" id="promotion_type">
                                                            <option value="P" selected>Products</option>
                                                            <option value="C" >Categories</option>
                                                        </select>
                                                        @if ($errors->has('promotion_type'))
                                                            <span class="help-block">
                                                            <strong>{{ $errors->first('promotion_type') }}</strong>
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="form-group" {{ $errors->has('product_ids') ? ' has-error' : '' }}  id="product_ids_div">
                                                    <label class="col-sm-2 control-label">Select Products<span class="label-mandatory">*</span>:</label>
                                                    <div class="col-sm-7">
                                                        <select class="form-control order_status select2" name="product_ids[]" multiple id="product_ids">
                                                            @foreach( $productIds as $productId)
                                                                <option value="{{$productId->id}}" >{{$productId->name}}</option>
                                                            @endforeach
                                                        </select>
                                                        @if ($errors->has('product_ids'))
                                                            <span class="help-block">
                                                        <strong>{{ $errors->first('product_ids') }}</strong>
                                                    </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                    <div class="row" id="categories_div" style="display: none">
                                                        <label class="col-sm-2 control-label">Assign Categories</label>
                                                        <div class="col-md-7 box-select">
                                                            <div class="box box-default collapsed-box">
                                                                <div class="box-header with-border">
                                                                    <h3 class="box-title">Select Categories</h3>
                                                                    <div class="box-tools pull-right">
                                                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                                                                        </button>
                                                                    </div>
                                                                    <!-- /.box-tools -->
                                                                </div>
                                                                <!-- /.box-header -->
                                                                <div class="box-body">
                                                                    @foreach($categories as $mainCategory)
                                                                        <input type="checkbox" class="mainCategories" name="categories[]" value="{{$mainCategory->id}}" <?php if(in_array($mainCategory->id,$selectedMainCategories)) echo "checked"; ?>><strong>{{$mainCategory->name}}</strong>
                                                                        @if($mainCategory['subCategories'] != null)
                                                                            @foreach($mainCategory['subCategories'] as $subCategory)
                                                                                <div class="cat-1"><input type="checkbox" class="subCategory" name="categories[]" value="{{$subCategory->id}}" <?php if(in_array($subCategory->id,$selectedSubCategories)) echo "checked"; ?>> <span style="font-size: 17px !important;">{{$subCategory->name}}</span></div>
                                                                                @if($subCategory['subSubCategories']!=null)
                                                                                    @foreach($subCategory['subSubCategories'] as $subSubCategories)
                                                                                        <div class="cat-2"><input type="checkbox" class="subSubCategories" name="categories[]" value="{{$subSubCategories->id}}" <?php if(in_array($subSubCategories->id,$selectedSubSubCategories)) echo "checked"; ?>><span style="font-size: 16px !important;">{{$subSubCategories->name}}</span></div>
                                                                                    @endforeach
                                                                                @endif
                                                                            @endforeach
                                                                        @endif
                                                                    @endforeach
                                                                </div>
                                                                <!-- /.box-body -->
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="hr-line-dashed"></div>

                                                    <div class="form-group" {{ $errors->has('coupons_for') ? ' has-error' : '' }}>
                                                        <label class="col-sm-2 control-label">Select customers type to creator coupon<span class="label-mandatory">*</span>:</label>
                                                        <div class="col-sm-7">
                                                            <select class="form-control order_status" name="coupons_for" id="coupons_for">
                                                                <option value="0" selected>All Customers</option>
                                                                <option value="1" >Selected Cities Customers</option>
                                                            </select>
                                                            @if ($errors->has('coupons_for'))
                                                                <span class="help-block">
                                                                    <strong>{{ $errors->first('coupons_for') }}</strong>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="form-group" {{ $errors->has('customers_cities') ? ' has-error' : '' }} id="customers_cities_div" style="display: none">
                                                        <label class="col-sm-2 control-label">Select Customers Cities<span class="label-mandatory">*</span>:</label>
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
            $("#promotion_type").change(function(){
                if(this.value == "C"){
                    $("#categories_div").show();
                    $("#product_ids_div").hide();
                }else if(this.value == "P"){
                    $("#categories_div").hide();
                    $("#product_ids_div").show();
                }
            });

            $("#coupons_for").change(function(){
                if(this.value == 1){
                    $("#customers_cities_div").show();
                }else{
                    $("#customers_cities_div").hide();
                }
            });

        });

        $("#submit_button").click(function (event) {
            event.preventDefault();//
            var promotion_type = $("#promotion_type").val();
            var mainCategories = $('.mainCategories:checked').val();
            var subCategory = $('.subCategory:checked').val();
            var subSubCategories = $('.subSubCategories:checked').val();
            if($("#email_title").val() == ""){
                alert("Please enter email title");
            }else if( promotion_type  == "C" && mainCategories == undefined && subCategory == undefined && subSubCategories == undefined){
                alert("Please select categories to create a product promotions");
            }else if( promotion_type  == "P" && $("#product_ids").val() == null){
                alert("Please select products to create a promotions");
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
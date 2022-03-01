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
            View Customer Address
        </h1>
        <ol class="breadcrumb">
            <li><a href="home"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="/administrator/list-customer">List Customers</a></li>
            <li class="active"> View Customer Address</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-body">

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
                        <div class="col-sm-6">
                            <h4>Customer Name : <b>{{$customer->first_name}} {{$customer->last_name}}</b></h4>
                            <h4>Email Address : <b>{{$customer->email_address}}</b></h4>
                            <h4>Phone : <b>{{$customer->phone}}</b></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <i class="fa fa-home"></i>
                        <h3 class="box-title">Shipping Addresses</h3>
                    </div>
                    <!-- /.box-header -->
                   @foreach($shippingAddresses as $key=> $address)
                    <div class="box-body">
                        <div>
                            <a href="/administrator/edit-customer-address/{{$address->id}}"><p><strong>{{$key+1}}{{". "}}{{$address['address_title']}}</strong></p></a>
                            <p><strong>{{$address['full_name']}}</strong></p>
                            <p>{{$address['address_line_1']}}</p>
                            <p>{{$address['address_line_2']}} {{$address['city']}} {{$address['state']}} </p>
                            <p>{{$address['country']}}</p>
                            <p>{{$address['pin_code']}}</p>
                        </div>
                    </div>
                    @endforeach
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- ./col -->

            <div class="col-md-6">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <i class="fa fa-home"></i>
                        <h3 class="box-title">Billing Addresses</h3>
                    </div>
                    <!-- /.box-header -->
                    @foreach($billingAddresses as $key=> $address)
                        <div class="box-body">
                            <div>
                                <a href="/administrator/edit-customer-address/{{$address->id}}"><p><strong>{{$key+1}}{{". "}}{{$address['address_title']}}</strong></p></a>
                                <p><strong>{{$address['full_name']}}</strong></p>
                                <p>{{$address['address_line_1']}}</p>
                                <p>{{$address['address_line_2']}} {{$address['city']}} {{$address['state']}} </p>
                                <p>{{$address['country']}}</p>
                                <p>{{$address['pin_code']}}</p>
                            </div>
                        </div>
                @endforeach
                <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- ./col -->
        </div>
    </section>
@endsection
@push('scripts')
    <script
            src="https://code.jquery.com/jquery-1.12.4.js"
            integrity="sha256-Qw82+bXyGq6MydymqBxNPYTaUXXq7c8v3CwiYwLLNXU="
            crossorigin="anonymous"></script>

@endpush

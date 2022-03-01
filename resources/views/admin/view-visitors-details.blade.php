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
            View Visitors Details
        </h1>
        <ol class="breadcrumb">
            <li><a href="home"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="/administrator/administrator/list-visitors">List Visitors</a></li>
            <li class="active"> View Visitors Details</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <i class="fa fa-home"></i>
                        <h3 class="box-title">Visitors IP details</h3>
                    </div>
                    <!-- /.box-header -->
                        <div class="box-body">
                            <div class="box-body table-responsive no-padding">
                                <table class="table table-hover">
                                    <tr><td><label>Customer Id  </label></td><td><strong>{{$visitors->customer_id}}</strong><br></td></tr>
                                    <tr><td><label>Customer Name  </label></td><td><strong>@if($visitors->customer){{$visitors->customer->first_name}} {{$visitors->customer->last_name}}@endif</strong><br></td></tr>
                                    <tr><td><label>Ip Address  </label></td><td>{{$visitors->ip_address}}<br></td></tr>
                                    <tr><td><label>City  </label></td><td>{{$visitors->city}}<br></td></tr>
                                    <tr><td><label>Region  </label></td><td>{{$visitors->region}}<br></td></tr>
                                    <tr><td><label>Region Code  </label></td><td>{{$visitors->regionCode}}<br></td></tr>
                                    <tr><td><label>Region Name  </label></td><td>{{$visitors->regionName}}<br></td></tr>
                                    <tr><td><label>Country Code  </label></td><td>{{$visitors->countryCode}}<br></td></tr>
                                    <tr><td><label>Country Name  </label></td><td>{{$visitors->countryName}}<br></td></tr>
                                    <tr><td><label>Continent Name  </label></td><td>{{$visitors->continentName}}<br></td></tr>
                                    <tr><td><label>Latitude  </label></td><td>{{$visitors->latitude}}<br></td></tr>
                                    <tr><td><label>Longitude  </label></td><td>{{$visitors->longitude}}<br></td></tr>
                                    <tr><td><label>Google Map  </label></td><td><a target="_blank" href="https://www.google.com/maps/place/{{$visitors->latitude}},{{$visitors->longitude}}" class="btn btn-primary">Check Map</a><br></td></tr>
                                    <tr><td><label>Timezone  </label></td><td>{{$visitors->timezone}}<br></td></tr>
                                    <tr><td><label>Isp Name  </label></td><td>{{$visitors->isp_name}}<br></td></tr>
                                    <tr><td><label>Organization  </label></td><td>{{$visitors->org}}<br></td></tr>
                                    <tr><td><label>Location Details By  </label></td><td>{{$visitors->details_by}}<br></td></tr>
                                    <tr><td><label>Created At  </label></td><td>{{$visitors->created_at}}<br></td></tr>
                                    <tr><td><label>Updated At  </label></td><td>{{$visitors->updated_at}}<br></td></tr>
                                </table>
                            </div>
                        </div>
                <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- ./col -->
            <div class="col-md-12" id="visitor_pages">
                <div class="box box-solid">
                    <div class="box-header">
                        <h3 class="box-title">Visitors Pages</h3>
                    </div>
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover">
                            <tbody>
                            <tr>
                                <th> Sr. No. </th>
                                <th> URL </th>
                                <th> Time </th>
                            </tr>
                            @foreach($visitorsPages as $key=> $visitorsPage)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td><a href="{{$visitorsPage['url']}}"><strong>{{$visitorsPage['url']}}</strong></a></td>
                                    <td>{{$visitorsPage['created_at']}}</td>
                                </tr>
                            @endforeach
                            </tbody></table>
                    </div>
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

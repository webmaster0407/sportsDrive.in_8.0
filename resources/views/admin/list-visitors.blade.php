@extends('layouts.admin')
@push('stylesheets')
    <link href="{{ URL::asset('css/select2.min.css')}}" rel="stylesheet">
@endpush
@push('scripts')
<script type="text/javascript" src="{{ asset("/js/setCommon.js")}}"></script>
<script src="{{ asset("plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>

{{--select 2 JS--}}
<script src="{{ URL::asset('js/select2.full.min.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $(".select2").select2();
    });
</script>
@endpush
@section('content')
    <style>
        tr:nth-child(even) {
            background-color: #dddddd;
        }
    </style>
<script language="javascript" type="text/javascript" src="{{ asset("/js/setCommon.js")}}"></script>
<section class="content-header">
    <h1>
       List Visitors
    </h1>
    <ol class="breadcrumb">
        <li><a href="home"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">List Visitors</li>
    </ol>
</section>
<section class="content">
    <div class="row">

        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box">
                  <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="box-header">
                        <form name="searchIp" action="/administrator/list-visitors" method="post">
                            {{csrf_field()}}
                            <div class="col-md-6 col-sm-6 ">
                                <p>
                                    <label>Search by IP address: </label><input class="form-control" type="text" name="searchByIp" value="{{ session("ipsearch")}}">
                                </p>
                                <p>
                                    <label>Search by Name: </label>
                                    <select class="form-control select2" name="searchId">
                                        <option selected value="">Select Customer Name</option>
                                        @foreach($customers as $customer)
                                            <option @if(session("searchId") == $customer->id ){{"selected"}}@endif value="{{$customer->id}}">{{$customer->first_name}} {{$customer->last_name}}</option>
                                        @endforeach
                                    </select>
                                </p>
                                <button class="btn btn-sm btn-primary " type="submit" name="submit" value="Search"  style="float: right; margin: 10px"><i class='fa'></i>Search</button>
                                <a href="/administrator/list-visitors"><button class="btn btn-sm btn-primary " type="button" name="submit" value="Clear"  style="float: right;margin: 10px"><i class='fa'></i>Clear</button></a>
                            </div>
                        </form>


                        <form name="searchIp" action="/administrator/list-visitors" method="post">
                            {{csrf_field()}}
                            <input type="hidden" name="type" value="county">
                            <div class="col-md-6 col-sm-6 ">
                                <p>
                                    <label>Search by Country: </label>
                                    <select class="form-control select2" name="country">
                                        <option selected value="">Select Country</option>
                                        @foreach($countries as $key=>$country)
                                            <option @if(session("country") == $country ){{"selected"}}@endif value="{{$country}}">{{ucfirst($country)}}</option>
                                        @endforeach
                                    </select>
                                </p>
                                <button class="btn btn-sm btn-primary " type="submit" name="submit" value="Search"  style="float: right; margin: 10px"><i class='fa'></i>Search</button>
                                <a href="/administrator/list-visitors"><button class="btn btn-sm btn-primary " type="button" name="submit" value="Clear"  style="float: right;margin: 10px"><i class='fa'></i>Clear</button></a>
                            </div>
                        </form>
                    </div>
                  <!-- /.box-header -->
                  <div class="box-body">
                    <table id="visitorsTable" class="table table-bordered table-striped">
                      <thead>
                      <tr>
                        <th>Date and Time</th>
                        <th>Active</th>
                        <th>Customer Name</th>
                        <th>City</th>
                        <th>Country</th>
                          <th>Duration On WebSite<br>(HH:MM:SS)</th>
                        <th>Number oF Visits to website(Till Date)</th>
                        <th>Page Visits</th>
                        <th>Notifications</th>
                        <th>IP</th>
                      </tr>
                      </thead>
                      <tbody>
                      <?php $i = 1;?>
                        @foreach ($data as $key => $customerIp)
                            @php
                                $now = \Carbon\Carbon::now();
                                $firstVisitTime = \Carbon\Carbon::now();
                                $LastVisitTime = \Carbon\Carbon::now();
                                    if(count($customerIp->VisitorsPages)>0){
                                          $firstVisitTime = $customerIp->VisitorsPages[0]['created_at'];
                                          $LastVisitTime = $customerIp->VisitorsPages[count($customerIp->VisitorsPages)-1]['created_at'];
                                          $LastVisitTimeActive = $customerIp->VisitorsPages[count($customerIp->VisitorsPages)-1]['created_at'];
                                    }else{
                                        $LastVisitTimeActive = date('Y-m-d H:i:s', strtotime('-8 days'));
                                    }
                            @endphp
                          <tr  @if($customerIp->customer) style="color: blue"; @else style="color: black"; @endif>
                              <td> {{date("d M H:i A",strtotime($customerIp->created_at)) }} </td>
                              <td>
                                  @if(strtotime($now)- strtotime($LastVisitTimeActive)<45)
                                      <img src="{{ asset("/images/green.png")}}" width="20px">
                                  @elseif(strtotime($now)- strtotime($LastVisitTimeActive)<120)
                                      <img src="{{ asset("/images/orange.png")}}" width="20px">
                                  @else
                                      <img src="{{ asset("/images/red.png")}}" width="20px">
                                  @endif
                              </td>
                              <td> @if($customerIp->customer){{$customerIp->customer->first_name}} {{$customerIp->customer->last_name}}@else {{"Visitor"}}@endif</td>
                              <td> {{$customerIp->city}}</td>
                              <td> {{$customerIp->countryName}}</td>
                              <td> {{ gmdate("H:i:s", strtotime($LastVisitTime)- strtotime($firstVisitTime))}}</td>
                              <td> {{$totalVisits[$customerIp->id]}}</td>
                              <td> <a href="/administrator/visitors-details/{{$customerIp->id}}#visitor_pages">{{count($customerIp->VisitorsPages)}}</a></td>
                              <td>@if(count($customerIp->Notifications)>0)<a href="/administrator/list-notifications/{{$customerIp->id}}"> {{count($customerIp->Notifications)}}</a>@else {{"0"}} @endif</td>
                              <td><a href="/administrator/visitors-details/{{$customerIp->id}}">{{$customerIp->ip_address}}</a></td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                  <!-- /.box-body -->
                </div>
                <div class="dataTables_paginate paging_simple_numbers" id="attributeTable_paginate">
                    {!! $data->render() !!}
                </div>
            </div>

        </div>
    </div>
</section>
<script>
$("#checkAll").change(function () {
    $("input:checkbox").prop('checked', $(this).prop("checked"));
});
</script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>

<script>
    $(function () {
        $('#visitorsTable').DataTable({
            "paging": false,
            "lengthChange": false,
            "searching": false,
            "ordering": false,
            "info": false,
            "autoWidth": false,
            "scrollX": true,
        });
    });
</script>
@endsection

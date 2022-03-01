@extends('layouts.admin')
@push('stylesheets')
    <!-- SELECT 2 JS -->
    <link href="{{{ URL::asset('css/select2.min.css')}}}" rel="stylesheet">
@endpush
@push('scripts')
<script type="text/javascript" src="{{ asset("/js/setCommon.js")}}"></script>
<script src="{{ asset("plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>

@endpush

@section('content')
<script language="javascript" type="text/javascript" src="{{ asset("/js/setCommon.js")}}"></script>
<section class="content-header">
    <h1>
       List Notifications
    </h1>
    <ol class="breadcrumb">
        <li><a href="home"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">List Notifications</li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box">
                  <input type="hidden" name="_token" value="{{ csrf_token() }}">
                  <div class="box-body">
                    <table id="customerTable" class="table table-bordered table-striped">
                      <thead>
                      <tr>
                        <th>SrNo.</th>
                        <th>User Name</th>
                        <th>Notifications</th>
                        <th>Created At</th>
                      </tr>
                      </thead>
                      <tbody>
                      <?php $i = 1;?>
                        @foreach ($data as $key => $not)
                          <tr>
                              <td>{{$key+1}}</td>
                              @if($not->customer)
                                <td><a href="/administrator/edit-customer/{{$not->customer->id}}"> {{$not->customer->first_name}}{{"  "}}{{$not->customer->last_name}}</a></td>
                              @else
                                  <td> {{ "Not Available" }}</td>
                              @endif
                              <td> {{ $not->notification }}</td>
                              <td>{{ $not->created_at }}</td>
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
        $('#customerTable').DataTable({
            "paging": false,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "info": false,
            "autoWidth": false,
            "scrollX": true,
        });
    });
</script>

<!-- Select2 -->
<script src="{{{ URL::asset('js/select2.full.min.js')}}}"></script>
<script type="text/javascript">

    $(document).ready(function() {
        $(".select2").select2();
    });

</script>
@endsection

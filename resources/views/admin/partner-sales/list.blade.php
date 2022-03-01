@extends('layouts.admin')

@push('scripts')
    <script type="text/javascript" src="{{ asset("/js/setCommon.js")}}"></script>
    <script src="{{ asset("plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>

@endpush

@section('content')
    <script language="javascript" type="text/javascript" src="{{ asset("/js/setCommon.js")}}"></script>
    <form method="POST" id="frmCustomer" name="frmCustomer" action="change-status-partner">
        <section class="content-header">
            <h1>
                Partner Sales Details({{$partner->first_name}} {{$partner->last_name}})
            </h1>
            <ol class="breadcrumb">
                <li><a href="home"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li class="active">Partner Sales Details</li>
            </ol>
        </section>
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
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
                        <div class="box">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <!-- /.box-header -->
                            <div class="box-body">
                                <table  class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Details</th>
                                       @foreach($salesWindows as $salesWindow)
                                           <th>{{date("F j, Y   ",strtotime($salesWindow->start_date)) }} - {{date("F j, Y",strtotime($salesWindow->end_date))}}</th>
                                        @endforeach
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>Total Units Sold</td>
                                        @foreach($salesWindowsSum as $sales)
                                        <td>{{$sales['totalUnitSold']}}</td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td>TOTAL Sales Value without GST</td>
                                        @foreach($salesWindowsSum as $sales)
                                            <td>{{floor($sales['totalSale'])}}</td>
                                        @endforeach
                                    </tr>
                                     @if($partner['flat_comission'] == null)
                                         <tr>  <td>Category 10
                                                 (72 pcs. and more in
                                                 one Sales Window - 3
                                                 months)</td>
                                             @foreach($salesWindowsSum as $sales)
                                                 @if($sales['totalUnitSold'] > 71)
                                                     <td>{{floor($sales['totalCommission'])}}</td>
                                                 @else
                                                     <td>{{"NA"}}</td>
                                                 @endif
                                             @endforeach
                                         </tr>
                                         <tr><td>Category 7.5
                                                 36 - 71 pcs. in one
                                                 Sales Window (3
                                                 months</td>
                                             @foreach($salesWindowsSum as $sales)
                                                 @if($sales['totalUnitSold'] > 35 && $sales['totalUnitSold'] <= 71)
                                                     <td>{{floor($sales['totalCommission'])}}</td>
                                                 @else
                                                     <td>{{"NA"}}</td>
                                                 @endif
                                             @endforeach
                                         </tr>
                                         <tr><td>Category 5
                                                 1 - 35 pcs. in one Sales
                                                 Window (3 months)</td>
                                             @foreach($salesWindowsSum as $sales)
                                                 @if($sales['totalUnitSold'] > 0 && $sales['totalUnitSold'] <= 35)
                                                     <td>{{floor($sales['totalCommission'])}}</td>
                                                 @else
                                                     <td>{{"NA"}}</td>
                                                 @endif
                                             @endforeach
                                         </tr>
                                      @else
                                         <tr><td>Flat Commission : </td>
                                             @foreach($salesWindowsSum as $sales)
                                                 <td>{{floor($sales['totalCommission'])}}</td>
                                             @endforeach
                                         </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.box-body -->
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </form>
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
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "scrollX": true,
                "columnDefs": [ { "targets": [0], "orderable": false },{ "sType": "title-string", "aTargets": [5] }  ],
                "order": [[ 1, 'asc' ]]
            });
        });
    </script>
@endsection

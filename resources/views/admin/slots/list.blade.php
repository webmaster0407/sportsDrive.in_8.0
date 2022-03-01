@extends('layouts.admin')
@push('scripts')
    <script type="text/javascript" src="{{ asset("/js/setCommon.js")}}"></script>
    <script src="{{ asset("plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
@endpush
@section('content')
    <script language="javascript" type="text/javascript" src="{{ asset("/js/setCommon.js")}}"></script>
    <form method="POST" id="frmCustomer" name="frmCustomer" action="change-status-slot">
        <section class="content-header">
            <h1>
                List Slots
            </h1>
            <ol class="breadcrumb">
                <li><a href="home"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li class="active">List Slots</li>
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
                            <div class="box-header">
                                <div class="col-md-9 col-sm-9">
                                    <p>
                                        @if((count($slots)) != 0)
                                            <button class="btn btn-danger btn-sm" type="button" name="delete" value="Delete" onClick="JavaScript:CallOperation(this.value,'frmCustomer','chk[]');">Delete</button>
                                        @endif
                                        <input type="hidden" name="operationFlag" value="">
                                    </p>
                                </div>
                                <div class="col-md-3 col-sm-3">
                                    <button class="btn btn-sm btn-primary " type="button" name="add" value="Add" onClick="window.location='/administrator/add-slot'" style="float: right;"><i class='fa fa-plus-circle'></i> Add Slot</button>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <table id="customerTable" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <td><input type="checkbox" id="checkAll" name="chkAll" value="checkbox" onClick="JavaScript:CheckAll('frmCustomer', 'chkAll', 'chk[]');"></td>
                                        <th>SrNo.</th>
                                        <th>Edit</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Created On</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $i = 1;  ?>
                                    @foreach ($slots as $slot)
                                        <tr>
                                            <td><div class="i-checks"><label><input type="checkbox" name="chk[]" value="{{$slot->id}}" class="text" /></label></div></td>
                                            <td>{{$i}}</td>
                                            <td> <a href="/administrator/edit-slot/{{$slot->id}}">Edit</a></td>
                                            <td>{{$slot->start_date}}</td>
                                            <td>{{$slot->end_date}}</td>
                                            <td><?php echo  date('d-M-Y H:i:s',strtotime($slot->created_at)) ?></td>
                                        </tr>
                                        <?php $i++; ?>
                                    @endforeach
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

@extends('layouts.admin')

@push('scripts')
    <script type="text/javascript" src="{{ asset("/js/setCommon.js")}}"></script>
    <script src="{{ asset("plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>

@endpush

@section('content')
    <script language="javascript" type="text/javascript" src="{{ asset("/js/setCommon.js")}}"></script>
    <form method="POST" id="frmCustomer" name="frmCustomer" action="change-status-customer">
        <section class="content-header">
            <h1>
                List Customer OTP's
            </h1>
            <ol class="breadcrumb">
                <li><a href="home"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li class="active">List Customer</li>
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
                                <table id="otpTable" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Sr No.</th>
                                        <th>Name</th>
                                        <th>Mobile</th>
                                        <th>Email Address</th>
                                        <th>OTP</th>
                                        <th>Message Count</th>
                                        <th>Is Verified</th>
                                        <th>created_at</th>
                                        <th>updated_at</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($otps as $key=>$otp)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td>{{$otp->name}}</td>
                                            <td>{{$otp->mobile}}</td>
                                            <td>{{$otp->email}}</td>
                                            <td>{{$otp->otp}}</td>
                                            <td>{{$otp->message_count}}</td>
                                            <td class="center">@if($otp->is_verified == 1) <img src="{{{ URL::asset('img/tick.png')}}}" /> @else <img src="{{{ URL::asset('img/cross.png')}}}"/> @endif</td>
                                            <td><?php echo  date('d-M-Y H:i:s',strtotime($otp->created_at)) ?></td>
                                            <td><?php echo  date('d-M-Y H:i:s',strtotime($otp->updated_at)) ?></td>
                                        </tr>
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
    <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
    <script>
        $(function () {
            $('#otpTable').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "scrollX": true,
                "columnDefs": [ { "targets": [0], "orderable": false },{ "sType": "title-string", "aTargets": [5] }  ],
                "order": [[ 7, 'desc' ]]
            });
        });
    </script>
@endsection

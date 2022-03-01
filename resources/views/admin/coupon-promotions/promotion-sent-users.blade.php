@extends('layouts.admin')

@push('scripts')
    <script type="text/javascript" src="{{ asset("/js/setCommon.js")}}"></script>
    <script src="{{ asset("plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>

    <!-- for  highslide  -->
    <script type="text/javascript" src="{{asset('../highslide/highslide-with-gallery.js')}}"></script>
    <link rel="stylesheet" type="text/css" href="{{asset('../highslide/highslide.css')}}" />
    <script type="text/javascript">
        hs.graphicsDir = '../../highslide/graphics/';
        hs.align = 'center';
        hs.transitions = ['expand', 'crossfade'];
        hs.outlineType = 'rounded-white';
        hs.fadeInOut = true;
        //hs.dimmingOpacity = 0.75;

        // Add the controlbar
        hs.addSlideshow({
            //slideshowGroup: 'group1',
            interval: 5000,
            repeat: false,
            useControls: true,
            fixedControls: 'fit',
            overlayOptions: {
                opacity: .75,
                position: 'bottom center',
                hideOnMouseOut: true
            }
        });
    </script>
@endpush

@section('content')
    <script language="javascript" type="text/javascript" src="{{ asset("/js/setCommon.js")}}"></script>
    <form method="POST" id="frmCoupons" name="frmCoupons" action="change-status-coupons-promotions">
        <section class="content-header">
            <h1>
                List Promotions Coupons Users
            </h1>
            <ol class="breadcrumb">
                <li><a href="home"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li class="active">List Promotions Coupons Users</li>
            </ol>
        </section>
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">List Promotions Coupons Users</h3>
                        </div>
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
                                        <input type="hidden" name="operationFlag" value="">
                                    </p>
                                </div>
                                <div class="col-md-3 col-sm-3">
                                    <button class="btn btn-sm btn-primary " type="button" name="add" value="Add" onClick="window.location = '/administrator/add-coupon-promotions'" style="float: right;"><i class='fa fa-plus-circle'></i> Add Promotion Coupon</button>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <table id="bannerTable" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <td><input type="checkbox" id="checkAll" name="chkAll" value="checkbox" onClick="JavaScript:CheckAll('frmCoupons', 'chkAll', 'chk[]');"></td>
                                        <th>Sr No.</th>
                                        <th>Email Address</th>
                                        <th>Mobile</th>
                                        <th>Code</th>
                                        <th>Added On</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $i = 1; ?>
                                    @foreach ($couponsUsers as $couponsUser)
                                        <tr>
                                            <td><div class="i-checks"><label><input type="checkbox" name="chk[]" value="{{$couponsUser->id}}" class="text" /></label></div></td>
                                            <td>{{$i}}</td>
                                            <td>{{$couponsUser->email_address}}</td>
                                            <td> {{$couponsUser->mobile_number}}</td>
                                            <td> {{$couponsUser->code}}</td>
                                            <td><?php echo date('d-M-Y', strtotime($couponsUser->created_at)) ?></td>
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
            $('#bannerTable').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "scrollX": true,
                "columnDefs": [{"targets": [0, 3], "orderable": false}, {"sType": "title-string", "aTargets": [4]}],
                "order": [[1, 'asc']]
            });
        });
    </script>
@endsection

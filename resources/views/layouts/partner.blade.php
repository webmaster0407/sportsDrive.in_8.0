<!DOCTYPE html>
<html>
<head>
    <link rel="shortcut icon" href="/images/favicon.png" type="image/x-icon">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf_token" content="{{ csrf_token() }}">
    <title>Partner Portal</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="{{ asset("bootstrap/css/bootstrap.min.css") }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset("dist/css/AdminLTE.min.css") }}">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link href="{{ asset("dist/css/skins/_all-skins.min.css") }}" rel="stylesheet">
    <!-- iCheck -->
    <link href="{{ asset("plugins/iCheck/flat/blue.css") }}" rel="stylesheet">
    <!-- Morris chart -->
    <link href="{{ asset("plugins/morris/morris.css") }}" rel="stylesheet">
    <!-- jvectormap -->
    <link href="{{ asset("plugins/jvectormap/jquery-jvectormap-1.2.2.css") }}" rel="stylesheet">
    <!-- Date Picker -->
    <link href="{{ asset("plugins/datepicker/datepicker3.css") }}" rel="stylesheet">
    <!-- Daterange picker -->
    <link href="{{ asset("plugins/daterangepicker/daterangepicker.css") }}" rel="stylesheet">
    <!-- bootstrap wysihtml5 - text editor -->
    <link href="{{ asset("plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css") }}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset("css/custom_Admin.css")}}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables/dataTables.bootstrap.css')}}">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" type="text/css" href="/css/bootstrap-notifications.min.css">
    @stack('stylesheets')
    @stack('scripts')
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

    <header class="main-header">
        <!-- Logo -->
        @include('partner.includes.header')
    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- Sidebar user panel -->
            @include('partner.includes.sidebar')
        </section>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
    @yield('content')

    <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <footer class="main-footer">
        <div class="pull-right hidden-xs">
            <b>Version</b> 2.3.8
        </div>
        <strong>Copyright &copy; <script language="JavaScript" type="text/javascript">
                now = new Date
                theYear=now.getYear()
                if (theYear < 1900)
                    theYear=theYear+1900
                document.write(theYear)
            </script> <a href="#">Sports Drive</a>.</strong> All rights
        reserved.
    </footer>

    <!-- /.control-sidebar -->
    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->
@include('partner.includes.footer')
</body>
</html>

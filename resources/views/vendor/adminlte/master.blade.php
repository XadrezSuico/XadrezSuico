<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
        @yield('title_prefix', config('adminlte.title_prefix', ''))
        @yield('title', config('adminlte.title', 'AdminLTE 2')) -
        @yield('title_postfix', config('adminlte.title_postfix', '')) - Versão {{config('app.version')}}
    </title>
    @yield('header_meta')
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/vendor/bootstrap/dist/css/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/vendor/font-awesome/css/font-awesome.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/vendor/Ionicons/css/ionicons.min.css') }}">

    @if(config('adminlte.plugins.select2'))
        <!-- Select2 -->
        <link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
    @endif

    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/AdminLTE.min.css') }}">

    @if(config('adminlte.plugins.datatables'))
        <!-- DataTables with bootstrap 3 style -->
        <link rel="stylesheet" href="{{ asset('vendor/datatables/datatables.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/datatables/Responsive-2.2.2/css/responsive.dataTables.min.css') }}">
    @endif
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/bower/loading-indicator/dist/loading.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/bower/loading-circle/loading-circle.min.css')}}">
    <style>
        .loading_circle_div{
            width: 100%;
            text-align: center;
            color: green;
            font-size: 7rem;
            margin: 10rem auto;
        }
        .loading_circle_div span{
            font-size: 7rem;
        }
    </style>
    @yield('adminlte_css')

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition @yield('body_class')">

@yield('body')

<script src="{{ asset('vendor/adminlte/vendor/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/adminlte/vendor/jquery/dist/jquery.slimscroll.min.js') }}"></script>
<script src="{{ asset('vendor/adminlte/vendor/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/jquery.mask.min.js') }}"></script>

@if(config('adminlte.plugins.select2'))
    <!-- Select2 -->
    <script src="{{ asset('vendor/select2/js/select2.min.js') }}"></script>
@endif

@if(config('adminlte.plugins.datatables'))
    <!-- DataTables with bootstrap 3 renderer -->
    <script src="{{ asset('vendor/datatables/datatables.min.js') }}"></script>

    <script src="{{ asset('vendor/datatables/DataTables-1.10.18/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/Responsive-2.2.2/js/dataTables.responsive.min.js') }}"></script>
    <!-- <script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script> -->
@endif

@if(config('adminlte.plugins.chartjs'))
    <!-- ChartJS -->
    <script src="{{ asset('vendor/chartjs/Chart.bundle.min.js') }}"></script>
@endif
<script src="{{ asset('vendor/bower/loading-indicator/dist/loading.min.js') }}"></script>
<script type="text/javascript">
	loading_default_animation = 'circle';
</script>
@yield('adminlte_js')

</body>
</html>

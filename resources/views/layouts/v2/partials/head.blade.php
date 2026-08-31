<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>
    @yield('title_prefix', '')
    @yield('title', config('xadrezsuico.name', 'Sistema'))
    @yield('title_postfix', ' - ' . config('xadrezsuico.name', 'Sistema'))
    - Versão {{ config('app.version') }}
</title>

<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
@stack('styles-before-v2')
@if(file_exists(public_path('css/app-v2.css')))
    <link rel="stylesheet" href="{{ asset('css/app-v2.css') }}?v={{ filemtime(public_path('css/app-v2.css')) }}">
@endif

<link rel="stylesheet" href="{{ asset('vendor/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/datatables/Responsive-2.2.2/css/responsive.dataTables.min.css') }}">

@stack('styles')
@yield('css')

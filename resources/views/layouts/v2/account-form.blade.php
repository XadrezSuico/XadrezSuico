@extends('layouts.v2.guest')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/v2/account-pages.css') }}?v={{ filemtime(public_path('css/v2/account-pages.css')) }}">
@endpush

@section('content')
<section class="v2-account">
    <h1>@yield('account-title')</h1>
    @if(session('status'))<div class="v2-account-status" role="status">{{ session('status') }}</div>@endif
    @yield('account-content')
    <a class="v2-account-back" href="{{ route('login') }}">Voltar ao login</a>
</section>
@endsection

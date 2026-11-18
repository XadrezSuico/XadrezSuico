@extends(Auth::check() ? 'layouts.v2.app' : 'layouts.v2.guest')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/v2/account-pages.css') }}?v={{ filemtime(public_path('css/v2/account-pages.css')) }}">
@endpush

@section('content')
<article class="v2-document">
    <header><h1>@yield('document-title')</h1></header>
    <div class="v2-document-body">@yield('document-content')</div>
    <footer><a href="{{ Auth::check() ? url('/home') : url('/login') }}">Voltar ao início</a></footer>
</article>
@endsection

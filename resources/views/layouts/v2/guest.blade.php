<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.v2.partials.head')
</head>
<body class="bg-brand-surface text-gray-800 antialiased">
    <div class="flex min-h-screen flex-col items-center justify-center px-4 py-8 sm:px-6">
        @yield('content')

        <footer class="mt-8 text-center text-sm text-gray-500">
            Versão {{ config('app.version') }}
        </footer>
    </div>

    @stack('scripts')
    @yield('js')
</body>
</html>

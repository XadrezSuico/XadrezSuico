{{--
  Guia de migração para layout v2:
  1. @extends('layouts.v2.app')
  2. @section('page-header') para título
  3. Usar @include('components.v2.panel') e demais componentes v2
  4. @push('scripts') para JS da página
  5. Adicionar @source em resources/css/v2/app.css e rodar npm run build:v2-css
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.v2.partials.head')
</head>
<body class="bg-brand-surface text-gray-800 antialiased">
    <div id="v2-app" class="flex min-h-screen">
        @include('layouts.v2.partials.sidebar')

        <div class="flex min-h-screen flex-1 flex-col lg:pl-64">
            @include('layouts.v2.partials.topbar')

            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @hasSection('page-header')
                    <div class="mb-6">
                        <h1 class="text-2xl font-semibold text-brand-dark">@yield('page-header')</h1>
                    </div>
                @endif

                @include('layouts.v2.partials.alerts')

                @yield('content')
            </main>

            <footer class="border-t border-purple-100 bg-white px-4 py-3 text-sm text-gray-500 sm:px-6 lg:px-8">
                {{ date('d/m/Y H:i:s') }}
            </footer>
        </div>
    </div>

    <div id="v2-sidebar-backdrop" class="fixed inset-0 z-30 hidden bg-black/40 lg:hidden" aria-hidden="true"></div>

    @include('layouts.v2.partials.scripts')
</body>
</html>

@extends('layouts.v2.guest')

@section('title', 'Login')

@section('content')
    @php
        $logoName = config('xadrezsuico.name', 'Sistema');
        $companyHtml = config('xadrezsuico.company_user_html', '');
    @endphp

    <div class="w-full max-w-md">
        <div class="mb-8 text-center">
            <h1 class="text-2xl font-semibold text-brand-dark">
                {{ $logoName }}
                @if($companyHtml)
                    <span class="block text-sm font-normal text-gray-500">{!! strip_tags($companyHtml) !!}</span>
                @endif
            </h1>
            <p class="mt-2 text-sm text-gray-600">Entre com suas credenciais para continuar</p>
        </div>

        @component('components.v2.panel')
            <form action="{{ url(config('adminlte.login_url', 'login')) }}" method="post" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700">E-mail</label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           autocomplete="email"
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm shadow-sm transition focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20 {{ $errors->has('email') ? 'border-red-300 focus:border-red-400 focus:ring-red-200' : '' }}"
                           placeholder="seu@email.com">
                    @if ($errors->has('email'))
                        <p class="mt-1.5 text-sm text-red-600">{{ $errors->first('email') }}</p>
                    @endif
                </div>

                <div>
                    <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700">Senha</label>
                    <input type="password"
                           id="password"
                           name="password"
                           required
                           autocomplete="current-password"
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm shadow-sm transition focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20 {{ $errors->has('password') ? 'border-red-300 focus:border-red-400 focus:ring-red-200' : '' }}"
                           placeholder="••••••••">
                    @if ($errors->has('password'))
                        <p class="mt-1.5 text-sm text-red-600">{{ $errors->first('password') }}</p>
                    @endif
                </div>

                <div class="flex items-center justify-between gap-4">
                    <label class="inline-flex cursor-pointer items-center gap-2 text-sm text-gray-600">
                        <input type="checkbox"
                               name="remember"
                               class="h-4 w-4 rounded border-gray-300 text-brand focus:ring-brand/30">
                        Lembrar-me
                    </label>

                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-lg bg-brand px-4 py-2 text-sm font-medium text-white transition hover:bg-brand-dark">
                        Entrar
                    </button>
                </div>
            </form>

            <div class="mt-6 border-t border-purple-100 pt-4 text-center">
                <a href="{{ url(config('adminlte.password_reset_url', 'password/reset')) }}"
                   class="text-sm text-brand hover:text-brand-dark hover:underline">
                    Esqueci minha senha
                </a>
            </div>
        @endcomponent
    </div>
@endsection

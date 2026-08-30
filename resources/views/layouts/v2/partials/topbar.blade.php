<header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-purple-100 bg-white px-4 shadow-sm sm:px-6 lg:px-8">
    <div class="flex items-center gap-3">
        <button type="button"
                id="v2-sidebar-toggle"
                class="inline-flex items-center justify-center rounded-lg p-2 text-brand hover:bg-brand-surface lg:hidden"
                aria-label="Abrir menu">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>
        <span class="text-sm font-medium text-gray-600 lg:hidden">{{ config('xadrezsuico.name', 'Sistema') }}</span>
    </div>

    <div class="flex items-center gap-2 sm:gap-4">
        @auth
            <span class="hidden text-sm text-gray-600 sm:inline">{{ Auth::user()->name }}</span>
            <a href="{{ url('/usuario/password/' . Auth::user()->id) }}"
               class="inline-flex items-center rounded-lg px-3 py-2 text-sm text-brand hover:bg-brand-surface"
               title="Alterar Senha">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0V10.5M4.5 10.5h15v8.25a1.5 1.5 0 01-1.5 1.5h-12a1.5 1.5 0 01-1.5-1.5V10.5z" />
                </svg>
            </a>
            <a href="#"
               onclick="event.preventDefault(); document.getElementById('v2-logout-form').submit();"
               class="inline-flex items-center rounded-lg bg-brand px-3 py-2 text-sm font-medium text-white hover:bg-brand-dark">
                Sair
            </a>
            <form id="v2-logout-form" action="{{ url('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        @endauth
    </div>
</header>

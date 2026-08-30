@php
    $canManageInscricao = (
        \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
        \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id, [4, 5]) ||
        \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
    );

    $iconDashboard = '<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-full w-full"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>';
    $iconInscricao = '<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-full w-full"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>';
    $iconLink = '<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-full w-full"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v8.25A2.25 2.25 0 005.25 18.75h13.5A2.25 2.25 0 0021 16.5V8.25m-7.5 0V6A2.25 2.25 0 0011.25 3.75h-6A2.25 2.25 0 003 6v.75m7.5 0h7.5"/></svg>';
@endphp

<div class="flex min-w-[7.5rem] flex-wrap items-center gap-1">
    @include('components.v2.btn', [
        'href' => url('/evento/dashboard/' . $evento->id),
        'label' => 'Painel',
        'variant' => 'secondary',
        'size' => 'sm',
        'icon' => $iconDashboard,
        'tooltip' => 'Abrir dashboard do evento',
    ])
    @if($canManageInscricao)
        @include('components.v2.btn', [
            'href' => url('/inscricao/' . $evento->id),
            'label' => 'Inscrição',
            'variant' => 'warning',
            'size' => 'sm',
            'target' => '_blank',
            'icon' => $iconInscricao,
            'tooltip' => 'Nova inscrição interna',
        ])
    @endif
    @include('components.v2.btn', [
        'href' => $evento->getEventPublicLink(),
        'label' => 'Divulgar',
        'variant' => 'success',
        'size' => 'sm',
        'target' => '_blank',
        'icon' => $iconLink,
        'tooltip' => 'Abrir link público de divulgação',
    ])
</div>

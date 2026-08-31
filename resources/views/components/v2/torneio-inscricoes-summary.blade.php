@php
    $showDetails = $showDetails ?? (
        \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
        \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id, [4, 5]) ||
        \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
    );
    $total = $torneio->getCountInscritos();

    $iconUsers = '<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-full w-full"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>';
    $iconCheck = '<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-full w-full"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
    $iconPresent = '<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-full w-full"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>';
    $iconResult = '<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-full w-full"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0V7.875c0-.621-.504-1.125-1.125-1.125H9.497m5.007 0V4.875A1.125 1.125 0 0013.372 3.75H9.497"/></svg>';
    $iconPaid = '<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-full w-full"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>';
    $iconPending = '<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-full w-full"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
    $iconFree = '<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-full w-full"><path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1114.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>';
    $iconLichess = '<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-full w-full"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>';
    $iconMissing = '<svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-full w-full"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>';
@endphp

<div class="min-w-[10rem] space-y-2">
    <div>
        <p class="mb-1 text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400">Total</p>
        <div class="flex flex-wrap items-center gap-1">
            @include('components.v2.stat-badge', [
                'value' => $total,
                'tooltip' => 'Total de inscritos no torneio',
                'tone' => 'brand',
                'icon' => $iconUsers,
            ])
        </div>
    </div>

    @if($showDetails)
        <div>
            <p class="mb-1 text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400">Situação</p>
            <div class="flex flex-wrap items-center gap-1">
                @include('components.v2.stat-badge', [
                    'value' => $torneio->getCountInscritosConfirmados(),
                    'tooltip' => 'Inscrições confirmadas',
                    'tone' => 'green',
                    'icon' => $iconCheck,
                ])
                @include('components.v2.stat-badge', [
                    'value' => $torneio->quantosInscritosPresentes(),
                    'tooltip' => 'Inscritos presentes',
                    'tone' => 'blue',
                    'icon' => $iconPresent,
                ])
                @include('components.v2.stat-badge', [
                    'value' => $torneio->getCountInscritosResultados(),
                    'tooltip' => 'Inscritos com resultado',
                    'tone' => 'violet',
                    'icon' => $iconResult,
                ])
            </div>
        </div>

        @if($evento->is_lichess_integration)
            <div>
                <p class="mb-1 text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400">Lichess</p>
                <div class="flex flex-wrap items-center gap-1">
                    @include('components.v2.stat-badge', [
                        'value' => $torneio->getCountLichessConfirmadosnoTorneio(),
                        'tooltip' => 'Inscritos no torneio Lichess.org',
                        'tone' => 'violet',
                        'icon' => $iconLichess,
                    ])
                    @include('components.v2.stat-badge', [
                        'value' => $torneio->getCountInscritos() - $torneio->getCountLichessConfirmadosnoTorneio(),
                        'tooltip' => 'Confirmados ainda não inscritos no Lichess',
                        'tone' => 'amber',
                        'icon' => $iconMissing,
                    ])
                </div>
            </div>
        @endif

        @if($evento->xadrezsuicopag_uuid)
            <div>
                <p class="mb-1 text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400">Pagamento</p>
                <div class="flex flex-wrap items-center gap-1">
                    @include('components.v2.stat-badge', [
                        'value' => $torneio->howManyPaid(),
                        'tooltip' => 'Inscrições com pagamento confirmado',
                        'tone' => 'green',
                        'icon' => $iconPaid,
                    ])
                    @include('components.v2.stat-badge', [
                        'value' => $torneio->howManyNotPaid(),
                        'tooltip' => 'Inscrições com pagamento pendente',
                        'tone' => 'amber',
                        'icon' => $iconPending,
                    ])
                    @include('components.v2.stat-badge', [
                        'value' => $torneio->howManyFree(),
                        'tooltip' => 'Inscrições gratuitas (categorias sem cobrança)',
                        'tone' => 'gray',
                        'icon' => $iconFree,
                    ])
                </div>
            </div>
        @endif
    @endif
</div>

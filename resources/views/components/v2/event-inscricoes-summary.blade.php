@php
    $showDetails = $showDetails ?? (
        \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
        \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id, [4, 5]) ||
        \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
    );
    $total = $evento->quantosInscritos();
    $max = $evento->maximo_inscricoes_evento;
@endphp

<div class="min-w-[9rem] space-y-1.5 text-xs leading-snug">
    <div>
        <span class="text-[0.65rem] font-medium uppercase tracking-wide text-gray-400">Total</span>
        <p class="text-base font-semibold text-brand-dark">
            {{ $total }}@if($max)<span class="font-normal text-gray-400">/{{ $max }}</span>@endif
        </p>
    </div>

    @if($showDetails)
        <dl class="grid grid-cols-[4.5rem_1fr] gap-x-1 gap-y-0.5 text-gray-700">
            <dt class="text-gray-400">Confirm.</dt>
            <dd class="font-medium">{{ $evento->quantosInscritosConfirmados() }}</dd>
            <dt class="text-gray-400">Presentes</dt>
            <dd class="font-medium">{{ $evento->quantosInscritosPresentes() }}</dd>
        </dl>

        @if($evento->is_lichess_integration)
            <div class="border-t border-purple-100 pt-1.5">
                <p class="mb-1 text-[0.65rem] font-semibold uppercase tracking-wide text-gray-500">Lichess</p>
                <dl class="grid grid-cols-[4.5rem_1fr] gap-x-1 gap-y-0.5 text-gray-700">
                    <dt class="text-gray-400">Inscritos</dt>
                    <dd class="font-medium">{{ $evento->quantosInscritosConfirmadosLichess() }}</dd>
                    <dt class="text-gray-400">Pendentes</dt>
                    <dd class="font-medium">{{ $evento->quantosInscritosFaltamLichess() }}</dd>
                </dl>
            </div>
        @endif

        @if($evento->xadrezsuicopag_uuid)
            <div class="border-t border-purple-100 pt-1.5">
                <p class="mb-1 text-[0.65rem] font-semibold uppercase tracking-wide text-gray-500">Pagamento</p>
                <dl class="grid grid-cols-[4.5rem_1fr] gap-x-1 gap-y-0.5 text-gray-700">
                    <dt class="text-gray-400">Pagos</dt>
                    <dd class="font-medium">{{ $evento->howManyPaid() }}</dd>
                    <dt class="text-gray-400">Pendentes</dt>
                    <dd class="font-medium">{{ $evento->howManyNotPaid() }}</dd>
                    <dt class="text-gray-400">Gratuitos</dt>
                    <dd class="font-medium">{{ $evento->howManyFree() }}</dd>
                </dl>
            </div>
        @endif
    @endif
</div>

<p class="mb-6 text-gray-600">Você está logado!</p>

@if(\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal())
    <div class="mb-8 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @include('components.v2.stat-card', [
            'value' => \App\Email::where([['is_sent', '=', 0]])->count(),
            'label' => 'E-mails pendentes',
            'icon' => 'envelope',
            'tone' => 'aqua',
        ])
        @include('components.v2.stat-card', [
            'value' => \App\Evento::countAllReceivingRegister(),
            'label' => 'Eventos Recebendo Inscrições',
            'icon' => 'certificate',
            'tone' => 'green',
        ])
    </div>
@endif

@component('components.v2.panel', ['title' => 'Próximos Eventos'])
    <div class="overflow-x-auto">
        <table id="tabela" class="display w-full text-sm" style="width: 100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nome do Evento</th>
                    <th>Cidade</th>
                    <th>Local do Evento</th>
                    <th>Datas</th>
                    <th>Inscrições</th>
                    <th>Recebendo Inscrições?</th>
                    <th>Opções</th>
                </tr>
            </thead>
            <tbody>
                @foreach(\App\Evento::where([['data_fim', '>=', date('Y-m-d', time() - (60 * 60 * 24))]])->get() as $evento)
                    @if(
                        \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
                        \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id, [3, 4]) ||
                        \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [6, 7])
                    )
                        <tr>
                            <td>{{ $evento->id }}</td>
                            <td>{{ $evento->name }}<br/>({{ $evento->grupo_evento->name }})</td>
                            <td>{{ $evento->cidade->name }}/{{ trim($evento->cidade->estado->abbr) }} - {{ $evento->cidade->estado->pais->codigo_iso }}</td>
                            <td>{{ $evento->local }}</td>
                            <td data-order="{{ $evento->data_inicio }}">
                                @if($evento->getDataInicio() == $evento->getDataFim())
                                    {{ $evento->getDataInicio() }}
                                @else
                                    {{ $evento->getDataInicio() }}<br/>{{ $evento->getDataFim() }}
                                @endif
                            </td>
                            <td data-order="{{ $evento->quantosInscritos() }}">
                                Total de Inscritos: {{ $evento->quantosInscritos() }}@if($evento->maximo_inscricoes_evento)/{{ $evento->maximo_inscricoes_evento }}@endif<br/>
                                @if(
                                    \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
                                    \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id, [4, 5]) ||
                                    \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
                                )
                                    Confirmados: {{ $evento->quantosInscritosConfirmados() }}<br/>
                                    Presentes: {{ $evento->quantosInscritosPresentes() }}
                                    <hr class="my-2 border-purple-100"/>
                                    @if($evento->is_lichess_integration)
                                        <strong>Torneio Lichess.org</strong><br/>
                                        Inscritos: <strong>{{ $evento->quantosInscritosConfirmadosLichess() }}</strong><br/>
                                        Não Inscritos: <strong>{{ $evento->quantosInscritosFaltamLichess() }}</strong>
                                    @endif
                                    @if($evento->xadrezsuicopag_uuid)
                                        <strong>Pagamento:</strong><br/>
                                        Pagos: <strong>{{ $evento->howManyPaid() }}</strong><br/>
                                        Pagamento Pendente: <strong>{{ $evento->howManyNotPaid() }}</strong><br/>
                                        Gratuidades (Categorias Gratuitas): <strong>{{ $evento->howManyFree() }}</strong>
                                    @endif
                                @endif
                            </td>
                            <td>@if(!$evento->inscricoes_encerradas()) Sim @else <strong>Não</strong> @endif</td>
                            <td class="space-y-2">
                                @include('components.v2.btn', [
                                    'href' => url('/evento/dashboard/' . $evento->id),
                                    'label' => 'Dashboard',
                                    'variant' => 'secondary',
                                    'block' => true,
                                ])
                                @if(
                                    \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
                                    \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id, [4, 5]) ||
                                    \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
                                )
                                    @include('components.v2.btn', [
                                        'href' => url('/inscricao/' . $evento->id),
                                        'label' => 'Nova Inscrição (Interno)',
                                        'variant' => 'warning',
                                        'target' => '_blank',
                                        'block' => true,
                                    ])
                                @endif
                                @include('components.v2.btn', [
                                    'href' => $evento->getEventPublicLink(),
                                    'label' => 'Link de Divulgação',
                                    'variant' => 'success',
                                    'target' => '_blank',
                                    'block' => true,
                                ])
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
@endcomponent

<div class="mt-8">
    <details class="group overflow-hidden rounded-xl border border-purple-100 bg-white shadow-sm">
        <summary class="flex cursor-pointer list-none items-center justify-between border-b border-purple-100 bg-brand-surface px-4 py-3 sm:px-6">
            <h3 class="text-base font-semibold text-brand-dark">Eventos Recebendo Inscrições</h3>
            <span class="text-brand transition group-open:rotate-180">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </span>
        </summary>
        <div class="p-4 sm:p-6">
            <div class="overflow-x-auto">
                <table id="tabela_recebendo_inscricoes" class="display w-full text-sm" style="width: 100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nome do Evento</th>
                            <th>Cidade</th>
                            <th>Local do Evento</th>
                            <th>Datas</th>
                            <th>Inscrições</th>
                            <th>Opções</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(\App\Evento::getAllReceivingRegister() as $evento)
                            @if(
                                \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
                                \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id, [3, 4]) ||
                                \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [6, 7])
                            )
                                <tr>
                                    <td>{{ $evento->id }}</td>
                                    <td>{{ $evento->name }}<br/>({{ $evento->grupo_evento->name }})</td>
                                    <td>{{ $evento->cidade->name }}/{{ trim($evento->cidade->estado->abbr) }} - {{ $evento->cidade->estado->pais->codigo_iso }}</td>
                                    <td>{{ $evento->local }}</td>
                                    <td data-order="{{ $evento->data_inicio }}">
                                        @if($evento->getDataInicio() == $evento->getDataFim())
                                            {{ $evento->getDataInicio() }}
                                        @else
                                            {{ $evento->getDataInicio() }}<br/>{{ $evento->getDataFim() }}
                                        @endif
                                    </td>
                                    <td data-order="{{ $evento->quantosInscritos() }}">
                                        Total de Inscritos: {{ $evento->quantosInscritos() }}@if($evento->maximo_inscricoes_evento)/{{ $evento->maximo_inscricoes_evento }}@endif<br/>
                                        @if(
                                            \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
                                            \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id, [4, 5]) ||
                                            \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
                                        )
                                            Confirmados: {{ $evento->quantosInscritosConfirmados() }}<br/>
                                            Presentes: {{ $evento->quantosInscritosPresentes() }}
                                            <hr class="my-2 border-purple-100"/>
                                            @if($evento->is_lichess_integration)
                                                <strong>Torneio Lichess.org</strong><br/>
                                                Inscritos: <strong>{{ $evento->quantosInscritosConfirmadosLichess() }}</strong><br/>
                                                Não Inscritos: <strong>{{ $evento->quantosInscritosFaltamLichess() }}</strong>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="space-y-2">
                                        @include('components.v2.btn', [
                                            'href' => url('/evento/dashboard/' . $evento->id),
                                            'label' => 'Dashboard',
                                            'variant' => 'secondary',
                                            'block' => true,
                                        ])
                                        @if(
                                            \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
                                            \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id, [4, 5]) ||
                                            \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
                                        )
                                            @include('components.v2.btn', [
                                                'href' => url('/inscricao/' . $evento->id),
                                                'label' => 'Nova Inscrição (Interno)',
                                                'variant' => 'warning',
                                                'target' => '_blank',
                                                'block' => true,
                                            ])
                                        @endif
                                        @include('components.v2.btn', [
                                            'href' => $evento->getEventPublicLink(),
                                            'label' => 'Link de Divulgação',
                                            'variant' => 'success',
                                            'target' => '_blank',
                                            'block' => true,
                                        ])
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </details>
</div>

@push('scripts')
<script type="text/javascript">
    $(document).ready(function () {
        $('#tabela').DataTable({
            responsive: true,
        });
        $('#tabela_recebendo_inscricoes').DataTable({
            responsive: true,
        });
    });
</script>
@endpush

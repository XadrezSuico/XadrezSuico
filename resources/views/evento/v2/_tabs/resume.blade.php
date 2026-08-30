@php
    $totals = $dashboard_stats['totals'];
    $payment = $dashboard_stats['payment'];
    $bigger_tournament = $dashboard_stats['bigger_tournament'];
    $upcoming_timeline = $evento->getUpcomingTimelineItems(3);
    $inscricoes_list_url = url('/evento/' . $evento->id . '/inscricoes/list');

    $resume_pct = function ($parte, $total) {
        if ($total <= 0) {
            return 0;
        }
        return min(100, (int) round(100 * $parte / $total));
    };
@endphp

@component('components.v2.panel')
    <div class="rounded-lg border border-sky-200 bg-sky-50 p-4 text-sm text-sky-900">
        <p class="font-semibold">Sobre esta aba</p>
        <p class="mt-1">Visão analítica: funil de inscrições, breakdown por torneio e pagamentos.</p>
    </div>
@endcomponent

@include('evento.v2._partials.dashboard_alerts', ['compact' => false, 'alert_collapse_id' => 'resume'])

<div class="v2-stat-grid">
    @foreach([
        ['value' => $totals['inscritos'], 'label' => 'Inscritos', 'tone' => 'aqua', 'icon' => 'users'],
        ['value' => $totals['confirmados'], 'label' => 'Confirmados', 'tone' => 'green', 'icon' => 'check'],
        ['value' => $totals['presentes'], 'label' => 'Presentes', 'tone' => 'brand', 'icon' => 'certificate'],
        ['value' => $totals['resultados'], 'label' => 'Com resultados', 'tone' => 'brand', 'icon' => 'chart'],
    ] as $card)
        <a href="{{ $inscricoes_list_url }}" class="block transition hover:opacity-95">
            @include('components.v2.stat-card', [
                'value' => $card['value'],
                'label' => $card['label'],
                'tone' => $card['tone'],
                'icon' => $card['icon'],
            ])
        </a>
    @endforeach
</div>

@component('components.v2.panel', ['title' => 'Atalhos rápidos'])
    <div class="v2-btn-grid">
        @if(
            \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
            \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id, [4, 5]) ||
            \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        )
            @include('components.v2.btn', ['href' => url('/inscricao/' . $evento->id), 'label' => 'Nova Inscrição', 'variant' => 'success', 'size' => 'sm'])
            @include('components.v2.btn', ['href' => $evento->getEventPublicLink(), 'label' => 'Link Público', 'variant' => 'primary', 'size' => 'sm'])
        @endif
        @include('components.v2.btn', ['href' => $inscricoes_list_url, 'label' => 'Lista de Inscritos', 'size' => 'sm'])
        @if(
            \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
            \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id, [4]) ||
            \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        )
            @include('components.v2.btn', ['href' => '/evento/classificar/' . $evento->id, 'label' => 'Classificar Evento', 'variant' => 'success', 'size' => 'sm'])
        @endif
        @include('components.v2.btn', ['href' => url('/evento/' . $evento->id . '/exports/emparceirador'), 'label' => 'Emparceirador', 'size' => 'sm'])
    </div>
@endcomponent

<div class="v2-panel-grid v2-panel-grid--3">
    @component('components.v2.panel', ['title' => 'Funil de inscrições'])
        @if($totals['inscritos'] === 0)
            <p class="text-sm text-gray-500">Nenhum inscrito registrado neste evento.</p>
        @else
            @foreach([
                ['label' => 'Confirmados sobre inscritos', 'parte' => $totals['confirmados'], 'color' => 'bg-emerald-500'],
                ['label' => 'Presentes sobre inscritos', 'parte' => $totals['presentes'], 'color' => 'bg-amber-500'],
                ['label' => 'Com resultados sobre inscritos', 'parte' => $totals['resultados'], 'color' => 'bg-cyan-500'],
            ] as $bar)
                @php $pct = $resume_pct($bar['parte'], $totals['inscritos']); @endphp
                <div class="mb-4 last:mb-0">
                    <div class="mb-1 flex justify-between text-sm">
                        <span class="text-gray-600">{{ $bar['label'] }}</span>
                        <span class="font-medium text-gray-800"><strong>{{ $bar['parte'] }}</strong>/{{ $totals['inscritos'] }} ({{ $pct }}%)</span>
                    </div>
                    <div class="h-2 overflow-hidden rounded-full bg-gray-200">
                        <div class="{{ $bar['color'] }} h-full rounded-full" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            @endforeach
        @endif
    @endcomponent

    @component('components.v2.panel', ['title' => 'Por torneio'])
        @if(count($dashboard_stats['torneios']) === 0)
            <p class="text-sm text-gray-500">Nenhum torneio cadastrado.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-purple-100 text-left text-gray-500">
                            <th class="pb-2 pr-4">Torneio</th>
                            <th class="pb-2 pr-4 text-right">Inscritos</th>
                            <th class="pb-2 pr-4 text-right">Confirmados</th>
                            <th class="pb-2 text-right">Presentes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dashboard_stats['torneios'] as $torneio_stat)
                            <tr class="border-b border-purple-50 {{ $bigger_tournament['status'] && $bigger_tournament['tournament']->id === $torneio_stat['id'] ? 'bg-brand-surface' : '' }}">
                                <td class="py-2 pr-4">
                                    {{ $torneio_stat['name'] }}
                                    @if($bigger_tournament['status'] && $bigger_tournament['tournament']->id === $torneio_stat['id'])
                                        <span class="ml-1 rounded bg-brand px-1.5 py-0.5 text-xs text-white">Maior</span>
                                    @endif
                                </td>
                                <td class="py-2 pr-4 text-right">{{ $torneio_stat['inscritos'] }}</td>
                                <td class="py-2 pr-4 text-right">{{ $torneio_stat['confirmados'] }}</td>
                                <td class="py-2 text-right">{{ $torneio_stat['presentes'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endcomponent

    <div class="space-y-6">
        @component('components.v2.panel', ['title' => 'Status e configuração'])
            <div class="flex flex-wrap gap-2 text-xs">
                <span class="rounded-full px-2 py-1 {{ $evento->is_inscricoes_bloqueadas ? 'bg-red-100 text-red-800' : 'bg-emerald-100 text-emerald-800' }}">
                    Inscrições {{ $evento->is_inscricoes_bloqueadas ? 'bloqueadas' : 'permitidas' }}
                </span>
                <span class="rounded-full px-2 py-1 {{ $evento->permite_edicao_inscricao ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700' }}">
                    Edição {{ $evento->permite_edicao_inscricao ? 'permitida' : 'bloqueada' }}
                </span>
                <span class="rounded-full px-2 py-1 {{ $evento->mostrar_resultados ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700' }}">
                    Resultados {{ $evento->mostrar_resultados ? 'públicos' : 'privados' }}
                </span>
                <span class="rounded-full px-2 py-1 {{ $evento->classificavel ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700' }}">
                    Classificação {{ $evento->classificavel ? 'habilitada' : 'desabilitada' }}
                </span>
            </div>
        @endcomponent

        @component('components.v2.panel', ['title' => 'Torneio e presença'])
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-gray-500">Maior torneio</dt>
                    <dd class="font-medium text-gray-800">
                        @if($bigger_tournament['status'])
                            {{ $bigger_tournament['name'] }} ({{ $bigger_tournament['total'] }})
                        @else
                            <span class="text-gray-500">{{ $bigger_tournament['tournament'] }}</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500">Emparceiramentos importados</dt>
                    <dd class="font-medium">{{ $totals['emparceiramentos'] }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Clubes / equipes presentes</dt>
                    <dd class="font-medium">{{ $totals['clubes'] }}</dd>
                </div>
            </dl>
        @endcomponent

        @component('components.v2.panel', ['title' => 'Próximos marcos'])
            @if(count($upcoming_timeline) === 0)
                <p class="text-sm text-gray-500">Nenhum marco futuro cadastrado.</p>
            @else
                <ul class="space-y-3 text-sm">
                    @foreach($upcoming_timeline as $item)
                        <li>
                            <strong>{{ $item['datetime'] }}</strong>
                            @if($item['is_expected'])
                                <span class="ml-1 rounded bg-amber-100 px-1.5 py-0.5 text-xs text-amber-800">Previsão</span>
                            @endif
                            <p class="text-gray-500">{{ $item['text'] }}</p>
                        </li>
                    @endforeach
                </ul>
            @endif
            <a href="{{ url('/evento/dashboard/' . $evento->id . '?tab=timeline') }}" class="mt-3 inline-block text-sm text-brand hover:underline">Gerenciar timeline →</a>
        @endcomponent
    </div>
</div>

@if($evento->isPaid())
    @component('components.v2.panel', ['title' => 'Pagamentos'])
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-purple-100 text-left text-gray-500">
                        <th class="pb-2 pr-4">Situação</th>
                        <th class="pb-2 pr-4 text-right">Total</th>
                        <th class="pb-2 pr-4 text-right">Confirmados</th>
                        <th class="pb-2 pr-4 text-right">Presentes</th>
                        <th class="pb-2 text-right">Com resultados</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach([
                        ['label' => 'Pagos', 'key' => 'paid', 'class' => 'bg-emerald-100 text-emerald-800'],
                        ['label' => 'Gratuidade', 'key' => 'free', 'class' => 'bg-sky-100 text-sky-800'],
                        ['label' => 'Não pagos', 'key' => 'not_paid', 'class' => 'bg-amber-100 text-amber-800'],
                    ] as $row)
                        <tr class="border-b border-purple-50">
                            <td class="py-2 pr-4"><span class="rounded px-2 py-0.5 text-xs {{ $row['class'] }}">{{ $row['label'] }}</span></td>
                            <td class="py-2 pr-4 text-right">{{ $payment[$row['key']]['total'] }}</td>
                            <td class="py-2 pr-4 text-right">{{ $payment[$row['key']]['confirmados'] }}</td>
                            <td class="py-2 pr-4 text-right">{{ $payment[$row['key']]['presentes'] }}</td>
                            <td class="py-2 text-right">{{ $payment[$row['key']]['resultados'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-brand-surface font-semibold">
                        <td class="py-2 pr-4">Geral</td>
                        <td class="py-2 pr-4 text-right">{{ $totals['inscritos'] }}</td>
                        <td class="py-2 pr-4 text-right">{{ $totals['confirmados'] }}</td>
                        <td class="py-2 pr-4 text-right">{{ $totals['presentes'] }}</td>
                        <td class="py-2 text-right">{{ $totals['resultados'] }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endcomponent
@endif

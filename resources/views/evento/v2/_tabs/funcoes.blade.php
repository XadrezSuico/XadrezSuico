@php
    $iconPlus = '<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-full w-full"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>';
    $iconLink = '<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-full w-full"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/></svg>';
    $iconList = '<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-full w-full"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>';
    $iconDownload = '<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-full w-full"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>';
    $iconEye = '<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-full w-full"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>';
    $iconSort = '<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-full w-full"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5"/></svg>';
    $canInscricao = (
        \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
        \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id, [4, 5]) ||
        \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
    );
    $canClassificar = (
        \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
        \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id, [4]) ||
        \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
    );
@endphp

@component('components.v2.panel')
    <div class="mb-4 rounded-lg border border-sky-200 bg-sky-50 p-4 text-sm text-sky-900">
        <p class="font-semibold">Sobre esta aba</p>
        <p class="mt-1 text-sky-800">Atalhos operacionais e interruptores do evento. Os toggles salvam automaticamente. Métricas detalhadas na aba <a href="{{ url('/evento/dashboard/' . $evento->id . '?tab=resume') }}" class="font-medium underline">Resumo</a>.</p>
    </div>
@endcomponent

<div class="v2-panel-grid v2-panel-grid--2">
    @component('components.v2.panel', ['title' => 'Inscrições'])
        @if($canInscricao)
            <div class="v2-action-grid v2-action-grid--3">
                @if($evento->layout_version == 2)
                    @include('components.v2.action-card', ['href' => url('/inscricao/' . $evento->id), 'label' => 'Nova Inscrição', 'icon' => $iconPlus, 'variant' => 'success'])
                    @include('components.v2.action-card', ['href' => $evento->getEventPublicLink(), 'label' => 'Link Divulgação', 'icon' => $iconLink, 'variant' => 'primary'])
                @else
                    @php $inscUrl = $evento->e_inscricao_apenas_com_link ? url('/inscricao/' . $evento->id . '?token=' . $evento->token) : url('/inscricao/' . $evento->id); @endphp
                    @if($evento->e_inscricao_apenas_com_link)
                        <div class="sm:col-span-2 xl:col-span-3 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-900">Inscrição apenas pelo link compartilhado.</div>
                    @endif
                    @include('components.v2.action-card', ['href' => $inscUrl, 'label' => 'Nova Inscrição', 'icon' => $iconPlus, 'variant' => 'success'])
                    @include('components.v2.action-card', ['href' => $inscUrl, 'label' => 'Link Divulgação', 'icon' => $iconLink, 'variant' => 'primary'])
                @endif
                @if($evento->e_permite_confirmacao_publica)
                    @php $confUrl = $evento->e_inscricao_apenas_com_link ? url('/inscricao/' . $evento->id . '/confirmacao?token=' . $evento->token) : url('/inscricao/' . $evento->id . '/confirmacao'); @endphp
                    @include('components.v2.action-card', ['href' => $confUrl, 'label' => 'Confirmação Pública', 'icon' => $iconLink, 'variant' => 'primary'])
                @endif
            </div>
        @endif

        @include('components.v2.action-card', [
            'href' => url('/evento/' . $evento->id . '/inscricoes/list'),
            'label' => 'Lista de Inscritos',
            'icon' => $iconList,
            'class' => 'mt-3',
        ])

        <div class="mt-6 space-y-4 border-t border-purple-100 pt-4">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Configurações</p>
            @include('components.v2.switch', [
                'id' => 'toggle_inscricoes',
                'checked' => !$evento->is_inscricoes_bloqueadas,
                'label' => 'Permitir Inscrições',
            ])
            @include('components.v2.switch', [
                'id' => 'toggle_edicao_inscricao',
                'checked' => $evento->permite_edicao_inscricao,
                'label' => 'Permitir Edição de Inscrição',
            ])
        </div>

        @if($evento->isPaid())
            <div class="mt-4">
                @include('components.v2.btn', [
                    'href' => url('/evento/' . $evento->id . '/toggleregistrationpaidconfirmed'),
                    'label' => $evento->getConfig('flag__registration_paid_confirmed', true) ? 'Não confirmar inscrição paga auto.' : 'Confirmar inscrição paga auto.',
                    'variant' => 'warning',
                    'size' => 'sm',
                ])
            </div>
        @endif
    @endcomponent

    @component('components.v2.panel', ['title' => 'Emparceiramento'])
        <p class="mb-4 text-sm text-gray-500">Exporte para o emparceirador ou compartilhe o acompanhamento público.</p>
        <div class="v2-action-grid v2-action-grid--2">
            @include('components.v2.action-card', ['href' => url('/evento/' . $evento->id . '/exports/emparceirador'), 'label' => 'Baixar (todas — sem dados)', 'icon' => $iconDownload])
            @include('components.v2.action-card', ['href' => url('/evento/' . $evento->id . '/exports/xadrezsuicoemparceirador/data'), 'label' => 'Baixar (confirmadas — com dados)', 'icon' => $iconDownload])
            @include('components.v2.action-card', ['href' => url('/evento/acompanhar/' . $evento->id), 'label' => 'Acompanhar (público)', 'icon' => $iconEye])
        </div>
    @endcomponent
</div>

@if($canClassificar)
    <div class="mt-6 v2-panel-grid v2-panel-grid--2">
        @component('components.v2.panel', ['title' => 'Classificação e resultados'])
            <div class="v2-action-grid v2-action-grid--2">
                @include('components.v2.action-card', ['href' => '/evento/classificar/' . $evento->id, 'label' => 'Classificar Evento', 'icon' => $iconSort, 'variant' => 'success'])
                @include('components.v2.action-card', ['href' => url('/evento/classificacao/' . $evento->id), 'label' => 'Visualizar Classificação', 'icon' => $iconEye])
            </div>
            <div class="mt-6 border-t border-purple-100 pt-4">
                @include('components.v2.switch', [
                    'id' => 'toggle_resultados',
                    'checked' => $evento->mostrar_resultados,
                    'label' => 'Permitir visualização pública dos resultados',
                ])
            </div>
            @if($evento->event_team_awards()->count() > 0)
                <div class="mt-4 v2-action-grid v2-action-grid--2">
                    @include('components.v2.action-card', ['href' => url('/evento/premiacao_time/classificar/' . $evento->id), 'label' => 'Classificar Times', 'icon' => $iconSort])
                    @include('components.v2.action-card', ['href' => url('/evento/' . $evento->id . '/team_awards/standings'), 'label' => 'Premiações de Times', 'icon' => $iconList])
                </div>
            @endif
            @include('components.v2.action-card', [
                'href' => url('/evento/dashboard/' . $evento->id . '?tab=premiacao_equipe'),
                'label' => 'Configurar Premiação por Equipes',
                'icon' => $iconSort,
                'class' => 'mt-3',
            ])
        @endcomponent

        @component('components.v2.panel', ['title' => 'Rating (Swiss-Manager)'])
            @include('components.v2.action-card', [
                'href' => url('/evento/' . $evento->id . '/enxadristas/sm/inscritos'),
                'label' => 'Lista de rating dos inscritos',
                'icon' => $iconDownload,
                'target' => '_blank',
            ])
            @if($evento->tipo_rating)
                <div class="mt-4 space-y-3 border-t border-purple-100 pt-4">
                    @include('components.v2.switch', [
                        'id' => 'toggle_rating',
                        'checked' => $evento->is_rating_calculate_enabled,
                        'label' => 'Permitir Cálculo do Rating Interno',
                    ])
                    <div id="toggle_rating_status_container" @if(!$evento->is_rating_calculate_enabled) style="display:none;" @endif>
                        @if($evento->consegueCalcularRating() == 0)
                            @include('components.v2.action-card', ['href' => '#', 'label' => 'Calcular Rating (emparceiramento não importado)', 'disabled' => true])
                        @else
                            @include('components.v2.action-card', ['href' => url('/evento/' . $evento->id . '/rating/calculate'), 'label' => 'Calcular Rating', 'icon' => $iconSort])
                        @endif
                    </div>
                </div>
            @else
                <p class="mt-3 text-sm text-gray-500">Este evento não utiliza rating interno configurado.</p>
            @endif
        @endcomponent
    </div>
@endif

@if($canClassificar && ($evento->evento_classificador_id > 0 || $evento->grupo_evento_classificador_id > 0))
    @component('components.v2.panel', ['title' => 'Classificador vinculado'])
        <div class="v2-action-grid v2-action-grid--2">
            @if($evento->evento_classificador_id > 0)
                @include('components.v2.action-card', ['href' => url('/evento/' . $evento->id . '/gerenciamento/torneio_3/import'), 'label' => 'Importar do Evento Classificador', 'icon' => $iconPlus])
            @else
                @include('components.v2.action-card', ['href' => url('/evento/' . $evento->id . '/gerenciamento/import'), 'label' => 'Importar do Grupo Classificador', 'icon' => $iconPlus])
            @endif
            @include('components.v2.action-card', [
                'href' => $evento->evento_classificador_id > 0 ? url('/evento/' . $evento->id . '/gerenciamento/torneio_3/removeAll') : url('/evento/' . $evento->id . '/gerenciamento/removeAll'),
                'label' => 'Remover todas as Inscrições',
                'variant' => 'warning',
            ])
        </div>
    @endcomponent
@endif

@if($evento->grupo_evento->hasConfig('is_pr_esporte', true))
    @component('components.v2.panel', ['title' => 'Paraná Esporte'])
        <div class="v2-action-grid v2-action-grid--3">
            @include('components.v2.action-card', ['href' => url('/evento/' . $evento->id . '/imports/ingadigital/file'), 'label' => 'Importar Arquivo', 'icon' => $iconPlus])
            @if($evento->tipo_modalidade == 0)
                @include('components.v2.action-card', ['href' => url('/evento/' . $evento->id . '/exports/presporte/team'), 'label' => 'Confirmação equipes (.xlsx)', 'icon' => $iconDownload])
                @include('components.v2.action-card', ['href' => url('/evento/' . $evento->id . '/exports/presporte/team/pdf'), 'label' => 'Confirmação equipes (.pdf)', 'icon' => $iconDownload])
            @else
                @include('components.v2.action-card', ['href' => url('/evento/' . $evento->id . '/exports/presporte/single'), 'label' => 'Confirmação individual (.xlsx)', 'icon' => $iconDownload])
                @include('components.v2.action-card', ['href' => url('/evento/' . $evento->id . '/exports/presporte/single/pdf'), 'label' => 'Confirmação individual (.pdf)', 'icon' => $iconDownload])
            @endif
        </div>
    @endcomponent
@endif

<div class="mt-6 v2-panel-grid v2-panel-grid--2">
    @component('components.v2.panel', ['title' => 'Configurações gerais'])
        <div class="space-y-4">
            @include('components.v2.switch', ['id' => 'toggle_classificavel', 'checked' => $evento->classificavel, 'label' => 'Permitir classificação geral deste evento'])
            @include('components.v2.switch', ['id' => 'toggle_resultados_automaticos', 'checked' => !$evento->e_resultados_manuais, 'label' => 'Resultados Automáticos'])
        </div>
    @endcomponent

    @component('components.v2.panel', ['title' => 'Relatórios'])
        <div class="v2-action-grid v2-action-grid--2">
            @include('components.v2.action-card', ['href' => url('/evento/' . $evento->id . '/relatorios/premiados'), 'label' => 'Enxadristas Premiados', 'icon' => $iconList])
            @if($evento->calcula_cbx || $evento->calcula_fide)
                @include('components.v2.action-card', ['href' => url('/evento/' . $evento->id . '/relatorios/comparacao-cadastros'), 'label' => 'Comparação de Cadastros', 'icon' => $iconList])
            @endif
            @if($evento->tipo_modalidade == 0 && ($evento->calcula_cbx || $evento->calcula_fide))
                @include('components.v2.action-card', ['href' => url('/evento/' . $evento->id . '/relatorios/anuidade-cbx'), 'label' => 'Anuidade CBX', 'icon' => $iconList])
            @endif
        </div>
    @endcomponent
</div>

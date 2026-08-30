@extends('layouts.v2.event')

@php
    $activeTab = $tab ?: 'funcoes';
    $v2Tabs = [
        'funcoes' => 'evento.v2._tabs.funcoes',
        'resume' => 'evento.v2._tabs.resume',
    ];
    $legacyTabs = [
        'editar_evento', 'pagina', 'timeline', 'criterio_desempate', 'premiacao_equipe',
        'categoria', 'categorias_relacionadas', 'evento_filho', 'torneio',
        'campo_personalizado', 'email_template', 'classificator',
    ];
@endphp

@section('event-content')
    @if(isset($v2Tabs[$activeTab]))
        @include($v2Tabs[$activeTab])
    @elseif(in_array($activeTab, $legacyTabs, true))
        <div class="v2-legacy-tab">
            @include('evento._tabs.' . $activeTab)
        </div>
    @else
        @include('evento.v2._tabs.funcoes')
    @endif
@endsection

@push('scripts')
    @include('evento.v2._scripts.dashboard', compact('evento', 'tab', 'categorias', 'criterios_desempate', 'tipos_torneio', 'softwares', 'tipos_rating', 'xadrezsuicopag_controller'))
@endpush

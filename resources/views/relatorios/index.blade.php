@extends('layouts.v2.app')

@section('title', 'Relatórios')

@section('page-header', 'Relatórios')

@section('content')
@php
    $iconList = '<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-full w-full"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>';
    $iconChart = '<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-full w-full"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>';
@endphp

<p class="mb-6 text-gray-600">Relatórios gerais do sistema sobre cadastros e integração com entidades externas.</p>

<div class="v2-panel-grid v2-panel-grid--2">
    @component('components.v2.panel', ['title' => 'Comparação de Cadastros'])
        <p class="mb-4 text-sm text-gray-600">
            Compara o nome do cadastro local com o nome integrado da entidade (CBX/FIDE), considerando todos os enxadristas do sistema.
        </p>
        <div class="v2-action-grid v2-action-grid--2">
            @include('components.v2.action-card', [
                'href' => route('relatorios.comparacao_cadastros', ['modo' => 'cbx']),
                'label' => 'Comparação CBX',
                'description' => 'Todos os enxadristas com ID CBX',
                'icon' => $iconList,
            ])
            @include('components.v2.action-card', [
                'href' => route('relatorios.comparacao_cadastros', ['modo' => 'fide']),
                'label' => 'Comparação FIDE',
                'description' => 'Todos os enxadristas com ID FIDE',
                'icon' => $iconList,
            ])
            @include('components.v2.action-card', [
                'href' => route('relatorios.comparacao_cadastros', ['modo' => 'cbx-fide']),
                'label' => 'Comparação CBX e FIDE',
                'description' => 'Visão combinada das duas entidades',
                'icon' => $iconList,
            ])
        </div>
    @endcomponent

    @component('components.v2.panel', ['title' => 'Resumo de Integração'])
        <p class="mb-4 text-sm text-gray-600">
            Resumo do status de integração com CBX ou FIDE, incluindo totais e listagem detalhada por enxadrista.
        </p>
        <div class="v2-action-grid v2-action-grid--2">
            @include('components.v2.action-card', [
                'href' => route('relatorios.resumo_integracao', ['entidade' => 'cbx']),
                'label' => 'Resumo CBX',
                'description' => 'Status de integração com a CBX',
                'icon' => $iconChart,
            ])
            @include('components.v2.action-card', [
                'href' => route('relatorios.resumo_integracao', ['entidade' => 'fide']),
                'label' => 'Resumo FIDE',
                'description' => 'Status de integração com a FIDE',
                'icon' => $iconChart,
            ])
        </div>
    @endcomponent
</div>
@endsection

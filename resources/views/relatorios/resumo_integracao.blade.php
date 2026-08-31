@extends('layouts.v2.app')

@section('title', $titulo)

@section('page-header', $titulo)

@section('content')
<div class="mb-4">
    @include('components.v2.btn', [
        'href' => route('relatorios.index'),
        'label' => '← Voltar aos Relatórios',
        'variant' => 'secondary',
        'size' => 'sm',
    ])
</div>

<div class="mb-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
    @include('components.v2.stat-card', [
        'value' => $dados['kpis']['com_id'],
        'label' => 'Enxadristas com ID ' . $dados['entidade_label'],
        'icon' => 'user',
        'tone' => 'brand',
    ])
    @include('components.v2.stat-card', [
        'value' => $dados['kpis']['atualizacao_mes'],
        'label' => 'Atualização este mês',
        'icon' => 'check',
        'tone' => 'green',
    ])
    @include('components.v2.stat-card', [
        'value' => $dados['kpis']['erro'],
        'label' => 'Erro de integração',
        'icon' => 'chart',
        'tone' => 'aqua',
    ])
    @include('components.v2.stat-card', [
        'value' => $dados['kpis']['pendente'],
        'label' => 'Pendentes de integração',
        'icon' => 'certificate',
        'tone' => 'green',
    ])
</div>

@component('components.v2.panel', ['title' => 'Detalhamento'])
    <p class="mb-4 text-sm text-gray-600">
        Listagem de enxadristas com ID {{ $dados['entidade_label'] }} informado, incluindo data da última integração e status atual.
    </p>
    @include('relatorios._partials.resumo_integracao_table', ['dados' => $dados])
@endcomponent
@endsection

@push('styles')
<style>
    .display-none, .displayNone {
        display: none;
    }

    .parecer-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 3px;
        font-size: 12px;
        font-weight: bold;
        white-space: nowrap;
    }

    .parecer-confere {
        background-color: #00a65a;
        color: #fff;
    }

    .parecer-verificar {
        background-color: #f39c12;
        color: #fff;
    }

    .parecer-nao-confere,
    .parecer-nao-integrado {
        background-color: #dd4b39;
        color: #fff;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 9999px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .status-integrado {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status-erro {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .status-pendente {
        background-color: #fef3c7;
        color: #92400e;
    }

    .status-sem-id {
        background-color: #f3f4f6;
        color: #374151;
    }
</style>
@endpush

@push('scripts')
@foreach([
    'https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js',
    'https://cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js',
    'https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js',
    'https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js',
] as $url)
<script type="text/javascript" src="{{ $url }}"></script>
@endforeach
<script type="text/javascript">
    $(document).ready(function () {
        if ($('#tabela-integracao').length) {
            $('#tabela-integracao').DataTable({
                responsive: true,
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                order: [[0, 'asc']],
                columnDefs: [{ targets: 0, visible: false }],
                paging: false,
            });
        }
    });
</script>
@endpush

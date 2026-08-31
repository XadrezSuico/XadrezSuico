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

@component('components.v2.panel', ['title' => 'Legenda'])
    <div class="legenda-parecer">
        <span class="parecer-badge parecer-nao-confere">Não confere</span>
        <span class="parecer-badge parecer-nao-integrado">Nome não integrado</span>
        <span class="parecer-badge parecer-verificar">Verificar</span>
        <span class="parecer-badge parecer-confere">Confere</span>
        <p class="mt-3 text-sm text-gray-600">
            Compara o nome do cadastro local com o nome integrado da entidade (CBX/FIDE), quando o ID estiver informado.
            Registros com maior divergência aparecem primeiro.
        </p>
    </div>
@endcomponent

<div class="mt-6">
    @component('components.v2.panel', ['title' => 'Enxadristas'])
        @include('relatorios._partials.comparacao_cadastros_table')
    @endcomponent
</div>
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

    .legenda-parecer .parecer-badge {
        margin-right: 8px;
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
        if ($('#tabela-comparacao').length) {
            $('#tabela-comparacao').DataTable({
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

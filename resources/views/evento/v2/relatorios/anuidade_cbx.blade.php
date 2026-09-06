@extends('layouts.v2.event-page', ['pageTitle' => 'Anuidade CBX - ' . $evento->name])

@section('event-page-content')
    @include('evento.v2._content.relatorios.anuidade_cbx')
@endsection

@push('styles')
    <style>
        .display-none, .displayNone {
            display: none;
        }

        .box-title.evento {
            font-size: 2.5rem;
            font-weight: bold;
        }

        .anuidade-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
            white-space: nowrap;
        }

        .anuidade-pago {
            background-color: #00a65a;
            color: #fff;
        }

        .anuidade-pendente {
            background-color: #dd4b39;
            color: #fff;
        }

        .anuidade-sem-id {
            background-color: #777;
            color: #fff;
        }

        .anuidade-aguardando {
            background-color: #f39c12;
            color: #fff;
        }

        .anuidade-erro {
            background-color: #8b0000;
            color: #fff;
        }

        .anuidade-icon {
            margin-left: 6px;
        }

        .fa-spinner {
            color: orange;
        }

        .fa-check {
            color: green;
        }

        .fa-times {
            color: red;
        }

        .anuidade-comprovante-label {
            margin: 0;
            font-weight: normal;
            white-space: nowrap;
            cursor: pointer;
        }

        .anuidade-comprovante-check {
            margin-right: 4px;
            vertical-align: middle;
        }
    </style>
@endpush

@push('event-scripts')
@foreach(array(
    "https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js",
    "https://cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js",
    "https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js",
    "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js",
    "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js",
    "https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js",
    "https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"
    ) as $url)
<script type="text/javascript" src="{{ $url }}"></script>
@endforeach
<script type="text/javascript">
    var enxadristas = [];
    var enxadristasConsulta = [];
    var erro = false;
    var dataTableInicializado = false;

    @php($j = 0)
    @php($k = 0)
    @foreach($linhas as $linha)
        @if($linha['tem_id_cbx'] && $linha['requer_consulta'])
            enxadristasConsulta[{{ $k++ }}] = {{ $linha['enxadrista_id'] }};
        @endif
        enxadristas[{{ $j++ }}] = {{ $linha['enxadrista_id'] }};
    @endforeach

    var callUrlBase = '{{ url("/evento/" . $evento->id . "/relatorios/anuidade-cbx/call") }}';
    var comprovanteUrlBase = '{{ url("/evento/" . $evento->id . "/relatorios/anuidade-cbx/comprovante") }}';
    var badgeClasses = {
        pago: 'anuidade-pago',
        pendente: 'anuidade-pendente',
        sem_id: 'anuidade-sem-id',
        aguardando: 'anuidade-aguardando',
        erro: 'anuidade-erro'
    };

    function statusPermiteComprovante(status) {
        return status === 'pendente' || status === 'erro';
    }

    function atualizarComprovante(enxadristaId, status, recebido) {
        var $cell = $('#linha_' + enxadristaId).find('.col-comprovante');
        var checked = !!recebido;

        if (!statusPermiteComprovante(status)) {
            $cell.empty();
            return;
        }

        if ($cell.find('.anuidade-comprovante-check').length === 0) {
            $cell.html(
                '<label class="anuidade-comprovante-label" title="Comprovante de pagamento recebido">' +
                    '<input type="checkbox" class="anuidade-comprovante-check" ' +
                    'id="comprovante_' + enxadristaId + '" ' +
                    'data-enxadrista-id="' + enxadristaId + '"> Recebido' +
                '</label>'
            );
        }

        $cell.find('.anuidade-comprovante-check')
            .prop('checked', checked)
            .prop('disabled', false);
    }

    function atualizarLinha(enxadristaId, data) {
        var $row = $('#linha_' + enxadristaId);
        var status = data.status || 'erro';
        var label = data.label || 'Erro';
        var classe = badgeClasses[status] || badgeClasses.erro;

        $row.find('.col-data-pagto').text(data.data_pagto || '-');
        $row.find('.col-status').html(
            '<span class="anuidade-badge ' + classe + '" title="' + (data.detalhe || '') + '">' + label + '</span>'
        );
        $row.attr('data-status-ordenacao', statusOrdenacao(status));
        atualizarComprovante(enxadristaId, status, data.comprovante_recebido);
    }

    function statusOrdenacao(status) {
        switch (status) {
            case 'pendente':
            case 'erro':
                return 0;
            case 'aguardando':
                return 1;
            case 'pago':
                return 2;
            case 'sem_id':
                return 3;
            default:
                return 4;
        }
    }

    function marcarIcone(enxadristaId, sucesso) {
        var $icon = $('#enxadrista_' + enxadristaId + '_icon');
        $icon.removeClass('fa-spinner');
        $icon.addClass(sucesso ? 'fa-check' : 'fa-times');
        if (!sucesso) {
            erro = true;
        }
    }

    function finalizar() {
        $('#processo_aguarde').hide(400);
        if (!erro) {
            $('#processo_sucesso').show(400);
        } else {
            $('#processo_erro').show(400);
        }

        if (!dataTableInicializado && $('#tabela').length) {
            dataTableInicializado = true;
            $('#tabela').DataTable({
                responsive: true,
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                order: [[0, 'asc']],
                columnDefs: [
                    { targets: 0, visible: false }
                ],
                paging: false
            });
        }
    }

    function proximoConsulta(i) {
        if (i >= enxadristasConsulta.length) {
            finalizar();
            return;
        }

        var enxadristaId = enxadristasConsulta[i];
        $('#enxadrista_' + enxadristaId + '_icon').show(200);

        $.getJSON(callUrlBase + '/' + enxadristaId, function(data) {
            atualizarLinha(enxadristaId, data);
            marcarIcone(enxadristaId, data.ok == 1);
            setTimeout(function() {
                proximoConsulta(i + 1);
            }, 400);
        }).fail(function() {
            atualizarLinha(enxadristaId, {
                status: 'erro',
                label: 'Erro',
                data_pagto: '-',
                detalhe: 'Falha na requisição.'
            });
            marcarIcone(enxadristaId, false);
            setTimeout(function() {
                proximoConsulta(i + 1);
            }, 400);
        });
    }

    function start() {
        if (enxadristasConsulta.length === 0) {
            finalizar();
            return;
        }
        proximoConsulta(0);
    }

    $(document).ready(function() {
        $(document).on('change', '.anuidade-comprovante-check', function() {
            var $input = $(this);
            var enxadristaId = $input.data('enxadrista-id');
            var previousChecked = !$input.prop('checked');
            var isChecked = $input.prop('checked');

            $input.prop('disabled', true);

            $.ajax({
                url: comprovanteUrlBase + '/' + enxadristaId,
                type: 'GET',
                dataType: 'json',
                data: { enabled: isChecked ? 1 : 0 },
                success: function(response) {
                    if (response.ok) {
                        $input.prop('checked', !!response.enabled);
                    } else {
                        $input.prop('checked', previousChecked);
                        if (response.message) {
                            alert(response.message);
                        }
                    }
                },
                error: function() {
                    $input.prop('checked', previousChecked);
                    alert('Falha ao salvar o comprovante.');
                },
                complete: function() {
                    $input.prop('disabled', false);
                }
            });
        });

        setTimeout(function() {
            start();
        }, 1000);
    });
</script>
@endpush

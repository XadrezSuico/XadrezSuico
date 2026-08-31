<script type="text/javascript" src="{{ url('/js/jquery.mask.min.js') }}"></script>
<script type="text/javascript" src="{{ url('/vendor/bower/ckeditor/ckeditor.js') }}"></script>
@foreach([
    'https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js',
    'https://cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js',
    'https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js',
    'https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js',
] as $scriptUrl)
    <script type="text/javascript" src="{{ $scriptUrl }}"></script>
@endforeach
<script type="text/javascript">
$(document).ready(function () {
    if (typeof CKEDITOR !== 'undefined' && document.getElementById('texto')) {
        CKEDITOR.replace('texto');
    }
    if (typeof CKEDITOR !== 'undefined' && document.getElementById('orientacao_pos_inscricao')) {
        CKEDITOR.replace('orientacao_pos_inscricao');
    }

    var select2Fields = [
        '#torneio_template_id', '#tipo_modalidade', '#layout_version', '#exportacao_sm_modelo',
        '#categoria_id', '#category_xadrezsuicopag_uuid', '#criterio_desempate_id',
        '#criterio_desempate_geral_id', '#tipo_torneio_id', '#tipo_ratings_id',
        '#evento_classificador_id', '#grupo_evento_classificador_id', '#estados_id', '#cidade_id',
    ];
    select2Fields.forEach(function (selector) {
        if ($(selector).length) {
            $(selector).select2();
        }
    });

    if ($('#modalNovoTorneio').length) {
        var $modalNovoTorneio = $('#modalNovoTorneio');
        if (!$modalNovoTorneio.parent().is('body')) {
            $modalNovoTorneio.appendTo('body');
        }
        var novoTorneioSelect2Fields = ['#novo_torneio_tipo_torneio_id', '#novo_torneio_softwares_id'];

        function initNovoTorneioSelect2() {
            novoTorneioSelect2Fields.forEach(function (selector) {
                var $field = $(selector);
                if (!$field.length) {
                    return;
                }
                if ($field.hasClass('select2-hidden-accessible')) {
                    $field.select2('destroy');
                }
                $field.select2({
                    dropdownParent: $modalNovoTorneio,
                    width: '100%',
                });
            });
        }

        $modalNovoTorneio.on('shown.bs.modal', function () {
            initNovoTorneioSelect2();
            $('#novo_torneio_name').trigger('focus');
        });
        $modalNovoTorneio.on('hidden.bs.modal', function () {
            novoTorneioSelect2Fields.forEach(function (selector) {
                var $field = $(selector);
                if ($field.hasClass('select2-hidden-accessible')) {
                    $field.select2('destroy');
                }
                $field.val('').trigger('change');
            });
            $('#novo_torneio_name').val('');
        });
    }

    if ($('#tipo_modalidade').length) {
        $('#tipo_modalidade').val([{{ $evento->tipo_modalidade }}]).change();
    }
    if ($('#exportacao_sm_modelo').length) {
        $('#exportacao_sm_modelo').val([{{ $evento->exportacao_sm_modelo }}]).change();
    }
    if ($('#layout_version').length) {
        $('#layout_version').val([{{ $evento->layout_version }}]).change();
    }
    @if($evento->tipo_rating)
        if ($('#tipo_ratings_id').length) {
            $('#tipo_ratings_id').val([{{ $evento->tipo_rating->tipo_ratings_id }}]).change();
        }
    @endif

    if ($('.pais_select2').length) {
        $('.pais_select2').select2({
            ajax: {
                url: '{{ url('/api/v1/location/country/select2') }}',
                delay: 250,
                processResults: function (data) {
                    return { results: data.results };
                }
            }
        });
    }

    if ($('#pais_id').length) {
        $('#pais_id').on('select2:select', function () {
            if (typeof Loading !== 'undefined') {
                Loading.enable(loading_default_animation, 10000);
            }
            buscaEstados(false, function () {
                if (typeof Loading !== 'undefined') {
                    Loading.destroy();
                }
            });
        });
    }

    if ($('#estados_id').length) {
        $('#estados_id').on('select2:select', function () {
            if (typeof Loading !== 'undefined') {
                Loading.enable(loading_default_animation, 10000);
            }
            buscaCidades(function () {
                if (typeof Loading !== 'undefined') {
                    Loading.destroy();
                }
            });
        });
    }

    @if($evento->cidade && $evento->cidade->estado && $evento->cidade->estado->pais)
        if (typeof Loading !== 'undefined') {
            Loading.enable(loading_default_animation, 10000);
        }
        var newOptionPais = new Option(
            "{{ $evento->cidade->estado->pais->nome }} ({{ $evento->cidade->estado->pais->codigo_iso }})",
            "{{ $evento->cidade->estado->pais->id }}",
            false,
            false
        );
        $('#pais_id').append(newOptionPais).trigger('change');
        $('#pais_id').val({{ $evento->cidade->estado->pais->id }}).change();
        buscaEstados(false, function () {
            setTimeout(function () {
                $('#estados_id').val({{ $evento->cidade->estado->id }}).change();
                setTimeout(function () {
                    buscaCidades(function () {
                        $('#cidade_id').val({{ $evento->cidade_id }}).change();
                        if (typeof Loading !== 'undefined') {
                            Loading.destroy();
                        }
                    });
                }, 200);
            }, 200);
        });
    @endif

    @if($evento->classificador)
        if ($('#evento_classificador_id').length) {
            $('#evento_classificador_id').val([{{ $evento->classificador->id }}]).change();
        }
    @endif
    @if($evento->grupo_evento_classificador)
        if ($('#grupo_evento_classificador_id').length) {
            $('#grupo_evento_classificador_id').val([{{ $evento->grupo_evento_classificador->id }}]).change();
        }
    @endif

    if ($('#tabela_categoria').length) {
        $('#tabela_categoria').DataTable({ responsive: true });
    }
    if ($('#tabela_criterio_desempate_geral').length) {
        $('#tabela_criterio_desempate_geral').DataTable({ responsive: true, ordering: false });
    }
    if ($('#tabela_pontuacao').length) {
        $('#tabela_pontuacao').DataTable({ responsive: true, ordering: false });
    }

    setTimeout(function () {
        $('.select2').css('width', '100%');
    }, 1000);

    var maskFields = {
        '#evento_data_inicio': '00/00/0000',
        '#evento_data_fim': '00/00/0000',
        '#date_start_registration': '00/00/0000 00:00',
        '#evento_data_limite_inscricoes_abertas': '00/00/0000 00:00',
        '#confirmacao_publica_inicio': '00/00/0000 00:00',
        '#confirmacao_publica_final': '00/00/0000 00:00',
    };
    Object.keys(maskFields).forEach(function (selector) {
        if ($(selector).length && typeof $.fn.mask === 'function') {
            $(selector).mask(maskFields[selector]);
        }
    });
    if (typeof $.fn.mask === 'function') {
        $('.timeline-datetime').each(function () {
            $(this).mask('00/00/0000 00:00');
        });
    }

    function eventToggleOk(response) {
        return response.ok === true || response.ok === 1 || response.ok === '1';
    }

    function applyEventToggleSwitch($input, response, previousChecked) {
        if (eventToggleOk(response)) {
            if (typeof response.enabled !== 'undefined') {
                $input.prop('checked', !!response.enabled);
            }
            return true;
        }
        $input.prop('checked', previousChecked);
        return false;
    }

    function bindEventToggleSwitch(selector, url, options) {
        options = options || {};
        $(selector).change(function () {
            var $input = $(this);
            var previousChecked = !$input.prop('checked');
            var isChecked = $input.prop('checked');

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                data: { enabled: isChecked ? 1 : 0 },
                success: function (response) {
                    if (applyEventToggleSwitch($input, response, previousChecked)) {
                        if (options.onEnabled && typeof response.enabled !== 'undefined') {
                            options.onEnabled(!!response.enabled);
                        }
                        Swal.fire({
                            icon: 'success',
                            title: response.message || options.successMessage || 'Atualizado com sucesso!',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: response.message || options.errorMessage || 'Erro ao atualizar',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    }
                },
                error: function (xhr) {
                    $input.prop('checked', previousChecked);
                    var msg = options.errorMessage || 'Erro ao comunicar com o servidor';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: msg,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                }
            });
        });
    }

    bindEventToggleSwitch('#toggle_inscricoes', "{{ url('/evento/'.$evento->id.'/toggleinscricoes') }}", {
        successMessage: 'Status das inscrições atualizado com sucesso!'
    });
    bindEventToggleSwitch('#toggle_edicao_inscricao', "{{ url('/evento/'.$evento->id.'/toggleedicaoinscricao') }}", {
        successMessage: 'Status da edição de inscrição atualizado com sucesso!'
    });
    bindEventToggleSwitch('#toggle_classificavel', "{{ url('/evento/'.$evento->id.'/toggleclassificavel') }}");
    bindEventToggleSwitch('#toggle_resultados_automaticos', "{{ url('/evento/'.$evento->id.'/togglemanual') }}");
    bindEventToggleSwitch('#toggle_rating', "{{ url('/evento/'.$evento->id.'/togglerating') }}", {
        successMessage: 'Status do cálculo de rating atualizado com sucesso!',
        onEnabled: function (enabled) {
            if (enabled) {
                $('#toggle_rating_status_container').show();
            } else {
                $('#toggle_rating_status_container').hide();
            }
        }
    });
    bindEventToggleSwitch('#toggle_resultados', "{{ url('/evento/'.$evento->id.'/toggleresultados') }}");
});

function buscaEstados(buscaCidade, callback) {
    $('#estados_id').html('').trigger('change');
    $.getJSON('{{ url('/estado/search') }}/'.concat($('#pais_id').val()), function (data) {
        for (var i = 0; i < data.results.length; i++) {
            var newOptionEstado = new Option('#'.concat(data.results[i].id).concat(' - ').concat(data.results[i].text), data.results[i].id, false, false);
            $('#estados_id').append(newOptionEstado).trigger('change');
            if (i + 1 === data.results.length) {
                if (callback) { callback(); }
                if (buscaCidade) { buscaCidades(false); }
            }
        }
        if (data.results.length === 0) {
            if (callback) { callback(); }
            if (buscaCidade) { buscaCidades(false); }
        }
    });
}

function buscaCidades(callback) {
    $('#cidade_id').html('').trigger('change');
    $.getJSON('{{ url('/cidade/search') }}/'.concat($('#estados_id').val()), function (data) {
        for (var i = 0; i < data.results.length; i++) {
            var newOptionCidade = new Option('#'.concat(data.results[i].id).concat(' - ').concat(data.results[i].text), data.results[i].id, false, false);
            $('#cidade_id').append(newOptionCidade).trigger('change');
            if (i + 1 === data.results.length && callback) {
                callback();
            }
        }
        if (data.results.length === 0 && callback) {
            callback();
        }
    });
}
</script>

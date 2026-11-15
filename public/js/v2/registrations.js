(function ($) {
    'use strict';
    window.initV2TableTooltips = function () {
        if ($.fn.tooltip) {
            $('body').tooltip({selector: '.v2-icon-action, .v2-export-action', placement: 'top', container: 'body', trigger: 'hover focus',
                template: '<div class="tooltip v2-registration-tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'});
        }
    };
    window.initV2RegistrationsTable = function (selector, options) {
        var buttons = [
            {extend: 'copy', text: '<i class="fa fa-copy" aria-hidden="true"></i> Copiar'},
            {extend: 'csv', text: '<i class="fa fa-file-text-o" aria-hidden="true"></i> CSV'},
            {extend: 'excel', text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel'},
            {extend: 'pdf', text: '<i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF', orientation: 'landscape', pageSize: 'A3'},
            {extend: 'print', text: '<i class="fa fa-print" aria-hidden="true"></i> Imprimir'}
        ];
        var table = $(selector).DataTable($.extend({
            responsive: false,
            scrollX: true,
            autoWidth: false,
            dom: '<"v2-registration-toolbar"Bf>rt<"v2-registration-footer"ip>',
            buttons: buttons,
            language: {
                search: 'Buscar:', zeroRecords: 'Nenhuma inscrição encontrada',
                info: '_START_ a _END_ de _TOTAL_ inscrições', infoEmpty: 'Nenhuma inscrição',
                infoFiltered: '(filtrado de _MAX_)', emptyTable: 'Nenhuma inscrição cadastrada',
                paginate: {previous: 'Anterior', next: 'Próxima'},
                buttons: {copyTitle: 'Copiado', copySuccess: {1: '1 linha copiada', _: '%d linhas copiadas'}}
            }
        }, options || {}));
        var exportLabels = ['Copiar lista', 'Exportar CSV', 'Exportar Excel', 'Exportar PDF', 'Imprimir lista'];
        var exportGroup = $(table.buttons().container());
        exportGroup.addClass('input-group v2-export-actions').attr({role: 'group', 'aria-label': 'Exportar inscrições'});
        exportGroup.find('button').each(function (index) {
            $(this).addClass('v2-export-action').attr({title: exportLabels[index], 'aria-label': exportLabels[index], 'data-placement': 'top'});
        });
        window.initV2TableTooltips();
        return table;
    };
})(jQuery);

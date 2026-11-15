(function ($) {
    'use strict';
    window.initV2DashboardManagedTable = function (selector, options) {
        if ($.fn.dataTable.isDataTable(selector)) return $(selector).DataTable();
        var hasActions = $(selector).find('thead th.v2-reg-options').length > 0;
        $(selector).attr('data-has-actions', hasActions ? 'true' : 'false');
        var buttons = [
            {extend: 'copy', text: '<i class="fa fa-copy" aria-hidden="true"></i> Copiar'},
            {extend: 'csv', text: '<i class="fa fa-file-text-o" aria-hidden="true"></i> CSV'},
            {extend: 'excel', text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel'},
            {extend: 'pdf', text: '<i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF', orientation: 'landscape', pageSize: 'A3'},
            {extend: 'print', text: '<i class="fa fa-print" aria-hidden="true"></i> Imprimir'}
        ].map(function (button) {
            button.exportOptions = {columns: ':not(.v2-reg-options)'};
            return button;
        });
        var table = window.initV2RegistrationsTable(selector, $.extend(true, {
            ordering: $(selector).attr('data-ordering') !== 'false',
            paging: $(selector).attr('data-paging') !== 'false',
            columnDefs: hasActions ? [{targets: -1, orderable: false, searchable: false}] : [],
            buttons: buttons,
            language: {
                search: 'Buscar:', zeroRecords: 'Nenhum registro encontrado',
                info: '_START_ a _END_ de _TOTAL_ registros', infoEmpty: 'Nenhum registro',
                infoFiltered: '(filtrado de _MAX_)', emptyTable: 'Nenhum registro cadastrado',
                paginate: {previous: 'Anterior', next: 'Próxima'},
                buttons: {copyTitle: 'Copiado', copySuccess: {1: '1 linha copiada', _: '%d linhas copiadas'}}
            }
        }, options || {}));
        $(table.buttons().container()).attr('aria-label', 'Exportar tabela');
        return table;
    };
    $(function () {
        $('.v2-dashboard-tables[data-enhance-tables] table').not('.v2-registration-table').each(function (index) {
            var $table = $(this);
            if (!$table.find('thead th').length) return;
            $table.removeClass('table-responsive table-condensed').addClass('table v2-registration-table v2-dashboard-table');
            if (!this.id || document.querySelectorAll('[id="' + this.id + '"]').length > 1) {
                this.id = 'v2-dashboard-table-' + index;
            }
            if (!$table.parent().closest('.v2-registration-scroll, .v2-table-scroll, .table-responsive, .overflow-x-auto').length) {
                $table.wrap('<div class="v2-registration-scroll"></div>');
            }
            $table.attr('data-has-actions', 'false');
            var heading = $table.find('thead tr').first().children().last();
            if (/^(opções|ações)$/i.test(heading.text().trim()) || (!heading.text().trim() && $table.find('tbody td:last-child a.btn, tbody td:last-child button.btn').length)) {
                heading.addClass('v2-reg-options');
                $table.attr('data-has-actions', 'true');
                $table.find('tbody tr').each(function () {
                    if ($(this).children().length !== $table.find('thead tr').last().children().length) return;
                    var cell = $(this).children().last().addClass('v2-reg-options');
                    var actions = cell.find('a.btn, button.btn');
                    if (!actions.length) return;
                    cell.wrapInner('<div class="input-group v2-registration-actions" role="group" aria-label="Ações"></div>');
                    actions.each(function () {
                        var action = $(this);
                        var label = action.attr('title') || action.text().trim() || 'Abrir';
                        var danger = action.hasClass('btn-danger');
                        if (label === 'Abrir' && danger) label = 'Remover';
                        var warning = action.hasClass('btn-warning');
                        var success = action.hasClass('btn-success');
                        var icon = action.find('i.fa').first().clone().attr('aria-hidden', 'true');
                        if (!icon.length) {
                            var kind = danger ? 'trash' : /inscri/i.test(label) ? 'user-plus' : /divulgação/i.test(label) ? 'external-link' : /dashboard|gerenciar/i.test(label) ? 'dashboard' : 'pencil';
                            icon = $('<i aria-hidden="true"></i>').addClass('fa fa-' + kind);
                        }
                        action.removeClass('btn btn-default btn-primary btn-success btn-danger btn-warning btn-info btn-sm btn-xs mr-1')
                            .addClass('v2-icon-action').toggleClass('v2-icon-action--danger', danger)
                            .toggleClass('v2-icon-action--warning', warning).toggleClass('v2-icon-action--success', success)
                            .attr({title: label, 'aria-label': label, 'data-placement': 'top'}).empty().append(icon);
                    });
                });
            }
            if ($table.find('tbody [colspan], thead [colspan], tbody input, tbody select, tbody textarea').length) {
                $table.attr('data-table-static', 'true');
            }
            var firstLabel = $table.find('thead th').first().text().trim();
            if (firstLabel !== '#') {
                $table.addClass('v2-dashboard-table--text-first');
                if (!$table.is('[data-dashboard-managed]')) $table.attr({'data-paging': 'false', 'data-ordering': 'false'});
            }
            if ($table.hasClass('v2-criterio-group__table') || /criterio_desempate|pontuacao/.test(this.id)) {
                $table.attr('data-ordering', 'false');
            }
        });
        $('.v2-dashboard-tables .v2-dashboard-table').not('[data-dashboard-managed], [data-table-static]').each(function () {
            window.initV2DashboardManagedTable(this);
        });
        window.initV2TableTooltips();
        function adjustVisibleTables() {
            $.fn.dataTable.tables({visible: true, api: true}).columns.adjust();
        }
        $('.v2-dashboard-tables a[data-toggle="tab"]').on('shown.bs.tab', adjustVisibleTables);
        $(window).on('resize.dashboardTables', adjustVisibleTables);
        adjustVisibleTables();
    });
})(jQuery);

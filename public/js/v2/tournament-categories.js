(function ($) {
    'use strict';
    window.initTournamentCategorySelection = function (table) {
        var root = $('.js-tournament-categories');
        var selected = new Set();
        var submitting = false;
        function filtered() { return $(table.rows({search: 'applied'}).nodes()).find('.js-select-category'); }
        function sync() {
            $(table.rows().nodes()).find('.js-select-category').each(function () { this.checked = selected.has(this.value); });
            var rows = filtered(), checked = rows.filter(function () { return selected.has(this.value); }).length;
            root.find('.js-select-all-categories').prop('checked', rows.length > 0 && checked === rows.length).prop('indeterminate', checked > 0 && checked < rows.length);
            root.find('.js-category-count').text(selected.size + ' selecionada(s)');
            root.find('.js-transfer-categories, .js-delete-categories, .js-clear-category-selection').prop('disabled', selected.size === 0 || submitting);
        }
        function submit(action, ids) {
            if (submitting || !ids.length) return;
            var form = root.find('.js-category-batch-form');
            form.find('input:not([name="_token"])').remove();
            function field(name, value) { $('<input>', {type: 'hidden', name: name, value: value}).appendTo(form); }
            field('action', action);
            ids.forEach(function (id) { field('category_ids[]', id); });
            if (action === 'delete') field('confirmation', 'delete_categories');
            else field('tournament_id', root.find('.js-category-target').val());
            submitting = true;
            sync();
            form[0].submit();
        }
        function confirmDelete(ids, name) {
            if (submitting || !ids.length) return;
            Swal.fire({
                title: ids.length === 1 ? 'Excluir categoria deste torneio?' : 'Excluir ' + ids.length + ' categorias deste torneio?',
                text: (name ? name + '. ' : '') + 'Os vínculos selecionados serão removidos. As inscrições existentes não serão excluídas.',
                icon: 'warning', showCancelButton: true, confirmButtonText: 'Sim, excluir', cancelButtonText: 'Cancelar',
                confirmButtonColor: '#dc2626', focusCancel: true
            }).then(function (result) { if (result.isConfirmed) submit('delete', ids); });
        }
        root.on('change', '.js-select-category', function () { if (this.checked) selected.add(this.value); else selected.delete(this.value); sync(); });
        root.on('change', '.js-select-all-categories', function () { var checked = this.checked; filtered().each(function () { if (checked) selected.add(this.value); else selected.delete(this.value); }); sync(); });
        root.on('click', '.js-clear-category-selection', function () { selected.clear(); sync(); });
        root.on('click', '.js-delete-categories', function () { confirmDelete(Array.from(selected)); });
        root.on('click', '.js-remove-category', function () { confirmDelete([String($(this).attr('data-category-link'))], $(this).attr('data-category-name')); });
        root.on('click', '.js-transfer-categories', function () {
            if (!root.find('.js-category-target').val()) { Swal.fire({icon: 'info', title: 'Selecione o torneio de destino', text: 'Escolha para onde as categorias e suas inscrições serão migradas.'}); return; }
            submit('transfer', Array.from(selected));
        });
        table.on('draw.dt search.dt', sync);
        sync();
    };
})(jQuery);

(function ($) {
    'use strict';
    window.enhanceV2CatalogActions = function () {
        $('.v2-catalog table tbody td:last-child a.btn').each(function () {
            var link = $(this), href = link.attr('href') || '';
            var label = link.attr('aria-label') || link.text().trim() || (/\/delete\//.test(href) ? 'Excluir' : 'Editar');
            var icon = /\/delete\//.test(href) ? 'trash' : (/\/union\//.test(href) ? 'exchange' : 'pencil');
            link.addClass('v2-icon-action').attr({'title': label, 'aria-label': label, 'data-placement': 'top', 'data-original-title': label}).html('<i class="fa fa-' + icon + '" aria-hidden="true"></i>');
        });
        if (window.initV2TableTooltips) window.initV2TableTooltips();
    };
    $(function () {
        window.enhanceV2CatalogActions();
        $('.v2-catalog').on('click', 'a[href*="/delete/"]', function (event) {
            event.preventDefault();
            var href = this.href;
            Swal.fire({icon: 'warning', title: 'Excluir este registro?', text: 'Confirme para continuar com a exclusão.', showCancelButton: true, confirmButtonText: 'Sim, excluir', cancelButtonText: 'Cancelar', focusCancel: true, confirmButtonColor: '#dc2626'})
                .then(function (result) { if (result.isConfirmed) window.location.assign(href); });
        });
        $('.v2-catalog-nav a').each(function () {
            $(this).prepend('<i class="fa fa-' + (/\/new(?:$|\?)/.test(this.href) ? 'plus' : 'arrow-left') + '" aria-hidden="true"></i> ');
        });
    });
})(jQuery);

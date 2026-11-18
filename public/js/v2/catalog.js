(function ($) {
    'use strict';
    window.enhanceV2CatalogActions = function () {
        $('.v2-catalog table tbody td:last-child a.btn').each(function () {
            var link = $(this), href = link.attr('href') || '';
            var label = link.attr('aria-label') || link.text().trim() || (/\/delete\//.test(href) ? 'Excluir' : 'Editar');
            var icon = (/\/delete\//.test(href) || link.is('[data-confirm-delete]')) ? 'trash' : (/\/union\//.test(href) ? 'exchange' : 'pencil');
            link.addClass('v2-icon-action').attr({'title': label, 'aria-label': label, 'data-placement': 'top', 'data-original-title': label}).html('<i class="fa fa-' + icon + '" aria-hidden="true"></i>');
        });
        if (window.initV2TableTooltips) window.initV2TableTooltips();
    };
    $(function () {
        window.enhanceV2CatalogActions();
        $('.v2-catalog').on('click', 'a[href*="/delete/"], a[data-confirm-delete]', function (event) {
            event.preventDefault();
            var href = this.href, isProfile = $(this).is('[data-confirm-delete]');
            Swal.fire({icon: 'warning', title: isProfile ? 'Remover este perfil do usuário?' : 'Excluir este registro?', text: isProfile ? 'O acesso concedido por este vínculo será removido.' : 'Confirme para continuar com a exclusão.', showCancelButton: true, confirmButtonText: isProfile ? 'Sim, remover' : 'Sim, excluir', cancelButtonText: 'Cancelar', focusCancel: true, confirmButtonColor: '#dc2626'})
                .then(function (result) { if (result.isConfirmed) window.location.assign(href); });
        });
        $('.v2-catalog-nav a').each(function () {
            $(this).prepend('<i class="fa fa-' + (/\/new(?:$|\?)/.test(this.href) ? 'plus' : (/\/password\//.test(this.href) ? 'key' : 'arrow-left')) + '" aria-hidden="true"></i> ');
        });
    });
})(jQuery);

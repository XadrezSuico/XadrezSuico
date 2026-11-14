(function () {
    'use strict';
    var root = document.getElementById('v2-app');
    if (!root) return;
    var sidebar = document.getElementById('v2-sidebar');
    var backdrop = document.getElementById('v2-sidebar-backdrop');
    var toggle = document.getElementById('v2-sidebar-toggle');
    var desktop = window.matchMedia('(min-width: 1024px)');
    var open = false;

    function syncSidebar() {
        if (!sidebar || !backdrop || !toggle) return;
        var mobileOpen = open && !desktop.matches;
        sidebar.classList.toggle('-translate-x-full', !mobileOpen);
        sidebar.inert = !desktop.matches && !mobileOpen;
        sidebar.setAttribute('aria-hidden', String(sidebar.inert));
        backdrop.classList.toggle('hidden', !mobileOpen);
        document.body.classList.toggle('v2-sidebar-open', mobileOpen);
        toggle.setAttribute('aria-expanded', String(mobileOpen));
        toggle.setAttribute('aria-label', mobileOpen ? 'Fechar menu' : 'Abrir menu');
    }
    function closeSidebar() { open = false; syncSidebar(); }
    if (toggle) toggle.addEventListener('click', function () {
        open = !open; syncSidebar();
        if (open && sidebar) {
            var link = sidebar.querySelector('a[href]');
            if (link) link.focus();
        }
    });
    if (backdrop) backdrop.addEventListener('click', closeSidebar);
    if (sidebar) sidebar.addEventListener('click', function (event) {
        if (event.target.closest('a[href]') && !desktop.matches) closeSidebar();
    });
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && open) { closeSidebar(); if (toggle) toggle.focus(); }
        if (event.key === 'Tab' && open && !desktop.matches && sidebar) {
            var focusable = Array.prototype.filter.call(sidebar.querySelectorAll('a[href], button, input, select, textarea, [tabindex="0"]'), function (el) {
                return !el.disabled && el.getClientRects().length > 0;
            });
            if (!focusable.length) return;
            var first = focusable[0], last = focusable[focusable.length - 1];
            if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
            if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
        }
    });
    function resizedSidebar() { open = false; syncSidebar(); }
    if (desktop.addEventListener) desktop.addEventListener('change', resizedSidebar);
    else desktop.addListener(resizedSidebar);
    syncSidebar();

    function wrapTables(container) {
        var tables = container.matches && container.matches('table') ? [container] : container.querySelectorAll('table');
        Array.prototype.forEach.call(tables, function (table) {
            if (!table.parentElement || table.parentElement.closest('.v2-registration-scroll, .v2-table-scroll, .table-responsive, .overflow-x-auto, .dataTables_scroll, .dtr-details')) return;
            var wrapper = document.createElement('div');
            wrapper.className = 'v2-table-scroll';
            wrapper.setAttribute('role', 'region');
            wrapper.setAttribute('aria-label', 'Tabela com rolagem horizontal');
            wrapper.tabIndex = 0;
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        });
    }
    var main = root.querySelector('main');
    if (main) {
        wrapTables(main);
        new MutationObserver(function (records) {
            records.forEach(function (record) {
                Array.prototype.forEach.call(record.addedNodes, function (node) {
                    if (node.nodeType === 1) wrapTables(node);
                });
            });
        }).observe(main, {childList: true, subtree: true});
    }
    var resizeTimer;
    function adjustTables() {
        var $ = window.jQuery;
        if (!$ || !$.fn.dataTable) return;
        var tables = $.fn.dataTable.tables({visible: true, api: true});
        tables.columns.adjust();
        if (tables.responsive && tables.responsive.recalc) tables.responsive.recalc();
    }
    window.addEventListener('resize', function () {
        clearTimeout(resizeTimer); resizeTimer = setTimeout(adjustTables, 150);
    });
    document.addEventListener('toggle', function (event) {
        if (event.target.tagName === 'DETAILS' && event.target.open) adjustTables();
    }, true);
    if (window.jQuery) window.jQuery(document).on('shown.bs.tab shown.bs.modal', adjustTables);
})();

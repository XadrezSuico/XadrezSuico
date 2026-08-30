<script src="{{ asset('vendor/adminlte/vendor/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/DataTables-1.10.18/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/Responsive-2.2.2/js/dataTables.responsive.min.js') }}"></script>

<script>
    (function () {
        var sidebar = document.getElementById('v2-sidebar');
        var backdrop = document.getElementById('v2-sidebar-backdrop');
        var toggle = document.getElementById('v2-sidebar-toggle');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            backdrop.classList.remove('hidden');
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            backdrop.classList.add('hidden');
        }

        if (toggle) {
            toggle.addEventListener('click', function () {
                if (sidebar.classList.contains('-translate-x-full')) {
                    openSidebar();
                } else {
                    closeSidebar();
                }
            });
        }

        if (backdrop) {
            backdrop.addEventListener('click', closeSidebar);
        }
    })();
</script>

@stack('scripts')
@yield('js')

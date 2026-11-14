<script src="{{ asset('vendor/adminlte/vendor/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/DataTables-1.10.18/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/Responsive-2.2.2/js/dataTables.responsive.min.js') }}"></script>

<script src="{{ asset('js/v2/responsive.js') }}?v={{ filemtime(public_path('js/v2/responsive.js')) }}"></script>

@stack('scripts')
@yield('js')

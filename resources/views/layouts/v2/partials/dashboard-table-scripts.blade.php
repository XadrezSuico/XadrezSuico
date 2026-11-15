@foreach([
    'https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js',
    'https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js',
    'https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js'
] as $tableScript)
<script src="{{ $tableScript }}"></script>
@endforeach
<script src="{{ asset('js/v2/registrations.js') }}?v={{ filemtime(public_path('js/v2/registrations.js')) }}"></script>
<script src="{{ asset('js/v2/dashboard-tables.js') }}?v={{ filemtime(public_path('js/v2/dashboard-tables.js')) }}"></script>

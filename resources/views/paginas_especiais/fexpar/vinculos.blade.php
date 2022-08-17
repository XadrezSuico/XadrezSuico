@extends('adminlte::page')

@section('title', "Vinculos Federativos para ".date("Y")." (Consulta em ".date("d/m/Y H:i:s")." - Link: ".url("/especiais/fexpar/vinculos").")")

@section('content_header')
    <h1>Vinculos Federativos para {{date("Y")}}</h1>
@stop

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div class="box">
        <div class="box-body">
            <p>Esta lista compreende os seguintes os enxadristas que atendem a todos os seguintes requisitos:</p>
            <ol>
                <li>Possuem cadastro com CPF e RG;</li>
                <li>Possuem vínculo de cidade em alguma cidade do Paraná em seu cadastro de Enxadrista;</li>
                <li>Possuem vínculo de clube em alguma entidade do Paraná em seu cadastro de Enxadrista;</li>
                <li>Jogou algum evento registrado pelo sistema XadrezSuíço entre 01 de Janeiro de {{date("Y")}} e 31 de Dezembro de {{date("Y")}}.</li>
            </ol>
            <p></p>
            <hr/>
            <table id="tabela" class="table-responsive table-condensed table-striped" style="width: 100%">
                <thead>
                    <tr>
                        <th># - ID FEXPAR</th>
                        <th>ID CBX</th>
                        <th>ID FIDE</th>
                        <th>Nome</th>
                        <th>Data de Nascimento</th>
                        <th>Cidade</th>
                        <th>Clube</th>
                        <th width="20%">Opções</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vinculos as $vinculo)
                        <tr>
                            <td>{{$vinculo->enxadrista->id}}</td>
                            <td>{{$vinculo->enxadrista->cbx_id}}</td>
                            <td>{{$vinculo->enxadrista->fide_id}}</td>
                            <td>{{$vinculo->enxadrista->name}}</td>
                            <td>{{$vinculo->enxadrista->getBorn()}}</td>
                            <td>{{$vinculo->enxadrista->cidade->name}}</td>
                            <td>{{$vinculo->enxadrista->clube->name}}</td>
                            <td>
                                <a class="btn btn-default" href="{{url("/especiais/fexpar/vinculos/".$vinculo->uuid)}}" role="button">Visualizar</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section("js")
@foreach(array(
    "https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js",
    "https://cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js",
    "https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js",
    "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js",
    "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js",
    "https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js",
    "https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"
    ) as $url)
<script type="text/javascript" src="{{$url}}"></script>
@endforeach
<script type="text/javascript">
    $(document).ready(function(){
        $("#tabela").DataTable({
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            pageLength: 50
        });
    });
</script>
@endsection

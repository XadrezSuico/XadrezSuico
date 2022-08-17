@extends('adminlte::page')

@section('title', "Gestão de Vínculos Federativos")

@section('content_header')
    <h1>Gestão de Vínculos Federativos</h1>
@stop

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div class="box">
        <div class="box-body">
            <p>Nesta tela você poderá</p>
            <hr/>
            <table id="tabela" class="table-responsive table-condensed table-striped" style="width: 100%">
                <thead>
                    <tr>
                        <th># - ID FEXPAR</th>
                        <th>Nome</th>
                        <th>Data de Nascimento</th>
                        <th>ID CBX</th>
                        <th>ID FIDE</th>
                        <th>Cidade</th>
                        <th>Clube</th>
                        <th>Tem vinculo para {{date("Y")}}?</th>
                        <th width="20%">Opções</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section("js")
<script type="text/javascript">
    $(document).ready(function(){
        $("#tabela").DataTable({
            processing: true,
            serverSide: true,
            searchDelay: 500,
            ajax: '{{url("/fexpar/vinculos/api/searchList/")}}',
            language: {
                "decimal":        "",
                "emptyTable":     "Não há dados na tabela",
                "info":           "Mostrando de _START_ para _END_ de um total de _TOTAL_ registros",
                "infoEmpty":      "Mostrando de 0 para 0 de um total de 0 registros",
                "infoFiltered":   "(filtrado de um total de _MAX_ registros)",
                "infoPostFix":    "",
                "thousands":      ",",
                "lengthMenu":     "Mostrar _MENU_",
                "loadingRecords": "Carregando...",
                "processing":     "Processando...",
                "search":         "Pesquisar:",
                "zeroRecords":    "Não foram encontrados registros seguindo o filtro",
                "paginate": {
                    "first":      "Primeiro",
                    "last":       "Último",
                    "next":       "Próximo",
                    "previous":   "Anterior"
                },
                "aria": {
                    "sortAscending":  ": ativar para organizar em ordem crescente da coluna",
                    "sortDescending": ": ativar para organizar em ordem descrescente da coluna"
                }
            }
        });
    });
</script>
@endsection

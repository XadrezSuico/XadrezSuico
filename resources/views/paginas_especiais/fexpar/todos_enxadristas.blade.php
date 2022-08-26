@extends('adminlte::page')

@section('title', "Enxadristas com Cadastro - FEXPAR")

@section('content_header')
    <h1>Enxadristas com Cadastro - FEXPAR</h1>
@stop

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div class="box">
        <div class="box-body">
            <p>Esta lista compreende todos os cadastros existentes no sistema XadrezSuíço, preservando algumas informações considerando a LGPD.</p>
            <p>Vale constar que esta lista pode possuir registros duplicados, visto que a obrigação do preenchimento correto dos dados do enxadrista é do responsável pela inscrição do mesmo.</p>
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
            responsive: true,
            pageLength: 50,
            processing: true,
            serverSide: true,
            searchDelay: 500,
            ajax: '{{url("/especiais/fexpar/todos_enxadristas/api/searchList")}}',
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

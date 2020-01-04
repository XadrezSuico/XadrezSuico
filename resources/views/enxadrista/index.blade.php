@extends('adminlte::page')

@php
        $permitido_edicao = false;
        if(
            \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
            \Illuminate\Support\Facades\Auth::user()->hasPermissionEventsByPerfil([4])
        ){
            $permitido_edicao = true;
        }
@endphp

@section('title', 'Enxadristas')

@section("css")
  <link rel="stylesheet" href="{{url("/plugins/datatables/dataTables.bootstrap.css")}}">
@endsection

@section('content_header')
    <h1>Enxadristas</h1>
@stop

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    @if($permitido_edicao)
        <ul class="nav nav-pills">
            <li role="presentation"><a href="{{url("/enxadrista/new")}}">Novo Enxadrista</a></li>
            
            @if(
                \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal()
            )
                <li role="presentation"><a href="{{url("/enxadrista/download")}}">Baixar Base de Dados (Apenas Administradores e Super-Administradores)</a></li>
            @endif
        </ul>
    @endif
    <div class="box">
        <div class="box-body">
            <table id="tabela" class="table-responsive table-condensed table-striped" style="width: 100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Data de Nascimento</th>
                        <th>Sexo</th>
                        <th>IDs</th>
                        <th>Cidade</th>
                        <th>Clube</th>
                        <th width="70px;">Opções</th>
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
    $(function () {
        $("#tabela").DataTable({
            processing: true,  
            serverSide: true, 
            searchDelay: 500,
            ajax: '{{url("/enxadrista/api/searchList/")}}',
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

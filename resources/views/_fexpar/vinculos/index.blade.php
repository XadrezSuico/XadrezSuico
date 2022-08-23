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
            <p>Nesta tela você poderá gerenciar os vínculos dos enxadristas.</p>
            <p>Existem dois tipos de vínculos:</p>
            <ul>
                <li>
                    <strong>Automático:</strong> É o vínculo identificado pelo XadrezSuíço, onde segue alguns critérios para tal.<br/>
                    O primeiro é que o clube deve estar vinculado a uma cidade do Paraná e estar habilitado para vínculo (Campo <strong>É clube válido para vinculo federativo?</strong> no cadastro de Clube).<br/>
                    Após isso, de madrugada é efetuado em duas etapas os vínculos: a Pré-vinculação e a Vinculação.
                    <ul>
                        <li>
                            <strong>Pré-vinculação:</strong> É o processo que verifica se o enxadrista está apto para vínculo. Nesta etapa verifica-se se o mesmo atende os seguintes requisitos:
                            <ol>
                                <li>Cadastro com CPF e RG;</li>
                                <li>Possuir vínculo de cidade em alguma cidade do Paraná em seu cadastro de Enxadrista;</li>
                                <li>Possuir vínculo de clube em alguma entidade do Paraná apta em seu cadastro de Enxadrista;</li>
                                <li>Ter participado (confirmado sem W.O.) em algum evento no ano de {{date("Y")}} gerenciado por completo no XadrezSuíço.</li>
                            </ol>
                            Os enxadristas que atendem estes requisitos, automaticamente recebem um pré-vinculo ao clube que está no cadastro de enxadrista. O qual poderá ser confirmado na noite subsequente.
                        </li>
                        <li>
                            <strong>Vinculação:</strong> É o processo que verifica se os enxadristas com pré-vinculo possuem o principal requisito para vínculo: Ter participado de ao menos um evento válido por este clube.<br/>
                            Este processo é efetuado diariamente durante a madrugada, e se houver uma inscrição do enxadrista (inscrição confirmada e sem W.O.) há a confirmação do vínculo, e assim a permissão do uso dele para esta entidade.
                        </li>
                    </ul>
                </li>
                <li>
                    <strong>Manual:</strong> Este é o processo que acontece através da ação de um usuário com permissão, onde pode alterar o vínculo automático fornecido ou então criar um vínculo para algum enxadrista ainda não vinculado.<br/>
                    Este processo serve também para vincular enxadristas que participaram em algum evento válido para vínculo não gerenciado pelo XadrezSuíço. É necessário que o usuário informe manualmente os nomes dos eventos que geraram este vínculo.
                </li>
            </ul>
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

@extends('adminlte::page')

@section("title", "Visualizar Premiados - Grupo de Evento: ".$grupo_evento->name." - Acesso em ".date("d-m-Y H:i:s"))

@section('content_header')
  <h1>Grupo de Evento: {{$grupo_evento->name}} >> Visualizar Premiados</h1>
@stop


@section('css')
	<style>
		.display-none, .displayNone{
			display: none;
		}

		.box-title.evento{
			font-size: 2.5rem;
			font-weight: bold;
		}
	</style>
@endsection

@section("content")

<!-- Main row -->
<ul class="nav nav-pills">
  <li role="presentation"><a href="/grupoevento/dashboard/{{$grupo_evento->id}}">Voltar ao Grupo de Evento</a></li>
</ul>
<div class="row">
  <!-- Left col -->
  <section class="col-lg-12 connectedSortable">
	<!-- general form elements -->
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title">Lista de Premiados</h3>
			<div class="pull-right box-tools">
			</div>
		</div>

		<div class="box-body">
			<table id="tabela" class="table-responsive table-condensed table-striped" style="width: 100%">
                <thead>
                    <tr>
                        <th>Categoria</th>
                        <th>Posição</th>
                        <th>Código Enxadrista</th>
                        <th>Nome do Enxadrista</th>
                        <th>Cidade</th>
                        <th>Clube</th>
                        <th>E-mail</th>
                        <th>Celular</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($grupo_evento->categorias->all() as $categoria)
                        @foreach($grupo_evento->pontuacoes_enxadrista()->where([["categoria_id",$categoria->id]])->orderBy("posicao","ASC")->limit($categoria->getHowManyStandingPlaces())->get() as $pontuacao_enxadrista)
                            <tr>
                                <td>{{$pontuacao_enxadrista->categoria->name}}</td>
                                <td>{{$pontuacao_enxadrista->posicao}}</td>
                                <td>{{$pontuacao_enxadrista->enxadrista->id}}</td>
                                <td>{{$pontuacao_enxadrista->enxadrista->getNomePrivado()}}</td>
                                <td>{{$pontuacao_enxadrista->enxadrista->cidade->getName()}}</td>
                                <td>@if($pontuacao_enxadrista->enxadrista->clube) {{$pontuacao_enxadrista->enxadrista->clube->name}} @else - @endif</td>
                                <td>{{$pontuacao_enxadrista->enxadrista->email}}</td>
                                <td>{{$pontuacao_enxadrista->enxadrista->celular}}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
		</div>
	</div>

  </section>
  <!-- /.Left col -->
</div>
<!-- /.row (main row) -->

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
            ordering: false,
            paging: false
        });
    });
</script>
@endsection

@extends('adminlte::page')

@section("title", "Visualizar Lista de Inscrições")

@section('content_header')
  <h1>Visualizar Lista de Inscrições</h1>
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
  <li role="presentation"><a href="/evento/dashboard/{{$evento->id}}">Voltar à Dashboard de Evento</a></li>
</ul>
<div class="row">
  <!-- Left col -->
  <section class="col-lg-12 connectedSortable">
	<!-- general form elements -->
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title evento">Evento: {{$evento->name}}</h3>
			<div class="pull-right box-tools">
			</div>
		</div>
	</div>
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title">Lista de Inscrições</h3>
			<div class="pull-right box-tools">
			</div>
		</div>

		<div class="box-body">
			<table id="tabela" class="table-responsive table-condensed table-striped" style="width: 100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Código Enxadrista</th>
                        <th>Nome do Enxadrista</th>
                        <th>Data de Nascimento</th>
                        <th>Categoria Inscrição</th>
                        <th>Cidade</th>
                        <th>Clube</th>
                        <th>Inscrição Inicial</th>
                        <th>Posição</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($evento->getInscricoes() as $inscricao)
                        <tr>
                            <td>{{$inscricao->id}}</td>
                            <td>{{$inscricao->enxadrista->id}}</td>
                            <td>
                                {{$inscricao->enxadrista->getNomePrivado()}}
                            </td>
                            <td>{{$inscricao->enxadrista->getNascimentoPrivado()}}</td>
                            <td>{{$inscricao->categoria->name}}</td>
                            <td>{{$inscricao->getCidade()}}</td>
                            <td>@if($inscricao->clube) {{$inscricao->clube->name}} @else - @endif</td>
                            <td>@if($inscricao->from) {{$inscricao->from->id}} @else - @endif</td>
                            <td>@if($inscricao->from) @if($inscricao->from->posicao) {{$inscricao->from->posicao}} @else - @endif @else - @endif</td>
                        </tr>
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

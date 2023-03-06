@extends('adminlte::page')

@section("title", "Visualizar Lista de Inscrições do Evento #".$evento->id." - ".$evento->name." [".Str::uuid()."]")

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
                        @if($evento->tipo_rating)
                            <th>Rating</th>
                            <th>Possui rating?</th>
                        @endif
                        @if($evento->isPaid())
                            <th>Pago?</th>
                        @endif
                        <th>Confirmado?</th>
                        <th>Inscrição Inicial</th>
                        <th>Posição</th>
				    	@foreach($evento->campos() as $campo)
                            <th>{{$campo->name}}</th>
                        @endforeach
                        @if($evento->classificador)
                            @foreach($evento->classificador->campos() as $campo)
                                <th>{{$campo->name}}</th>
                            @endforeach
                        @endif
                        @if($evento->is_lichess_integration)
                            <th>Link de Acesso</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($evento->getInscricoes() as $inscricao_ordered)
                        @php($inscricao = $evento->enxadristaInscrito($inscricao_ordered->enxadrista->id))
                        <tr>
                            <td>{{$inscricao->id}}</td>
                            <td>{{$inscricao->enxadrista->id}}</td>
                            <td>
                                {{$inscricao->enxadrista->getNomePrivado()}}
                            </td>
                            <td>{{$inscricao->enxadrista->getNascimentoPrivado()}}</td>
                            <td>{{$inscricao->categoria->id}} - {{$inscricao->categoria->name}}</td>
                            <td>{{$inscricao->getCidade()}}</td>
                            <td>@if($inscricao->clube) {{$inscricao->clube->name}} @else - @endif</td>
                            @if($evento->tipo_rating)
                                <td>{{$inscricao->enxadrista->ratingParaEvento($evento->id,true)}}</td>
                                <td>@if($inscricao->enxadrista->hasRatingParaEvento($evento->id)) Sim @endif</td>
                            @endif
                            @if($evento->isPaid())
                                <td>
                                    @if($inscricao->categoria->isPaid($evento->id))
                                        @if($inscricao->paid)
                                            Pago
                                        @else
                                            <strong>Pagamento Pendente</strong>
                                        @endif
                                    @else
                                        Categoria Gratuita.
                                    @endif
                                </td>
                            @endif
                            <td>
                                @if($inscricao->confirmado) Sim @else Não @endif
                            </th>
                            <td>@if($inscricao->from) {{$inscricao->from->id}} @else - @endif</td>
                            <td>@if($inscricao->from) @if($inscricao->from->posicao) {{$inscricao->from->posicao}} @else - @endif @else - @endif</td>
                            @foreach($evento->campos() as $campo)
                                @if($inscricao->hasOpcao($campo->id))
                                    <td>{{$inscricao->getOpcao($campo->id)->opcao->name}}</td>
                                @else
                                    <td>-</td>
                                @endif
                            @endforeach
                            @if($evento->classificador)
                                @foreach($evento->classificador->campos() as $campo)
                                    @if($inscricao->from->getOpcao($campo->id))
                                        <td>{{$inscricao->from->getOpcao($campo->id)->opcao->name}}</td>
                                    @else
                                        <td>-</td>
                                    @endif
                                @endforeach
                            @endif
                            @if($evento->is_lichess_integration)
                                <td>
                                    @if(!$inscricao->is_lichess_found)
                                        <a href="{{$inscricao->getLichessProcessLink()}}" class="btn btn-sm btn-success">
                                            <strong>Link para Inscrição no Torneio do Lichess.org - Para encaminhar para o enxadrista se inscrever.</strong>
                                        </a>
                                    @else
                                        <a href="{{$evento->getLichessTournamentLink()}}" class="btn btn-sm btn-warning">
                                            <strong>Já Está no Torneio</strong> - Link do Torneio
                                        </a>
                                    @endif
                                </td>
                            @endif
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

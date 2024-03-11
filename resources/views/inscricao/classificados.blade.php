@extends('adminlte::page')

@section("title", "Visualizar Classificados - Evento: ".$evento->name)

@section('content_header')
  <h1>Visualizar Classificados</h1>
  <h4>Evento: {{$evento->id}} - {{$evento->name}}</h4>
  <h4>Classificador: {{$xdzsc_classificador->id}} - Para Evento: {{$xdzsc_classificador->event->id}} - {{$xdzsc_classificador->event->name}}</h4>
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

		<div class="box-body">
			@if($evento->pagina)
				@if($evento->pagina->imagem) <div style="width: 100%; text-align: center;"><img src="data:image/png;base64, {!!$evento->pagina->imagem!!}" width="100%" style="max-width: 800px"/></div> <br/> @endif
				@if($evento->pagina->texto) {!!$evento->pagina->texto!!} <br/> @endif
				@if($evento->pagina->imagem || $evento->pagina->texto) <hr/> @endif
			@endif
			<strong>Categorias:</strong><br/>
			@foreach($evento->categorias->all() as $categoria)
				{{$categoria->categoria->name}},
			@endforeach<br/>
			<strong>Cidade:</strong> {{$evento->cidade->name}}<br/>
			<strong>Local:</strong> {{$evento->local}}<br/>
			<strong>Data:</strong>
            @if($evento->getDataInicio() == $evento->getDataFim())
                {{$evento->getDataInicio()}}
            @else
                {{$evento->getDataInicio()}} - {{$evento->getDataFim()}}
            @endif<br/>
			<strong>Maiores informações em:</strong> <a href="{{$evento->link}}" target="_blank">{{$evento->link}}</a><br/>
			@if($evento->maximo_inscricoes_evento)
				<hr/>
				<strong>Total de Inscritos até o presente momento:</strong> {{$evento->quantosInscritos()}}.<br/>
				<strong>Limite de Inscritos:</strong> {{$evento->maximo_inscricoes_evento}}.<br/>
				<hr/>
			@endif
			@if($evento->getDataFimInscricoesOnline()) <h3><strong>Inscrições antecipadas até:</strong> {{$evento->getDataFimInscricoesOnline()}}.</h3>@endif
            @if($evento->is_lichess_integration)
                Informações do Lichess.org são atualizadas a cada 6 horas.
            @endif
        </div>
	</div>
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title">Lista de Classificados</h3>
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
                        <th>Regra</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($xdzsc_classificador->getRegistrationsClassificated() as $inscricao)
                        <tr>
                            <td>{{$inscricao->categoria->name}}</td>
                            <td>{{$inscricao->posicao}}</td>
                            <td>{{$inscricao->enxadrista->id}}</td>
                            <td>{{$inscricao->enxadrista->getNomePrivado()}}</td>
                            <td>{{$inscricao->getCidade()}}</td>
                            <td>@if($inscricao->clube) {{$inscricao->clube->getName()}} @else - @endif</td>

                            <td>
                                @if($inscricao->hasConfig("event_classificator_rule_id"))
                                    @php($rule_that_classificated = \App\Classification\EventClassificateRule::where([["id", "=", $inscricao->getConfig("event_classificator_rule_id",true)]])->first())

                                    @php($classified_event_rule = \App\Enum\ClassificationTypeRule::get($rule_that_classificated->type)["name"])

                                    @if ($rule_that_classificated->value)
                                        {{$classified_event_rule}} - Valor: {{$rule_that_classificated->value}}
                                    @endif
                                    @if ($rule_that_classificated->event)
                                        {{$classified_event_rule}} - Evento: {{$rule_that_classificated->event->name}}
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
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

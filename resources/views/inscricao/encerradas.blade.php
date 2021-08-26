@extends('adminlte::page')

@section("title", "INSCRIÇÕES ANTECIPADAS FINALIZADAS!")

@section('content_header')
  <h1>INSCRIÇÕES ANTECIPADAS FINALIZADAS!</h1>
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
<div class="modal fade modal-danger" id="alerts" tabindex="-1" role="dialog" aria-labelledby="alerts">
  <div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">INSCRIÇÕES ANTECIPADAS FINALIZADAS!</h4>
        </div>
        <div class="modal-body">
        <span id="alertsMessage">{{env("MENSAGEM_FIM_INSCRICOES","O prazo para Inscrições Antecipadas para este evento se encerrou ou o limite de inscrições se completou. As mesmas podem ser feitas no local conforme regulamento.")}}</span>
        </div>
        <div class="modal-footer">
        <button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
			</div>
		</div>
  </div>
</div>
@if($evento->e_permite_visualizar_lista_inscritos_publica)
	<ul class="nav nav-pills">
		<li role="presentation"><a href="/inscricao/visualizar/{{$evento->id}}">Visualizar Lista de Inscrições</a></li>
	</ul>
@endif
<div class="row">
  <!-- Left col -->
  <section class="col-lg-12 connectedSortable">
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title evento">Evento: {{$evento->name}}</h3>
			<div class="pull-right box-tools">
			</div>
		</div>

		<div class="box-body">
			@if($evento->e_permite_visualizar_lista_inscritos_publica)
                <a href="{{url("/inscricao/visualizar/".$evento->id)}}" class="btn btn-lg btn-info btn-block">
                    Visualizar Lista de Inscrições
                </a><br/>
            @endif
            @if($evento->hasTorneiosEmparceiradosByXadrezSuico())
                <a href="{{url("/evento/acompanhar/".$evento->id)}}" class="btn btn-lg btn-primary btn-block">
                    Acompanhe o Emparceiramento do Torneio
                </a><br/>
            @endif
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
		</div>
	</div>
  </section>
  <!-- /.Left col -->
</div>
<!-- /.row (main row) -->

@endsection

@section("js")
<!-- Morris.js charts -->
<script type="text/javascript" src="{{url("/js/jquery.mask.min.js")}}"></script>
<script type="text/javascript">
  $(document).ready(function(){
	$("#alerts").modal();
  });
</script>
@endsection

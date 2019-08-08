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
        <span id="alertsMessage">{{env("MENSAGEM_FIM_INSCRICOES","O prazo para Inscrições Antecipadas para este evento se encerrou. As mesmas podem ser feitas no local conforme regulamento.")}}</span>
        </div>
        <div class="modal-footer">
        <button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
			</div>
		</div>
  </div>
</div>
<div class="row">
  <!-- Left col -->
  <section class="col-lg-12 connectedSortable">
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title">Evento: {{$evento->name}}</h3>
			<div class="pull-right box-tools">
			</div>
		</div>

		<div class="box-body">
			<strong>Categorias:</strong><br/>
			@foreach($evento->categorias->all() as $categoria)
				{{$categoria->categoria->name}}, 
			@endforeach<br/>
			<strong>Cidade:</strong> {{$evento->cidade->name}}<br/>
			<strong>Local:</strong> {{$evento->local}}<br/>
			<strong>Data:</strong> {{$evento->getDataInicio()}}<br/>
			<strong>Maiores informações em:</strong> <a href="{{$evento->link}}" target="_blank">{{$evento->link}}</a><br/>
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

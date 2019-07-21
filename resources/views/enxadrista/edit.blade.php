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

@section("title", "Editar Enxadrista")

@section('content_header')
  <h1>Editar Enxadrista</h1>
  <h3>Código: {{$enxadrista->id}}</h3>
@stop


@section('css')
	<style>
		.display-none, .displayNone{
			display: none;
		}
	</style>
@endsection

@section("content")
<!-- Main row -->
<ul class="nav nav-pills">
  <li role="presentation"><a href="/enxadrista">Voltar a Lista de Enxadristas</a></li>
  <li role="presentation"><a href="/enxadrista/new">Novo Enxadrista</a></li>
</ul>
<div class="row">
  <!-- Left col -->
  <section class="col-lg-12 connectedSortable">
	<div class="box box-primary" id="inscricao">
		<div class="box-header">
			<h3 class="box-title">Editar Clube</h3>
		</div>
			<!-- form start -->
			@if($permitido_edicao) <form method="post"> @endif
				<div class="box-body">
					<div class="form-group">
						<label for="name">Nome Completo *</label>
						<input name="name" id="name" class="form-control" type="text" value="{{$enxadrista->name}}" @if(!$permitido_edicao) disabled="disabled" @endif />
					</div>
					<div class="form-group">
						<label for="born">Data de Nascimento *</label>
						<input name="born" id="born" class="form-control" type="text" value="{{$enxadrista->getBorn()}}" @if(!$permitido_edicao) disabled="disabled" @endif />
					</div>
					<div class="form-group">
						<label for="sexos_id">Sexo *</label>
						<select id="sexos_id" name="sexos_id" class="form-control" @if(!$permitido_edicao) disabled="disabled" @endif>
							<option value="">--- Selecione ---</option>
							@foreach($sexos as $sexo)
								<option value="{{$sexo->id}}">{{$sexo->name}}</option>
							@endforeach
						</select>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="cbx_rating">Rating CBX</label>
								<input name="cbx_rating" id="cbx_rating" class="form-control" type="text" value="{{$enxadrista->cbx_rating}}" @if(!$permitido_edicao) disabled="disabled" @endif />
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="fide_rating">Rating FIDE</label>
								<input name="fide_rating" id="fide_rating" class="form-control" type="text" value="{{$enxadrista->fide_rating}}" @if(!$permitido_edicao) disabled="disabled" @endif />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="cbx_id">ID CBX</label>
								<input name="cbx_id" id="cbx_id" class="form-control" type="text" value="{{$enxadrista->cbx_id}}" @if(!$permitido_edicao) disabled="disabled" @endif />
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="fide_id">ID FIDE</label>
								<input name="fide_id" id="fide_id" class="form-control" type="text" value="{{$enxadrista->fide_id}}" @if(!$permitido_edicao) disabled="disabled" @endif />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="email">E-mail</label>
								<input name="email" id="email" class="form-control" type="text" value="{{$enxadrista->email}}" @if(!$permitido_edicao) disabled="disabled" @endif />
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="celular">Celular</label>
								<input name="celular" id="celular" class="form-control" type="text" value="{{$enxadrista->celular}}" @if(!$permitido_edicao) disabled="disabled" @endif />
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="cidade_id">Cidade *</label>
						<select id="cidade_id" name="cidade_id" class="form-control" @if(!$permitido_edicao) disabled="disabled" @endif>
							<option value="">--- Selecione uma cidade ---</option>
							@foreach($cidades as $cidade)
								<option value="{{$cidade->id}}">{{$cidade->id}} - {{$cidade->name}}</option>
							@endforeach
						</select>
					</div>
					<div class="form-group">
						<label for="clube_id">Clube</label>
						<select id="clube_id" name="clube_id" class="form-control" @if(!$permitido_edicao) disabled="disabled" @endif>
							<option value="">--- Você pode selecionar um clube ---</option>
							@foreach($clubes as $clube)
								<option value="{{$clube->id}}">{{$clube->cidade->name}} - {{$clube->name}}</option>
							@endforeach
						</select>
					</div>
				</div>
			<!-- /.box-body -->

			@if($permitido_edicao)
				<div class="box-footer">
					<button type="submit" class="btn btn-success">Enviar</button>
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
				</div>
      		 </form> 
			@endif
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
		$("#cidade_id").select2().val([{{$enxadrista->cidade_id}}]).change();
		$("#clube_id").select2().val([{{$enxadrista->clube_id}}]).change();
		$("#sexos_id").select2().val([{{$enxadrista->sexos_id}}]).change();
		$("#celular").mask("(00) 00000-0000");
  });
</script>
@endsection

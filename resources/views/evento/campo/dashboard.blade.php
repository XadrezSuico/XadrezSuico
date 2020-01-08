@extends('adminlte::page')

@section("title", "Evento #".$evento->id." (".$evento->name.") >> Dashboard de Campo Personalizado")

@section('content_header')
  <h1>Evento #{{$evento->id}} ({{$evento->name}}) >> Dashboard de Campo Personalizado</h1>
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
		<li role="presentation"><a href="/evento/dashboard/{{$evento->id}}?tab=campo_personalizado">Voltar a Lista de Campos Personalizados na Dashboard de Evento</a></li>
	</ul>
	<div class="row">
  <section class="col-lg-6 connectedSortable">

	
		<div class="box box-primary" id="inscricao">
			<div class="box-header">
				<h3 class="box-title">Editar Campo Personalizado</h3>
			</div>
			<!-- form start -->
					<form method="post">
				<div class="box-body">
					<div class="form-group">
						<label for="name">Nome *</label>
						<input name="name" id="name" class="form-control" type="text" value="{{$campo->name}}" />
					</div>
					<div class="form-group">
						<label for="question">Questão *</label>
						<input name="question" id="question" class="form-control" type="text" value="{{$campo->question}}" />
					</div>
					<div class="form-group">
						<label for="campo_type">Tipo de Campo *</label>
						<select name="type" id="campo_type" class="form-control width-100" @if(!$user->hasPermissionGlobal()) disabled="disabled" @endif>
							<option value="">--- Selecione um tipo de campo ---</option>
							<option value="select">Seleção</option>
						</select>
					</div>
					<div class="form-group">
						<label for="campo_validator">Validação</label>
						<select name="validator" id="campo_validator" class="form-control width-100" @if(!$user->hasPermissionGlobal()) disabled="disabled" @endif>
							<option value="">--- Você pode selecionar uma validação ---</option>
							<option value="cpf">CPF</option>
						</select>
					</div>
					<div class="form-group">
						<label><input type="checkbox" id="is_active" name="is_active" @if($campo->is_active) checked="checked" @endif> Campo Ativo</label>
					</div>
				</div>
				<!-- /.box-body -->

				<div class="box-footer">
					<button type="submit" class="btn btn-success">Enviar</button>
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
				</div>
					</form>
		</div>
  </section>
  <section class="col-lg-6 connectedSortable">
		<!-- Sexos da Categoria -->
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title">Relacionar Nova Opção</h3>
			</div>
			<!-- form start -->
			<form method="post" action="{{url("/evento/".$evento->id."/campos/".$campo->id."/opcao/add")}}">
				<div class="box-body">
					<div class="form-group">
						<label for="name">Nome *</label>
						<input name="name" id="name" class="form-control" type="text" />
					</div>
					<div class="form-group">
						<label for="response">Resposta *</label>
						<input name="response" id="response" class="form-control" type="text" />
					</div>
					<div class="form-group">
						<label for="value">Valor</label>
						<input name="value" id="value" class="form-control" type="text" />
					</div>
				</div>
				<!-- /.box-body -->

				<div class="box-footer">
					<button type="submit" class="btn btn-success">Enviar</button>
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
				</div>
			</form>
		</div>
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title">Opções</h3>
			</div>
			<!-- form start -->
				<div class="box-body">
					<table id="tabela_opcoes" class="table-responsive table-condensed table-striped" style="width: 100%">
						<thead>
							<tr>
								<th>#</th>
								<th>Nome</th>
								<th>Resposta</th>
								<th>Valor</th>
								<th width="20%">Opções</th>
							</tr>
						</thead>
						<tbody>
							@foreach($campo->opcoes->all() as $opcao)
								<tr>
									<td>{{$opcao->id}}</td>
									<td>{{$opcao->name}}</td>
									<td>{{$opcao->response}}</td>
									<td>{{$opcao->value}}</td>
									<td>
										@if($opcao->isDeletavel()) <a class="btn btn-danger" href="{{url("/evento/".$evento->id."/campos/".$campo->id."/opcao/remove/".$opcao->id)}}" role="button"><i class="fa fa-times"></i></a> @endif
									</td>
								</tr>
							@endforeach
						</tbody>
          			</table>
				</div>
				<!-- /.box-body -->
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
		$("#tabela_opcoes").DataTable();

		$("#campo_type").select2();
		$("#campo_type").val("{{$campo->type}}").change();
		$("#campo_validator").select2();
		@if($campo->validator) $("#campo_validator").val("{{$campo->validator}}").change(); @endif
  });
</script>
@endsection

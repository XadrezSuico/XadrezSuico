@extends('adminlte::page')

@section('title', 'Usuários - Editar')

@section('content_header')
    <h1>Usuários >> Editar</h1>
@stop

@section('content')
	@if (session('status'))
		<div class="alert alert-success">
				{{ session('status') }}
		</div>
	@endif
	<ul class="nav nav-pills">
		<li role="presentation"><a href="{{url("/usuario/new")}}">Novo Usuario</a></li>
		<li role="presentation"><a href="{{url("/usuario/password/".$user->id)}}">Alterar Senha</a></li>
		<li role="presentation"><a href="{{url("/usuario")}}">Listar Todos</a></li>
	</ul>
	<div class="row">
		<div class="col-md-6">
			<div class="box">
				<div class="box-header">
					<h3 class="box-title">Cadastro</h3>
				</div>
				<div class="box-body">
					<form method="post">
						<div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
							<label for="nome">Nome</label>
							<input type="text" class="form-control" id="nome" name="name" placeholder="Nome do Usuário" value="{{$user->name}}">
							@if ($errors->has('name'))
									<span class="help-block">
											<strong>{{ $errors->first('name') }}</strong>
									</span>
							@endif						
						</div>
						<div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
							<label for="email">Email</label>
							<input type="text" class="form-control" id="email" name="email" placeholder="Email do Usuário" value="{{$user->email}}">
							@if ($errors->has('email'))
									<span class="help-block">
											<strong>{{ $errors->first('email') }}</strong>
									</span>
							@endif
						</div>
						<button type="submit" class="btn btn-success">Enviar</button>
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
					</form>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="box">
				<div class="box-header">
					<h3 class="box-title">Relacionar Perfil</h3>
				</div>
				<div class="box-body">
					<form method="post" action="{{url("/usuario/".$user->id."/perfis/add")}}">
						<div class="form-group">
							<label for="perfils_id">Perfil</label>
							<select class="form-control" name="perfils_id" id="perfils_id">
								<option value=""> --- Selecione ---</option>
								@foreach(\App\Perfil::all() as $perfil)
									<option value="{{$perfil->id}}">{{$perfil->id}} - {{$perfil->name}}</option>
								@endforeach
							</select>						
						</div>
						<div class="form-group" id="grupo_evento" style="display: none">
							<label for="grupo_evento_id">Grupo de Evento</label>
							<select class="form-control" name="grupo_evento_id" id="grupo_evento_id">
								<option value=""> --- Selecione ---</option>
								@foreach(\App\GrupoEvento::all() as $grupo_evento)
									<option value="{{$grupo_evento->id}}">{{$grupo_evento->id}} - {{$grupo_evento->name}}</option>
								@endforeach
							</select>						
						</div>
						<div class="form-group" id="evento" style="display: none">
							<label for="evento_id">Evento</label>
							<select class="form-control" name="evento_id" id="evento_id">
								<option value=""> --- Selecione ---</option>
								@foreach(\App\Evento::all() as $evento)
									<option value="{{$evento->id}}">{{$evento->id}} - {{$evento->name}}</option>
								@endforeach
							</select>						
						</div>
						<button type="submit" class="btn btn-success">Enviar</button>
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
					</form>
				</div>
			</div>
			<div class="box">
				<div class="box-header">
					<h3 class="box-title">Perfis do Usuário</h3>
				</div>
				<div class="box-body">
					<table id="tabela_categoria" class="table-responsive table-condensed table-striped" style="width: 100%">
						<thead>
							<tr>
								<th>#</th>
								<th>Perfil</th>
								<th>Relacionamento</th>
								<th width="20%">Opções</th>
							</tr>
						</thead>
						<tbody>
							@foreach($user->perfis->all() as $perfil)
								<tr>
									<td>{{$perfil->perfil->id}}</td>
									<td>{{$perfil->perfil->name}}</td>
									<td>
										@if(
											$perfil->perfil->id == 3 ||
											$perfil->perfil->id == 4 ||
											$perfil->perfil->id == 5
										)
											Evento: {{$perfil->evento->id}} - {{$perfil->evento->name}}
										@else
											@if(
												$perfil->perfil->id == 6
											)
												Grupo de Evento: {{$perfil->grupo_evento->id}} - {{$perfil->grupo_evento->name}}
											@else
												-
											@endif
										@endif
									</td>
									<td>
										<a class="btn btn-danger" href="{{url("/usuario/".$user->id."/perfis/remove/".$perfil->id)}}" role="button"><i class="fa fa-times"></i></a>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
@endsection
@section("js")
<script>
	$(document).ready(function(){
		$("#perfils_id").select2();
		$("#grupo_evento_id").select2();
		$("#evento_id").select2();

		$("#perfils_id").on('select2:select',function(){			
			// if(!$("#grupo_evento").css('display') == 'none'){
				$("#grupo_evento").hide(100);
			// }
			// if(!$("#evento").css('display') == 'none'){
				$("#evento").hide(100);
			// }
			setTimeout(function(){
				if(
					$("#perfils_id").val() == 3 ||
					$("#perfils_id").val() == 4 ||
					$("#perfils_id").val() == 5
				){
					$("#evento").show(100);
				}else if($("#perfils_id").val() == 6){
					$("#grupo_evento").show(100);
				}
			},300);
		});
	});
</script>
@endsection
@section("css")
	<style>
		.form-control, .select2{
			width: 100% !important;
		}
	</style>
@endsection
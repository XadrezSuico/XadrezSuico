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
		@if(\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobalbyPerfil([1])) <li role="presentation"><a href="{{url("/usuario/password/".$user->id)}}">Alterar Senha</a></li>@endif
		<li role="presentation"><a href="{{url("/usuario")}}">Listar Todos</a></li>
	</ul>
	<div class="row">
		<div class="col-md-6">
			<div class="box">
				<div class="box-header">
					<h3 class="box-title">Cadastro</h3>
				</div>
				<div class="box-body">
					@if(\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal()) <form method="post"> @endif
						<div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
							<label for="nome">Nome</label>
							<input type="text" class="form-control" id="nome" name="name" placeholder="Nome do Usuário" value="{{$user->name}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal()) disabled="disabled" @endif >
							@if ($errors->has('name'))
									<span class="help-block">
											<strong>{{ $errors->first('name') }}</strong>
									</span>
							@endif
						</div>
						<div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
							<label for="email">Email</label>
							<input type="text" class="form-control" id="email" name="email" placeholder="Email do Usuário" value="{{$user->email}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal()) disabled="disabled" @endif >
							@if ($errors->has('email'))
									<span class="help-block">
											<strong>{{ $errors->first('email') }}</strong>
									</span>
							@endif
						</div>
						<button type="submit" class="btn btn-success" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal()) disabled="disabled" @endif >Enviar</button>
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
					@if(\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal()) </form> @endif
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
								@if(\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobalbyPerfil([1]))
									<option value="1">1 - Super-Administrador</option>
									<option value="2">2 - Administrador</option>
								@endif
								@if(\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() || \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventsByPerfil([7])))
									<option value="3">3 - Diretor de Torneio</option>
									<option value="4">4 - Árbitro Mesa</option>
									<option value="5">5 - Árbitro de Confirmação</option>
									<option value="6">6 - Diretor de Grupo de Evento</option>
									<option value="7">7 - Administrador de Grupo de Evento</option>
								@endif
								@if(\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobalbyPerfil([1]))
									<option value="8">8 - Coordenador de Cadastro de Cidades e Clubes</option>
									<option value="9">9 - Coordenador de Enxadristas</option>
                                    @if(env("ENTITY_DOMAIN",null) == "fexpar.com.br")
									    <option value="10">10 - FEXPAR - Gestor de Vínculos Federativos</option>
                                    @endif
                                    @if(env("XADREZSUICOPAG_URI",null) && env("XADREZSUICOPAG_SYSTEM_ID",null) && env("XADREZSUICOPAG_SYSTEM_TOKEN",null))
									    <option value="11">11 - XadrezSuíçoPAG - Administrador</option>
									    <option value="12">12 - XadrezSuíçoPAG - Gerente</option>
									    <option value="13">13 - XadrezSuíçoPAG - Operador</option>
                                    @endif
								@endif
							</select>
						</div>
						<div class="form-group" id="grupo_evento" style="display: none">
							<label for="grupo_evento_id">Grupo de Evento</label>
							<select class="form-control" name="grupo_evento_id" id="grupo_evento_id">
								<option value=""> --- Selecione ---</option>
								@foreach(\App\GrupoEvento::all() as $grupo_evento)
                        			@if(
                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($grupo_evento->id,[7])
                                    )
										<option value="{{$grupo_evento->id}}">{{$grupo_evento->id}} - {{$grupo_evento->name}}</option>
									@endif
								@endforeach
							</select>
						</div>
						<div class="form-group" id="evento" style="display: none">
							<label for="evento_id">Evento</label>
							<select class="form-control" name="evento_id" id="evento_id">
								<option value=""> --- Selecione ---</option>
								@foreach(\App\Evento::all() as $evento)
                        			@if(\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
                                    \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7]) ||
                                    \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]))
										<option value="{{$evento->id}}">{{$evento->id}} - {{$evento->name}}</option>
									@endif
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
                                @php($permission_check = false)
                                @if(
                                    \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal()
                                )
                                    @php($permission_check = true)
                                @endif
                                @if(
                                    ($perfil->perfil->id == 3 ||
                                    $perfil->perfil->id == 4 ||
                                    $perfil->perfil->id == 5)
                                    &&
                                    !$permission_check
                                )
                                    @if(
                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfilByGroupEvent($perfil->evento->id,[7]) ||
                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($perfil->id,[4])
                                    )
                                        @php($permission_check = true)
                                    @endif
                                @endif
                                @if(
                                    ($perfil->perfil->id == 6 ||
                                    $perfil->perfil->id == 7)
                                    &&
                                    !$permission_check
                                )
                                    @if(
                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($perfil->evento->id,[7]) ||
                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($perfil->id,[4])
                                    )
                                        @php($permission_check = true)
                                    @endif
                                @endif
                                @if($permission_check)
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
                                                    $perfil->perfil->id == 6 ||
                                                    $perfil->perfil->id == 7
                                                )
                                                    Grupo de Evento: {{$perfil->grupo_evento->id}} - {{$perfil->grupo_evento->name}}
                                                @else
                                                    -
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if(
                                                $perfil->perfil->id == 3 ||
                                                $perfil->perfil->id == 4 ||
                                                $perfil->perfil->id == 5
                                            )
                                                @if(\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobalbyPerfil([1]) || \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($perfil->evento->grupo_evento->id,[7]))
                                                    <a class="btn btn-danger" href="{{url("/usuario/".$user->id."/perfis/remove/".$perfil->id)}}" role="button"><i class="fa fa-times"></i></a>
                                                @endif
                                            @else
                                                @if(
                                                    $perfil->perfil->id == 6 ||
                                                    $perfil->perfil->id == 7
                                                )
                                                @if(\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobalbyPerfil([1]) || \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($perfil->grupo_evento->id,[7]))
                                                    <a class="btn btn-danger" href="{{url("/usuario/".$user->id."/perfis/remove/".$perfil->id)}}" role="button"><i class="fa fa-times"></i></a>
                                                @endif
                                                @else
                                                    @if(\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobalbyPerfil([1]))
                                                        <a class="btn btn-danger" href="{{url("/usuario/".$user->id."/perfis/remove/".$perfil->id)}}" role="button"><i class="fa fa-times"></i></a>
                                                    @endif
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
							    @endif
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
				}else if(
					$("#perfils_id").val() == 6 ||
					$("#perfils_id").val() == 7
				){
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

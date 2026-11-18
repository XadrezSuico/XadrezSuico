@extends('layouts.v2.catalog')

@section('title', 'Usuários - Alterar Senha')

@section('page-header')
    Usuários >> Alterar Senha
@endsection

@section('catalog-content')

	@if ($ok)
		<div class="alert alert-success">
			<strong>Tudo certo!</strong><br/>
			Sua senha foi alterada com sucesso!
		</div>
	@endif
	@if($USER->hasPermissionGlobalbyPerfil([1]))
		<ul class="v2-catalog-nav">
			<li role="presentation"><a href="{{url("/usuario/new")}}">Novo Usuario</a></li>
			<li role="presentation"><a href="{{url("/usuario")}}">Listar Todos</a></li>
		</ul>
	@endif
    <div class="v2-catalog-panel">
        <div class="v2-catalog-panel-body">
			<form method="post">
				<div class="v2-catalog-field">
					<label for="password">Usuário:</label> {{$user->email}}.
				</div>
				<div class="v2-catalog-field {{ $errors->has('password') ? ' has-error' : '' }}">
					<label for="password">Senha</label>
					<input type="password" class="v2-catalog-input" id="password" name="password" autocomplete="new-password" placeholder="Senha do Usuário">
					@if ($errors->has('password'))
							<span class="v2-catalog-error">
									<strong>{{ $errors->first('password') }}</strong>
							</span>
					@endif							
				</div>
				<div class="v2-catalog-field {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
					<label for="password_confirmation">Confirmação de Senha</label>
					<input type="password" class="v2-catalog-input" id="password_confirmation" name="password_confirmation" autocomplete="new-password" placeholder="Confirmação da Senha do Usuário">
					@if ($errors->has('password_confirmation'))
							<span class="v2-catalog-error">
									<strong>{{ $errors->first('password_confirmation') }}</strong>
							</span>
					@endif							
				</div>
				<button type="submit" class="btn btn-success"><i class="fa fa-check" aria-hidden="true"></i> Salvar</button>
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
			</form>
		</div>
	</div>
@endsection

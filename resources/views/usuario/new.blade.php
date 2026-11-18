@extends('layouts.v2.catalog')

@section('title', 'Usuários - Novo')

@section('page-header')
    Usuários >> Novo
@endsection

@section('catalog-content')
	<ul class="v2-catalog-nav">
		<li role="presentation"><a href="{{url("/usuario")}}">Listar Todos</a></li>
	</ul>

	<div class="v2-catalog-panel">
		<div class="v2-catalog-panel-body">
			<form method="post">
				<div class="v2-catalog-field {{ $errors->has('name') ? ' has-error' : '' }}">
					<label for="nome">Nome</label>
					<input type="text" class="v2-catalog-input" id="nome" name="name" placeholder="Nome do Usuário" value="{{old("name")}}">
					@if ($errors->has('name'))
						<span class="v2-catalog-error">
							<strong>{{ $errors->first('name') }}</strong>
						</span>
					@endif
				</div>
				<div class="v2-catalog-field {{ $errors->has('email') ? ' has-error' : '' }}">
					<label for="email">Email</label>
					<input type="email" class="v2-catalog-input" id="email" name="email" autocomplete="email" placeholder="Email do Usuário" value="{{old("email")}}">
					@if ($errors->has('email'))
						<span class="v2-catalog-error">
							<strong>{{ $errors->first('email') }}</strong>
						</span>
					@endif
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
@section("js")
@endsection

@extends('adminlte::page')

@section('title', 'Usuários - Novo')

@section('content_header')
	<h1>Usuários >> Novo</h1>
@stop

@section('content')
	@if (session('status'))
		<div class="alert alert-success">
			{{ session('status') }}
		</div>
	@endif
	<ul class="nav nav-pills">
		<li role="presentation"><a href="{{url("/usuario")}}">Listar Todos</a></li>
	</ul>

	<div class="box">
		<div class="box-body">
			<form method="post">
				<div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
					<label for="nome">Nome</label>
					<input type="text" class="form-control" id="nome" name="name" placeholder="Nome do Usuário" value="{{old("name")}}">
					@if ($errors->has('name'))
						<span class="help-block">
							<strong>{{ $errors->first('name') }}</strong>
						</span>
					@endif
				</div>
				<div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
					<label for="email">Email</label>
					<input type="text" class="form-control" id="email" name="email" placeholder="Email do Usuário" value="{{old("email")}}">
					@if ($errors->has('email'))
						<span class="help-block">
							<strong>{{ $errors->first('email') }}</strong>
						</span>
					@endif
				</div>
				<div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
					<label for="password">Senha</label>
					<input type="password" class="form-control" id="password" name="password" placeholder="Senha do Usuário">
					@if ($errors->has('password'))
						<span class="help-block">
							<strong>{{ $errors->first('password') }}</strong>
						</span>
					@endif
				</div>
				<div class="form-group {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
					<label for="password_confirmation">Confirmação de Senha</label>
					<input type="password" class="form-control" id="password" name="password_confirmation" placeholder="Confirmação da Senha do Usuário">
					@if ($errors->has('password_confirmation'))
						<span class="help-block">
							<strong>{{ $errors->first('password_confirmation') }}</strong>
						</span>
					@endif
				</div>
				<button type="submit" class="btn btn-success">Enviar</button>
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
			</form>
			
		</div>
	</div>
@endsection
@section("js")
@endsection

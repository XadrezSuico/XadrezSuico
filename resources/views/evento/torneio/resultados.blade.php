@extends('adminlte::page')

@section('title', 'Evento #'.$evento->id.' - Torneio #'.$torneio->id.' - Resultados')

@section('content_header')
    <h1>Evento #{{$evento->id}}: {{$evento->name}} - Torneio #{{$torneio->id}}: {{$torneio->name}} >> Resultados</h1>
@stop

@section('content')
	@if (session('status'))
		<div class="alert alert-success">
				{{ session('status') }}
		</div>
	@endif
	<ul class="nav nav-pills">
		<li role="presentation"><a href="{{url("/evento/".$evento->id."/torneios")}}">Listar Todos os Torneios</a></li>
	</ul>

    <div class="box">
        <div class="box-body">
			<form method="post">
				<div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
					<label for="nome">Resultados</label>
					<textarea name="results" class="form-control"></textarea>						
				</div>
				<button type="submit" class="btn btn-success">Enviar</button>
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
			</form>
		</div>
	</div>
@endsection
@section("js")
@endsection
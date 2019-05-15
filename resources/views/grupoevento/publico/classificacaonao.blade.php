@extends('adminlte::page')

@section('title', 'Evento #'.$evento->id.' - Resultados')

@section('content_header')
    <h1>Evento #{{$evento->id}} ({{$evento->name}}) - Resultados</h1>
@stop

@section('content')
	@if (session('status'))
		<div class="alert alert-success">
				{{ session('status') }}
		</div>
	@endif
    <div class="box">
        <div class="box-body">
			<h4>A classificação deste evento ainda não se encontra disponível para consulta.</h4>
		</div>
	</div>
@endsection
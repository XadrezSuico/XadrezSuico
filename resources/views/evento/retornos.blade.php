@extends('adminlte::page')

@section('title', 'Evento #'.$evento->id.' - '.$evento->name.' - Processamento da Classificação')

@section('content_header')
    <h1>Evento #{{$evento->id}}: {{$evento->name}} >> Processamento da Classificação</h1>
@stop

@section('content')
	@if (session('status'))
		<div class="alert alert-success">
				{{ session('status') }}
		</div>
	@endif
	<ul class="nav nav-pills">
		<li role="presentation"><a href="{{url("/evento/dashboard/".$evento->id)}}">Retornar à dashboard de Evento</a></li>
	</ul>

    <div class="box">
		<div class="box-header">
			<h3 class="box-title">Retorno do Processamento da Classificação</h3>
		</div>
        <div class="box-body">
            @php($i=1)
            @foreach($retornos as $linha)
                {{$i++}} - {!!$linha!!} <br/>
            @endforeach
		</div>
	</div>
@endsection
@section("js")
@endsection
@extends('adminlte::page')

@section('title', 'Evento #'.$evento->id.' - Torneio #'.$torneio->id.' - Processamento dos Emparceiramentos')

@section('content_header')
    <h1>Evento #{{$evento->id}}: {{$evento->name}} - Torneio #{{$torneio->id}}: {{$torneio->name}} >> Processamento dos Emparceiramentos</h1>
@stop

@section('content')
	@if (session('status'))
		<div class="alert alert-success">
				{{ session('status') }}
		</div>
	@endif
	<ul class="nav nav-pills">
		<li role="presentation"><a href="{{url("/evento/dashboard/".$evento->id."?tab=torneio")}}">Listar Todos os Torneios</a></li>
	</ul>

    <div class="box">
		<div class="box-header">
			<h3 class="box-title">Retorno do Processamento dos Emparceiramentos</h3>
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

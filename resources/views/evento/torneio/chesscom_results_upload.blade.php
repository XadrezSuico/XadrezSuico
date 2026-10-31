@extends('adminlte::page')

@section('title', 'Importar resultados do Chess.com')

@section('content_header')
    <h1>Resultados do Chess.com: {{$torneio->name}}</h1>
@stop

@section('content')
    <div class="alert alert-warning">
        Não foi possível concluir a importação automática. Baixe o arquivo de resultados pelo navegador e envie o mesmo arquivo abaixo, sem editar ou converter.
    </div>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="box">
        <div class="box-body">
            <p>Evento: {{$evento->name}} — Torneio: {{$torneio->name}}</p>
            <p>1. Abra o Chess.com e salve o arquivo de resultados. Se necessário, entre na sua conta no Chess.com.</p>
            <p><a class="btn btn-primary" href="{{$downloadUrl}}" target="_blank" rel="noopener noreferrer">Baixar resultados no Chess.com</a></p>
            <p>2. Selecione o arquivo CSV baixado e envie para processar os resultados.</p>
            <form method="post" action="{{$uploadUrl}}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="arquivo">Arquivo de resultados do Chess.com (CSV, até 10 MB)</label>
                    <input type="file" id="arquivo" name="arquivo" accept=".csv,text/csv" required>
                </div>
                <button type="submit" class="btn btn-success">Enviar e processar resultados</button>
                <a class="btn btn-default" href="{{url('/evento/dashboard/'.$evento->id.'?tab=torneio')}}">Voltar ao evento</a>
            </form>
        </div>
    </div>
@endsection

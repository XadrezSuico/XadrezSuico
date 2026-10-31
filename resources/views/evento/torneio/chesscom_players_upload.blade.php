@extends('adminlte::page')

@section('title', 'Conferir inscrições do Chess.com')

@section('content_header')
    <h1>Inscrições do Chess.com: {{$torneio->name}}</h1>
@stop

@section('content')
    <div class="alert alert-warning">
        Não foi possível concluir a conferência automática. Salve o arquivo do torneio pelo navegador e envie o mesmo arquivo abaixo, sem editar ou converter.
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
            <p>1. Abra o link abaixo. Quando o navegador exibir os dados do torneio, use “Salvar como” (Ctrl+S) para salvar o arquivo JSON.</p>
            <p><a class="btn btn-primary" href="{{$downloadUrl}}" target="_blank" rel="noopener noreferrer">Abrir arquivo de inscrições no Chess.com</a></p>
            <p>2. Selecione o arquivo JSON salvo e envie para conferir as inscrições. O torneio precisa estar finalizado no Chess.com.</p>
            <form method="post" action="{{$uploadUrl}}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="arquivo">Arquivo do torneio do Chess.com (JSON, até 10 MB)</label>
                    <input type="file" id="arquivo" name="arquivo" accept=".json,.txt,application/json,text/plain" required>
                </div>
                <button type="submit" class="btn btn-success">Enviar e conferir inscrições</button>
                <a class="btn btn-default" href="{{url('/evento/dashboard/'.$evento->id.'?tab=torneio')}}">Voltar ao evento</a>
            </form>
        </div>
    </div>
@endsection

@extends('adminlte::page')

@section('title', 'O que há de novo?')

@section('content_header')
    <h1>O que há de novo?</h1>
@stop

@section('content')
    <p>Nesta página estão as atualizações desde a primeira versão Beta lançada do sistema.</p>
    <hr/>
    @foreach($news as $new)
        <h3>Versão {{$new["name"]}}</h3>
        <ul>
            @foreach($new["news"] as $whats_new)
                <li>{!!$whats_new!!}</li>
            @endforeach
        </ul>
    @endforeach
@stop
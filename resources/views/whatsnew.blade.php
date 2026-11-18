@extends('layouts.v2.document')

@section('title', 'O que há de novo?')

@section('document-title')
    O que há de novo?
@endsection

@section('document-content')
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
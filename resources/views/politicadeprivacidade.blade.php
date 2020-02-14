@extends('adminlte::page')

@section('title', 'Política de Privacidade')

@section('content_header')
    <h1>Política de Privacidade</h1>
@stop

@section('content')
    <h3>A. Introdução</h3>
    <p>A privacidade dos visitantes e usuários de nossa plataforma ({{url("/")}}) é muito importante para nós, e estamos comprometidos em protegê-la.
        Esta política explica o que faremos com suas informações pessoais.</p>
        
    <h3>B. Da Atualização deste Documento</h3>
    <p> Este documento poderá ser atualizado, valendo a partir de 48 horas após a sua publicação. Acesse esta página com alguma frequência para ficar a par deste documento.</p>
    <br/>
    <p>{{env("CIDADE_POLITICA_PRIVACIDADE")}}, {{env("DATA_ALTERACAO_POLITICA_PRIVACIDADE")}}.</p>
@stop
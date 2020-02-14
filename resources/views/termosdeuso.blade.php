@extends('adminlte::page')

@section('title', 'Termos de Uso')

@section('content_header')
    <h1>Termos de Uso</h1>
@stop

@section('content')
    <h3>A. Condições Gerais</h3>
    <p>Este documento contém os Termos de Uso da plataforma de gerenciamento de Circuito de Xadrez XadrezSuíço, cuja aceitação plena e integral requisito
        para todos os seus Usuários. Eles incluem, além dos termos gerais, as políticas de responsabilidade, de privacidade e confidencialidade, a licença
        de uso do conteúdo, e as informações sobre como reportar violações.</p>
    <p> Usuário deverá ler e aceitar todas as condições aqui estabelecidas antes de seu cadastro na plataforma. Todas as contribuições são bem-vindas,
        desde que respeitadas as condições aqui expressas.</p>
    <p> A plataforma XadrezSuíço é uma plataforma virtual de gerenciamento de Circuitos de Xadrez, que atua no pré e pós-evento. No pré-evento atua com
        principalmente o gerenciamento de inscrições e exportação de arquivos para importação em softwares de Emparceiramento de Xadrez, e já no pós-evento,
        atua no recebimento do resultado obtido pelos enxadristas e com isto, efetua processamentos para principalmente efetuar o cálculo de pontuação final 
        do circuito e seus critérios de desempate.</p>
    <h3>B. Da Atualização deste Documento</h3>
    <p> Este documento poderá ser atualizado, valendo a partir de 48 horas após a sua publicação. Acesse esta página com alguma frequência para ficar a par deste documento.</p>
    <br/>
    <p>{{env("CIDADE_TERMOS_USO")}}, {{env("DATA_ALTERACAO_TERMOS_USO")}}.</p>
@stop
@extends('adminlte::page')

@section('title', "Vínculos Federativos para ".$vinculo_consulta->vinculo->ano." - Vínculo #".$vinculo_consulta->vinculo->uuid)

@section('content_header')
    <h1>Vínculos Federativos para {{$vinculo_consulta->vinculo->ano}} - Vínculo #{{$vinculo_consulta->vinculo->uuid}}</h1>
@stop

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    @if(!$vinculo_consulta->eConsultaAmazenadaIgualVinculo())
        <div class="alert alert-warning alert-dismissible">
            <h4><i class="icon fa fa-warning"></i> Aviso!</h4>
            Esta consulta de vínculo <strong>não está mais igual ao vínculo do enxadrista</strong>. É necessária a <a href="{{url("/especiais/fexpar/vinculos/".$vinculo_consulta->vinculo->uuid)}}">a realização de uma nova consulta</a> para obter as informações atualizadas.
        </div>
    @endif

    <div class="box">
        <div class="box-body">
            <h3>Vínculo <strong>#{{$vinculo_consulta->vinculo->uuid}}</strong></h3>
            <h4><strong>Ano:</strong> {{$vinculo_consulta->ano}}</h4>
            <h5>Consulta #{{$vinculo_consulta->uuid}} // Consulta por este código pode ser realizada em <u>{{url("/especiais/fexpar/vinculos/validacao")}}</u>.</h5>
            <hr/>
            <h4>Enxadrista:</h4>
            <h5><strong>Nome:</strong> {{$vinculo_consulta->enxadrista->name}}</h5>
            <h5><strong>ID FEXPAR:</strong> {{$vinculo_consulta->enxadrista->id}}</h5>
            <h5><strong>ID CBX:</strong> {{$vinculo_consulta->enxadrista->cbx_id}}</h5>
            <h5><strong>ID FIDE:</strong> {{$vinculo_consulta->enxadrista->fide_id}}</h5>
            <hr/>
            <h4>Vínculo:</h4>
            <h5><strong>Cidade:</strong> {{$vinculo_consulta->cidade->name}}</h5>
            <h5><strong>Clube:</strong> {{$vinculo_consulta->clube->name}}</h5>
            <hr/>
            <h4>Dados:</h4>
            <h5><strong>Tipo de Vínculo:</strong> {{$vinculo_consulta->getVinculoType()}}</h5>
            @if($vinculo_consulta->is_confirmed_system)
                <h5><strong>Quantos eventos que o enxadrista participou competindo por este clube:</strong> {{$vinculo_consulta->system_inscricoes_in_this_club_confirmed}} (Valor em <strong>{{$vinculo_consulta->getCreatedAt()}}</strong>)</h5>
                <small>Observação: Esta informação compreende apenas os registros de eventos que constam no XadrezSuíço que atendam os seguintes requisitos:
                    <ul>
                        <li>Esteja com a inscrição com a mesma cidade e clube do vínculo;</li>
                        <li>O evento em questão esteja devidamente homologado.</li>
                    </ul>
                    Vale salientar também que, o que vale é o ID de Cadastro de Clube quando é efetuada a validação do clube.
                </small>
            @endif
            @if($vinculo_consulta->is_confirmed_manually)
                <h5><strong>Eventos Jogados:</strong></h5>
                <p>{!!$vinculo_consulta->events_played!!}</p>
            @endif
            <hr/>
            <h5><strong>Eventos via Sistema XadrezSuíço (Informação obtida durante a consulta):</strong></h5>
            <table class="table" width="100%">
                <thead>
                    <tr>
                        <th>ID do Evento</th>
                        <th>ID da Inscrição</th>
                        <th>Grupo de Evento</th>
                        <th>Evento</th>
                        <th>Torneio</th>
                    </tr>
                </thead>
                @foreach($vinculo_consulta->enxadrista->getInscricoesByClube($vinculo_consulta->clube->id) as $inscricao)
                    <tr>
                        <td>{{$inscricao->torneio->evento->id}}</td>
                        <td>{{$inscricao->id}} (UUID: {{$inscricao->uuid}})</td>
                        <td>{{$inscricao->torneio->evento->grupo_evento->name}}</td>
                        <td>{{$inscricao->torneio->evento->name}}</td>
                        <td>{{$inscricao->torneio->name}}</td>
                    </tr>
                @endforeach
            </table>
            <hr/>
            Consulta realizada em {{$vinculo_consulta->getCreatedAt()}} através do IP {{$vinculo_consulta->ip}} e ficou registrada pelo código #{{$vinculo_consulta->uuid}}.<br/>
            Por questões de segurança, seu IP e a data e hora de acesso ({{date("d/m/Y H:i:s")}}) foram registradas à esta consulta.
            <hr/>
            <h4>Acesso à esta consulta:</h4>
            {!!$vinculo_consulta->getQrCode()!!}
        </div>
    </div>
@endsection

@section("js")
<script type="text/javascript">
    $(document).ready(function(){
    });
</script>
@endsection

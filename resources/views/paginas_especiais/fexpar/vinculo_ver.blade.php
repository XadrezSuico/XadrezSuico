@extends('adminlte::page')

@section('title', "Vinculos Federativos para ".$vinculo_consulta->vinculo->ano." - Vínculo #".$vinculo_consulta->vinculo->uuid)

@section('content_header')
    <h1>Vinculos Federativos para {{$vinculo_consulta->vinculo->ano}} - Vínculo #{{$vinculo_consulta->vinculo->uuid}}</h1>
@stop

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div class="box">
        <div class="box-body">
            <h3>Vínculo <strong>#{{$vinculo_consulta->vinculo->uuid}}</strong></h3>
            <hr/>
            <h4>Enxadrista:</h4>
            <h5><strong>Nome:</strong> {{$vinculo_consulta->enxadrista->name}}</h5>
            <h5><strong>ID FEXPAR:</strong> {{$vinculo_consulta->enxadrista->id}}</h5>
            <h5><strong>ID CBX:</strong> {{$vinculo_consulta->enxadrista->cbx_id}}</h5>
            <h5><strong>ID FIDE:</strong> {{$vinculo_consulta->enxadrista->fide_id}}</h5>
            <hr/>
            <h4>Vínculo:</h4>
            <h5><strong>Cidade:</strong> {{$vinculo_consulta->enxadrista->cidade->name}}</h5>
            <h5><strong>Clube:</strong> {{$vinculo_consulta->enxadrista->cidade->clube}}</h5>
            <hr/>
            <h4>Dados:</h4>
            <h5><strong>Tipo de Vínculo:</strong> {{$vinculo_consulta->getVinculoType()}}</h5>
            @if($vinculo_consulta->is_confirmed_system)
                <h5><strong>Quantos eventos que o enxadrista participou competindo por este clube:</strong> {{$vinculo_consulta->system_inscricoes_in_this_club_confirmed}}</h5>
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
                <h5><strong>Observações:</strong></h5>
                <p>{!!$vinculo_consulta->obs!!}</p>
            @endif
            <hr/>
            Consulta realizada em {{$vinculo_consulta->getCreatedAt()}} através do IP {{$vinculo_consulta->ip}} e ficou registrada pelo código #{{$vinculo_consulta->uuid}}.<br/>
            Por questões de segurança, seu IP e a data e hora de acesso foram registradas à esta consulta.
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

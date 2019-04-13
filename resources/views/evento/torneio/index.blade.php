@extends('adminlte::page')

@section('title', 'Evento #'.$evento->id." - Torneios")

@section('content_header')
    <h1>Evento #{{$evento->id}} ({{$evento->name}}) - Torneios</h1>
@stop

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    <ul class="nav nav-pills">
        <li role="presentation"><a href="{{url("/evento")}}">Voltar à Lista de Eventos</a></li>
        <li role="presentation"><a href="{{url("/evento/".$evento->id."/torneios/new")}}">Novo Torneio</a></li>
        <li role="presentation"><a href="{{url("/evento/inscricao/".$evento->id)}}">Nova Inscrição</a></li>
        <li role="presentation"><a href="{{url("/evento/inscricao/".$evento->id."/confirmacao")}}">Confirmar Inscrições</a></li>
        <li role="presentation"><a href="{{url("/evento/classificar/".$evento->id)}}">Classificar Evento</a></li>
    </ul>

    <div class="box">
        <div class="box-body">
            <table id="tabela" class="table-responsive table-condensed table-striped" style="width: 100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Categorias</th>
                        <th>Inscritos</th>
                        <th>Confirmados</th>
                        <th>Não Confirmados</th>
                        <th>Template de Torneio</th>
                        <th width="20%">Opções</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($torneios as $torneio)
                        <tr>
                            <td>{{$torneio->id}}</td>
                            <td>{{$torneio->name}}</td>
                            <td>
                                @foreach($torneio->categorias->all() as $categoria)
                                    {{$categoria->categoria->name}},
                                @endforeach
                            </td>
                            <td>{{$torneio->getCountInscritos()}}</td>
                            <td>{{$torneio->getCountInscritosConfirmados()}}</td>
                            <td>{{$torneio->getCountInscritosNaoConfirmados()}}</td>
                            <td>
                                @if($torneio->template)
                                    {{$torneio->template->name}}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <a class="btn btn-default" href="{{url("/evento/".$evento->id."/torneios/edit/".$torneio->id)}}" role="button">Editar</a>
                                <a class="btn btn-default" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes")}}" role="button">Inscrições</a>
                                <a class="btn btn-default" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/resultados")}}" role="button">Resultados</a>
                                <a class="btn btn-success" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/sm")}}" role="button" target="_blank">Baixar Inscrições Confirmadas</a>
                                <a class="btn btn-warning" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/sm/all")}}" role="button" target="_blank">Baixar Todas as Inscrições</a>
                                <a class="btn btn-success" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/relatorio/inscricoes")}}" role="button" target="_blank">Imprimir Inscrições</a>
                                <a class="btn btn-success" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/relatorio/inscricoes/alfabetico")}}" role="button" target="_blank">Imprimir Inscrições (Alfabético)</a>
                                <a class="btn btn-success" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/relatorio/inscricoes/alfabetico/cidade")}}" role="button" target="_blank">Imprimir Inscrições (Alfabético por Cidade/Clube)</a>
                                @if($torneio->isDeletavel()) <a class="btn btn-danger" href="{{url("/evento/".$evento->id."/torneios/delete/".$torneio->id)}}" role="button">Apagar</a> @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section("js")
<script type="text/javascript">
    $(document).ready(function(){
        $("#tabela").DataTable({
            responsive: true,
        });
    });
</script>
@endsection

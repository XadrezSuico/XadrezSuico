@extends('adminlte::page')

@section('title', 'Grupo de Evento #'.$grupo_evento->id.' ('.$grupo_evento->name.') - Resultados da Categoria "'.$categoria->name.'"')

@section('content_header')
    <h1>Grupo de Evento #{{$grupo_evento->id}} ({{$grupo_evento->name}}) - Resultados da Categoria #{{$categoria->id}} ({{$categoria->name}})</h1>
@stop

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    @if(\Illuminate\Support\Facades\Auth::check())
        <ul class="nav nav-pills">
            <li role="presentation"><a href="{{url("/grupoevento/classificacao/".$grupo_evento->id."")}}">Voltar à Seleção da Categoria</a></li>
        </ul>
    @else
        <ul class="nav nav-pills">
            <li role="presentation"><a href="{{url("/grupoevento/classificacao/".$grupo_evento->id)}}">Voltar à Seleção da Categoria</a></li>
        </ul>
	@endif

    <div class="box">
        <div class="box-body">
            <table id="tabela" class="table-responsive table-condensed table-striped" style="width: 100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Cód</th>
                        <th>Nome</th>
                        <th>Data de Nascimento</th>
                        <th>Cidade</th>
                        <th>Clube</th>
                        <th>Pontuação</th>
                        @foreach($criterios as $criterio)
                            <th>{{$criterio->criterio->code}}</th>
                        @endforeach
                    </tr>
                    @if(count($eventos) > 0)
                        <tr>
                            @php($i=1)
                            @foreach($eventos as $evento)
                                <th>E{{$i++}}</th>
                            @endforeach
                        </tr>
                    @endif
                </thead>
                <tbody>
                    @foreach($pontuacoes as $pontuacao)
                        <tr>
                            <td>{{$pontuacao->posicao}}</td>
                            <td><a href="{{url("/grupoevento/".$grupo_evento->id."/resultados/enxadrista/".$pontuacao->enxadrista->id)}}">#{{$pontuacao->enxadrista->id}}</a></td>
                            <td><a href="{{url("/grupoevento/".$grupo_evento->id."/resultados/enxadrista/".$pontuacao->enxadrista->id)}}">{{$pontuacao->enxadrista->name}}</a></td>
                            <td>{{$pontuacao->enxadrista->getBorn()}}</td>
                            <td>{{$pontuacao->enxadrista->cidade->name}}</td>
                            <td>@if($pontuacao->enxadrista->clube) {{$pontuacao->enxadrista->clube->name}} @else Sem Clube @endif</td>
                            @foreach($eventos as $evento)
                                @php($inscricao = $evento->enxadristaInscrito($pontuacao->enxadrista->id))
                                @if($inscricao)
                                    @if($inscricao->pontos_geral)
                                        <td>{{$inscricao->pontos_geral}}</td>
                                    @else
                                        <td>-</td>
                                    @endif
                                @else
                                    <td>-</td>
                                @endif
                            @endforeach
                            <td>{{$pontuacao->pontos}}</td>
                            @foreach($criterios as $criterio)
                                <td>
                                    @if($criterio->criterio->valor_desempate_geral($pontuacao->enxadrista->id,$grupo_evento->id,$categoria->id))
                                        {{$criterio->criterio->valor_desempate_geral($pontuacao->enxadrista->id,$grupo_evento->id,$categoria->id)->valor}}
                                    @else
                                        -
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <hr/>
            <div class="row">
                <div class="col-md-6">
                    <h4>Legenda dos Critérios de Desempate:</h4><br/>
                    @php($j=1)
                    @foreach($criterios as $criterio)
                        <strong>{{$j++}} - {{$criterio->criterio->code}}:</strong> {{$criterio->criterio->name}}<br/>
                    @endforeach
                </div>
                <div class="col-md-6">                    
                    @if(count($eventos) > 0)
                        <h4>Legenda dos Eventos:</h4><br/>
                        @php($i=1)
                        @foreach($eventos as $evento)
                            <strong>E{{$i++}}</strong>: {{$evento->name}}<br/>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section("js")
@foreach(array(
    "https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js",
    "https://cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js",
    "https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js",
    "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js",
    "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js",
    "https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js",
    "https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"
    ) as $url)
<script type="text/javascript" src="{{$url}}"></script>
@endforeach
<script type="text/javascript">
    $(document).ready(function(){
        $("#tabela").DataTable({
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            paging: false
        });
    });
</script>
@endsection

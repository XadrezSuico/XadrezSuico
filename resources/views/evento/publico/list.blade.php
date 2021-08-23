@extends('adminlte::page')

@section('title', 'Evento #'.$evento->id.' ('.$evento->name.') - Resultados da Categoria "'.$categoria->name.'"')

@section('content_header')
    <h1>Evento #{{$evento->id}} ({{$evento->name}}) - Resultados da Categoria #{{$categoria->id}} ({{$categoria->name}})</h1>
@stop

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    @if(\Illuminate\Support\Facades\Auth::check())
        <ul class="nav nav-pills">
            <li role="presentation"><a href="{{url("/evento/classificacao/".$evento->id."/interno")}}">Voltar à Seleção da Categoria</a></li>
        </ul>
    @else
        <ul class="nav nav-pills">
            <li role="presentation"><a href="{{url("/evento/classificacao/".$evento->id)}}">Voltar à Seleção da Categoria</a></li>
        </ul>
	@endif

    @if($evento->classifica)
        <div class="alert alert-success" role="alert">
            <h3 style="margin-top: 0;">Importante!</h3>
            <p>Os nomes em <strong><span style="color: green"><u>verde e sublinhados</u></span></strong> estão classificados para o <strong>{{$evento->classifica->name}}</strong>.</p>
            <p>Já os nomes em <strong><span style="color: orange"><u>laranja e sublinhados</u></span></strong> estão classificados para o <strong>{{$evento->classifica->name}}</strong> por <strong><u>outro evento classificatório</u></strong>.</p>
            <p>A <strong>lista de classificados pode sofrer alterações</strong> devido caso ocorra declínio por parte de algum(a) enxadrista, caso permitido assim pela organização ou pelo regulamento.</p>
        </div>
    @endif

    <div class="box">
        <div class="box-body">
            <table id="tabela" class="table-responsive table-condensed table-striped" style="width: 100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Data de Nascimento</th>
                        <th>Cidade</th>
                        <th>Clube</th>
                        <th>Pontuação</th>
                        @foreach($criterios as $criterio)
                            <th>{{$criterio->criterio->code}}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($inscricoes as $inscricao)
                        <tr>

                            <td data-sort='{{($inscricao->posicao) ? $inscricao->posicao : 999999999}}'>@if($inscricao->posicao) {{$inscricao->posicao}} @else - @endif</td>
                            @if($evento->classifica)
                                @if($evento->classifica->enxadristaInscrito($inscricao->enxadrista->id))
                                    <td style="font-weight: bold; color: green; text-decoration: underline" >
                                        #{{$inscricao->enxadrista->id}} - {{$inscricao->enxadrista->name}}
                                    </td>
                                @else
                                    @if($evento->classifica->grupo_evento->enxadristaJaInscritoEmOutroEvento($evento->classifica->id,$inscricao->enxadrista->id))
                                        <td style="font-weight: bold; color: orange; text-decoration: underline" >
                                            #{{$inscricao->enxadrista->id}} - {{$inscricao->enxadrista->name}}
                                        </td>
                                    @else
                                        <td>#{{$inscricao->enxadrista->id}} - {{$inscricao->enxadrista->name}}</td>
                                    @endif
                                @endif
                            @else
                                <td>#{{$inscricao->enxadrista->id}} - {{$inscricao->enxadrista->name}}</td>
                            @endif

                            <td>{{$inscricao->enxadrista->getNascimentoPublico()}}</td>
                            <td>{{$inscricao->cidade->name}}</td>
                            <td>@if($inscricao->clube) {{$inscricao->clube->name}} @else Sem Clube @endif</td>
                            <td>@if($inscricao->posicao) {{$inscricao->pontos}} @else - @endif</td>
                            @foreach($criterios as $criterio)
                                <th>@if($criterio->criterio->valor_criterio($inscricao->id)) {{$criterio->criterio->valor_criterio($inscricao->id)->valor}} @else - @endif</th>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <hr/>
            <h4>Legenda dos Critérios de Desempate:</h4><br/>
            @php($j=1)
            @foreach($criterios as $criterio)
                <strong>{{$j++}} - {{$criterio->criterio->code}}:</strong> {{$criterio->criterio->name}}<br/>
            @endforeach
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
            ]
        });
    });
</script>
@endsection

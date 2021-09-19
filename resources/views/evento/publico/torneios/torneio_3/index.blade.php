@extends('adminlte::page')

@section('title', 'Evento #'.$evento->id.' ('.$evento->name.') - Acompanhar Torneio')

@section('content_header')
    <h1>Evento #{{$evento->id}} ({{$evento->name}}) - Acompanhar Torneio</h1>
@stop


@section('css')
	<style>
		.display-none, .displayNone{
			display: none;
		}
		.width-100{
			width: 100% !important;
		}

        .emparceiramento{
            width: 100%;
            border: 1px solid #000;
            padding: 3px;
            background: rgb(223, 223, 223);
        }

        .arrows{
            font-size: 3rem;
        }

        .enxadrista_white{
            display:inline-block;
            background: white;
            color: black;
            padding: 4px;
            border-radius: 2px;
        }

        .enxadrista_black{
            display:inline-block;
            background: black;
            color: white;
            padding: 4px;
            border-radius: 2px;
        }

        .enxadrista_without_color{
            display:inline-block;
            background: gray;
            color: white;
            padding: 4px;
            border-radius: 2px;
        }

        .btn_enxadrista_color{
            width: 100%;
            word-wrap: break-word !important;
            white-space: inherit !important;
        }
        .btn_enxadrista_color.bg-white{
            background: white;
            color: black;
        }
        .btn_enxadrista_color.bg-black{
            background: black;
            color: white;
        }

        .resultados_confrontos{
            font-size: 2rem;
        }

        .resultados_confrontos .resultado{
            display: inline-block;
            background: #d2d6de;
            border-radius: 4px;
            padding: 0.2rem 0.4rem;
            margin: 0.5rem 0;
            font-weight: bold;

        }
	</style>
@endsection

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div class="box">
        <div class="box-body">
            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                @if($evento->e_permite_visualizar_lista_inscritos_publica)
                    <a href="{{url("/inscricao/visualizar/".$evento->id)}}" class="btn btn-lg btn-info btn-block">
                        Visualizar Lista de Inscrições
                    </a><br/>
                @endif
                <h3>Data: {{$evento->getDataInicio()}}</h3>
                <hr/>
                <p>Clique sobre a categoria que deseja ver o emparceiramento:</p>
                @foreach($evento->torneios()->orderBy("name","ASC")->get() as $torneio)
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="torneio_header_{{$torneio->id}}">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#torneio_content_{{$torneio->id}}" aria-expanded="true" aria-controls="torneio_content_{{$torneio->id}}">
                                    {{$torneio->name}}
                                </a>
                            </h4>
                        </div>
                        <div id="torneio_content_{{$torneio->id}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="torneio_header_{{$torneio->id}}">
                            <div class="panel-body">
                                @foreach($torneio->rodadas->all() as $rodada)
                                    <div class="row">
                                        <div class="col-md-12">
                                            @if($rodada->numero == 1)
                                                <h4 class="text-center"><strong>Semi-final</strong></h4>
                                            @else
                                                <h4 class="text-center"><strong>Final</strong></h4>
                                            @endif
                                        </div>
                                        @php($i = 1)
                                        @foreach($rodada->emparceiramentos->all() as $emparceiramento)
                                            <!-- EMPARCEIRAMENTO {{$emparceiramento->id}} -->
                                            <div class=" @if($rodada->numero == 1) col-xs-6 col-sm-4 col-sm-offset-{{$i++}} @else col-xs-12 col-sm-10 col-sm-offset-{{$i++}} @endif">
                                                <div class="emparceiramento text-center">
                                                    <div id="emparceiramento_{{$emparceiramento->id}}_enxadrista_a" class="center-block @if($emparceiramento->cor_a == 1) enxadrista_white @else @if($emparceiramento->cor_a == 2) enxadrista_black @else enxadrista_without_color @endif @endif">
                                                        <strong>@if($emparceiramento->inscricao_a) {{$emparceiramento->inscricao_A->enxadrista->name}} @if($evento->is_chess_com) ({{$emparceiramento->inscricao_A->enxadrista->chess_com_username}}) @endif  <span id="emparceiramento_{{$emparceiramento->id}}_a_trofeu" class=" @if($emparceiramento->resultado != -1) display-none @endif "><i class="fa fa-trophy"></i></span>  @else @if($rodada->numero == 2) Vencedor Jogo 1 @else - @endif @endif</strong>
                                                    </div>
                                                    @if($emparceiramento->inscricao_a) <div class="resultados_confrontos"><div class="resultado">{{$emparceiramento->getResultadoA()}}</div><br/></div>@endif
                                                    <i class="fa fa-times center-block"></i>
                                                    @if($emparceiramento->inscricao_b) <div class="resultados_confrontos"><div class="resultado">{{$emparceiramento->getResultadoB()}}</div><br/></div>@endif
                                                    <div id="emparceiramento_{{$emparceiramento->id}}_enxadrista_b" class="center-block @if($emparceiramento->cor_b == 1) enxadrista_white @else @if($emparceiramento->cor_b == 2) enxadrista_black @else enxadrista_without_color @endif @endif">
                                                        <strong>@if($emparceiramento->inscricao_b) {{$emparceiramento->inscricao_B->enxadrista->name}}  @if($evento->is_chess_com) ({{$emparceiramento->inscricao_B->enxadrista->chess_com_username}}) @endif  <span id="emparceiramento_{{$emparceiramento->id}}_b_trofeu" class=" @if($emparceiramento->resultado != 1) display-none @endif "><i class="fa fa-trophy"></i></span>  @else @if($rodada->numero == 2) Vencedor Jogo 2 @else - @endif @endif</strong>
                                                    </div>

                                                        @foreach($emparceiramento->armageddons->all() as $armageddon)
                                                            <h5><strong>Desempate:</strong></h5>
                                                            <div id="emparceiramento_{{$armageddon->id}}_enxadrista_a" class="center-block @if($armageddon->cor_a == 1) enxadrista_white @else @if($armageddon->cor_a == 2) enxadrista_black @else enxadrista_without_color @endif @endif">
                                                                <strong>@if($armageddon->inscricao_a) {{$armageddon->inscricao_A->enxadrista->name}}  <span id="emparceiramento_{{$armageddon->id}}_a_trofeu" class=" @if($armageddon->resultado != -1) display-none @endif "><i class="fa fa-trophy"></i></span>  @else - @endif</strong>
                                                            </div><br/>
                                                            @if($armageddon->inscricao_a) <div class="resultados_confrontos"><div class="resultado">{{$armageddon->getResultadoA()}}</div><br/></div>@endif
                                                            <i class="fa fa-times center-block"></i>
                                                            @if($armageddon->inscricao_b) <div class="resultados_confrontos"><div class="resultado">{{$armageddon->getResultadoB()}}</div><br/></div>@endif
                                                            <div id="emparceiramento_{{$armageddon->id}}_enxadrista_b" class="center-block @if($armageddon->cor_b == 1) enxadrista_white @else @if($armageddon->cor_b == 2) enxadrista_black @else enxadrista_without_color @endif @endif">
                                                                <strong>@if($armageddon->inscricao_b) {{$armageddon->inscricao_B->enxadrista->name}}  <span id="emparceiramento_{{$armageddon->id}}_b_trofeu" class=" @if($armageddon->resultado != 1) display-none @endif "><i class="fa fa-trophy"></i></span>  @else - @endif</strong>
                                                            </div>
                                                        @endforeach
                                                </div>
                                                @if($rodada->numero < 2)
                                                    <div class="text-center arrows">
                                                        <i class="fa fa-arrow-circle-down"></i>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
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
            ]
        });
    });
</script>
@endsection

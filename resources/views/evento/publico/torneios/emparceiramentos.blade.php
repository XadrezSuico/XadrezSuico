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
                    @if($torneio->tipo_torneio_id == 3)
                        @include("evento.publico.torneios.torneio_3.index")
                    @else
                        @include("evento.publico.torneios.torneio_default.index")
                    @endif
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

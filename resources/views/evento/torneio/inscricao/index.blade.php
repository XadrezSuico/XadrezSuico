@extends('adminlte::page')

@section('title', 'Evento #'.$evento->id." - Torneio #".$torneio->id." - Inscrições")

@section('content_header')
    <h1>Evento #{{$evento->id}} ({{$evento->name}}) - Torneio #{{$torneio->id}} - Inscrições</h1>
@stop

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    <ul class="nav nav-pills">
        <li role="presentation"><a href="{{url("/evento/dashboard/".$evento->id."?tab=torneio")}}">Voltar à Lista de Torneios</a></li>
        @if(
            \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
            \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
			\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
        )
            <li role="presentation"><a href="{{url("/inscricao/".$evento->id)}}">Nova Inscrição ou Confirmar Inscrições</a></li>
        @endif
    </ul>

    <div class="box">
        <div class="box-body">
            <table id="tabela" class="table-responsive table-condensed table-striped" style="width: 100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        @if($evento->tipo_rating) <th>Rating</th> @endif
                        @if($evento->usa_fide) 
                            <th>ID FIDE</th>
                            <th>Rating FIDE</th>
                        @endif
                        @if($evento->usa_cbx) 
                            <th>ID CBX</th>
                            <th>Rating CBX</th>
                        @endif
                        @if($evento->usa_lbx)
                            <th>ID LBX</th>
                            <th>Rating LBX</th>
                        @endif
                        <th>Categoria</th>
                        <th>Cidade</th>
                        <th>Clube</th>
                        <th>Confirmado?</th>
                        <th>Data e Hora</th>
                        <th>Opções</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inscricoes as $inscricao)
                        <tr>
                            <td>{{$inscricao->id}}</td>
                            <td>#{{$inscricao->enxadrista->id}} - <a href="{{url("/enxadrista/edit/".$inscricao->enxadrista->id)}}" target="_blank">{{$inscricao->enxadrista->name}}</a></td>
                            @if($evento->tipo_rating) <td>@if($inscricao->enxadrista->ratings()->where([["tipo_ratings_id","=",$evento->tipo_rating->tipo_ratings_id]])->count() > 0) {{$inscricao->enxadrista->ratings()->where([["tipo_ratings_id","=",$evento->tipo_rating->tipo_ratings_id]])->first()->valor}} @else Não Há @endif</td> @endif
                            @if($evento->usa_fide) 
                                <td>{{$inscricao->enxadrista->fide_id}}</td> 
                                <td>{{$inscricao->enxadrista->showRating(0,$evento->tipo_modalidade)}}</td> 
                            @endif
                            @if($evento->usa_cbx) 
                                <td>{{$inscricao->enxadrista->cbx_id}}</td> 
                                <td>{{$inscricao->enxadrista->showRating(1,$evento->tipo_modalidade)}}</td> 
                            @endif
                            @if($evento->usa_lbx) 
                                <td>{{$inscricao->enxadrista->lbx_id}}</td> 
                                <td>{{$inscricao->enxadrista->showRating(2,$evento->tipo_modalidade)}}</td> 
                            @endif
                            <td>{{$inscricao->categoria->name}}</td>
                            <td>{{$inscricao->cidade->name}}</td>
                            <td>@if($inscricao->clube) {{$inscricao->clube->name}} @else Sem Clube @endif</td>
                            <td>@if($inscricao->confirmado) Sim @else Não @endif</td>
                            <td>{{$inscricao->getCreatedAt()}}</td>
                            <td>
                            
                                @if(
                                    \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
                                    \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
						            \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
                                )
                                    @if($inscricao->confirmado) <a class="btn btn-default" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/unconfirm/".$inscricao->id)}}" role="button">Desconfirmar</a> @endif
                                    <a class="btn btn-default" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/edit/".$inscricao->id)}}" role="button">Editar</a>
                                    @if($inscricao->isDeletavel()) <a class="btn btn-danger" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/delete/".$inscricao->id)}}" role="button">Apagar</a> @endif
                                @else
                                    <a class="btn btn-default" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/edit/".$inscricao->id)}}" role="button">Visualizar</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
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

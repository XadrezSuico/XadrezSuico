@extends('adminlte::page')

@section('title', 'Evento #'.$evento->id." - Torneio #".$torneio->id." - Inscrições")

@section('content_header')
    <h1>Evento #{{$evento->id}} ({{$evento->name}}) - Torneio #{{$torneio->id}} ({{$torneio->name}}) - Inscrições</h1>
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
                        @if($evento->hasConfig("is_team_tournament"))
                            <th>Clube/Time</th>
                            <th>Tabuleiro</th>
                        @endif
                        <th>Nome</th>
                        @if($evento->tipo_rating) <th>Rating</th> @endif
                        @if($evento->is_lichess || $evento->is_lichess_integration) <th>Usuário Lichess.org</th> @endif
                        @if($evento->is_lichess_integration)
                            <th>Está no Time do Lichess.org?</th>
                            <th>Inscrito Lichess.org?</th>
                        @endif
                        @if($evento->is_chess_com) <th>Usuário Chess.com</th> @endif
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
                        @if(!$evento->hasConfig("is_team_tournament"))
                            <th>Clube</th>
                        @endif
                        <th>Confirmado?</th>
                        @if($torneio->software->isChessCom())
                            <th>Foi reconhecida inscrição no Torneio?</th>
                        @endif
                        @if($evento->classifica)
                            <th>Permite Classificar?</th>
                        @endif
                        <th>Data e Hora</th>
                        <th>Opções</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inscricoes as $inscricao)
                        <tr>
                            <td>{{$inscricao->id}}</td>
                            @if($evento->hasConfig("is_team_tournament"))
                                <td>@if($inscricao->clube) {{$inscricao->clube->name}} @else Sem Clube @endif</td>
                                <td>{{ ($inscricao->hasConfig("team_order")) ? $inscricao->getConfig("team_table",true) : '' }}</td>
                            @endif
                            <td>#{{$inscricao->enxadrista->id}} - <a href="{{url("/enxadrista/edit/".$inscricao->enxadrista->id)}}" target="_blank">{{$inscricao->enxadrista->name}}</a></td>
                            @if($evento->tipo_rating) <td>{{$inscricao->enxadrista->ratingParaEvento($evento->id)}}</td> @endif
                            @if($evento->is_lichess || $evento->is_lichess_integration) <td>{{$inscricao->enxadrista->lichess_username}}</td> @endif
                            @if($evento->is_lichess_integration)
                                <td>@if($inscricao->is_lichess_team_found) Sim @else <strong><span style="color:red">Não</span></strong> @endif</td>
                                <td>@if($inscricao->is_lichess_found) Sim @else <strong><span style="color:red">Não</span></strong> @endif</td>
                            @endif
                            @if($evento->is_chess_com)
                                <td>
                                    @if($inscricao->hasConfig("chesscom_username"))
                                        {{$inscricao->getConfig("chesscom_username",true)}}
                                    @else
                                        <strong>{{$inscricao->enxadrista->chess_com_username}}</strong>
                                    @endif
                                </td>
                            @endif
                            @if($evento->usa_fide)
                                <td>{{$inscricao->enxadrista->fide_id}}</td>
                                <td>{{$inscricao->enxadrista->showRating(0,$evento->tipo_modalidade, $evento->getConfig("fide_sequence"))}}</td>
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
                            @if(!$evento->hasConfig("is_team_tournament"))
                                <td>@if($inscricao->clube) {{$inscricao->clube->name}} @else Sem Clube @endif</td>
                            @endif
                            <td>@if($inscricao->confirmado) Sim @else Não @endif</td>
                            @if($torneio->software->isChessCom())
                                <td>
                                    @if($inscricao->hasConfig("chesscom_registration_found"))
                                        <strong>Sim</strong>
                                    @else
                                        Não
                                    @endif
                                </td>
                            @endif
                            @if($evento->classifica)
                                <td>@if(!$inscricao->desconsiderar_classificado) Sim @else Não @endif</td>
                            @endif
                            <td  data-sort='{{$inscricao->created_at}}'>{{$inscricao->getCreatedAt()}}</td>
                            <td>

                                @if(
                                    \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
                                    \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
						            \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
                                )
                                    @if($inscricao->torneio->evento->is_lichess_integration)
                                        @if(!$inscricao->is_lichess_found)
                                            <a href="https://api.whatsapp.com/send?phone=55{{$inscricao->enxadrista->celular}}&text=Olá {{$inscricao->enxadrista->name}}! Você preencheu sua inscrição para o {{$inscricao->torneio->evento->name}}, mas falta prosseguir com sua inscrição no Lichess.org. Favor seguir os passos em: {{$inscricao->getLichessProcessLink()}}. Este link serve para se inscrever na etapa no Lichess.org e vincular à sua inscrição do formulário. Qualquer dúvida, estamos à disposição." class="btn btn-success" target="_blank">
                                                <strong>Enviar Mensagem no<br/>Whatsapp sobre a<br/>Inscrição no Lichess.org</strong>
                                            </a><br/>
                                            @if(!$inscricao->is_whatsapp_sent)
                                                @if(env("TWILIO_SID",false) && env("TWILIO_TOKEN",false))
                                                    <a class="btn btn-default" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/whatsapp/".$inscricao->id)}}" role="button">Enviar Alerta Via Whatsapp</a>
                                                @endif
                                            @endif
                                        @endif
                                    @endif
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

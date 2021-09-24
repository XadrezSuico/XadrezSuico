@extends('adminlte::page')

@section("title", "Lichess.org: Inscrever no Torneio")

@section('content_header')
  <h1>Lichess.org: Inscrever no Torneio</h1>
@stop


@section('css')
	<style>
		.display-none, .displayNone{
			display: none;
		}

		.box-title.evento{
			font-size: 2.5rem;
			font-weight: bold;
		}
		#texto_pesquisa{
			font-size: 2rem;
		}
		#processo_inscricao .box-body{
			min-height: 500px;
		}
		#pesquisa{
			min-height: 400px;
		}
		#pesquisa ul li{
			font-size: 1.5rem;
		}
		.this_is_select2, .select2{
			width: 100% !important;
		}
        #successMessage a{
            color: #fff !important;
        }
	</style>
@endsection

@section("content")

<!-- Main row -->
<ul class="nav nav-pills">
  @if(\Illuminate\Support\Facades\Auth::check())
  	@if(
		\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
		\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($inscricao->torneio->evento->id,[3,4]) ||
		\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($inscricao->torneio->evento->grupo_evento->id,[6])
	)
		<li role="presentation"><a href="/evento/dashboard/{{$inscricao->torneio->evento->id}}"><strong>Gerenciar Evento (ADMIN)</strong></a></li>
	@endif
  @endif
</ul>
<div class="row">
  <!-- Left col -->
  <section class="col-lg-12 connectedSortable">
	<!-- general form elements -->
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title evento">Evento: {{$inscricao->torneio->evento->name}}</h3>
			<div class="pull-right box-tools">
			</div>
		</div>

		<div class="box-body">
            <h3>Dados da Inscrição:</h3>
            <h3>ID da Inscrição: {{$inscricao->id}}</h3>
            <h3>ID do Enxadrista: {{$inscricao->enxadrista->id}}</h3>
            <h3>Nome Completo: {{$inscricao->enxadrista->name}}</h3>
            <h3>Data de Nascimento: {{$inscricao->enxadrista->getNascimentoPublico()}}</h3>
            <h3>Categoria: {{$inscricao->categoria->name}}</h3>
            <hr/>
            @switch($passo)
                @case(1)
                    @if($inscricao->torneio->evento->isLichessDelayToEnter())
                        <h3>É hora de efetuar sua Inscrição no Torneio do Lichess.org. Clique no botão abaixo para ser redirecionado ao Lichess para efetuar login e fornecer acesso ao XadrezSuíço:</h3>
                        <a href="{{url("/inscricao/".$inscricao->uuid."/lichess/redirect")}}" class="btn btn-lg btn-success btn-block">
                            <strong>Logar no Lichess.org</strong>
                        </a><br/>
                        <h1><strong>IMPORTANTE!</strong> O login deve ser efetuado com o login do Lichess.org do enxadrista. Caso você seja um professor e esteja efetuando a inscrição de um aluno, copie o link da página e envie o enxadrista para que possa concluir o processo.</h1>
                    @else
                        <h3>Não é mais possível vincular sua inscrição para jogar este torneio.</h3>
                    @endif
                    @break
                @case(2)
                    @if($inscricao->torneio->evento->isLichessDelayToEnter())
                        <h2>Ótimo, <strong>{{$username}}</strong>!</h2>
                        <h3>Quando você clicar no botão abaixo entenda o que vai acontecer:</h3>
                        <ol>
                            <li><h4>No cadastro de <strong>{{$inscricao->enxadrista->name}}</strong> será atualizado o campo <strong>USUÁRIO DO LICHESS.ORG</strong> para <strong>{{$username}}</strong>;</h4></li>
                            <li><h4>Você será inserido no time <strong>{{$inscricao->torneio->evento->getLichessTeamLink()}}</strong>;</h4></li>
                            <li><h4>Você será inscrito no torneio <strong>{{$inscricao->torneio->evento->getLichessTournamentLink()}}</strong>;</h4></li>
                        </ol>
                        <h3>Você confirma esse procedimento?</h3>
                        <button type="button" class="btn btn-lg btn-success btn-block" data-toggle="modal" data-target="#modal_confirmacao_lichess">
                            <strong>Sim, me inscreva no Torneio do Lichess.org.</strong>
                        </button>
                        <!-- Modal -->
                        <div class="modal modal-warning fade" id="modal_confirmacao_lichess" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="modal_confirmacao_lichess_title" style="text-align: center">ALERTA!</h4>
                                    </div>
                                    <div class="modal-body" style="text-align: center">
                                        <h4>Você tem certeza que o <strong>USUÁRIO DO LICHESS.ORG</strong></h4>
                                        <h2><strong>{{$username}}</strong></h2>
                                        <h4>pertence a</h4>
                                        <h2><strong>{{$inscricao->enxadrista->name}}</strong></h2>?
                                    </div>
                                    <div class="modal-footer">
                                        <a href="{{url("/inscricao/".$inscricao->uuid."/lichess/confirm")}}" class="btn btn-lg btn-success btn-block">
                                            <strong>Sim, me inscreva no Torneio do Lichess.org.</strong>
                                        </a>
                                        <a href="{{url("/inscricao/".$inscricao->uuid."/lichess/clear")}}" class="btn btn-lg btn-danger btn-block">
                                            <strong>Não, me leve novamente para efetuar o login no Lichess.org</strong>
                                        </a><br/>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <br/>
                        <a href="{{url("/inscricao/".$inscricao->uuid."/lichess/clear")}}" class="btn btn-lg btn-danger btn-block">
                            <strong>Não, me leve novamente para efetuar o login no Lichess.org</strong>
                        </a><br/>
                    @else
                        <h3>Não é mais possível vincular sua inscrição para jogar este torneio.</h3>
                    @endif
                    @break
                @case(3)
                    <h2>Tudo certo, <strong>{{$inscricao->enxadrista->lichess_username}}</strong>!</h2>
                    <h3>Sua inscrição foi efetuada com sucesso! Uma confirmação foi enviada ao seu e-mail de cadastro.</h3>
                    <h3>Lembre-se de na data e hora marcada estar na página do torneio: <a href="{{$inscricao->torneio->evento->getLichessTournamentLink()}}">{{$inscricao->torneio->evento->getLichessTournamentLink()}}</a>.</h3>
            @endswitch
	    </div>

	</div>

  </section>
  <!-- /.Left col -->
</div>
<!-- /.row (main row) -->
@endsection

@section("js")
<!-- Morris.js charts -->
<script type="text/javascript" src="{{url("/js/jquery.mask.min.js")}}"></script>
<script type="text/javascript">
	nome_enxadrista = "";
	last_timeOut = 0;
	tipo_documentos = false;
  	$(document).ready(function(){
    });
</script>
@endsection

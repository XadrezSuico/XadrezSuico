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
                    @else
                        <h3>Não é mais possível vincular sua inscrição para jogar este torneio.</h3>
                    @endif
                    @break
                @case(2)
                    @if($inscricao->torneio->evento->isLichessDelayToEnter())
                        <h2>Ótimo, <strong>{{$username}}</strong>!</h2>
                        <h3>Quando você clicar no botão abaixo entenda o que vai acontecer:</h3>
                        <ol>
                            <li>O usuário do Lichess.org de seu cadastro será atualizado para <strong>{{$username}}</strong>;</li>
                            <li>Você será inserido no time <strong>{{$inscricao->torneio->evento->getLichessTeamLink()}}</strong>;</li>
                            <li>Após isto, você será direcionado para o Torneio (<strong>{{$inscricao->torneio->evento->getLichessTournamentLink()}}</strong>) para que finalize sua inscrição.</li>
                        </ol>
                        <h3>Você confirma esse procedimento?</h3>
                        <a href="{{url("/inscricao/".$inscricao->uuid."/lichess/confirm")}}" class="btn btn-lg btn-success btn-block">
                            <strong>Sim, me inscreva no Torneio do Lichess.org.</strong>
                        </a><br/>
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

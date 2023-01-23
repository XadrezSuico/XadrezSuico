@extends('adminlte::page')

@section("title", "Efetuar Inscrição")

@section('content_header')
  <h1>Efetuar Inscrição</h1>
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


        .field-required{
            color: red;
        }

        #successMessage a.btn-default{
            color: green !important;
        }
	</style>
@endsection

@section("content")

<div class="modal fade modal-warning" id="asks" tabindex="-1" role="dialog" aria-labelledby="alerts">
	<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">ATENÇÃO!</h4>
				</div>
				<div class="modal-body">
					<span id="asksMessage"></span>
					<h5>Dados do Enxadrista</h5>
					ID: <strong><span id="asksMessage_id"></span></strong><br/>
					Nome Completo: <strong><span id="asksMessage_name"></span></strong><br/>
					Data de Nascimento: <strong><span id="asksMessage_born"></span></strong><br/>
					Cidade: <strong><span id="asksMessage_city"></span></strong>
					<input type="hidden" id="asksMessage_jaInscrito" />
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" id="naoUsarCadastroEnxadrista">Não, vou conferir os dados e enviar novamente</button>
					<button type="button" class="btn btn-success" id="usarCadastroEnxadrista">Sim, este cadastro é deste enxadrista.</button>
				</div>
			</div>
	</div>
</div>
<div class="modal fade modal-warning" id="novoEstado" tabindex="-1" role="dialog" aria-labelledby="alerts">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Cadastrar Novo Estado</h4>
            </div>
            <div class="modal-body">
				<div class="form-group">
					<label for="estado_pais_id" class="field-required">País *</label>
					<select id="estado_pais_id" class="pais_id this_is_select2 form-control">
						<option value="">--- Selecione um país ---</option>
						@foreach(\App\Pais::all() as $pais)
							<option value="{{$pais->id}}">{{$pais->nome}} @if($pais->codigo_iso) ({{$pais->codigo_iso}}) @endif</option>
						@endforeach
					</select>
				</div>
                <div class="form-group">
                    <label for="name" class="field-required">Nome *</label>
                    <input type="text" name="name" class="form-control" id="estado_nome" placeholder="Insira o Nome Completo da Cidade" required="required">
                </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-warning" data-dismiss="modal">Não Quero Mais</button>
            <button type="button" id="cadastrarEstado" class="btn btn-success">Cadastrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal-warning" id="novaCidade" tabindex="-1" role="dialog" aria-labelledby="alerts">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Cadastrar Nova Cidade</h4>
            </div>
            <div class="modal-body">
				<div class="form-group">
					<label for="cidade_pais_id" class="field-required">País *</label>
					<select id="cidade_pais_id" class="pais_id this_is_select2 form-control">
						<option value="">--- Selecione um país ---</option>
						@foreach(\App\Pais::all() as $pais)
							<option value="{{$pais->id}}">{{$pais->nome}} @if($pais->codigo_iso) ({{$pais->codigo_iso}}) @endif</option>
						@endforeach
					</select>
				</div>
				<div class="form-group">
					<label for="cidade_estados_id" class="field-required">Estado/Província *</label>
					<select id="cidade_estados_id" class="estados_id this_is_select2 form-control">
						<option value="">--- Selecione um país primeiro ---</option>
					</select>
				</div>
                <div class="form-group">
                    <label for="name" class="field-required">Nome *</label>
                    <input type="text" name="name" class="form-control" id="cidade_nome" placeholder="Insira o Nome Completo da Cidade" required="required">
                </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-warning" data-dismiss="modal">Não Quero Mais</button>
            <button type="button" id="cadastrarCidade" class="btn btn-success">Cadastrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal-warning" id="novoClube" tabindex="-1" role="dialog" aria-labelledby="alerts">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Cadastrar Novo Clube/Instituição/Escola</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="name" class="field-required">Nome *</label>
                    <input type="text" name="name" class="form-control" id="clube_nome" placeholder="Insira o Nome Completo do Clube" required="required">
                </div>
				<div class="form-group">
					<label for="clube_pais_id" class="field-required">País *</label>
					<select id="clube_pais_id" class="pais_id this_is_select2 form-control">
						<option value="">--- Selecione um país ---</option>
						@foreach(\App\Pais::all() as $pais)
							<option value="{{$pais->id}}">{{$pais->nome}} @if($pais->codigo_iso) ({{$pais->codigo_iso}}) @endif</option>
						@endforeach
					</select>
				</div>
				<div class="form-group">
					<label for="clube_estados_id" class="field-required">Estado/Província *</label>
					<select id="clube_estados_id" class="estados_id this_is_select2 form-control">
						<option value="">--- Selecione um país primeiro ---</option>
					</select>
				</div>
                <div class="form-group">
                    <label for="clube_cidade_id" class="field-required">Cidade *</label>
                    <select id="clube_cidade_id" class="this_is_select2 form-control">
                        <option value="">--- Selecione uma cidade ---</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-warning" data-dismiss="modal">Não Quero Mais</button>
            <button type="button" id="cadastrarClube" class="btn btn-success">Cadastrar</button>
            </div>
        </div>
    </div>
</div>
<!-- Main row -->
<ul class="nav nav-pills">
  @if(\Illuminate\Support\Facades\Auth::check())
  	@if(
		\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
		\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[3,4]) ||
		\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[6])
	)
		<li role="presentation"><a href="/evento/dashboard/{{$evento->id}}"><strong>Gerenciar Evento (ADMIN)</strong></a></li>
	@endif
  @endif
</ul>
<div class="row">
  <!-- Left col -->
  <section class="col-lg-12 connectedSortable">
	<!-- general form elements -->
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title evento">Evento: {{$evento->name}}</h3>
			<div class="pull-right box-tools">
			</div>
		</div>

		<div class="box-body">
            <button type="button" id="go_to_inscricao" class="btn btn-lg btn-success btn-block">
                <strong>Inscreva-se</strong>
            </button><br/>
            @if($evento->e_permite_visualizar_lista_inscritos_publica)
                <a href="{{url("/inscricao/visualizar/".$evento->id)}}" class="btn btn-lg btn-info btn-block">
                    Visualizar Lista de Inscrições
                </a><br/>
            @endif
            @if($evento->hasTorneiosEmparceiradosByXadrezSuico())
                <a href="{{url("/evento/acompanhar/".$evento->id)}}" class="btn btn-lg btn-primary btn-block">
                    Acompanhe o Emparceiramento do Torneio
                </a><br/>
            @endif
			@if($evento->pagina)
				@if($evento->pagina->imagem) <div style="width: 100%; text-align: center;"><img src="data:image/png;base64,{!!$evento->pagina->imagem!!}" width="100%" style="max-width: 800px"/></div> <br/> @endif
				@if($evento->pagina->texto) {!!$evento->pagina->texto!!} <br/> @endif
				@if($evento->pagina->imagem || $evento->pagina->texto) <hr/> @endif
			@endif
			<strong>Categorias:</strong><br/>
			@foreach($evento->categorias->all() as $categoria)
				{{$categoria->categoria->name}},
			@endforeach<br/>
			<strong>Cidade:</strong> {{$evento->cidade->name}}<br/>
			<strong>Local:</strong> {{$evento->local}}<br/>
			<strong>Data:</strong>
            @if($evento->getDataInicio() == $evento->getDataFim())
                {{$evento->getDataInicio()}}
            @else
                {{$evento->getDataInicio()}} - {{$evento->getDataFim()}}
            @endif<br/>
			<strong>Maiores informações em:</strong> <a href="{{$evento->link}}" target="_blank">{{$evento->link}}</a><br/>
			@if($evento->maximo_inscricoes_evento)
				<hr/>
				<strong>Total de Inscritos até o presente momento:</strong> {{$evento->quantosInscritos()}}.<br/>
				<strong>Limite de Inscritos:</strong> {{$evento->maximo_inscricoes_evento}}.<br/>
				<hr/>
			@endif
			@if($evento->getDataFimInscricoesOnline()) <h3><strong>Inscrições antecipadas até:</strong> {{$evento->getDataFimInscricoesOnline()}}.</h3>@endif
		</div>
	</div>

	<div class="box box-primary" id="processo_inscricao">
		<div class="box-header">
			<h3 class="box-title">Processo de Inscrição</h3>
			<div class="pull-right box-tools">
			</div>
		</div>
		<div class="box-body">
			<div id="form_pesquisa">
				<div class="alert alert-success" role="alert">
					@if(rand(0,4) == 4) <strong><h4>O processo para inscrição mudou... e para melhor!</h4></strong><br/> @endif
					Comece digitando o nome do(a) enxadrista, e caso o(a) mesmo(a) já possua cadastro, ele irá aparecer logo abaixo em "Resultado da Pesquisa", aí é só clicar no nome do(a) mesmo(a) e continuar o processo.<br/>
					Caso ele(a) não apareça na lista, clique em "O(a) enxadrista não aparece na lista" para efetuar o cadastro dele(a).
				</div>
				<h3>Comece a Inserir o nome ou documento do enxadrista</h3>
				<input type="text" id="texto_pesquisa" class="form-control" placeholder="Comece a digitar o nome ou documento do enxadrista para efetuar a pesquisa..." val="" />
				<hr/>
			</div>
			<div id="pesquisa">
				<h3>Resultado da Pesquisa:</h3>
				<div>
					<p>Comece a digitar o nome do enxadrista para começar a pesquisa...</p>
				</div>
			</div>
			<div id="enxadrista" style="display:none">
                <div class="alert alert-warning" id="alert_new_enxadrista">
                    Você está começando o processo de cadastro de um novo enxadrista.<br/>
                    Lembre-se de fazer isto apenas senão achar o enxadrista na busca!<br/>
                    <hr/>
                    O processo é feito em 5 etapas. Caso ocorram erros de preenchimento, apenas aparecerão no final da 5a etapa.<br/>
                    Na 2a etapa serão pedidos uma lista de documentos, porém é obrigatório informar <strong>AO MENOS um deles</strong>.<br/>
                    Todos os campos com <strong class="field-required">* e em vermelho são obrigatórios</strong>.
                </div>
                <div class="alert alert-warning" style="display:none" id="alert_update_enxadrista">
                    Você está começando o processo de cadastro de atualização do cadastro de um enxadrista.
                    <hr/>
                    O processo é feito em 5 etapas. Caso ocorram erros de preenchimento, apenas aparecerão no final da 5a etapa.<br/>
                    Na 2a etapa serão pedidos uma lista de documentos, porém é obrigatório informar <strong>AO MENOS um deles</strong>.<br/>
                    Todos os campos com <strong class="field-required">* e em vermelho são obrigatórios</strong>.
                </div>
				<h3>Cadastro de Enxadrista:</h3>
				<hr/>
				<div id="enxadrista_1">
					<h4>Passo 1/5 - Dados Básicos:</h4>
					<div class="form-group">
						<label for="name" class="field-required">Nome Completo *</label>
						<input name="name" id="name" class="form-control cadastro_enxadrista_input" type="text" />
					</div>
					<div class="form-group">
						<label for="born" class="field-required">Data de Nascimento *</label>
						<input name="born" id="born" class="form-control cadastro_enxadrista_input" type="text" />
					</div>
					<div class="form-group">
						<label for="sexos_id" class="field-required">Sexo *</label>
						<select id="sexos_id" name="sexos_id" class="form-control this_is_select2 cadastro_enxadrista_select">
							<option value="">--- Selecione ---</option>
							@foreach($sexos as $sexo)
								<option value="{{$sexo->id}}">{{$sexo->name}}</option>
							@endforeach
						</select>
					</div>
					<div class="form-group">
						<label for="pais_nascimento_id" class="field-required">País de Nascimento *</label>
						<select id="pais_nascimento_id" name="pais_nascimento_id" class="form-control this_is_select2 cadastro_enxadrista_select">
							<option value="">--- Selecione um país ---</option>
							@foreach(\App\Pais::all() as $pais)
								<option value="{{$pais->id}}">{{$pais->nome}} @if($pais->codigo_iso) ({{$pais->codigo_iso}}) @endif</option>
							@endforeach
						</select>
					</div>
				</div>
				<div id="enxadrista_2" style="display:none">
					<h4>Passo 2/5 - Documentos:</h4>
					<div class="alert alert-warning">
						<strong>É OBRIGATÓRIO informar ao menos um documento.</strong> Além disto, poderá haver documentos que são obrigatórios, porém, estes estarão identificados com <strong>*</strong>.<br/>
						<br/>
						O documento informado será <strong>utilizado a fim de Confirmação da Inscrição</strong> - Então informe <strong>documento(s) válido(s)</strong> dentre os que estão listados.
					</div>
					<div id="documentos">
						<p>Não há documentos para este país.</p>
					</div>
				</div>
				<div id="enxadrista_3" style="display:none">
					<h4>Passo 3/5 - Outras Informações:</h4>
					<div class="row">
						<div class="col-md-12">
                            <div class="alert alert-warning" role="alert">
                                <div class="form-group">
                                    <label for="email" class="field-required">E-mail *</label>
                                    <input name="email" id="email" class="form-control cadastro_enxadrista_input" type="text" />
                                </div>
                                <p><strong>IMPORTANTE!</strong> Prefira por <u>um e-mail do <strong>Gmail</strong></u>, visto que os e-mails da Microsoft (Hotmail.com, Outlook.com, Live.com e etc) possuem dificuldades para recebimento dos e-mails.</p>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="pais_celular_id" class="field-required">País do Celular *</label>
								<select id="pais_celular_id" name="pais_celular_id" class="form-control this_is_select2 cadastro_enxadrista_select">
									<option value="">--- Selecione um país ---</option>
									@foreach(\App\Pais::all() as $pais)
										<option value="{{$pais->id}}">{{$pais->nome}} @if($pais->codigo_iso) ({{$pais->codigo_iso}}) @endif</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="celular" class="field-required">Celular *</label>
								<input name="celular" id="celular" class="form-control cadastro_enxadrista_input" type="text" />
							</div>
						</div>
					</div>
				</div>
				<div id="enxadrista_4" style="display:none">
					<h4>Passo 4/5 - Cadastros nas Entidades e Plataformas Online:</h4>
					<div class="alert alert-warning">
						Caso o(a) enxadrista possua cadastro na CBX (Confederação Brasileira de Xadrez), FIDE (Federação Internacional de Xadrez) ou então na LBX (Liga Brasileira de Xadrez) é indispensável a informação dos códigos referentes a cada entidade para que seja utilizado o Rating do(a) Enxadrista para os Torneios, de acordo com cada Evento.
					</div>
					@if($evento->calcula_cbx && !$evento->calcula_fide && !$evento->cbx_required && !$evento->fide_required)
						<!-- Calcula CBX -->
						<div class="alert alert-danger">
							Este evento <strong>calcula Rating CBX</strong>, portanto é obrigatória a inserção do ID de Cadastro junto à CBX. Caso ainda não possua, acesse
							<a href="http://cbx.com.br/cadastro">http://cbx.com.br/cadastro</a> e efetue o seu cadastro antes de efetuar a inscrição.<br/>
							<strong>IMPORTANTE!</strong> O cadastro junto a CBX demora até 48 horas úteis para gerar o seu ID.<br/>
							@if($evento->tipo_modalidade == 0)
								<strong>TAMBÉM É IMPORTANTE!</strong> Para este evento <strong>É NECESSÁRIO</strong> pagar a ANUIDADE da CBX.
							@else
								<strong>TAMBÉM É IMPORTANTE!</strong> Para este evento NÃO É NECESSÁRIO pagar a ANUIDADE da CBX.
							@endif
						</div>
					@else
						@if(!$evento->calcula_cbx && $evento->calcula_fide && !$evento->cbx_required && !$evento->fide_required)
							<!-- Calcula FIDE -->
							<div class="alert alert-danger">
								Este evento <strong>calcula Rating FIDE</strong>, portanto para jogadores Brasileiros é obrigatória a inserção do ID de Cadastro junto à CBX,
								e no caso de jogadores Estrangeiros, é obrigatório que o mesmo <strong>ID FIDE</strong> para poder jogar este evento.<br/>
								Caso seja Brasileiro e não possua cadastro junto à CBX, acesse <a href="http://cbx.com.br/cadastro">http://cbx.com.br/cadastro</a>
								e efetue o seu cadastro antes de efetuar a inscrição.<br/>
								<strong>IMPORTANTE!</strong> O cadastro junto a CBX demora até 48 horas úteis para gerar o seu ID.<br/>
								@if($evento->tipo_modalidade == 0)
									<strong>TAMBÉM É IMPORTANTE!</strong> Para este evento <strong>É NECESSÁRIO</strong> pagar a ANUIDADE da CBX.
								@else
									<strong>TAMBÉM É IMPORTANTE!</strong> Para este evento NÃO É NECESSÁRIO pagar a ANUIDADE da CBX.
								@endif
							</div>
						@else
							@if($evento->calcula_cbx && $evento->calcula_fide && !$evento->cbx_required && !$evento->fide_required)
								<!-- Calcula CBX e FIDE -->
								<div class="alert alert-danger">
									Este evento <strong>calcula Rating FIDE</strong> e também <strong>calcula Rating CBX</strong>, portanto para jogadores Brasileiros é obrigatória a inserção do ID de Cadastro junto à CBX,
									e no caso de jogadores Estrangeiros, é obrigatório que o mesmo informe seu <strong>ID FIDE</strong> e também o seu <strong>ID CBX</strong> para poder jogar este evento.<br/>
									Caso seja Brasileiro e não possua cadastro junto à CBX, acesse <a href="http://cbx.com.br/cadastro">http://cbx.com.br/cadastro</a>
									e efetue o seu cadastro antes de efetuar a inscrição.<br/>
									<strong>IMPORTANTE!</strong> O cadastro junto a CBX demora até 48 horas úteis para gerar o seu ID.<br/>
									@if($evento->tipo_modalidade == 0)
										<strong>TAMBÉM É IMPORTANTE!</strong> Para este evento <strong>É NECESSÁRIO</strong> pagar a ANUIDADE da CBX.
									@else
										<strong>TAMBÉM É IMPORTANTE!</strong> Para este evento NÃO É NECESSÁRIO pagar a ANUIDADE da CBX.
									@endif
								</div>
							@else
								@if(!$evento->calcula_cbx && !$evento->calcula_fide && $evento->cbx_required && !$evento->fide_required)
									<!-- Obrigatório informar ID CBX -->
									<div class="alert alert-danger">
										Para este evento <strong>é obrigatório a inserção</strong> do ID de Cadastro junto à CBX. Caso ainda não possua, acesse
										<a href="http://cbx.com.br/cadastro">http://cbx.com.br/cadastro</a> e efetue o seu cadastro antes de efetuar a inscrição.<br/>
										<strong>IMPORTANTE!</strong> O cadastro junto a CBX demora até 48 horas úteis para gerar o seu ID.<br/>
										@if($evento->tipo_modalidade == 0)
											<strong>TAMBÉM É IMPORTANTE!</strong> Para este evento <strong>É NECESSÁRIO</strong> pagar a ANUIDADE da CBX.
										@else
											<strong>TAMBÉM É IMPORTANTE!</strong> Para este evento NÃO É NECESSÁRIO pagar a ANUIDADE da CBX.
										@endif
									</div>
								@else
									@if(!$evento->calcula_cbx && !$evento->calcula_fide && !$evento->cbx_required && $evento->fide_required)
										<!-- Obrigatório informar ID FIDE -->

										<div class="alert alert-danger">
											Para este evento <strong>é obrigatório</strong> para jogadores Brasileiros a inserção do ID de Cadastro junto à CBX,
											e no caso de jogadores Estrangeiros, é obrigatório que o mesmo <strong>ID FIDE</strong> para poder jogar este evento.<br/>
											Caso seja Brasileiro e não possua cadastro junto à CBX, acesse <a href="http://cbx.com.br/cadastro">http://cbx.com.br/cadastro</a>
											e efetue o seu cadastro antes de efetuar a inscrição.<br/>
											<strong>IMPORTANTE!</strong> O cadastro junto a CBX demora até 48 horas úteis para gerar o seu ID.<br/>
											@if($evento->tipo_modalidade == 0)
												<strong>TAMBÉM É IMPORTANTE!</strong> Para este evento <strong>É NECESSÁRIO</strong> pagar a ANUIDADE da CBX.
											@else
												<strong>TAMBÉM É IMPORTANTE!</strong> Para este evento NÃO É NECESSÁRIO pagar a ANUIDADE da CBX.
											@endif
										</div>
									@else
										@if(!$evento->calcula_cbx && !$evento->calcula_fide && $evento->cbx_required && $evento->fide_required)
											<!-- Obrigatório informar ID CBX e FIDE -->

											<div class="alert alert-danger">
												Para este evento <strong>é obrigatório</strong> para jogadores Brasileiros a inserção do ID de Cadastro junto à CBX,
												e no caso de jogadores Estrangeiros, é obrigatório que o mesmo <strong>ID FIDE</strong> para poder jogar este evento.<br/>
												Caso seja Brasileiro e não possua cadastro junto à CBX, acesse <a href="http://cbx.com.br/cadastro">http://cbx.com.br/cadastro</a>
												e efetue o seu cadastro antes de efetuar a inscrição.<br/>
												<strong>IMPORTANTE!</strong> O cadastro junto a CBX demora até 48 horas úteis para gerar o seu ID.<br/>
												@if($evento->tipo_modalidade == 0)
													<strong>TAMBÉM É IMPORTANTE!</strong> Para este evento <strong>É NECESSÁRIO</strong> pagar a ANUIDADE da CBX.
												@else
													<strong>TAMBÉM É IMPORTANTE!</strong> Para este evento NÃO É NECESSÁRIO pagar a ANUIDADE da CBX.
												@endif
											</div>
										@endif
									@endif
								@endif
							@endif
						@endif
					@endif
					<div class="row mb-2">
						<div class="col-md-4">
							<div class="form-group">
								<label for="cbx_id" id="cbx_id_label">ID CBX <span id="cbx_required" style="display:none">*</span></label>
								<input name="cbx_id" id="cbx_id" class="form-control cadastro_enxadrista_input" type="text" />
								É possível efetuar a pesquisa de ID CBX pelo site <a href="http://cbx.com.br" target="_blank">http://cbx.com.br</a> - Barra Lateral Direita - Buscar Jogadores.<br/>
								<hr/>
								Caso possua alguma dúvida sobre como encontrar, confira o vídeo tutorial <a href="https://youtu.be/csFzNDomNcw" target="_blank">clicando aqui</a>.
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="fide_id" id="fide_id_label">ID FIDE <span id="fide_required" style="display:none">*</span></label>
								<input name="fide_id" id="fide_id" class="form-control cadastro_enxadrista_input" type="text" />
								É possível efetuar a pesquisa de ID FIDE pelo site <a href="http://ratings.fide.com" target="_blank">http://ratings.fide.com</a> - Search Database - Clique no nome e procure pelo campo "FIDE ID" na página.<br/>
								<hr/>
								Caso possua alguma dúvida sobre como encontrar, confira o vídeo tutorial <a href="https://youtu.be/14PxrkqXtiA" target="_blank">clicando aqui</a>.
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="lbx_id">ID LBX</label>
								<input name="lbx_id" id="lbx_id" class="form-control cadastro_enxadrista_input" type="text" />
								É possível efetuar a pesquisa de ID LBX pelo site <a href="https://www.talsker.com/" target="_blank">https://www.talsker.com/</a> - Formulário no Topo da Página "Procure por Jogadores" - Utilize o código que aparece no campo LBX.<br/>
								<hr/>
								Caso possua alguma dúvida sobre como encontrar, confira o vídeo tutorial <a href="https://youtu.be/d0a0CS8WROY" target="_blank">clicando aqui</a>.
							</div>
						</div>
                    </div>
					<div class="row mb-2">
						<div class="col-md-4">
							<div class="form-group">
								<label for="lichess_username" id="lichess_username_label">Nome de Usuário - Lichess.org <span id="lichess_required" style="display:none">*</span></label>
								<input name="lichess_username" id="lichess_username" class="form-control cadastro_enxadrista_input" type="text" />
								Site da Plataforma: <a href="http://lichess.org" target="_blank">http://lichess.org</a>.<br/>
                                Tutorial para Cadastro no Lichess.org: <a href="https://youtu.be/FtXj5dHI-Mg" target="_blank">https://youtu.be/FtXj5dHI-Mg</a>
								<hr/>
								{{-- Caso possua alguma dúvida sobre como encontrar, confira o vídeo tutorial <a href="https://youtu.be/csFzNDomNcw" target="_blank">clicando aqui</a>. --}}
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="chess_com_username" id="chess_com_username_label">Nome de Usuário - Chess.com <span id="chess_com_required" style="display:none">*</span></label>
								<input name="chess_com_username" id="chess_com_username" class="form-control cadastro_enxadrista_input" type="text" />
								Site da Plataforma: <a href="http://chess.com" target="_blank">http://chess.com</a>.<br/>
                                Tutorial para Cadastro no Chess.com: <a href="https://youtu.be/H9eJW1Z3qzQ" target="_blank">https://youtu.be/H9eJW1Z3qzQ</a>
								<hr/>
								{{-- Caso possua alguma dúvida sobre como encontrar, confira o vídeo tutorial <a href="https://youtu.be/14PxrkqXtiA" target="_blank">clicando aqui</a>. --}}
							</div>
						</div>
					</div>
				</div>
				<div id="enxadrista_5" style="display:none">
					<h4>Passo 5/5 - Vínculo do Enxadrista</h4>
					<div class="form-group mb-2">
						<label for="pais_id" class="field-required">País do Vínculo *</label>
						<select id="pais_id" name="pais_id" class="form-control this_is_select2 cadastro_enxadrista_select">
							<option value="">--- Selecione um País ---</option>
							@foreach(\App\Pais::all() as $pais)
								<option value="{{$pais->id}}">{{$pais->nome}} @if($pais->codigo_iso) ({{$pais->codigo_iso}}) @endif</option>
							@endforeach
						</select>
					</div>
					<div class="form-group mb-2">
						<label for="estados_id" class="field-required">Estado do Vínculo *</label>
						<select id="estados_id" name="estados_id" class="form-control this_is_select2 cadastro_enxadrista_select">
							<option value="">--- Selecione um país antes ---</option>
						</select>
                    	<button id="estadoNaoCadastradoEnxadrista" onclick="chamaCadastroEstado(0)" class="btn btn-success">O meu estado não está cadastrado</button>
					</div>
					<div class="form-group mb-2">
						<label for="cidade_id" class="field-required">Cidade do Vínculo *</label>
						<select id="cidade_id" name="cidade_id" class="form-control this_is_select2 cadastro_enxadrista_select">
							<option value="">--- Selecione um estado antes ---</option>
						</select>
                    	<button id="cidadeNaoCadastradaEnxadrista" onclick="chamaCadastroCidade(0)" class="btn btn-success">A minha cidade não está cadastrada</button>
					</div>
					<div class="form-group mb-2">
						<label for="clube_id">Clube/Instituição/Escola</label>
						<select id="clube_id" name="clube_id" class="form-control this_is_select2 cadastro_enxadrista_select">
							<option value="">--- Você pode selecionar um clube/instituição/escola ---</option>
							@foreach(\App\Clube::all() as $clube)
								<option value="{{$clube->id}}">{{$clube->cidade->estado->pais->name}}-{{$clube->cidade->estado->name}}/{{$clube->cidade->name}} - {{$clube->name}}</option>
							@endforeach
						</select>
                    	<button id="clubeNaoCadastradoEnxadrista" onclick="chamaCadastroClube(0)" class="btn btn-success">O meu clube/instituição/escola não está cadastrado</button>
					</div>
					<div class="form-group">
						<label><input type="checkbox" id="enxadrista_xadrezsuico_aceito"> Eu aceito o <a href="{{url("/termosdeuso")}}" target="_blank">termo de uso</a> e a <a href="{{url("/politicadeprivacidade")}}" target="_blank">política de privacidade</a> da Plataforma de Gerenciamento de Circuitos de Xadrez - XadrezSuíço - Implementada pela <u>{{env("IMPLEMENTADO_POR")}}</u>.</label>
					</div>
				</div>
			</div>
			<div id="inscricao" style="display:none">
				<h3>Inscrição:</h3>
				<h4>ID: <span id="enxadrista_mostrar_id">Carregando...</span></h4>
				<h4>Nome Completo: <span id="enxadrista_mostrar_nome">Carregando...</span></h4>
				<h4>Data de Nascimento: <span id="enxadrista_mostrar_born">Carregando...</span></h4>
				<h4>ID CBX: <span id="enxadrista_mostrar_id_cbx">Carregando...</span></h4>
				<h4>ID FIDE: <span id="enxadrista_mostrar_id_fide">Carregando...</span></h4>
				<h4>ID LBX: <span id="enxadrista_mostrar_id_lbx">Carregando...</span></h4>
				<hr/>
                <div class="alert alert-warning" role="alert">
                    <h4><strong>Verifique a categoria da inscrição:</strong></h4>
                    <div class="form-group">
                        <label for="inscricao_categoria_id" class="field-required">Categoria *</label>
                        <select id="inscricao_categoria_id" class="this_is_select2 form-control">
                            <option value="">--- Selecione ---</option>
                        </select>
                    </div>
					<label class="field-required"><input type="checkbox" id="inscricao_categoria_conferida"> Categoria conferida *</label><br/>
                </div>
				<div class="form-group">
					<label for="inscricao_pais_id" class="field-required">País *</label>
					<select id="inscricao_pais_id" class="pais_id this_is_select2 form-control">
						<option value="">--- Selecione um país ---</option>
						@foreach(\App\Pais::all() as $pais)
							<option value="{{$pais->id}}">{{$pais->nome}} @if($pais->codigo_iso) ({{$pais->codigo_iso}}) @endif</option>
						@endforeach
					</select>
				</div>
				<div class="form-group">
					<label for="inscricao_estados_id" class="field-required">Estado/Província *</label>
					<select id="inscricao_estados_id" class="estados_id this_is_select2 form-control">
						<option value="">--- Selecione um país primeiro ---</option>
					</select>
                    <button id="estadoNaoCadastradoInscricao" onclick="chamaCadastroEstado(1)" class="btn btn-success">O meu estado não está cadastrado</button>
				</div>
				<div class="form-group">
					<label for="cidade_id" class="field-required">Cidade *</label>
					<select id="inscricao_cidade_id" class="cidade_id this_is_select2 form-control">
						<option value="">--- Selecione um estado primeiro ---</option>
					</select>
                    <button id="cidadeNaoCadastradaInscricao" onclick="chamaCadastroCidade(2)" class="btn btn-success">A minha cidade não está cadastrada</button>
				</div>
				<div class="form-group">
					<label for="clube_id">Clube/Instituição/Escola</label>
					<select id="inscricao_clube_id" class="clube_id this_is_select2 form-control">
						<option value="">--- Você pode escolher um clube/instituição/escola ---</option>
						@foreach(\App\Clube::all() as $clube)
							<option value="{{$clube->id}}">{{$clube->cidade->estado->pais->name}}-{{$clube->cidade->estado->name}}/{{$clube->cidade->name}} - {{$clube->name}}</option>
						@endforeach
					</select>
                    <button id="clubeNaoCadastradoInscricao" onclick="chamaCadastroClube(1)" class="btn btn-success">O meu clube/instituição/escola não está cadastrado</button>
				</div>
				@foreach($evento->campos() as $campo)
					<div class="form-group">
						<label for="campo_personalizado_{{$campo->id}}" @if($campo->is_required) class="field-required" @endif>{{$campo->question}} @if($campo->is_required) * @endif </label>
						<select id="campo_personalizado_{{$campo->id}}" class="campo_personalizado form-control this_is_select2">
							<option value="">--- Selecione uma opção ---</option>
							@foreach($campo->opcoes->all() as $opcao)
								<option value="{{$opcao->id}}">{{$opcao->response}}</option>
							@endforeach
						</select>
					</div>
				@endforeach
				@if($permite_confirmacao)
					<div class="form-group">
							@if(!$evento->is_lichess_integration)
                                @if($evento->data_inicio <= date("Y-m-d",time() + (60*60*24)))
                                    <label><input type="checkbox" id="inscricao_confirmar"> Inscrição Confirmada</label><br/>
                                @endif
                            @endif
							<label><input type="checkbox" id="inscricao_atualizar"> Atualizar Cadastro (Cidade e Clube)</label><br/>
					</div>
				@else
					<div class="form-group">
						<label><input type="checkbox" id="regulamento_aceito"> Eu aceito o @if($evento->grupo_evento->regulamento_link) <a href="{{$evento->grupo_evento->regulamento_link}}" target="_blank"> @endif regulamento do  {{$evento->grupo_evento->name}} @if($evento->grupo_evento->regulamento_link) </a> @endif integralmente.</label><br/>
						<label><input type="checkbox" id="xadrezsuico_aceito"> Eu aceito o <a href="{{url("/termosdeuso")}}" target="_blank">termo de uso</a> e a <a href="{{url("/politicadeprivacidade")}}" target="_blank">política de privacidade</a> da Plataforma de Gerenciamento de Circuitos de Xadrez - XadrezSuíço - Implementada pela <u>{{env("IMPLEMENTADO_POR")}}</u>.</label>
						<label><input type="checkbox" id="imagem_aceito"> Eu (ou Responsável) cedo meus direitos de imagem do Enxadrista aos organizadores do <u>{{$evento->name}}</u> de forma gratuita, a título de divulgação em material impresso ou digital em caráter informativo.</label>
					</div>
				@endif
				<button id="enviar_inscricao" class="btn btn-success">Enviar Inscrição</button>
				<button id="cancelar_inscricao" class="btn btn-danger">Cancelar Inscrição</button>
			</div>
			<div id="confirmacao" style="display:none">
				<h3>Confirmar Inscrição:</h3>
				<h4>ID: <span id="enxadrista_confirmar_id">Carregando...</span></h4>
				<h4>Nome Completo: <span id="enxadrista_confirmar_nome">Carregando...</span></h4>
				<h4>Data de Nascimento: <span id="enxadrista_confirmar_born">Carregando...</span></h4>
				<h4>ID CBX: <span id="enxadrista_confirmar_id_cbx">Carregando...</span></h4>
				<h4>ID FIDE: <span id="enxadrista_confirmar_id_fide">Carregando...</span></h4>
				<h4>ID LBX: <span id="enxadrista_confirmar_id_lbx">Carregando...</span></h4>
				<hr/>
				<input type="hidden" id="enxadrista_id" />
                <div class="alert alert-warning" role="alert">
                    <h4><strong>Verifique a categoria da inscrição:</strong></h4>
                    <div class="form-group">
                        <label for="confirmacao_categoria_id" class="field-required">Categoria *</label>
                        <select id="confirmacao_categoria_id" class="this_is_select2 form-control">
                            <option value="">--- Selecione ---</option>
                        </select>
                    </div>
					<label class="field-required"><input type="checkbox" id="confirmacao_categoria_conferida"> Categoria conferida *</label><br/>
                </div>
				<div class="form-group">
					<label for="confirmacao_pais_id" class="field-required">País *</label>
					<select id="confirmacao_pais_id" class="pais_id this_is_select2 form-control">
						<option value="">--- Selecione um país ---</option>
						@foreach(\App\Pais::all() as $pais)
							<option value="{{$pais->id}}">{{$pais->nome}} @if($pais->codigo_iso) ({{$pais->codigo_iso}}) @endif</option>
						@endforeach
					</select>
				</div>
				<div class="form-group">
					<label for="confirmacao_estados_id" class="field-required">Estado/Província *</label>
					<select id="confirmacao_estados_id" class="estados_id this_is_select2 form-control">
						<option value="">--- Selecione um país primeiro ---</option>
					</select>
                    <button id="estadoNaoCadastradoConfirmacao" onclick="chamaCadastroEstado(2)" class="btn btn-success">O meu estado não está cadastrado</button>
				</div>
				<div class="form-group">
					<label for="confirmacao_cidade_id" class="field-required">Cidade *</label>
					<select id="confirmacao_cidade_id" class="cidade_id this_is_select2 form-control">
						<option value="">--- Selecione um estado primeiro ---</option>
					</select>
                    <button id="cidadeNaoCadastradaConfirmacao" onclick="chamaCadastroCidade(2)" class="btn btn-success">A minha cidade não está cadastrada</button>
				</div>
				<div class="form-group">
					<label for="confirmacao_clube_id">Clube/Instituição/Escola</label>
					<select id="confirmacao_clube_id" class="clube_id this_is_select2 form-control">
						<option value="">--- Você pode escolher um clube/instituição/escola ---</option>
						@foreach(\App\Clube::all() as $clube)
							<option value="{{$clube->id}}">{{$clube->cidade->estado->pais->name}}-{{$clube->cidade->estado->name}}/{{$clube->cidade->name}} - {{$clube->name}}</option>
						@endforeach
					</select>
                    <button id="clubeNaoCadastradoInscricao" onclick="chamaCadastroClube(2)" class="btn btn-success">O meu clube/instituição/escola não está cadastrado</button>
				</div>
				<div class="form-group">
					<label><input type="checkbox" id="atualizar_cadastro_confirmacao"> Atualizar Cadastro (Cidade e Clube)</label><br/>
				</div>
				<button id="confirmar_inscricao" class="btn btn-success">Confirmar Inscrição</button>
				<button id="cancelar_confirmacao" class="btn btn-danger">Cancelar Confirmação</button>
			</div>
		</div>
		<div class="box-footer">
			<div id="enxadrista_footer" style="display:none">
				<div class="progress">
					<div id="barra_progresso_cadastro" class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
						<span class="sr-only">0% Complete</span>
					</div>
				</div>
				<div id="enxadrista_footer_1" class="row">
					<div class="col-md-6" style="text-align: left">
						<button class="btn btn-danger" id="cancelar_cadastro">Cancelar Cadastro</button>
					</div>
					<div class="col-md-6" style="text-align: right">
						<button class="btn btn-success" id="cadastro_passo_2">Próximo Passo - Documentos (2/5)</button>
					</div>
				</div>
				<div id="enxadrista_footer_2" class="row" style="display:none">
					<div class="col-md-6" style="text-align: left">
						<button class="btn btn-warning" id="cadastro_voltar_passo_1">Voltar - Dados Básicos (1/5)</button>
					</div>
					<div class="col-md-6" style="text-align: right">
						<button class="btn btn-success" id="cadastro_passo_3">Próximo Passo - Outras Informações (3/5)</button>
					</div>
				</div>
				<div id="enxadrista_footer_3" class="row" style="display:none">
					<div class="col-md-6" style="text-align: left">
						<button class="btn btn-warning" id="cadastro_voltar_passo_2">Voltar Passo - Documentos (2/5)</button>
					</div>
					<div class="col-md-6" style="text-align: right">
						<button class="btn btn-success" id="cadastro_passo_4">Próximo Passo - Cadastros nas Entidades e Plataformas Online (4/5)</button>
					</div>
				</div>
				<div id="enxadrista_footer_4" class="row" style="display:none">
					<div class="col-md-6" style="text-align: left">
						<button class="btn btn-warning" id="cadastro_voltar_passo_3">Voltar Passo - Outras Informações (3/5)</button>
					</div>
					<div class="col-md-6" style="text-align: right">
						<button class="btn btn-success" id="cadastro_passo_5">Próximo Passo - Vínculo do Enxadrista (5/5)</button>
					</div>
				</div>
				<div id="enxadrista_footer_5" class="row" style="display:none">
					<div class="col-md-6" style="text-align: left">
						<button class="btn btn-warning" id="cadastro_voltar_passo_4">Voltar Passo - Cadastros nas Entidades e Plataformas Online (4/5)</button>
					</div>
					<div class="col-md-6" style="text-align: right">
						<button class="btn btn-success" id="enviar_cadastro">Finalizar Cadastro de Enxadrista</button>
						<button class="btn btn-success" id="enviar_atualizacao" style="display:none">Finalizar Atualização Cadastral</button>
					</div>
				</div>
			</div>
		</div>
	</div>

  </section>
  <!-- /.Left col -->
</div>
<!-- /.row (main row) -->
<input type="hidden" id="temporary_enxadrista_id" class="temporary_enxadrista" />
<input type="hidden" id="temporary_estados_id" class="temporary_enxadrista" />
<input type="hidden" id="temporary_cidade_id" class="temporary_enxadrista" />
<input type="hidden" id="enxadrista_id" />
<input type="hidden" id="inscricao_id" />

<!-- Para processo de confirmação -->
<input type="hidden" id="temporary_confirmacao_categoria_id" class="temporary_confirmacao" />
<input type="hidden" id="temporary_confirmacao_cidade_id" class="temporary_confirmacao" />
<input type="hidden" id="temporary_confirmacao_clube_id" class="temporary_confirmacao" />

<!-- Para saber de onde veio o clique para o cadastro de estado -->
<input type="hidden" id="where_from_cadastro_estado" />

<!-- Para saber de onde veio o clique para o cadastro de cidade -->
<input type="hidden" id="where_from_cadastro_cidade" />

<!-- Para saber de onde veio o clique para o cadastro de clube -->
<input type="hidden" id="where_from_cadastro_clube" />
@endsection

@section("js")
<!-- Morris.js charts -->
<script type="text/javascript" src="{{url("/js/jquery.mask.min.js")}}"></script>
<script type="text/javascript">
	nome_enxadrista = "";
	last_timeOut = 0;
	tipo_documentos = false;
  	$(document).ready(function(){
		$(".this_is_select2").select2();
		$("#born").mask("00/00/0000");

		@if(env("PAIS_DEFAULT"))
			$("#pais_nascimento_id").val({{env("PAIS_DEFAULT")}}).change();
			$("#pais_celular_id").val({{env("PAIS_DEFAULT")}}).change();
			$("#pais_id").val({{env("PAIS_DEFAULT")}}).change();

			Loading.enable(loading_default_animation,10000);
			buscaEstados(0,false,function(){
				mascaraCelular();
				@if(env("ESTADO_DEFAULT"))
					$("#estados_id").val({{env("ESTADO_DEFAULT")}}).change();
				@endif
				buscaCidades(0,function(){
					@if(env("CIDADE_DEFAULT"))
						$("#cidade_id").val({{env("CIDADE_DEFAULT")}}).change();
					@endif
					Loading.destroy();
				});
			});
			setTimeout(function(){
				confereCadastrosEntidades();
			},300);
		@endif

		$("#clube_id").select2({
			ajax: {
				url: '{{url("/inscricao/v2/".$evento->id."/busca/clube")}}',
				delay: 250,
				processResults: function (data) {
					return {
						results: data.results
					};
				}
			}
		});

		$("#enxadrista_clube_id").select2({
			ajax: {
				url: '{{url("/inscricao/v2/".$evento->id."/busca/clube")}}',
				delay: 250,
				processResults: function (data) {
					return {
						results: data.results
					};
				}
			}
		});

		$("#inscricao_clube_id").select2({
			ajax: {
				url: '{{url("/inscricao/v2/".$evento->id."/busca/clube")}}',
				delay: 250,
				processResults: function (data) {
					return {
						results: data.results
					};
				}
			}
		});

		$("#texto_pesquisa").on("keyup",function(){
			$("#pesquisa div").html("<div class='loading_circle_div'><span class='loading-circle'></span></div>");
			nome_enxadrista = $("#texto_pesquisa").val();
			if(last_timeOut > 0){
				clearTimeout(last_timeOut);
				console.log("Cancelando TimeOut: ".concat(last_timeOut));
				last_timeOut = 0;
			}
			last_timeOut = setTimeout(function(){
				last_timeOut = 0;
				$.getJSON("{{url("/inscricao/v2/".$evento->id."/busca/enxadrista")}}?q=".concat(nome_enxadrista),function(data){
					html = "";
					for (i = 0; i < data.results.length; i++) {
						@if($user)
							if(data.results[i].status == 0){
								if(data.results[i].permitida_inscricao){
									html = html.concat("<a class='btn btn-default btn-large permitida_inscricao' onclick='selectEnxadrista(").concat(data.results[i].id).concat(",false)'>").concat(data.results[i].name).concat("</a><br/>");
								}else{
									html = html.concat("<a class='btn btn-default btn-large' disabled='disabled'>").concat(data.results[i].name).concat("</a><br/>");
								}
							}else{
								if(data.results[i].status == 1){
									html = html.concat("<a class='btn btn-default btn-large permitida_inscricao' onclick='selectEnxadrista(").concat(data.results[i].id).concat(",false)' title='Efetuar Inscrição'>").concat(data.results[i].name).concat("</a><br/>");
								}else if(data.results[i].status == 2){
                                    @if($evento->data_inicio <= date("Y-m-d",time() + (60*60*24)))
    									html = html.concat("<a class='btn btn-success btn-large permitida_inscricao' onclick='selectConfirmarEnxadrista(").concat(data.results[i].inscricao_id).concat(",false)' title='Confirmar Enxadrista'>").concat(data.results[i].name).concat(" (Inscrito)</a><br/>");
                                    @else
    									html = html.concat("<a class='btn btn-default btn-large' disabled='disabled'>").concat(data.results[i].name).concat("</a><br/>");
                                    @endif
								}else if(data.results[i].status == 3){
									html = html.concat("<a class='btn btn-danger btn-large permitida_inscricao' onclick='enviarDesconfirmacao(").concat(data.results[i].inscricao_id).concat(",false)' title='Desconfirmar Enxadrista'>").concat(data.results[i].name).concat(" (Confirmado)</a><br/>");
								}
							}
						@else
							if(data.results[i].permitida_inscricao){
								html = html.concat("<a class='btn btn-default btn-large permitida_inscricao' onclick='selectEnxadrista(").concat(data.results[i].id).concat(",false)'>").concat(data.results[i].name).concat("</a><br/>");
							}else{
								html = html.concat("<a class='btn btn-default btn-large' disabled='disabled'>").concat(data.results[i].name).concat("</a><br/>");
							}
						@endif
						html = html.concat("Informações: ").concat(data.results[i].text).concat("<br/><br/>");
					}
					if(data.results.length == 0){
						html = html.concat("<p>A pesquisa não retornou resultado.</p><br/>");
					}
					if(!data.hasMore){
						html = html.concat("<a class='btn btn-warning' onclick='novoEnxadrista()'>O(a) enxadrista não aparece na lista. <strong>Clique aqui para cadastrar</strong></a><br/>");
					}else{
						html = html.concat("<p>Há um limte de até 30 nomes por consulta. Para permitir o cadastro de um novo enxadrista é necessário que o nome do enxadrista esteja completo para a pesquisa.</p><br/>");
					}
					$("#pesquisa div").html(html);
				});
			},"1000");
		});

		$("#cancelar_cadastro").on("click",function(){
			Loading.enable(loading_default_animation, 800);
			$(".permitida_inscricao").removeAttr("disabled");
			$(".cadastro_enxadrista_input").removeAttr("disabled");
			$(".cadastro_enxadrista_select").removeAttr("disabled");


			zeraCadastro(true,true);
		});

		$("#cancelar_inscricao").on("click",function(){
			Loading.enable(loading_default_animation, 800);
			$(".permitida_inscricao").removeAttr("disabled");

			zeraInscricao();
		});

		$("#cancelar_confirmacao").on("click",function(){
			Loading.enable(loading_default_animation, 800);
			$(".permitida_inscricao").removeAttr("disabled");

			zeraConfirmacao();
		});

		$("#enviar_inscricao").on("click",function(){
			Loading.enable(loading_default_animation, 10000);

			enviarInscricao();
		});

		$("#enviar_atualizacao").on("click",function(){
			Loading.enable(loading_default_animation, 30000);

			enviarAtualizacaoEnxadrista();
		});

		$("#confirmar_inscricao").on("click",function(){
			Loading.enable(loading_default_animation, 10000);

			enviarConfirmacao();
		});


		$("#usarCadastroEnxadrista").on("click",function(){
			if($("#asksMessage_jaInscrito").val() == 1 || $("#asksMessage_jaInscrito").val() == "true" || $("#asksMessage_jaInscrito").val() == true){
				zeraCadastro(true,true);
				$("#asks").modal('hide');
				$("#alertsMessage").html("O enxadrista já se encontra inscrito para este evento. Caso necessite efetuar alguma alteração, por favor, solicite à equipe do evento.");
				$("#alerts").modal();
			}else{
				$("#barra_progresso_cadastro").css("width","100%");
				zeraCadastro(true,false);
				selectEnxadrista($("#enxadrista_id").val(),function(){
					$("#asksMessage_jaInscrito").val("");
				});
				$("#asks").modal('hide');
			}
		});
		$("#naoUsarCadastroEnxadrista").on("click",function(){
			$("#barra_progresso_cadastro").css("width","80%");
			$("#enxadrista_id").val("");
			$("#asksMessage_jaInscrito").val("");
			$("#asks").modal('hide');
		});

		$("#inscricao_pais_id").on("select2:select",function(){
			Loading.enable(loading_default_animation, 1000);
			buscaEstados(1,true,false);
			verificaLiberaCadastro(1);
		});
		$("#inscricao_estados_id").on("select2:select",function(){
			Loading.enable(loading_default_animation, 800);
			buscaCidades(1,false);
		});


		$("#pais_nascimento_id").on("select2:select",function(){
			Loading.enable(loading_default_animation,10000);
			buscaTipoDocumentos(function(){
				Loading.destroy();
			});
			confereCadastrosEntidades();
		});

		$("#clube_pais_id").on("select2:select",function(){
			Loading.enable(loading_default_animation,10000);
			verificaLiberaCadastro(-1);
			buscaEstados(-1,function(){
				buscaCidades(-1,false);
			})
		});
		$("#pais_id").on("select2:select",function(){
			Loading.enable(loading_default_animation,10000);
			verificaLiberaCadastro(0);
			buscaEstados(0,function(){
				buscaCidades(0,false);
			})
		});
		$("#inscricao_pais_id").on("select2:select",function(){
			Loading.enable(loading_default_animation,10000);
			verificaLiberaCadastro(1);
			buscaEstados(1,function(){
				buscaCidades(1,false);
			})
		});
		$("#confirmacao_pais_id").on("select2:select",function(){
			Loading.enable(loading_default_animation,10000);
			verificaLiberaCadastro(2);
			buscaEstados(2,function(){
				buscaCidades(2,false);
			})
		});

		$("#clube_estados_id").on("select2:select",function(){
			Loading.enable(loading_default_animation,800);
			buscaCidades(-1,false);
		});
		$("#estados_id").on("select2:select",function(){
			Loading.enable(loading_default_animation,800);
			buscaCidades(0,false);
		});
		$("#inscricao_estados_id").on("select2:select",function(){
			Loading.enable(loading_default_animation,800);
			buscaCidades(1,false);
		});
		$("#confirmacao_estados_id").on("select2:select",function(){
			Loading.enable(loading_default_animation,800);
			buscaCidades(2,false);
		});


		$("#cadastro_passo_2").on("click",function(){
			Loading.enable(loading_default_animation, 500);

			$("#enxadrista_1").css("display","none");
			$("#enxadrista_footer_1").css("display","none");
			$("#enxadrista_2").css("display","");
			$("#enxadrista_footer_2").css("display","");
			$("#barra_progresso_cadastro").css("width","20%");
		});

		$("#cadastro_passo_3").on("click",function(){
			Loading.enable(loading_default_animation, 500);

			$("#enxadrista_2").css("display","none");
			$("#enxadrista_footer_2").css("display","none");
			$("#enxadrista_3").css("display","");
			$("#enxadrista_footer_3").css("display","");
			$("#barra_progresso_cadastro").css("width","40%");
		});

		$("#cadastro_passo_4").on("click",function(){
			Loading.enable(loading_default_animation, 500);

			$("#enxadrista_3").css("display","none");
			$("#enxadrista_footer_3").css("display","none");
			$("#enxadrista_4").css("display","");
			$("#enxadrista_footer_4").css("display","");
			$("#barra_progresso_cadastro").css("width","60%");
		});

		$("#cadastro_passo_5").on("click",function(){
			Loading.enable(loading_default_animation, 500);

			$("#enxadrista_4").css("display","none");
			$("#enxadrista_footer_4").css("display","none");
			$("#enxadrista_5").css("display","");
			$("#enxadrista_footer_5").css("display","");
			$("#barra_progresso_cadastro").css("width","80%");
		});




		$("#cadastro_voltar_passo_1").on("click",function(){
			Loading.enable(loading_default_animation, 500);

			$("#enxadrista_2").css("display","none");
			$("#enxadrista_footer_2").css("display","none");
			$("#enxadrista_1").css("display","");
			$("#enxadrista_footer_1").css("display","");
			$("#barra_progresso_cadastro").css("width","0%");
		});

		$("#cadastro_voltar_passo_2").on("click",function(){
			Loading.enable(loading_default_animation, 500);

			$("#enxadrista_3").css("display","none");
			$("#enxadrista_footer_3").css("display","none");
			$("#enxadrista_2").css("display","");
			$("#enxadrista_footer_2").css("display","");
			$("#barra_progresso_cadastro").css("width","20%");
		});

		$("#cadastro_voltar_passo_3").on("click",function(){
			Loading.enable(loading_default_animation, 500);

			$("#enxadrista_4").css("display","none");
			$("#enxadrista_footer_4").css("display","none");
			$("#enxadrista_3").css("display","");
			$("#enxadrista_footer_3").css("display","");
			$("#barra_progresso_cadastro").css("width","40%");
		});

		$("#cadastro_voltar_passo_4").on("click",function(){
			Loading.enable(loading_default_animation, 500);

			$("#enxadrista_5").css("display","none");
			$("#enxadrista_footer_5").css("display","none");
			$("#enxadrista_4").css("display","");
			$("#enxadrista_footer_4").css("display","");
			$("#barra_progresso_cadastro").css("width","60%");
		});

		$("#enviar_cadastro").on("click",function(){
		    Loading.enable(loading_default_animation,10000);
			enviarNovoEnxadrista();
		});





		$("#cadastrarCidade").on("click",function(){
			salvarCadastroCidade();
		});
		$("#cadastrarEstado").on("click",function(){
			salvarCadastroEstado();
		});
		$("#cadastrarClube").on("click",function(){
			salvarCadastroClube();
		});
		$("#go_to_inscricao").on("click",function(){
			goToInscricao();
		});
        @if($go_to_inscricao)
            goToInscricao();
        @endif
  	});

	function novoEnxadrista(){
    	Loading.enable(loading_default_animation, 1000);
        $("#alert_new_enxadrista").css("display","");
        $("#alert_update_enxadrista").css("display","none");
		$("#texto_pesquisa").attr("disabled","disabled");
		$("#form_pesquisa").css("display","none");
		$("#pesquisa").css("display","none");
		$("#enxadrista").css("display","");
		$("#enxadrista_footer").css("display","");

		if($("#pais_nascimento_id").val() > 0){
			buscaTipoDocumentos(function(){
				Loading.destroy();
			});
			confereCadastrosEntidades();
		}


		if($("#pais_id").val() > 0){
			verificaLiberaCadastro(0);
		}

	}
	fields = "";
	function selectEnxadrista(id,callback_on_ok){
    	Loading.enable(loading_default_animation, 10000);
		$("#enxadrista_id").val(id);
		$.getJSON("{{url("/inscricao/v2/".$evento->id."/enxadrista/conferencia")}}/".concat($("#enxadrista_id").val()),function(data){
			if(data.ok == 0){
				$("#alert_new_enxadrista").css("display","none");
				$("#alert_update_enxadrista").css("display","");
				// $(".cadastro_enxadrista_input").attr("disabled","disabled");
				// $(".cadastro_enxadrista_select").attr("disabled","disabled");
				$("#enviar_cadastro").css("display","none");
				$("#enviar_atualizacao").css("display","");

				$("#temporary_enxadrista_id").val(data.fields.id);
				if(data.fields.name){
					$("#name").val(data.fields.name);
					$("#name").attr("disabled","disabled");
				}
				if(data.fields.born){
					$("#born").val(data.fields.born);
					$("#born").attr("disabled","disabled");
				}
				if(data.fields.sexos_id){
					$("#sexos_id").val(data.fields.sexos_id).change();
					$("#sexos_id").attr("disabled","disabled");
				}
				if(data.fields.pais_nascimento_id){
					$("#pais_nascimento_id").val(data.fields.pais_nascimento_id).change();
					$("#pais_nascimento_id").attr("disabled","disabled");
					setTimeout(function(){
						buscaTipoDocumentos();
					},400);
					confereCadastrosEntidades();
				}else{
					@if(env("PAIS_DEFAULT"))
						$("#pais_nascimento_id").val({{env("PAIS_DEFAULT")}}).change();
					@else
						$("#pais_nascimento_id").val("").change();
					@endif
					$("#pais_nascimento_id").removeAttr("disabled");
					setTimeout(function(){
						buscaTipoDocumentos();
					},200);
					confereCadastrosEntidades();
				}
				if(data.fields.cbx_id){
					$("#cbx_id").val(data.fields.cbx_id);
				}else{
					$("#cbx_id").val("");
                }
				if(data.fields.fide_id){
					$("#fide_id").val(data.fields.fide_id);
				}else{
					$("#fide_id").val("");
                }
				if(data.fields.lbx_id){
					$("#lbx_id").val(data.fields.lbx_id);
				}else{
					$("#lbx_id").val("");
                }
				if(data.fields.chess_com_username){
					$("#chess_com_username").val(data.fields.chess_com_username);
				}else{
					$("#chess_com_username").val("");
                }
				if(data.fields.lichess_username){
					$("#lichess_username").val(data.fields.lichess_username);
				}else{
					$("#lichess_username").val("");
                }
				if(data.fields.pais_id){
					setTimeout(function(){
						$("#pais_id").val(data.fields.pais_id).change();
						$("#temporary_estados_id").val(data.fields.estados_id);
						$("#temporary_cidade_id").val(data.fields.cidade_id);
						buscaEstados(0,false,function(){
							$("#estados_id").val($("#temporary_estados_id").val()).change();
							setTimeout(function(){
								buscaCidades(0,function(){
									$("#cidade_id").val($("#temporary_cidade_id").val()).change();
									setTimeout(function(){
										Loading.destroy();
									},500);
								});
							},200);
						});
					},200);
				}
                if(data.fields.clube.id != 0){
                    var newOptionClube = new Option(data.fields.clube.name, data.fields.clube.id, false, false);
                    $('#clube_id').append(newOptionClube).trigger('change');
                    $("#clube_id").val(data.fields.clube.id).change();
                }

				$("#form_pesquisa").css("display","none");
				$("#pesquisa").css("display","none");
				$("#enxadrista").css("display","");
				$("#enxadrista_footer").css("display","");
				$("#inscricao").css("display","none");
				$("#alertsMessage").html(data.message);
				$("#alerts").modal();
			}else{
				$(".cadastro_enxadrista_input").removeAttr("disabled");
				$(".cadastro_enxadrista_select").removeAttr("disabled");

				$("#texto_pesquisa").attr("disabled","disabled");
				$(".permitida_inscricao").attr("disabled","disabled");

				$(".campo_personalizado").val(0).change();
				$("#regulamento_aceito").prop('checked',false);
				$("#regulamento_aceito").removeAttr('checked');
				$("#xadrezsuico_aceito").prop('checked',false);
				$("#xadrezsuico_aceito").removeAttr('checked');
				$("#inscricao_categoria_conferida").removeAttr('checked');
				$("#inscricao_categoria_conferida").prop('checked',false);
				$("#imagem_aceito").prop('checked',false);
				$("#imagem_aceito").removeAttr('checked');

				$.getJSON("{{url("/inscricao/v2/".$evento->id."/enxadrista")}}/".concat($("#enxadrista_id").val()),function(data){
					if(data.ok == 1){
						$("#enxadrista_mostrar_id").html(data.data.id);
						$("#enxadrista_mostrar_nome").html(data.data.name);
						$("#enxadrista_mostrar_born").html(data.data.born);
						$("#enxadrista_mostrar_id_cbx").html(data.data.cbx_id);
						$("#enxadrista_mostrar_id_fide").html(data.data.fide_id);
						$("#enxadrista_mostrar_id_lbx").html(data.data.lbx_id);
						$('#inscricao_categoria_id').html("").trigger('change');
						for (i = 0; i < data.data.categorias.length; i++) {
							var newOptionCategoria = new Option(data.data.categorias[i].name, data.data.categorias[i].id, false, false);
							$('#inscricao_categoria_id').append(newOptionCategoria).trigger('change');
							if(data.data.categorias.length == 1){
								$("#inscricao_categoria_id").val(data.data.categorias[i].id).change();
								$("#inscricao_categoria_id").attr("disabled","disabled").change();
							}else{
								$("#inscricao_categoria_id").removeAttr("disabled").change();
							}
						}
						$("#inscricao_pais_id").val(data.data.cidade.estado.pais.id).change();
						setTimeout(function(){
							buscaEstados(1,false,function(){
								$("#inscricao_estados_id").val(data.data.cidade.estado.id).change();
								setTimeout(function(){
									buscaCidades(1,function(){
										$("#inscricao_cidade_id").val(data.data.cidade.id).change();
										setTimeout(function(){
											Loading.destroy();
										},500);
									});
								},200);
							});
							verificaLiberaCadastro(1);
						},200);

						if(data.data.clube.id != 0){
							var newOptionClube = new Option(data.data.clube.name, data.data.clube.id, false, false);
							$('#inscricao_clube_id').append(newOptionClube).trigger('change');
							$("#inscricao_clube_id").val(data.data.clube.id).change();
						}else{
							$("#inscricao_clube_id").val(0).change();
                        }

						$("#form_pesquisa").css("display","none");
						$("#pesquisa").css("display","none");
						$("#enxadrista").css("display","none");
						$("#inscricao").css("display","");

						if(callback_on_ok){
							callback_on_ok();
						}
					}else{
						$("#texto_pesquisa").removeAttr("disabled");
						$(".permitida_inscricao").removeAttr("disabled","disabled");
						$("#alertsMessage").html(data.message);
						$("#alerts").modal();
						Loading.destroy();
					}
				})
				.fail(function(){
					$("#alertsMessage").html("Ocorreu um erro inesperado. Por favor, tente novamente mais tarde.");
					$("#alerts").modal();
					$("#texto_pesquisa").removeAttr("disabled");
					$(".permitida_inscricao").removeAttr("disabled","disabled");
					Loading.destroy();
				});
			}
		});

	}
	function selectConfirmarEnxadrista(id,callback_on_ok){
    	Loading.enable(loading_default_animation, 10000);
		$("#inscricao_id").val(id);
		$(".cadastro_enxadrista_input").removeAttr("disabled");
		$(".cadastro_enxadrista_select").removeAttr("disabled");

		$("#texto_pesquisa").attr("disabled","disabled");
		$(".permitida_inscricao").attr("disabled","disabled");

		$.getJSON("{{url("/inscricao/v2/".$evento->id."/inscricao/get/")}}/".concat($("#inscricao_id").val()),function(data){
			if(data.ok == 1){
				$("#enxadrista_id").val(data.data.enxadrista_id);
				$("#temporary_confirmacao_categoria_id").val(data.data.categoria.id);
				$("#temporary_confirmacao_cidade_id").val(data.data.cidade.id);
				$("#temporary_confirmacao_clube_id").val(data.data.clube.id);

				$("#enxadrista_confirmar_id").html(data.data.id);
				$("#enxadrista_confirmar_nome").html(data.data.name);
				$("#enxadrista_confirmar_born").html(data.data.born);
				$("#enxadrista_confirmar_id_cbx").html(data.data.cbx_id);
				$("#enxadrista_confirmar_id_fide").html(data.data.fide_id);
				$("#enxadrista_confirmar_id_lbx").html(data.data.lbx_id);
				$('#confirmacao_categoria_id').html("").trigger('change');
				for (i = 0; i < data.data.categorias.length; i++) {
					var newOptionCategoria = new Option(data.data.categorias[i].name, data.data.categorias[i].id, false, false);
					$('#confirmacao_categoria_id').append(newOptionCategoria).trigger('change');
					if(data.data.categorias.length == 1){
						if($("#temporary_confirmacao_categoria_id").val() == data.data.categorias[i].id){
							$('#confirmacao_categoria_id').val($("#temporary_confirmacao_categoria_id").val()).trigger('change');
						}else{
							$('#confirmacao_categoria_id').val(data.data.categorias[i].id).trigger('change');
						}
						$("#confirmacao_categoria_id").attr("disabled","disabled").change();
					}else{
						if(i+1 == data.data.categorias.length){
							$('#confirmacao_categoria_id').val($("#temporary_confirmacao_categoria_id").val()).trigger('change');
						}
						$("#confirmacao_categoria_id").removeAttr("disabled").change();
					}
				}
				$("#confirmacao_pais_id").val(data.data.cidade.estado.pais.id).change();
				setTimeout(function(){
					buscaEstados(2,false,function(){
						$("#confirmacao_estados_id").val(data.data.cidade.estado.id).change();
						setTimeout(function(){
							buscaCidades(2,function(){
								$("#confirmacao_cidade_id").val(data.data.cidade.id).change();
							});
						},200);
					});
					verificaLiberaCadastro(2);
				},200);

				if(data.data.clube.id != 0){
					var newOptionClube = new Option(data.data.clube.name, data.data.clube.id, false, false);
					$('#confirmacao_clube_id').append(newOptionClube).trigger('change');
					$("#confirmacao_clube_id").val(data.data.clube.id).change();
				}else{
                    $("#confirmacao_clube_id").val(0).change();
                }

				$("#form_pesquisa").css("display","none");
				$("#pesquisa").css("display","none");
				$("#enxadrista").css("display","none");
				$("#inscricao").css("display","none");
				$("#confirmacao").css("display","");

				if(callback_on_ok){
					callback_on_ok();
					setTimeout(function(){
						$("#enxadrista_id").val("");
						$("#temporary_confirmacao_categoria_id").val("");
						$("#temporary_confirmacao_cidade_id").val("");
						$("#temporary_confirmacao_clube_id").val("");
						Loading.destroy();
					},1000);
				}
			}else{
				$("#texto_pesquisa").removeAttr("disabled");
				$(".permitida_inscricao").removeAttr("disabled","disabled");
			}

		})
		.fail(function(){
			$("#alertsMessage").html("Ocorreu um erro inesperado. Por favor, tente novamente mais tarde.");
			$("#alerts").modal();
			$("#texto_pesquisa").removeAttr("disabled");
			$(".permitida_inscricao").removeAttr("disabled","disabled");
			Loading.destroy();
		});

	}

	function buscaEstados(place,buscaCidade,callback){
		if(place == -2){
			// CADASTRO DE CIDADE
			$('#cidade_estados_id').html("").trigger('change');
			$.getJSON("{{url("/inscricao/v2/".$evento->id."/busca/estado/")}}/".concat($("#cidade_pais_id").val()),function(data){
				for (i = 0; i < data.results.length; i++) {
					var newOptionEstado = new Option("#".concat(data.results[i].id).concat(" - ").concat(data.results[i].text), data.results[i].id, false, false);
					$('#cidade_estados_id').append(newOptionEstado).trigger('change');
					if(i + 1 == data.results.length){
						if(callback){
							callback();
						}
					}
				}
				if(data.results.length == 0){
					if(callback){
						callback();
					}
				}
			});
		}else if(place == -1){
			// CADASTRO DE CLUBE
			$('#clube_estados_id').html("").trigger('change');
			$.getJSON("{{url("/inscricao/v2/".$evento->id."/busca/estado/")}}/".concat($("#clube_pais_id").val()),function(data){
				for (i = 0; i < data.results.length; i++) {
					var newOptionEstado = new Option("#".concat(data.results[i].id).concat(" - ").concat(data.results[i].text), data.results[i].id, false, false);
					$('#clube_estados_id').append(newOptionEstado).trigger('change');
					if(i + 1 == data.results.length){
						if(callback){
							callback();
						}
						if(buscaCidade){
							buscaCidades(place,false);
						}
					}
				}
				if(data.results.length == 0){
					if(callback){
						callback();
					}
					if(buscaCidade){
						buscaCidades(place,false);
					}
				}
			});
		}else if(place == 0){
			// CADASTRO DE ENXADRISTA
			$('#estados_id').html("").trigger('change');
			$.getJSON("{{url("/inscricao/v2/".$evento->id."/busca/estado/")}}/".concat($("#pais_id").val()),function(data){
				for (i = 0; i < data.results.length; i++) {
					var newOptionEstado = new Option("#".concat(data.results[i].id).concat(" - ").concat(data.results[i].text), data.results[i].id, false, false);
					$('#estados_id').append(newOptionEstado).trigger('change');
					if(i + 1 == data.results.length){
						if(callback){
							callback();
						}
						if(buscaCidade){
							buscaCidades(place,false);
						}
					}
				}
				if(data.results.length == 0){
					if(callback){
						callback();
					}
					if(buscaCidade){
						buscaCidades(place,false);
					}
				}
			});
		}else if(place == 1){
			// INSCRIÇÃO
			$('#inscricao_estados_id').html("").trigger('change');
			$.getJSON("{{url("/inscricao/v2/".$evento->id."/busca/estado/")}}/".concat($("#inscricao_pais_id").val()),function(data){
				for (i = 0; i < data.results.length; i++) {
					var newOptionEstado = new Option("#".concat(data.results[i].id).concat(" - ").concat(data.results[i].text), data.results[i].id, false, false);
					$('#inscricao_estados_id').append(newOptionEstado).trigger('change');
					if(i + 1 == data.results.length){
						if(callback){
							callback();
						}
						if(buscaCidade){
							buscaCidades(place,false);
						}
					}
				}
				if(data.results.length == 0){
					if(callback){
						callback();
					}
					if(buscaCidade){
						buscaCidades(place,false);
					}
				}
			});
		}else if(place == 2){
			// CONFIRMAÇÃO DE INSCRIÇÃO
			$('#confirmacao_estados_id').html("").trigger('change');
			$.getJSON("{{url("/inscricao/v2/".$evento->id."/busca/estado/")}}/".concat($("#confirmacao_pais_id").val()),function(data){
				for (i = 0; i < data.results.length; i++) {
					var newOptionEstado = new Option("#".concat(data.results[i].id).concat(" - ").concat(data.results[i].text), data.results[i].id, false, false);
					$('#confirmacao_estados_id').append(newOptionEstado).trigger('change');
					if(i + 1 == data.results.length){
						if(callback){
							callback();
						}
						if(buscaCidade){
							buscaCidades(place,false);
						}
					}
				}
				if(data.results.length == 0){
					if(callback){
						callback();
					}
					if(buscaCidade){
						buscaCidades(place,false);
					}
				}
			});
		}
	}

	function buscaCidades(place, callback){
		if(place == -1){
			$('#clube_cidade_id').html("").trigger('change');
			$.getJSON("{{url("/inscricao/v2/".$evento->id."/busca/cidade/")}}/".concat($("#clube_estados_id").val()),function(data){
				for (i = 0; i < data.results.length; i++) {
					var newOptionCidade = new Option("#".concat(data.results[i].id).concat(" - ").concat(data.results[i].text), data.results[i].id, false, false);
					$('#clube_cidade_id').append(newOptionCidade).trigger('change');
					if(i + 1 == data.results.length){
						if(callback){
							callback();
						}
					}
				}
				if(data.results.length == 0){
					if(callback){
						callback();
					}
				}
			});
		}else if(place == 0){
			$('#cidade_id').html("").trigger('change');
			$.getJSON("{{url("/inscricao/v2/".$evento->id."/busca/cidade/")}}/".concat($("#estados_id").val()),function(data){
				for (i = 0; i < data.results.length; i++) {
					var newOptionCidade = new Option("#".concat(data.results[i].id).concat(" - ").concat(data.results[i].text), data.results[i].id, false, false);
					$('#cidade_id').append(newOptionCidade).trigger('change');
					if(i + 1 == data.results.length){
						if(callback){
							callback();
						}
					}
				}
				if(data.results.length == 0){
					if(callback){
						callback();
					}
				}
			});
		}else if(place == 1){
			$('#inscricao_cidade_id').html("").trigger('change');
			$.getJSON("{{url("/inscricao/v2/".$evento->id."/busca/cidade/")}}/".concat($("#inscricao_estados_id").val()),function(data){
				for (i = 0; i < data.results.length; i++) {
					var newOptionCidade = new Option("#".concat(data.results[i].id).concat(" - ").concat(data.results[i].text), data.results[i].id, false, false);
					$('#inscricao_cidade_id').append(newOptionCidade).trigger('change');
					if(i + 1 == data.results.length){
						if(callback){
							callback();
						}
					}
				}
				if(data.results.length == 0){
					if(callback){
						callback();
					}
				}
			});
		}else if(place == 2){
			$('#confirmacao_cidade_id').html("").trigger('change');
			$.getJSON("{{url("/inscricao/v2/".$evento->id."/busca/cidade/")}}/".concat($("#confirmacao_estados_id").val()),function(data){
				for (i = 0; i < data.results.length; i++) {
					var newOptionCidade = new Option("#".concat(data.results[i].id).concat(" - ").concat(data.results[i].text), data.results[i].id, false, false);
					$('#confirmacao_cidade_id').append(newOptionCidade).trigger('change');
					if(i + 1 == data.results.length){
						if(callback){
							callback();
						}
					}
				}
				if(data.results.length == 0){
					if(callback){
						callback();
					}
				}
			});
		}
	}

	function verificaLiberaCadastro(place){
		if(place == 0){
			if($("#pais_id").val() == 33){
				$("#estadoNaoCadastradoEnxadrista").css("display","none");
				$("#cidadeNaoCadastradaEnxadrista").css("display","none");

			}else{
				$("#estadoNaoCadastradoEnxadrista").css("display","");
				$("#cidadeNaoCadastradaEnxadrista").css("display","");
			}
		}else if(place == 1){
			if($("#inscricao_pais_id").val() == 33){
				$("#estadoNaoCadastradoInscricao").css("display","none");
				$("#cidadeNaoCadastradaInscricao").css("display","none");

			}else{
				$("#estadoNaoCadastradoInscricao").css("display","");
				$("#cidadeNaoCadastradaInscricao").css("display","");
			}
		}else if(place == 2){
			if($("#confirmacao_pais_id").val() == 33){
				$("#estadoNaoCadastradoConfirmacao").css("display","none");
				$("#cidadeNaoCadastradaConfirmacao").css("display","none");

			}else{
				$("#estadoNaoCadastradoConfirmacao").css("display","");
				$("#cidadeNaoCadastradaConfirmacao").css("display","");
			}
		}
		Loading.destroy();
	}

	function enviarInscricao(){
		var data = "evento_id={{$evento->id}}&enxadrista_id=".concat($("#enxadrista_id").val()).concat("&categoria_id=").concat($("#inscricao_categoria_id").val()).concat("&cidade_id=").concat($("#inscricao_cidade_id").val()).concat("&clube_id=").concat($("#inscricao_clube_id").val());
		@if($permite_confirmacao)
			data = data.concat("&regulamento_aceito=true");
			data = data.concat("&xadrezsuico_aceito=true");
			data = data.concat("&imagem_aceito=true");

			if($("#inscricao_confirmar").is(":checked")){
				data = data.concat("&inscricao_confirmada=true");
			}
			if($("#inscricao_atualizar").is(":checked")){
				data = data.concat("&atualizar_cadastro=true");
			}
		@else
			if($("#regulamento_aceito").is(":checked")){
				data = data.concat("&regulamento_aceito=true");
			}
			if($("#xadrezsuico_aceito").is(":checked")){
				data = data.concat("&xadrezsuico_aceito=true");
			}
			if($("#imagem_aceito").is(":checked")){
				data = data.concat("&imagem_aceito=true");
			}
		@endif
        if($("#inscricao_categoria_conferida").is(":checked")){
            data = data.concat("&categoria_conferida=true");
        }
		@foreach($evento->campos() as $campo)
			data = data.concat("&campo_personalizado_{{$campo->id}}=").concat($("#campo_personalizado_{{$campo->id}}").val());
		@endforeach
			@if(isset($token))
				@if($token != "")
					data = data.concat("&token=").concat("{{$token}}");
				@endif
			@endif
		$.ajax({
			type: "post",
			url: "{{url("/inscricao/v2/".$evento->id."/inscricao")}}",
			data: data,
			dataType: "json",
			success: function(data){
				Loading.destroy();
				if(data.ok == 1){
					zeraInscricao();
					$("#texto_pesquisa").val("");
					$("#pesquisa div").html("");
					$("#inscricao").boxWidget('collapse');
                    if(data.is_lichess_integration == 1){
                        $("#successMessage").html("<strong>Sua inscrição foi efetuada no XadrezSuíço com sucesso!</strong><hr/>");
                        $("#successMessage").html($("#successMessage").html().concat("Você receberá em no máximo 30 minutos uma mensagem no endereço de e-mail do cadastro com a confirmação do preenchimento do formulário. Proceda com a inscrição no Lichess.org:<hr/>"));
                        $("#successMessage").html($("#successMessage").html().concat("Agora você necessita entrar no torneio do Lichess.org para poder jogar:<br/>"));
                        $("#successMessage").html($("#successMessage").html().concat("<a href=\"").concat(data.lichess_process_link).concat("\" class=\"btn btn-lg btn-default btn-block\" target=\"_blank\"><strong>Clique aqui</strong> e continue sua inscrição para o Torneio do Lichess.org</a><hr/>"));
                        @if($evento->e_permite_visualizar_lista_inscritos_publica)
                            //$("#successMessage").html($("#successMessage").html().concat("Caso não receba o e-mail, confira na lista de inscritos (Acessível no topo desta página em 'Visualizar Lista de Inscrições') e verifique se lá consta o nome do(a) enxadrista em questão."));
                            //$("#successMessage").html($("#successMessage").html().concat("Caso não apareça, tente novamente o processo de inscrição ou entre em contato com a organização."));
                        @endif
                        @if($evento->orientacao_pos_inscricao != NULL)
                            $("#successMessage").html($("#successMessage").html().concat("<br/><strong>Orientações pós-inscrição: </strong>"));
                            $("#successMessage").html($("#successMessage").html().concat("{!!preg_replace( "/\"/", "\\\"", preg_replace( "/\r|\n/", "", $evento->orientacao_pos_inscricao))!!}"));
                            $("#successMessage").html($("#successMessage").html().concat("<hr/>"));
                        @endif
                    }else{
                        $("#successMessage").html("<strong>Sua inscrição foi efetuada com sucesso!</strong><hr/>");
                        $("#successMessage").html($("#successMessage").html().concat("Você receberá em no máximo 30 minutos uma mensagem no endereço de e-mail do cadastro com a confirmação da inscrição para este evento.<hr/>"));
                        @if($evento->e_permite_visualizar_lista_inscritos_publica)
                            //$("#successMessage").html($("#successMessage").html().concat("Caso não receba o e-mail, confira na lista de inscritos (Acessível no topo desta página em 'Visualizar Lista de Inscrições') e verifique se lá consta o nome do(a) enxadrista em questão."));
                            //$("#successMessage").html($("#successMessage").html().concat("Caso não apareça, tente novamente o processo de inscrição ou entre em contato com a organização."));
                        @endif
                        @if($evento->orientacao_pos_inscricao != NULL)
                            $("#successMessage").html($("#successMessage").html().concat("<br/><strong>Orientações pós-inscrição: </strong>"));
                            $("#successMessage").html($("#successMessage").html().concat("{!!preg_replace( "/\"/", "\\\"", preg_replace( "/\r|\n/", "", $evento->orientacao_pos_inscricao))!!}"));
                            $("#successMessage").html($("#successMessage").html().concat("<hr/>"));
                        @endif
                    }
					$("#success").modal();
				}else{
					$("#alertsMessage").html(data.message);
					$("#alerts").modal();
				}
			}
		});
	}

	function enviarConfirmacao(){
		var data = "evento_id={{$evento->id}}&inscricao_id=".concat($("#inscricao_id").val()).concat("&categoria_id=").concat($("#confirmacao_categoria_id").val()).concat("&cidade_id=").concat($("#confirmacao_cidade_id").val()).concat("&clube_id=").concat($("#confirmacao_clube_id").val());
		if($("#atualizar_cadastro_confirmacao").is(":checked")){
			data = data.concat("&atualizar_cadastro=true");
		}
		@if(isset($token))
			@if($token != "")
				data = data.concat("&token=").concat("{{$token}}");
			@endif
		@endif

        if($("#confirmacao_categoria_conferida").is(":checked")){
            data = data.concat("&categoria_conferida=true");
        }
		$.ajax({
			type: "post",
			url: "{{url("/inscricao/v2/".$evento->id."/inscricao/confirmar")}}",
			data: data,
			dataType: "json",
			success: function(data){
				Loading.destroy();
				if(data.ok == 1){
					zeraConfirmacao();
					$("#texto_pesquisa").val("");
					$("#pesquisa div").html("");
					if(data.updated == 0){
						$("#successMessage").html("<strong>Sua inscrição foi confirmada com sucesso!</strong>");
					}else if(data.updated == 1){
						$("#successMessage").html("<strong>A inscrição foi confirmada e o cadastro de enxadrista atualizado com sucesso!</strong>");
					}
					$("#success").modal();
				}else{
					$("#alertsMessage").html(data.message);
					$("#alerts").modal();
				}
			}
		});
	}
	function enviarDesconfirmacao(id,callback_on_ok){
    	Loading.enable(loading_default_animation, 10000);
		$("#inscricao_id").val(id);

		$("#texto_pesquisa").attr("disabled","disabled");
		$(".permitida_inscricao").attr("disabled","disabled");

		$.getJSON("{{url("/inscricao/v2/".$evento->id."/inscricao/desconfirmar/")}}/".concat(id),function(data){
			Loading.destroy();

			$("#texto_pesquisa").removeAttr("disabled");
			$(".permitida_inscricao").removeAttr("disabled");

			$("#pesquisa div").html("");
			$("#texto_pesquisa").val("");

			$("#successMessage").html("A inscrição foi desconfirmada com sucesso!");
			$("#success").modal();
		})
		.fail(function(){
			$("#alertsMessage").html("Ocorreu um erro inesperado. Por favor, tente novamente mais tarde.");
			$("#alerts").modal();
			$("#texto_pesquisa").removeAttr("disabled");
			$(".permitida_inscricao").removeAttr("disabled","disabled");
			Loading.destroy();
		});

	}



	function mascaraCelular(){
		$("#celular").unmask();
		if($("#pais_celular_id").val() == 33){
			$("#celular").mask("(00) 00000-0000");
		}
	}

	function zeraInscricao(){
		$("#enxadrista_id").val("");
		$("#enxadrista_mostrar_id").html("");
		$("#enxadrista_mostrar_nome").html("");
		$("#enxadrista_mostrar_born").html("");
		$('#inscricao_categoria_id').html("").trigger('change');
		$("#inscricao_pais_id").val(0).change();


		$('#inscricao_categoria_conferida').prop( "checked", false );
		$("#inscricao").css("display","none");
		$("#form_pesquisa").css("display","");
		$("#pesquisa").css("display","");
		$("#texto_pesquisa").removeAttr("disabled");
	}

	function zeraCadastro(redirect_home,zeraEnxadrista){
		if(zeraEnxadrista){
			$("#enxadrista_id").val("");
		}
		$(".cadastro_enxadrista_input").val("");
		$(".cadastro_enxadrista_select").val("").change();
		$("#barra_progresso_cadastro").css("width","0%");

		$("#enxadrista").css("display","none");
		$("#enxadrista_1").css("display","");
		$("#enxadrista_2").css("display","none");
		$("#enxadrista_3").css("display","none");
		$("#enxadrista_4").css("display","none");
		$("#enxadrista_5").css("display","none");

		$("#enxadrista_footer").css("display","none");
		$("#enxadrista_footer_1").css("display","");
		$("#enxadrista_footer_2").css("display","none");
		$("#enxadrista_footer_3").css("display","none");
		$("#enxadrista_footer_4").css("display","none");
		$("#enxadrista_footer_5").css("display","none");


		@if(env("PAIS_DEFAULT"))
			$("#pais_nascimento_id").val({{env("PAIS_DEFAULT")}}).change();
			$("#pais_celular_id").val({{env("PAIS_DEFAULT")}}).change();
			$("#pais_id").val({{env("PAIS_DEFAULT")}}).change();

			Loading.enable(loading_default_animation,10000);
			buscaEstados(0,false,function(){
				mascaraCelular();
				@if(env("ESTADO_DEFAULT"))
					$("#estados_id").val({{env("ESTADO_DEFAULT")}}).change();
				@endif
				buscaCidades(0,function(){
					@if(env("CIDADE_DEFAULT"))
						$("#cidade_id").val({{env("CIDADE_DEFAULT")}}).change();
					@endif
					Loading.destroy();
				});
			});
			confereCadastrosEntidades();
		@endif

		$("#enviar_cadastro").css("display","");
		$("#enviar_atualizacao").css("display","none");
		$(".temporary_enxadrista").val("");


		if(redirect_home){
			$("#form_pesquisa").css("display","");
			$("#pesquisa").css("display","");
			$("#texto_pesquisa").removeAttr("disabled");
		}
	}


	function zeraConfirmacao(){
		$("#enxadrista_id").val("");
		$("#enxadrista_confirmar_id").html("");
		$("#enxadrista_confirmar_nome").html("");
		$("#enxadrista_confirmar_born").html("");
		$('#confirmacao_categoria_id').html("").trigger('change');
		$("#confirmacao_pais_id").val(0).change();


		$("#confirmacao").css("display","none");
		$("#form_pesquisa").css("display","");
		$("#pesquisa").css("display","");
		$("#texto_pesquisa").removeAttr("disabled");
	}

	function zeraCidade(){
		$("#where_from_cadastro_cidade").val("");
		$("#cidade_pais_id").val("");
		$("#cidade_estados_id").val("");
		$("#cidade_nome").val("");
	}

	function zeraClube(){
		$("#where_from_cadastro_clube").val("");
		$("#clube_pais_id").val("");
		$("#clube_estados_id").val("");
		$("#clube_cidade_id").val("");
		$("#clube_nome").val("");
	}

	function enviarNovoEnxadrista(){
		$("#barra_progresso_cadastro").css("width","90%");

		var data = "name=".concat($("#name").val())
			.concat("&born=").concat($("#born").val())
			.concat("&sexos_id=").concat($("#sexos_id").val())
			.concat("&pais_nascimento_id=").concat($("#pais_nascimento_id").val())
			.concat("&cbx_id=").concat($("#cbx_id").val())
			.concat("&fide_id=").concat($("#fide_id").val())
			.concat("&lbx_id=").concat($("#lbx_id").val())
			.concat("&chess_com_username=").concat($("#chess_com_username").val())
			.concat("&lichess_username=").concat($("#lichess_username").val())
			.concat("&email=").concat($("#email").val())
			.concat("&pais_celular_id=").concat($("#pais_celular_id").val())
			.concat("&celular=").concat($("#celular").val())
			.concat("&cidade_id=").concat($("#cidade_id").val())
			.concat("&clube_id=").concat($("#clube_id").val())
			.concat("&evento_id=").concat({{$evento->id}});

		if($("#enxadrista_xadrezsuico_aceito").is(":checked")){
			data = data.concat("&xadrezsuico_aceito=true");
		}

		if(tipo_documentos){
			for(var i = 0; i < tipo_documentos.length; i++){
				if($("#tipo_documento_".concat(tipo_documentos[i].id)).val() != ""){
					data = data.concat("&tipo_documento_").concat(tipo_documentos[i].id).concat("=").concat($("#tipo_documento_".concat(tipo_documentos[i].id)).val());
				}
			}
		}

		$.ajax({
			type: "post",
			url: "{{url("/inscricao/v2/".$evento->id."/enxadrista/novo")}}",
			data: data,
			dataType: "json",
			success: function(data){
				if(data.ok == 1){
					$("#barra_progresso_cadastro").css("width","100%");
					zeraCadastro(true,false);
					selectEnxadrista(data.enxadrista_id,function(){
						$("#successMessage").html("<strong>O cadastro do enxadrista foi efetuado com sucesso!</strong><hr/>Agora será possível <strong>efetuar a inscrição no evento!</strong>");
						$("#success").modal();
						Loading.destroy();
					});
				}else{
					if(data.ask == 1){
						$("#asksMessage").html(data.message);
						$("#asksMessage_id").html(data.enxadrista_id);
						$("#asksMessage_name").html(data.enxadrista_name);
						$("#asksMessage_city").html(data.enxadrista_city);
						$("#asksMessage_born").html(data.enxadrista_born);
						$("#enxadrista_id").val(data.enxadrista_id);
						$("#asksMessage_jaInscrito").val(data.esta_inscrito);
						$("#asks").modal();
						Loading.destroy();
					}else if(data.registred == 1){
						zeraCadastro(true,false);
						selectEnxadrista(data.enxadrista_id,function(){
							$("#alertsMessage").html(data.message);
							$("#alerts").modal();
						    Loading.destroy();
						});
					}else{
						$("#alertsMessage").html(data.message);
						$("#alerts").modal();
						Loading.destroy();
					}
				}
			}
		});
	}
	function enviarAtualizacaoEnxadrista(){
		$("#barra_progresso_cadastro").css("width","90%");
		var data = "";
		if($("#name").attr("disabled") == "disabled") data
		var data = "name=".concat($("#name").val())
			.concat("&born=").concat($("#born").val())
			.concat("&sexos_id=").concat($("#sexos_id").val())
			.concat("&pais_nascimento_id=").concat($("#pais_nascimento_id").val())
			.concat("&cbx_id=").concat($("#cbx_id").val())
			.concat("&fide_id=").concat($("#fide_id").val())
			.concat("&lbx_id=").concat($("#lbx_id").val())
			.concat("&chess_com_username=").concat($("#chess_com_username").val())
			.concat("&lichess_username=").concat($("#lichess_username").val())
			.concat("&email=").concat($("#email").val())
			.concat("&pais_celular_id=").concat($("#pais_celular_id").val())
			.concat("&celular=").concat($("#celular").val())
			.concat("&cidade_id=").concat($("#cidade_id").val())
			.concat("&clube_id=").concat($("#clube_id").val())
			.concat("&evento_id=").concat({{$evento->id}});

		if(tipo_documentos){
			for(var i = 0; i < tipo_documentos.length; i++){
				if($("#tipo_documento_".concat(tipo_documentos[i].id)).val() != ""){
					data = data.concat("&tipo_documento_").concat(tipo_documentos[i].id).concat("=").concat($("#tipo_documento_".concat(tipo_documentos[i].id)).val());
				}
			}
		}

		$.ajax({
			type: "post",
			url: "{{url("/inscricao/v2/".$evento->id."/enxadrista/atualizacao")}}/".concat($("#temporary_enxadrista_id").val()),
			data: data,
			dataType: "json",
			success: function(data){
				if(data.ok == 1){
					$("#barra_progresso_cadastro").css("width","100%");
					zeraCadastro(true,false);
					selectEnxadrista(data.enxadrista_id,function(){
						$("#successMessage").html("<strong>A atualização de cadastro do enxadrista foi efetuada com sucesso!</strong>");
						$("#success").modal();
						Loading.destroy();
					});
				}else{
					if(data.ask == 1){
						$("#asksMessage").html(data.message);
						$("#asksMessage_id").html(data.enxadrista_id);
						$("#asksMessage_name").html(data.enxadrista_name);
						$("#asksMessage_city").html(data.enxadrista_city);
						$("#asksMessage_born").html(data.enxadrista_born);
						$("#enxadrista_id").val(data.enxadrista_id);
						$("#asksMessage_jaInscrito").val(data.esta_inscrito);
						$("#asks").modal();
						Loading.destroy();
					}else if(data.registred == 1){
						zeraCadastro(true,false);
						selectEnxadrista(data.enxadrista_id,function(){
							$("#barra_progresso_cadastro").css("width","100%");
							$("#alertsMessage").html(data.message);
							$("#alerts").modal();
							Loading.destroy();
							setTimeout(function(){
								$("#barra_progresso_cadastro").css("width","0%");
							});
						});
					}else{
						$("#barra_progresso_cadastro").css("width","80%");
						$("#alertsMessage").html(data.message);
						$("#alerts").modal();
						Loading.destroy();
					}
				}
			}
		});
	}



	function buscaTipoDocumentos(callback){
		if($("#pais_nascimento_id").val() > 0){
			$('#documentos').html("");
			$.getJSON("{{url("/tipodocumento/searchByPais")}}/".concat($("#pais_nascimento_id").val()),function(data){
				for (i = 0; i < data.data.length; i++) {

					html = "";
					html = html.concat('<div class="form-group">');
					if(data.data[i].is_required){
						html = html.concat('<label for="tipo_documento_').concat(data.data[i].id).concat('" class="field-required">').concat(data.data[i].name).concat(' *</label>');
					}else{
						html = html.concat('<label for="tipo_documento_').concat(data.data[i].id).concat('">').concat(data.data[i].name).concat('</label>');
					}
					html = html.concat('<input name="tipo_documento_').concat(data.data[i].id).concat('" id="tipo_documento_').concat(data.data[i].id).concat('" class="form-control" type="text" />');
					html = html.concat('</div>');

					$('#documentos').append(html);

					if(data.data[i].pattern){
						$("#tipo_documento_".concat(data.data[i].id)).mask(data.data[i].pattern);
					}

					if(i+1 == data.data.length){
						tipo_documentos = data.data;
						if(callback){
							callback();
						}
					}
				}
				if(data.data.length == 0){
					tipo_documentos = false;
					if(callback){
						callback();
					}
				}
			});
		}else{
			if(callback){
				callback();
			}
			$('#documentos').html("<p>Selecione antes um país de nascimento...</p>");
		}
	}


	// CADASTRO DE CIDADE
	function chamaCadastroCidade(whereFrom){
		$("#where_from_cadastro_cidade").val(whereFrom);
		if(whereFrom == 0){
			$("#cidade_pais_id").val($("#pais_id").val()).change();
			setTimeout(function(){
				buscaEstados(-2,false,function(){
					$("#cidade_estados_id").val($("#estados_id").val()).change();
				});
			},200);
		}
		if(whereFrom == 1){
			$("#cidade_pais_id").val($("#inscricao_pais_id").val()).change();
			setTimeout(function(){
				buscaEstados(-2,false,function(){
					$("#cidade_estados_id").val($("#inscricao_estados_id").val()).change();
				});
			},200);
		}
		if(whereFrom == 2){
			$("#cidade_pais_id").val($("#confirmacao_pais_id").val()).change();
			setTimeout(function(){
				buscaEstados(-2,false,function(){
					$("#cidade_estados_id").val($("#confirmacao_estados_id").val()).change();
				});
			},200);
		}
		$("#novaCidade").modal();
	}

	function salvarCadastroCidade(){
		Loading.enable(loading_default_animation,10000);
		var data = "estados_id=".concat($("#cidade_estados_id").val()).concat("&name=").concat($("#cidade_nome").val());
		@if(isset($token))
			@if($token != "")
				data = data.concat("&token=").concat("{{$token}}");
			@endif
		@endif
		$.ajax({
			type: "post",
			url: "{{url("/inscricao/v2/".$evento->id."/cidade/nova")}}",
			data: data,
			dataType: "json",
			success: function(data){
				if(data.ok == 1){
					Loading.destroy();
					$("#successMessage").html("<strong>A cidade foi cadastrada com sucesso!</strong>");
					$("#success").modal();
					$("#novaCidade").modal('hide');

					if($("#where_from_cadastro_cidade").val() == 0){
						$("#pais_id").val(data.pais_id).change();
						setTimeout(function(){
							buscaEstados(0,false,function(){
								$("#estados_id").val(data.estados_id).change();
								setTimeout(function(){
									buscaCidades(0,function(){
										Loading.destroy();
										$("#cidade_id").val(data.cidade_id).change();
										$("#novaCidade").modal('hide');
									});
								},200);
							});
						},200);
					}else if($("#where_from_cadastro_cidade").val() == 1){
						$("#inscricao_pais_id").val(data.pais_id).change();
						setTimeout(function(){
							buscaEstados(1,false,function(){
								$("#inscricao_estados_id").val(data.estados_id).change();
								setTimeout(function(){
									buscaCidades(1,function(){
										Loading.destroy();
										$("#inscricao_cidade_id").val(data.cidade_id).change();
										$("#novaCidade").modal('hide');
									});
								},200);
							});
						},200);
					}else if($("#where_from_cadastro_cidade").val() == 2){
						$("#confirmacao_pais_id").val(data.pais_id).change();
						setTimeout(function(){
							buscaEstados(2,false,function(){
								$("#confirmacao_estados_id").val(data.estados_id).change();
								setTimeout(function(){
									buscaCidades(2,function(){
										Loading.destroy();
										$("#confirmacao_cidade_id").val(data.cidade_id).change();
										$("#novaCidade").modal('hide');
									});
								},200);
							});
						},200);
					}

					setTimeout(function(){
						$("#novaCidade").modal('hide');
						zeraCidade();
					},1000);
				}else{
					if(data.registred == 1){
						if($("#where_from_cadastro_cidade").val() == 0){
							$("#pais_id").val(data.pais_id).change();
							setTimeout(function(){
								buscaEstados(0,false,function(){
									$("#estados_id").val(data.estados_id).change();
									setTimeout(function(){
										buscaCidades(0,function(){
											Loading.destroy();
											$("#cidade_id").val(data.cidade_id).change();
											$("#novaCidade").modal('hide');
										});
									},200);
								});
							},200);
						}else if($("#where_from_cadastro_cidade").val() == 1){
							$("#inscricao_pais_id").val(data.pais_id).change();
							setTimeout(function(){
								buscaEstados(1,false,function(){
									$("#inscricao_estados_id").val(data.estados_id).change();
									setTimeout(function(){
										buscaCidades(1,function(){
											Loading.destroy();
											$("#inscricao_cidade_id").val(data.cidade_id).change();
											$("#novaCidade").modal('hide');
										});
									},200);
								})
							},200);
						}else if($("#where_from_cadastro_cidade").val() == 2){
							$("#confirmacao_pais_id").val(data.pais_id).change();
							setTimeout(function(){
								buscaEstados(2,false,function(){
									$("#confirmacao_estados_id").val(data.estados_id).change();
									setTimeout(function(){
										buscaCidades(2,function(){
											Loading.destroy();
											$("#confirmacao_cidade_id").val(data.cidade_id).change();
											$("#novaCidade").modal('hide');
										});
									},200);
								})
							},200);
						}

						setTimeout(function(){
							$("#novaCidade").modal('hide');
							zeraCidade();
						},1000);
					}else{
						Loading.destroy();
					}
					$("#alertsMessage").html(data.message);
					$("#alerts").modal();

				}
			}
		});
	}


	// CADASTRO DE ESTADO
	function chamaCadastroEstado(whereFrom){
		$("#where_from_cadastro_estado").val(whereFrom);
		if(whereFrom == 0){
			$("#estado_pais_id").val($("#pais_id").val()).change();
		}
		if(whereFrom == 1){
			$("#estado_pais_id").val($("#inscricao_pais_id").val()).change();
		}
		if(whereFrom == 2){
			$("#estado_pais_id").val($("#confirmacao_pais_id").val()).change();
		}
		$("#novoEstado").modal();
	}

	function salvarCadastroEstado(){
		Loading.enable(loading_default_animation,10000);
		var data = "pais_id=".concat($("#estado_pais_id").val()).concat("&name=").concat($("#estado_nome").val());
		@if(isset($token))
			@if($token != "")
				data = data.concat("&token=").concat("{{$token}}");
			@endif
		@endif
		$.ajax({
			type: "post",
			url: "{{url("/inscricao/v2/".$evento->id."/estado/novo")}}",
			data: data,
			dataType: "json",
			success: function(data){
				if(data.ok == 1){
					Loading.destroy();
					$("#successMessage").html("<strong>O estado foi cadastrado com sucesso!</strong>");
					$("#success").modal();
					$("#novoEstado").modal('hide');

					if($("#where_from_cadastro_estado").val() == 0){
						$("#pais_id").val(data.pais_id).change();
						setTimeout(function(){
							buscaEstados(0,false,function(){
								$("#estados_id").val(data.estados_id).change();
								setTimeout(function(){
									buscaCidades(0,false);
									$("#novoEstado").modal('hide');
								},200);
							});
						},200);
					}else if($("#where_from_cadastro_estado").val() == 1){
						$("#inscricao_pais_id").val(data.pais_id).change();
						setTimeout(function(){
							buscaEstados(1,false,function(){
								$("#inscricao_estados_id").val(data.estados_id).change();
								setTimeout(function(){
									buscaCidades(1,false);
									$("#novoEstado").modal('hide');
								},200);
							});
						},200);
					}else if($("#where_from_cadastro_estado").val() == 2){
						$("#confirmacao_pais_id").val(data.pais_id).change();
						setTimeout(function(){
							buscaEstados(2,false,function(){
								$("#confirmacao_estados_id").val(data.estados_id).change();
								setTimeout(function(){
									buscaCidades(2,false);
									$("#novoEstado").modal('hide');
								},200);
							});
						},200);
					}

					setTimeout(function(){
						$("#novoEstado").modal('hide');
						zeraCidade();
					},1000);
				}else{
					if(data.registred == 1){
						if($("#where_from_cadastro_cidade").val() == 0){
							$("#pais_id").val(data.pais_id).change();
							setTimeout(function(){
								buscaEstados(0,false,function(){
									$("#estados_id").val(data.estados_id).change();
									setTimeout(function(){
										buscaCidades(0,false);
										$("#novoEstado").modal('hide');
									},200);
								});
							},200);
						}else if($("#where_from_cadastro_cidade").val() == 1){
							$("#inscricao_pais_id").val(data.pais_id).change();
							setTimeout(function(){
								buscaEstados(1,false,function(){
									$("#inscricao_estados_id").val(data.estados_id).change();
									setTimeout(function(){
										buscaCidades(1,false);
										$("#novoEstado").modal('hide');
									},200);
								})
							},200);
						}else if($("#where_from_cadastro_cidade").val() == 2){
							$("#confirmacao_pais_id").val(data.pais_id).change();
							setTimeout(function(){
								buscaEstados(2,false,function(){
									$("#confirmacao_estados_id").val(data.estados_id).change();
									setTimeout(function(){
										buscaCidades(2,false);
										$("#novoEstado").modal('hide');
									},200);
								})
							},200);
						}

						setTimeout(function(){
							$("#novoEstado").modal('hide');
							zeraCidade();
						},1000);
					}else{
						Loading.destroy();
					}
					$("#alertsMessage").html(data.message);
					$("#alerts").modal();

				}
			}
		});
	}

	// CADASTRO DE CLUBE
	function chamaCadastroClube(whereFrom){
		$("#where_from_cadastro_clube").val(whereFrom);
		if(whereFrom == 0){
			$("#cidade_pais_id").val($("#pais_id").val()).change();
			setTimeout(function(){
				buscaEstados(-1,false,function(){
					$("#clube_estados_id").val($("#estados_id").val()).change();
					setTimeout(function(){
						buscaCidades(-1,function(){
							$("#clube_cidade_id").val($("#cidade_id").val()).change();
						});
					},200);
				});
			},200);
		}
		if(whereFrom == 1){
			$("#clube_pais_id").val($("#inscricao_pais_id").val()).change();
			setTimeout(function(){
				buscaEstados(-1,false,function(){
					$("#clube_estados_id").val($("#inscricao_estados_id").val()).change();
					setTimeout(function(){
						buscaCidades(-1,function(){
							$("#clube_cidade_id").val($("#inscricao_cidade_id").val()).change();
						});
					},200);
				});
			},200);
		}
		if(whereFrom == 2){
			$("#clube_pais_id").val($("#confirmacao_pais_id").val()).change();
			setTimeout(function(){
				buscaEstados(-1,false,function(){
					$("#clube_estados_id").val($("#confirmacao_estados_id").val()).change();
					setTimeout(function(){
						buscaCidades(-1,function(){
							$("#clube_cidade_id").val($("#confirmacao_cidade_id").val()).change();
						});
					},200);
				});
			},200);
		}
		$("#novoClube").modal();
	}

	function salvarCadastroClube(){
		Loading.enable(loading_default_animation,10000);
		var data = "cidade_id=".concat($("#clube_cidade_id").val()).concat("&name=").concat($("#clube_nome").val());
		@if(isset($token))
			@if($token != "")
				data = data.concat("&token=").concat("{{$token}}");
			@endif
		@endif
		$.ajax({
			type: "post",
			url: "{{url("/inscricao/v2/".$evento->id."/clube/novo")}}",
			data: data,
			dataType: "json",
			success: function(data){
				if(data.ok == 1){
					Loading.destroy();
					$("#successMessage").html("<strong>O clube/instituição/escola foi cadastrado com sucesso!</strong>");
					$("#success").modal();
					$("#novoClube").modal('hide');

					if($("#where_from_cadastro_clube").val() == 0){
						var newOptionClube = new Option(data.cidade_nome.concat(" | ").concat(data.clube_id).concat(" - ").concat(data.clube_nome), data.clube_id, false, false);
						$('#clube_id').append(newOptionClube).trigger('change');
						setTimeout(function(){
							$("#clube_id").val(data.clube_id).change();
						},200);
					}else if($("#where_from_cadastro_clube").val() == 1){
						var newOptionClube = new Option(data.cidade_nome.concat(" | ").concat(data.clube_id).concat(" - ").concat(data.clube_nome), data.clube_id, false, false);
						$('#inscricao_clube_id').append(newOptionClube).trigger('change');
						setTimeout(function(){
							$("#inscricao_clube_id").val(data.clube_id).change();
						},200);
					}else if($("#where_from_cadastro_clube").val() == 2){
						var newOptionClube = new Option(data.cidade_nome.concat(" | ").concat(data.clube_id).concat(" - ").concat(data.clube_nome), data.clube_id, false, false);
						$('#confirmacao_clube_id').append(newOptionClube).trigger('change');
						setTimeout(function(){
							$("#confirmacao_clube_id").val(data.clube_id).change();
						},200);
					}

					setTimeout(function(){
						$("#novoClube").modal('hide');
						zeraClube();
					},1000);
				}else{
					if(data.registred == 1){
						if($("#where_from_cadastro_clube").val() == 0){
							var newOptionClube = new Option(data.cidade_nome.concat(" | ").concat(data.clube_id).concat(" - ").concat(data.clube_nome), data.clube_id, false, false);
							$('#clube_id').append(newOptionClube).trigger('change');
							setTimeout(function(){
								Loading.destroy();
								$("#clube_id").val(data.clube_id).change();
							},200);
						}else if($("#where_from_cadastro_clube").val() == 1){
							var newOptionClube = new Option(data.cidade_nome.concat(" | ").concat(data.clube_id).concat(" - ").concat(data.clube_nome), data.clube_id, false, false);
							$('#inscricao_clube_id').append(newOptionClube).trigger('change');
							setTimeout(function(){
								Loading.destroy();
								$("#inscricao_clube_id").val(data.clube_id).change();
							},200);
						}else if($("#where_from_cadastro_clube").val() == 2){
							var newOptionClube = new Option(data.cidade_nome.concat(" | ").concat(data.clube_id).concat(" - ").concat(data.clube_nome), data.clube_id, false, false);
							$('#confirmacao_clube_id').append(newOptionClube).trigger('change');
							setTimeout(function(){
								Loading.destroy();
								$("#confirmacao_clube_id").val(data.clube_id).change();
							},200);
						}

						setTimeout(function(){
							$("#novoClube").modal('hide');
							zeraClube();
						},1000);
					}else{
						Loading.destroy();
					}
					$("#alertsMessage").html(data.message);
					$("#alerts").modal();

				}
			}
		});
	}

	function confereCadastrosEntidades(){
		// Se Calcula FIDE e não CBX
		@if($evento->calcula_fide && !$evento->calcula_cbx)
			if($("#pais_nascimento_id").val() == 33){
				$("#cbx_required").css("display","");
				$("#cbx_id_label").addClass("field-required");
				$("#fide_required").css("display","none");
				$("#fide_required_label").removeClass("field-required");
			}else{
				$("#cbx_required").css("display","none");
				$("#cbx_id_label").removeClass("field-required");
				$("#fide_required").css("display","");
				$("#fide_required_label").addClass("field-required");
			}
		@else
			// Se Calcula CBX e não FIDE
			@if(!$evento->calcula_fide && $evento->calcula_cbx)
				$("#cbx_required").css("display","");
				$("#cbx_id_label").addClass("field-required");
				$("#fide_required").css("display","none");
				$("#fide_required_label").removeClass("field-required");
			@else
				// Se Calcula CBX e FIDE
				@if($evento->calcula_fide && $evento->calcula_cbx)
					$("#cbx_required").css("display","");
				    $("#cbx_id_label").addClass("field-required");
					if($("#pais_nascimento_id").val() == 33){
						$("#fide_required").css("display","none");
				        $("#fide_required_label").removeClass("field-required");
					}else{
						$("#fide_required").css("display","");
				        $("#fide_required_label").addClass("field-required");
					}
				@else
					// Se FIDE é Obrigatório e não CBX
					@if($evento->fide_required && !$evento->cbx_required)
						if($("#pais_nascimento_id").val() == 33){
							$("#cbx_required").css("display","");
				            $("#cbx_id_label").addClass("field-required");
							$("#fide_required").css("display","none");
				            $("#fide_required_label").removeClass("field-required");
						}else{
							$("#cbx_required").css("display","none");
				            $("#cbx_id_label").removeClass("field-required");
							$("#fide_required").css("display","");
				            $("#fide_required_label").addClass("field-required");
						}
					@else
						// Se CBX é Obrigatório e não FIDE
						@if(!$evento->fide_required && $evento->cbx_required)
							$("#cbx_required").css("display","");
				            $("#cbx_id_label").addClass("field-required");
							$("#fide_required").css("display","none");
				            $("#fide_required_label").removeClass("field-required");
						@else
							// Se CBX e FIDE são Obrigatórios
							@if($evento->fide_required && $evento->cbx_required)
								$("#cbx_required").css("display","");
				                $("#cbx_id_label").removeClass("field-required");
								if($("#pais_nascimento_id").val() == 33){
									$("#fide_required").css("display","none");
				                    $("#fide_required_label").removeClass("field-required");
								}else{
									$("#fide_required").css("display","");
				                    $("#fide_required_label").addClass("field-required");
								}
							@else

							@endif
						@endif
					@endif
				@endif
			@endif
		@endif
		@if($evento->is_lichess)
			$("#lichess_required").css("display","");
			$("#lichess_username_label").addClass("field-required");
        @else
			$("#lichess_required").css("display","none");
			$("#lichess_username_label").removeClass("field-required");
        @endif
		@if($evento->is_chess_com)
			$("#chess_com_required").css("display","");
			$("#chess_com_username_label").addClass("field-required");
        @else
			$("#chess_com_required").css("display","none");
			$("#chess_com_username_label").removeClass("field-required");
        @endif
	}
    function goToInscricao(){
        $('html, body').stop().animate({
            'scrollTop': $("#processo_inscricao").offset().top
        }, 600, 'swing');
    }
</script>
@endsection

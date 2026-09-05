@php
        $permitido_edicao = false;
        if(
            \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
            \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
			\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
        ){
            $permitido_edicao = true;
        }
@endphp

@if($permitido_edicao)
    <div class="modal fade modal-warning" id="novaCidade" tabindex="-1" role="dialog" aria-labelledby="alerts">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Cadastrar Nova Cidade</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Nome *</label>
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
                <h4 class="modal-title">Cadastrar Novo Clube</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Nome *</label>
                        <input type="text" name="name" class="form-control" id="clube_nome" placeholder="Insira o Nome Completo do Clube" required="required">
                    </div>
                    <div class="form-group">
                        <label for="clube_cidade_id">Cidade *</label>
                        <select id="clube_cidade_id" class="form-control">
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
@endif
<!-- Main row -->
<div class="row">
  <!-- Left col -->
  <section class="col-lg-12 connectedSortable">
	<div class="box box-primary" id="inscricao">
		<div class="box-header">
            @if($permitido_edicao)
			    <h3 class="box-title">Editar Inscrição</h3>
            @else
			    <h3 class="box-title">Visualizar Inscrição</h3>
            @endif
		</div>
	  <!-- form start -->
        @if($permitido_edicao) <form method="post"> @endif
			<div class="box-body">
				<div class="form-group">
					<label for="inscricao_id">Enxadrista</label>
					<select id="inscricao_id" class="form-control" disabled="disabled">
						<option value="{{$inscricao->enxadrista->id}}">{{$inscricao->enxadrista->name}}</option>
					</select>
				</div>
				<div class="form-group">
					<label for="categoria_id">Categoria *</label>
					<select id="categoria_id" name="categoria_id" class="form-control" @if(!$permitido_edicao) disabled="disabled" @endif>
					</select>
				</div>
				<div class="form-group">
					<label for="cidade_id">Cidade *</label>
					<select id="cidade_id" name="cidade_id" class="cidade_id form-control" @if(!$permitido_edicao) disabled="disabled" @endif>
					</select>
                    @if($permitido_edicao) <button id="cidadeNaoCadastradaInscricao" class="btn btn-success">A cidade não está cadastrada</button> @endif
				</div>
				<div class="form-group">
					<label for="clube_id">Clube *</label>
					<select id="clube_id" name="clube_id" class="clube_id form-control" @if(!$permitido_edicao) disabled="disabled" @endif>
                        <option value="">Sem Clube</option>
					</select>
                    @if($permitido_edicao) <button id="clubeNaoCadastradoInscricao" class="btn btn-success">O clube não está cadastrado</button> @endif
				</div>
				@foreach($evento->campos() as $campo)
					<div class="form-group">
						<label for="campo_personalizado_{{$campo->id}}">{{$campo->question}} *</label>
						<select id="campo_personalizado_{{$campo->id}}" name="campo_personalizado_{{$campo->id}}" class="campo_personalizado form-control" @if(!$permitido_edicao) disabled="disabled" @endif>
							<option value="">--- Selecione uma opção ---</option>
							@foreach($campo->opcoes->all() as $opcao)
								<option value="{{$opcao->id}}">{{$opcao->response}}</option>
							@endforeach
						</select>
					</div>
				@endforeach
                @if($inscricao->confirmado && $evento->hasConfig("is_team_tournament"))
                    <hr/>
                    <h3>Configuração do Enxadrista no Time</h3>
                        <div class="form-group">
                            <label for="config__team_table">Tabuleiro *</label>
                            <input type="text" name="config__team_table" id="config__team_table" class="form-control" value="{{ ($inscricao->hasConfig("team_order")) ? $inscricao->getConfig("team_order",true) : '' }}" @if(!$permitido_edicao) disabled="disabled" @endif />
                        </div>

                @endif
                @if($evento->e_resultados_manuais && $inscricao->confirmado)
                    <hr/>
                    <h3>Resultados</h3>
                        <div class="form-group">
                            <label for="posicao">Posição *</label>
                            <input type="text" name="posicao" id="posicao" class="form-control" value="{{$inscricao->posicao}}" @if(!$permitido_edicao) disabled="disabled" @endif />
                        </div>
                        <div class="form-group">
                            <label for="pontos">Pontuação *</label>
                            <input type="text" name="pontos" id="pontos" class="form-control" value="{{$inscricao->pontos}}" @if(!$permitido_edicao) disabled="disabled" @endif />
                        </div>
                        <div class="form-group">
                            <label for="posicao_geral">Posição Geral *</label>
                            <input type="text" name="posicao_geral" id="posicao_geral" class="form-control" value="{{$inscricao->posicao_geral}}" @if(!$permitido_edicao) disabled="disabled" @endif />
                        </div>
                        <div class="form-group">
                            <label for="pontos_geral">Pontuação Geral *</label>
                            <input type="text" name="pontos_geral" id="pontos_geral" class="form-control" value="{{$inscricao->pontos_geral}}" @if(!$permitido_edicao) disabled="disabled" @endif />
                        </div>
                    <h4>Critérios de Desempate</h4>
                        @foreach($criterios as $criterio)
                            <div class="form-group">
                                <label for="criterio_{{$criterio->criterio->id}}">{{$criterio->criterio->name}} *</label>
                                <input type="text" name="criterio_{{$criterio->criterio->id}}" id="criterio_{{$criterio->criterio->id}}_{{$criterio->prioridade}}" class="form-control" value="@if($criterio->criterio->valor_criterio($inscricao->id,$criterio->prioridade)){{$criterio->criterio->valor_criterio($inscricao->id,$criterio->prioridade)->valor}}@endif" @if(!$permitido_edicao) disabled="disabled" @endif />
                            </div>
                        @endforeach
                    <hr/>
                @else
                    @if($inscricao->confirmado)
                        <hr/>
                        <h4>Critérios de Desempate Manuais</h4>
                            @foreach($criterios as $criterio)
                                <div class="form-group">
                                    <label for="criterio_{{$criterio->criterio->id}}">{{$criterio->criterio->name}} *</label>
                                    <input type="text" name="criterio_{{$criterio->criterio->id}}_{{$criterio->prioridade}}" id="criterio_{{$criterio->criterio->id}}_{{$criterio->prioridade}}" class="form-control" value="@if($criterio->criterio->valor_criterio($inscricao->id,$criterio->prioridade)){{$criterio->criterio->valor_criterio($inscricao->id,$criterio->prioridade)->valor}}@endif" @if(!$permitido_edicao) disabled="disabled" @endif />
                                </div>
                            @endforeach
                        <hr/>

                    @endif
                @endif
                @if($permitido_edicao)
                    <div class="form-group">
                        <label><input type="checkbox" id="atualizar_cadastro" name="atualizar_cadastro"> Atualizar Cadastro</label>
                    </div>
                    <hr/>
                    @if($inscricao->torneio->evento->is_lichess_integration)
                        <a href="{{$inscricao->getLichessProcessLink()}}" class="btn btn-lg btn-success btn-block">
                            <strong>Link para Inscrição no Torneio do Lichess.org - Para encaminhar para o enxadrista se inscrever.</strong>
                        </a><br/>
                    @endif
                @endif
				<div class="form-group">
					<label><input type="checkbox" id="desconsiderar_pontuacao_geral" name="desconsiderar_pontuacao_geral" @if(!$permitido_edicao) disabled="disabled" @endif @if($inscricao->desconsiderar_pontuacao_geral) checked="checked" @endif> Desconsiderar Inscrição para Pontuação Geral</label>
				</div>
				<div class="form-group">
					<label><input type="checkbox" id="is_desclassificado" name="is_desclassificado" @if(!$permitido_edicao) disabled="disabled" @endif @if($inscricao->is_desclassificado) checked="checked" @endif> Enxadrista Desclassificado</label>
				</div>
                @if($inscricao->torneio->evento->classifica)
                    <div class="form-group">
                        <label><input type="checkbox" id="desconsiderar_classificado" name="desconsiderar_classificado" @if(!$permitido_edicao) disabled="disabled" @endif @if($inscricao->desconsiderar_classificado) checked="checked" @endif> Desconsiderar Classificado para o Evento que este classifica</label>
                    </div>
                @endif
			</div>
			<!-- /.box-body -->

			<div class="box-footer">
				<button type="submit" class="btn btn-success">Enviar</button>
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
			</div>
        @if($permitido_edicao) </form> @endif
	</div>

  </section>
  <!-- /.Left col -->
</div>
<!-- /.row (main row) -->

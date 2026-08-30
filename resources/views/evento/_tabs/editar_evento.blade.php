				<br/>
				<section class="col-lg-12 connectedSortable">
					<div class="box box-primary">
						<div class="box-header">
							<h3 class="box-title">Editar Evento</h3>
						</div>
						<!-- form start -->

					@if(
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
					)
						<form method="post">
					@endif
							<div class="box-body">
								<div class="form-group">
									<label for="evento_name">Nome *</label>
									<input name="name" id="evento_name" class="form-control" type="text" value="{{$evento->name}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif />
								</div>
								<div class="form-group">
									<label for="evento_data_inicio">Data de Início *</label>
									<input name="data_inicio" id="evento_data_inicio" class="form-control" type="text" value="{{$evento->getDataInicio()}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif />
								</div>
								<div class="form-group">
									<label for="evento_data_fim">Data de Fim *</label>
									<input name="data_fim" id="evento_data_fim" class="form-control" type="text" value="{{$evento->getDataFim()}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif />
								</div>
								<div class="form-group">
									<label for="tipo_modalidade">Tipo de Modalidade *</label>
									<select name="tipo_modalidade" id="tipo_modalidade" class="form-control width-100" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif>
										<option value="">--- Você pode selecionar um tipo de modalidade ---</option>
										<option value="0">Convencional</option>
										<option value="1">Rápido</option>
										<option value="2">Relâmpago</option>
									</select>
								</div>
								<div class="form-group">
									<label for="layout_version">Tipo de Layout de Página *</label>
									<select name="layout_version" id="layout_version" class="form-control width-100" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif>
										<option value="">--- Você pode selecionar um tipo de layout de página ---</option>
										<option value="1">Versão 1 - Padrão</option>
										<option value="2">Versão 2 - Página de Evento</option>
									</select>
								</div>
								<div class="form-group">
									<label for="exportacao_sm_modelo">Tipo de Exportação - Swiss Manager *</label>
									<select name="exportacao_sm_modelo" id="exportacao_sm_modelo" class="form-control width-100" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif>
										<option value="0">Padrão {{config("xadrezsuico.name","XadrezSuíço")}}</option>
										<option value="1">FIDE</option>
										<option value="7">FIDE - Exporta somente clube</option>
										<option value="2">LBX</option>
										<option value="3">Padrão {{config("xadrezsuico.name","XadrezSuíço")}} (Nome no Sobrenome, e Sobrenome no Nome)</option>
										<option value="4">Padrão {{config("xadrezsuico.name","XadrezSuíço")}} Chess.com (Nome no Sobrenome, Sobrenome no Nome e no Nome também informa o Usuário do Chess.com)</option>
										<option value="5">Padrão {{config("xadrezsuico.name","XadrezSuíço")}} sem Cidade (Nome no Sobrenome, Sobrenome no Nome sem Cidade)</option>
										<option value="6">Padrão {{config("xadrezsuico.name","XadrezSuíço")}} sem Cidade (Nome no Sobrenome, Sobrenome no Nome sem Cidade) em Torneio por Equipe</option>
									</select>
								</div>
                                <div class="form-group">
                                    <label for="pais_id">País *</label>
                                    <select id="pais_id" name="pais_id" class="form-control pais_select2" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif>
                                        <option value="">--- Selecione um país ---</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="estados_id">Estado *</label>
                                    <select id="estados_id" name="estados_id" class="form-control this_is_select2" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif>
                                        <option value="">--- Selecione um país antes ---</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="cidade_id">Cidade *</label>
                                    <select id="cidade_id" name="cidade_id" class="form-control this_is_select2" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif>
                                        <option value="">--- Selecione um estado antes ---</option>
                                    </select>
                                </div>
								<div class="form-group">
									<label for="evento_local">Local *</label>
									<input name="local" id="evento_local" class="form-control" type="text" value="{{$evento->local}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif />
								</div>
								<div class="form-group">
									<label for="evento_link">Link</label>
									<input name="link" id="evento_link" class="form-control" type="text" value="{{$evento->link}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif />
								</div>
								<div class="form-group">
									<label for="evento_data_limite_inscricoes_abertas">Data e Hora de Início das Inscrições</label>
									<input name="date_start_registration" id="date_start_registration" class="form-control" type="text" value="{{$evento->getDataInicioInscricoesOnline()}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif />
								</div>
								<div class="form-group">
									<label for="evento_data_limite_inscricoes_abertas">Data e Hora Limite para Inscrições</label>
									<input name="data_limite_inscricoes_abertas" id="evento_data_limite_inscricoes_abertas" class="form-control" type="text" value="{{$evento->getDataFimInscricoesOnline()}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif />
								</div>
								<div class="form-group">
									<label for="maximo_inscricoes_evento">Número Máximo de Inscrições Permitidas no Evento</label>
									<input name="maximo_inscricoes_evento" id="maximo_inscricoes_evento" class="form-control" type="text" value="{{$evento->maximo_inscricoes_evento}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif />
								</div>
								<div class="form-group">
									<label for="orientacao_pos_inscricao">Orientações Pós-Inscrição</label>
									<textarea class="form-control" id="orientacao_pos_inscricao" name="orientacao_pos_inscricao" placeholder="Texto com as Orientações Pós-Inscrição" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif >{{$evento->orientacao_pos_inscricao}}</textarea>
								</div>
								<div class="form-group">
									<label><input type="checkbox" id="e_permite_visualizar_lista_inscritos_publica" name="e_permite_visualizar_lista_inscritos_publica" @if($evento->e_permite_visualizar_lista_inscritos_publica) checked="checked" @endif @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif > Permite a visualização da lista de inscrições de forma pública?</label>
								</div>
								<div class="form-group">
									<label><input type="checkbox" id="e_inscricao_apenas_com_link" name="e_inscricao_apenas_com_link" @if($evento->e_inscricao_apenas_com_link) checked="checked" @endif @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif > As inscrições deste evento deverão ser feitas apenas pelo link divulgado (Inscrição Privada)</label>
								</div>
                                <div class="form-group">
                                    <label><input type="checkbox" id="fed_use_club" name="fed_use_club" @if($evento->getConfig("fed_use_club",true)) checked="checked" @endif @if((!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) || $evento->usa_lbx) disabled="disabled" @endif> Utiliza a Abreviação do Clube no campo FED do Swiss-Manager</label>
                                </div>
                                <div class="form-group">
                                    <label @if($evento->usa_lbx) title="Rating FIDE não disponível para este evento. Motivo: Usa Rating LBX, e por isto não permite o uso de rating FIDE." @endif><input type="checkbox" id="usa_fide" name="usa_fide" @if($evento->usa_fide) checked="checked" @endif @if((!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) || $evento->usa_lbx) disabled="disabled" @endif> Utiliza Rating FIDE?</label>
                                </div>
                                <div class="form-group">
                                    <label @if($evento->usa_lbx) title="Rating FIDE não disponível para este evento. Motivo: Usa Rating LBX, e por isto não permite o uso de rating FIDE." @endif><input type="checkbox" id="calcula_fide" name="calcula_fide" @if($evento->calcula_fide) checked="checked" @endif @if((!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) || $evento->usa_lbx) disabled="disabled" @endif> Calcula Rating FIDE?</label>
                                </div>
                                <div class="form-group">
                                    <label @if($evento->usa_lbx) title="Rating FIDE não disponível para este evento. Motivo: Usa Rating LBX, e por isto não permite o uso de rating FIDE." @endif><input type="checkbox" id="fide_required" name="fide_required" @if($evento->fide_required) checked="checked" @endif @if((!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) || $evento->usa_lbx) disabled="disabled" @endif> Não Calcula Rating FIDE mas Obriga ID FIDE?</label>
                                </div>
                                @if($evento->usa_fide || $evento->calcula_fide)
                                    <div class="form-group">
                                        <label><input type="checkbox" id="fide_sequence" name="fide_sequence" @if($evento->getConfig("fide_sequence",true)) checked="checked" @endif @if((!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) || $evento->usa_lbx) disabled="disabled" @endif> Utiliza Sequência do Padrão da FIDE?</label>
                                    </div>
                                @endif
                                @if(!$evento->tipo_rating)
                                    <div class="form-group">
                                        <label><input type="checkbox" id="usa_cbx" name="usa_cbx" @if($evento->usa_cbx) checked="checked" @endif @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif > Utiliza Rating CBX?</label>
                                    </div>
                                    <div class="form-group">
                                        <label><input type="checkbox" id="calcula_cbx" name="calcula_cbx" @if($evento->calcula_cbx) checked="checked" @endif @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif > Calcula Rating CBX?</label>
                                    </div>
                                    <div class="form-group">
                                        <label><input type="checkbox" id="cbx_required" name="cbx_required" @if($evento->cbx_required) checked="checked" @endif @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif > Não Calcula Rating CBX mas Obriga ID CBX?</label>
                                    </div>
                                    <div class="form-group">
                                        <label><input type="checkbox" id="usa_lbx" name="usa_lbx" @if($evento->usa_lbx) checked="checked" @endif @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif > Utiliza Rating LBX?</label>
                                    </div>
                                    <div class="form-group">
                                        <label><input type="checkbox" id="is_lichess" name="is_lichess" @if($evento->is_lichess) checked="checked" @endif @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif > Necessita usuário da plataforma Lichess.org</label>
                                    </div>
                                    <div class="form-group">
                                        <label><input type="checkbox" id="is_lichess_integration" name="is_lichess_integration" @if($evento->is_lichess_integration) checked="checked" @endif @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif > Usa Integração com o Lichess.org</label>
                                    </div>
                                    <div class="form-group">
                                        <label for="lichess_team_id">Lichess.org: ID do Time/Equipe</label>
                                        <input name="lichess_team_id" id="lichess_team_id" class="form-control" type="text" value="{{$evento->lichess_team_id}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif />
                                        <small><strong>Importante!</strong> Aqui vai o ID do Time no Lichess. Vale constar que para que um torneio tenha integração com o {{config("xadrezsuico.name","XadrezSuíço")}} é necessário que seja efetuado por um time/equipe que {{config("xadrezsuico.name","XadrezSuíço")}} tenha permissões. Segue exemplo de onde encontrar o ID do Time: https://lichess.org/team/<strong>circuito-sesc-de-xadrez-2021</strong></small>
                                        @if($evento->lichess_team_id) <br/><a href="https://lichess.org/team/{{$evento->lichess_team_id}}" target="_blank">Link do Time</a> @endif
                                    </div>
                                    @if(\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() || \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) || \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7]))
                                        <div class="form-group">
                                            <label for="lichess_team_password">Lichess.org: Senha para a Entrada no Time/Equipe</label>
                                            <input name="lichess_team_password" id="lichess_team_password" class="form-control" type="text" value="{{$evento->lichess_team_password}}" />
                                            <small><strong>Importante!</strong> Aqui vai a Senha para a Entrada no Time no Lichess. Vale constar que para que um torneio tenha integração com o {{config("xadrezsuico.name","XadrezSuíço")}} é necessário que seja efetuado por um time/equipe que {{config("xadrezsuico.name","XadrezSuíço")}} tenha permissões.</small>
                                        </div>
                                    @endif
                                    <div class="form-group">
                                        <label for="lichess_tournament_id">Lichess.org: ID do Torneio</label>
                                        <input name="lichess_tournament_id" id="lichess_tournament_id" class="form-control" type="text" value="{{$evento->lichess_tournament_id}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif />
                                        <small><strong>Importante!</strong> Aqui vai o ID do Torneio no Lichess. Vale constar que para que um torneio tenha integração com o {{config("xadrezsuico.name","XadrezSuíço")}} é necessário que seja efetuado por um time/equipe que {{config("xadrezsuico.name","XadrezSuíço")}} tenha permissões. Segue exemplo de onde encontrar o ID do Torneio: https://lichess.org/swiss/<strong>ZDig8Z5Y</strong></small>
                                        @if($evento->lichess_tournament_id) <br/><a href="https://lichess.org/swiss/{{$evento->lichess_tournament_id}}" target="_blank">Link do Torneio</a> @endif
                                    </div>
                                    @if(\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() || \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) || \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7]))
                                        <div class="form-group">
                                            <label for="lichess_tournament_password">Lichess.org: Senha para a Inscrição no Torneio</label>
                                            <input name="lichess_tournament_password" id="lichess_tournament_password" class="form-control" type="text" value="{{$evento->lichess_tournament_password}}" />
                                            <small><strong>Importante!</strong> Aqui vai a Senha para a Inscrição no Torneio no Lichess. Vale constar que para que um torneio tenha integração com o {{config("xadrezsuico.name","XadrezSuíço")}} é necessário que seja efetuado por um time/equipe que {{config("xadrezsuico.name","XadrezSuíço")}} tenha permissões.</small>
                                        </div>
                                    @endif
                                @else
                                    <div class="form-group">
                                        <label><input type="checkbox" id="is_lichess" name="is_lichess" @if($evento->is_lichess) checked="checked" @endif @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif > Necessita usuário da plataforma Lichess.org</label>
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label><input type="checkbox" id="is_chess_com" name="is_chess_com" @if($evento->is_chess_com) checked="checked" @endif @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif > Necessita usuário da plataforma Chess.com</label>
                                </div>
								<hr/>
								<div class="form-group">
									<label for="tipo_ratings_id">Tipo de Rating</label>
									<select name="tipo_ratings_id" id="tipo_ratings_id" class="form-control width-100" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif>
										<option value="">--- Você pode selecionar um tipo de rating ---</option>
										@foreach($tipos_rating as $tipo_rating)
											<option value="{{$tipo_rating->id}}">{{$tipo_rating->id}} - {{$tipo_rating->name}}</option>
										@endforeach
									</select>
								</div>

                                @if($evento->grupo_evento->classificador)
                                    <div class="form-group">
                                        <label for="evento_classificador_id">Evento Classificador ({{$evento->grupo_evento->classificador->name}})</label>
                                        <select name="evento_classificador_id" id="evento_classificador_id" class="form-control width-100">
                                            <option value="">--- Você pode selecionar um evento ---</option>
                                            @foreach($evento->grupo_evento->classificador->eventos->all() as $ec)
                                                <option value="{{$ec->id}}">{{$ec->id}} - {{$ec->name}}</option>
                                            @endforeach
                                        </select>
                                        <small><strong>IMPORTANTE!</strong> Essa configuração serve para caso tenha um grupo de evento que classifica para este grupo de evento. Aqui vai o Evento Classificador que classifica para este Evento.</small>
                                    </div>
                                @else
                                    @if($evento->grupo_evento_classificador)
                                        <div class="form-group">
                                            <label for="grupo_evento_classificador_id">Grupo de Evento Classificador</label>
                                            <select name="grupo_evento_classificador_id" id="grupo_evento_classificador_id" class="form-control width-100">
                                                <option value="">--- Você pode selecionar um grupo de evento ---</option>
                                                @foreach($evento->grupo_evento->all() as $gec)
                                                    <option value="{{$gec->id}}">{{$gec->id}} - {{$gec->name}}</option>
                                                @endforeach
                                            </select>
                                            <small><strong>IMPORTANTE!</strong> Essa configuração serve para caso tenha um grupo de evento que classifica para este grupo de evento.</small>
                                        </div>
                                    @endif
                                @endif
                                <hr/>
                                <div class="form-group">
                                    <label><input type="checkbox" id="e_permite_confirmacao_publica" name="e_permite_confirmacao_publica" @if($evento->e_permite_confirmacao_publica) checked="checked" @endif @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif > Permite Confirmação Pública</label>
                                </div>
								<div class="form-group">
									<label for="confirmacao_publica_inicio">Confirmações: Data e Hora Inicial</label>
									<input name="confirmacao_publica_inicio" id="confirmacao_publica_inicio" class="form-control" type="text" value="{{$evento->getConfirmacoesDataInicial()}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif />
                                    <small><strong>IMPORTANTE!</strong> Os campos de Data e Hora Inicial e Data e Hora Final devem estar preenchidos para que a confirmação pública fique disponível.</small>
                                </div>
								<div class="form-group">
									<label for="confirmacao_publica_final">Confirmações: Data e Hora Final</label>
									<input name="confirmacao_publica_final" id="confirmacao_publica_final" class="form-control" type="text" value="{{$evento->getConfirmacoesDataFim()}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif />
                                    <small><strong>IMPORTANTE!</strong> Os campos de Data e Hora Inicial e Data e Hora Final devem estar preenchidos para que a confirmação pública fique disponível.</small>
								</div>

                                @if(
                                    env("XADREZSUICOPAG_URI",null) &&
                                    env("XADREZSUICOPAG_SYSTEM_ID",null) &&
                                    env("XADREZSUICOPAG_SYSTEM_TOKEN",null) &&
						            \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobalbyPerfil([1,10,11])
                                )
                                    <div class="form-group">
                                        <label for="xadrezsuicopag_uuid">PAG: UUID do Evento na Plataforma</label>
                                        <input name="xadrezsuicopag_uuid" id="xadrezsuicopag_uuid" class="form-control" type="text" value="{{$evento->xadrezsuicopag_uuid}}" />
                                        <small><strong>IMPORTANTE!</strong> Lembre-se de colocar o UUID do Evento na Plataforma caso deseje ativar o pagamento para este evento.</small>
                                    </div>
                                @endif
							</div>
							<!-- /.box-body -->

							<div class="box-footer">
								<button type="submit" class="btn btn-success">Enviar</button>
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
							</div>
					@if(
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
					)
						</form>
					@endif
					</div>
				</section>
			

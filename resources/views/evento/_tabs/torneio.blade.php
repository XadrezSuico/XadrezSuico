				<br/>
				@if(
					(
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
					)
				)
					<section class="col-lg-12 connectedSortable">

						<!-- Torneio -->
						<div class="box box-primary collapsed-box">
							<div class="box-header">
								<h3 class="box-title">Novo Torneio</h3>
								<div class="pull-right box-tools">
									<button type="button" class="btn btn-primary btn-sm pull-right" data-widget="collapse" data-toggle="tooltip" title="" style="margin-right: 5px;" data-original-title="Collapse">
										<i class="fa fa-plus"></i></button>
								</div>
							</div>
							<!-- form start -->
							<form method="post" action="{{url("/evento/".$evento->id."/torneios/new")}}">
								<div class="box-body">
									<div class="form-group">
										<label for="name">Nome</label>
										<input name="name" id="name" class="form-control" type="text" />
									</div>
									<div class="form-group">
										<label for="tipo_torneio_id">Tipo de Torneio</label>
										<select id="tipo_torneio_id" name="tipo_torneio_id" class="form-control">
											<option value="">-- Selecione --</option>
											@foreach(\App\TipoTorneio::all() as $tipo_torneio)
												<option value="{{$tipo_torneio->id}}">{{$tipo_torneio->name}}</option>
											@endforeach
										</select>
									</div>
									<div class="form-group">
										<label for="softwares_id">Software</label>
										<select id="torneio_softwares_id" name="softwares_id" class="form-control">
											<option value="">-- Selecione --</option>
											@foreach(\App\Software::all() as $software)
												<option value="{{$software->id}}">{{$software->name}}</option>
											@endforeach
										</select>
									</div>
								</div>
								<!-- /.box-body -->

								<div class="box-footer">
									<button type="submit" class="btn btn-success">Enviar</button>
									<input type="hidden" name="_token" value="{{ csrf_token() }}">
								</div>
							</form>
						</div>
					</section>
				@endif
				<section class="col-lg-12 connectedSortable">
					<div class="box box-primary">
						<div class="box-header">
							<h3 class="box-title">Torneios</h3>
						</div>
						<!-- form start -->
							<div class="box-body">
								<table id="tabela_torneio" class="table-responsive table-condensed table-striped" style="width: 100%">
									<thead>
										<tr>
											<th>#</th>
											<th>Nome</th>
											<th>Categorias</th>
											<th>Inscritos</th>
											<th>Resultados Importados?</th>
											<th>Tipo de Torneio</th>
											<th>Template de Torneio</th>
											<th width="20%">Opções</th>
										</tr>
									</thead>
									<tbody>
										@foreach($evento->torneios->all() as $torneio)
											<tr>
												<td>{{$torneio->id}}</td>
												<td>{{$torneio->name}}</td>
												<td>
													@foreach($torneio->categorias->all() as $categoria)
														{{$categoria->categoria->name}},
													@endforeach
												</td>
												<td>
                                                    Total de Inscritos: {{$torneio->getCountInscritos()}}<br/>
                                                    @if(
                                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
                                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4,5]) ||
                                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
                                                    )
                                                        Confirmados: {{$torneio->getCountInscritosConfirmados()}}<br/>
                                                        Presentes: {{$torneio->quantosInscritosPresentes()}}<br/>
                                                        Com Resultado: {{$torneio->getCountInscritosResultados()}}
                                                        <hr/>
                                                        @if($evento->xadrezsuicopag_uuid)
                                                            <strong>Pagamento:</strong><br/>
                                                            Pagos: <strong>{{$torneio->howManyPaid()}}</strong><br/>
                                                            Pagamento Pendente: <strong>{{$torneio->howManyNotPaid()}}</strong><br/>
                                                            Gratuidades (Categorias Gratuitas): <strong>{{$torneio->howManyFree()}}</strong>
                                                        @endif
                                                        @if($evento->is_lichess_integration)
                                                            <strong>Torneio Lichess.org</strong><br/>
                                                            Inscritos: <strong>{{$torneio->getCountLichessConfirmadosnoTorneio()}}</strong><br/>
                                                            Não Inscritos: <strong>{{$torneio->getCountInscritos() - $torneio->getCountLichessConfirmadosnoTorneio()}}</strong>
                                                        @endif
                                                    @endif
												</td>
												<td>
                                                    {{$torneio->getIsResultadosImportados()}}
												</td>
												<td>
													{{$torneio->tipo_torneio->name}}
												</td>
												<td>
													@if($torneio->template)
														{{$torneio->template->name}}
													@else
														-
													@endif
												</td>
												<td>

													@if(
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
														\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
													)
														<a class="btn btn-default" href="{{url("/evento/".$evento->id."/torneios/edit/".$torneio->id)}}" role="button">Editar</a>
														@if($torneio->tipo_torneio->id != 3)  <a class="btn btn-sm btn-warning" href="{{url("/evento/".$evento->id."/torneios/union/".$torneio->id)}}" role="button">Unir Torneios</a><br/> @endif
													@endif
													<a class="btn btn-default" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes")}}" role="button">Inscrições</a>
													@if(
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
														\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
													)
														@if(!$evento->e_resultados_manuais && !$torneio->evento->is_lichess_integration && !$torneio->software->isChessCom()) <a class="btn btn-default" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/resultados/file")}}" role="button">Resultados</a><br/> @endif
														@if(!$evento->e_resultados_manuais && !$torneio->evento->is_lichess_integration && !$torneio->software->isChessCom()) <a class="btn btn-default" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/emparceiramentos")}}" role="button">Emparceiramentos</a><br/> @endif

														@if($torneio->tipo_torneio->id == 3) <a class="btn btn-block btn-lg btn-success" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/gerenciamento/torneio_3")}}" role="button">Gerenciamento do Torneio</a><br/> @endif

                                                        <hr/>
                                                        <a class="btn btn-success" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/sm")}}" role="button" target="_blank">Baixar Inscrições Confirmadas</a><br/>
                                                        @if(
                                                            env("XADREZSUICOPAG_URI",null) &&
                                                            env("XADREZSUICOPAG_SYSTEM_ID",null) &&
                                                            env("XADREZSUICOPAG_SYSTEM_TOKEN",null)
                                                        )
                                                            @if($evento->isPaid())
                                                                <a class="btn btn-warning" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/sm/paid")}}" role="button" target="_blank">Baixar Inscrições Pagas</a><br/>
                                                            @endif
                                                        @endif
														<a class="btn btn-warning" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/sm/all")}}" role="button" target="_blank">Baixar Todas as Inscrições</a><br/>
														@if($evento->exportacao_sm_modelo == 6) <a class="btn btn-success" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/sm/teams/confirmed")}}" role="button" target="_blank">Baixar Todos os Times com Enxadristas Confirmados</a><br/> @endif
														@if($evento->exportacao_sm_modelo == 6) <a class="btn btn-warning" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/sm/teams")}}" role="button" target="_blank">Baixar Todos os Times</a><br/> @endif
                                                        <hr/>

													@endif
													<a class="btn btn-info" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/relatorio/inscricoes")}}" role="button" target="_blank">Imprimir Inscrições</a><br/>
													<a class="btn btn-info" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/relatorio/inscricoes/alfabetico")}}" role="button" target="_blank">Imprimir Inscrições (Alfabético)</a><br/>
													<a class="btn btn-info" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/relatorio/inscricoes/alfabetico/cidade")}}" role="button" target="_blank">Imprimir Inscrições (Alfabético por Cidade/Clube)</a><br/>
													@if($torneio->isDeletavel()) <a class="btn btn-danger" href="{{url("/evento/".$evento->id."/torneios/delete/".$torneio->id)}}" role="button">Apagar</a> @endif
                                                    @if(
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal()
                                                    )
                                                        @if($evento->torneios()->count() > 1)
                                                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" title="Separar em um novo evento: {{$torneio->id}} {{$torneio->name}}" data-target="#modalSeparate_{{$torneio->id}}">Separar em um novo evento (Admin)</button><br/>
                                                            <!-- Modal Copiar -->
                                                            <div class="modal fade modal-danger" id="modalSeparate_{{$torneio->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                                                <div class="modal-dialog" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                            <h4 class="modal-title" id="myModalLabel">Separar em um novo evento #{{$torneio->id}}: {{$torneio->name}}</h4>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <h2>Você tem certeza que pretende fazer isso?</h2><br>
                                                                            <h4>Assim que efetuar <strong>NÃO SERÁ POSSÍVEL</strong> retornar a configuração anterior.</h4>
                                                                            <h4>Você deseja efetuar a separação?</h4>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-success" data-dismiss="modal">Não quero mais</button>
                                                                            <a class="btn btn-danger" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/migrate_to_new_event")}}">Sim, quero separar em um novo evento (Admin)</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endif
                                                    @if($torneio->evento->is_lichess_integration)
                                                        <hr/>
                                                        <strong>Opções Lichess.org</strong><br/>
														<a class="btn btn-success" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/lichess/check_players_in")}}" role="button">Conferir Inscrições no Torneio do Lichess.org</a><br/>
                                                        Última Atualização: {{$torneio->getLastLichessPlayersUpdate()}}<br/>
														@if($torneio->evento->data_inicio <= date("Y-m-d")) <a class="btn btn-success" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/lichess/get_results")}}" role="button">Inserir Resultados do Torneio do Lichess.org</a><br/> @endif
														<a class="btn btn-danger" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/lichess/remove_lichess_players_not_found")}}" role="button">REMOVER os Players do Lichess.org que NÃO foram encontrados</a><br/>
                                                    @endif
                                                    @if($torneio->software->isChessCom())
                                                        <hr/>
                                                        <strong>Opções Chess.com</strong><br/>
                                                        @if($torneio->hasConfig("chesscom_tournament_slug"))
                                                            <a class="btn btn-success" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/chesscom/check_players_in")}}" role="button">Conferir Inscrições no Torneio do Chess.com</a><br/>
                                                            Última Atualização: {{$torneio->getLastChessComPlayersUpdate()}}<br/>
                                                            @if($torneio->evento->data_inicio <= date("Y-m-d"))
                                                                <a class="btn btn-success" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/chesscom/get_results")}}" role="button">Importar Resultados do Torneio do Chess.com</a><br/>
                                                            @endif
                                                        @else
                                                            <strong>Erro!</strong> O torneio ainda não possui a configuração do slug do torneio no Chess.com configurada. Edite este torneio e a configure para ser possível prosseguir.
                                                        @endif
													@endif

                                                    @if($evento->grupo_evento->hasConfig("is_pr_esporte",true))
                                                        <hr/>
                                                        <strong>Opções Paraná Esporte</strong><br/>
                                                    @endif
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
							<!-- /.box-body -->
					</div>
				</section>
			

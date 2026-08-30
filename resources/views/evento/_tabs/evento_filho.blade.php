				<br/>
				<section class="col-lg-12 connectedSortable">
					<div class="box box-primary">
						<div class="box-header">
							<h3 class="box-title">Eventos Filhos</h3>
						</div>
						<!-- form start -->
                        <div class="box-body">

                            <table id="tabela_evento" class="table-responsive table-condensed table-striped" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nome</th>
                                        <th>Período</th>
                                        @if(
                                            \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
                                            \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfilByGroupEvent($evento->grupo_evento->id,[4,5]) ||
                                            \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
                                        )
                                            <th>Status</th>
                                        @endif
                                        <th>Local</th>
                                        <th>Inscritos</th>
                                        <th width="20%">Opções</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($evento->event_children->all() as $evento_filho)
                                        @if(
                                            \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
                                            \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento_filho->id,[3,4,5]) ||
                                            \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento_filho->grupo_evento->id,[6,7])
                                        )
                                            <tr>
                                                <td>{{$evento_filho->id}}</td>
                                                <td>{{$evento_filho->name}}</td>
                                                <td data-order="{{$evento_filho->data_inicio}}">
                                                    @if($evento_filho->getDataInicio() == $evento_filho->getDataFim())
                                                        {{$evento_filho->getDataInicio()}}
                                                    @else
                                                        {{$evento_filho->getDataInicio()}}<br/>{{$evento_filho->getDataFim()}}
                                                    @endif
                                                </td>
                                                @if(
                                                    \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
                                                    \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento_filho->id,[4,5]) ||
                                                    \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento_filho->grupo_evento->id,[7])
                                                )
                                                    <td>
                                                        @if(!$evento_filho->inscricoes_encerradas())
                                                            <strong>Recebendo</strong> Inscrições
                                                        @else
                                                            Inscrições Encerradas e/ou Bloqueadas
                                                        @endif
                                                        <hr/>

                                                        @if($evento_filho->classificavel)
                                                            @if($evento_filho->consegueCalcularClassificacaoGeral())
                                                                <strong>Apto</strong> para Classificação Geral
                                                            @else
                                                                Não Liberado para Classificação Geral - Há Torneios não importados.
                                                            @endif
                                                        @else
                                                            Não Liberado para Classificação Geral - Não está liberado para cálculo da classificação geral.
                                                        @endif
                                                        <hr/>

                                                        @if($evento_filho->tipo_rating)
                                                            @if($evento_filho->consegueCalcularRating())
                                                                <strong>Apto</strong> para Cálculo de Rating
                                                            @else
                                                                Inapto para Cálculo de Rating - Falta importar emparceiramentos
                                                            @endif
                                                            <hr/>
                                                        @endif
                                                    </td>
                                                @endif
                                                <td>{{$evento_filho->cidade->getName()}} <br/> {{$evento_filho->local}}</td>
                                                <td>
                                                    Total de Inscritos: {{$evento_filho->quantosInscritos()}}<br/>
                                                    @if(
                                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
                                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento_filho->id,[4,5]) ||
                                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento_filho->grupo_evento->id,[7])
                                                    )
                                                        Confirmados: {{$evento_filho->quantosInscritosConfirmados()}}<br/>
                                                        Presentes: {{$evento_filho->quantosInscritosPresentes()}}
                                                        <hr/>
                                                        @if($evento_filho->is_lichess_integration)
                                                            <strong>Torneio Lichess.org</strong><br/>
                                                            Inscritos: <strong>{{$evento_filho->quantosInscritosConfirmadosLichess()}}</strong><br/>
                                                            Não Inscritos: <strong>{{$evento_filho->quantosInscritosFaltamLichess()}}</strong>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(
                                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
                                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento_filho->id,[3,4]) ||
                                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento_filho->grupo_evento->id,[6,7])
                                                    )
                                                        <a class="btn btn-default" href="{{url("/evento/dashboard/".$evento_filho->id)}}" role="button">Dashboard</a>
                                                    @endif
                                                    <a class="btn btn-success" href="{{$evento_filho->getEventPublicLink()}}" target="_blank" role="button">Link de Divulgação</a>
                                                    @if(
                                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
                                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento_filho->id,[4,5]) ||
                                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento_filho->grupo_evento->id,[7])
                                                    )
                                                        <a class="btn btn-success" href="{{url("/inscricao/".$evento_filho->id)}}" target="_blank" role="button">Nova Inscrição</a>
                                                    @endif

                                                    @if($evento_filho->isDeletavel() && (\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() || \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento_filho->grupo_evento->id,[7]) )) <a class="btn btn-danger" href="{{url("/evento/delete/".$evento_filho->id)}}" role="button">Apagar</a> @endif
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.box-body -->
					</div>
				</section>
			

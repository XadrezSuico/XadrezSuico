				<section class="col-lg-12 connectedSortable">
				<br/>
				@if(
					\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
					\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
					\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
				)
					<div class="box box-primary collapsed-box">
						<div class="box-header">
							<h3 class="box-title">Nova Relação de Categoria</h3>
								<div class="pull-right box-tools">
									<button type="button" class="btn btn-primary btn-sm pull-right" data-widget="collapse" data-toggle="tooltip" title="" style="margin-right: 5px;" data-original-title="Collapse">
										<i class="fa fa-plus"></i></button>
								</div>
						</div>
						<!-- form start -->
						<form method="post" action="{{url("/evento/".$evento->id."/categoria/add")}}">
							<div class="box-body">
								<div class="form-group">
									<label for="categoria_id">Categoria</label>
									<select name="categoria_id" id="categoria_id" class="form-control width-100">
										<option value="">--- Selecione ---</option>
										@foreach($categorias as $categoria)
											<option value="{{$categoria->id}}">{{$categoria->id}} - {{$categoria->name}}</option>
										@endforeach
									</select>


                                    @if(
                                        env("XADREZSUICOPAG_URI",null) &&
                                        env("XADREZSUICOPAG_SYSTEM_ID",null) &&
                                        env("XADREZSUICOPAG_SYSTEM_TOKEN",null) &&
                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobalbyPerfil([1,10,11]) &&
                                        $evento->xadrezsuicopag_uuid != ""
                                    )
                                        @if(
                                            $xadrezsuicopag_controller
                                        )
                                            @php($xadrezsuicopag_category_request = $xadrezsuicopag_controller->factory("categories")->list($evento->xadrezsuicopag_uuid))

                                            @if(
                                                $xadrezsuicopag_category_request->ok == 1
                                            )
                                                <label for="category_xadrezsuicopag_uuid">PAG: Categoria</label>
                                                <select name="xadrezsuicopag_uuid" id="category_xadrezsuicopag_uuid" class="form-control width-100">
                                                    <option value="">--- Sem Categoria no PAG ---</option>
                                                    @foreach($xadrezsuicopag_category_request->categories as $xadrezsuicopag_category)
                                                        <option value="{{$xadrezsuicopag_category->uuid}}">{{$xadrezsuicopag_category->uuid}} - {{$xadrezsuicopag_category->name}}</option>
                                                    @endforeach
                                                </select>
                                                <small><strong>IMPORTANTE!</strong> Apenas selecione uma categoria do PAG caso esta necessite pagamento.</small>
                                            @endif
                                        @endif
                                    @endif
								</div>
							</div>
							<!-- /.box-body -->

							<div class="box-footer">
								<button type="submit" class="btn btn-success">Enviar</button>
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
							</div>
						</form>
					</div>
				@endif
					<div class="box box-primary">
						<div class="box-header">
							<h3 class="box-title">Categorias Relacionadas</h3>
						</div>
						<!-- form start -->
							<div class="box-body">
								<table id="tabela_categoria" class="table-responsive table-condensed table-striped" style="width: 100%">
									<thead>
										<tr>
											<th>#</th>
											<th>Nome</th>
											<th>Vínculo Principal</th>
                                            @if(
                                                env("XADREZSUICOPAG_URI",null) &&
                                                env("XADREZSUICOPAG_SYSTEM_ID",null) &&
                                                env("XADREZSUICOPAG_SYSTEM_TOKEN",null) &&
                                                \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobalbyPerfil([1,10,11]) &&
                                                $evento->xadrezsuicopag_uuid != ""
                                            )
                                                <th>Vínculo PAG</th>
                                            @endif
											<th width="20%">Opções</th>
										</tr>
									</thead>
									<tbody>
										@foreach($evento->categorias->all() as $categoria)
											<tr>
												<td>{{$categoria->categoria->id}}</td>
												<td>{{$categoria->categoria->name}}</td>
												<td>
													@if($categoria->categoria->grupo_evento_id)
														Grupo de Evento: #{{$categoria->categoria->grupo_evento->id}} - {{$categoria->categoria->grupo_evento->name}}
													@else
														@if($categoria->categoria->evento_id)
															Evento: #{{$categoria->categoria->evento->id}} - {{$categoria->categoria->evento->name}}
														@else
															Estou Confuso. Não há vínculo.
														@endif
													@endif
												</td>

                                                @if(
                                                    env("XADREZSUICOPAG_URI",null) &&
                                                    env("XADREZSUICOPAG_SYSTEM_ID",null) &&
                                                    env("XADREZSUICOPAG_SYSTEM_TOKEN",null) &&
                                                    \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobalbyPerfil([1,10,11]) &&
                                                    $evento->xadrezsuicopag_uuid != ""
                                                )
                                                    <td>
                                                        @if($categoria->xadrezsuicopag_uuid)
                                                            @php($xadrezsuicopag_category_request = $xadrezsuicopag_controller->factory("category")->get($evento->xadrezsuicopag_uuid,$categoria->xadrezsuicopag_uuid))
                                                            @if($xadrezsuicopag_category_request->ok == 1)
                                                                {{$xadrezsuicopag_category_request->category->uuid}} -
                                                                {{$xadrezsuicopag_category_request->category->name}}
                                                            @else
                                                                Há um registro cadastrado, mas não existe uma categoria com este registro cadastrada no PAG.
                                                            @endif
                                                        @else
                                                            -- Não há --
                                                        @endif
                                                    </td>
                                                @endif
												<td>
													<a class="btn btn-success" href="{{url("/evento/".$evento->id."/categoria/edit/".$categoria->id)}}" role="button"><i class="fa fa-edit"></i></a>
                                                    @if($evento->torneios()->whereHas("categorias",function ($q) use ($categoria){ $q->where([["categoria_id","=",$categoria->categoria_id]]); })->count() == 0)
                                                        <a class="btn btn-warning" href="{{url("/evento/".$evento->id."/categoria/createTournament/".$categoria->id)}}" role="button"><i class="fa fa-plus"></i></a>
                                                    @endif
                                                    @if(
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
														\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
													)
														<a class="btn btn-danger" href="{{url("/evento/".$evento->id."/categoria/remove/".$categoria->id)}}" role="button"><i class="fa fa-times"></i></a>
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
			

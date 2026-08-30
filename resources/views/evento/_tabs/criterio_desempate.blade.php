				<br/>
				<section class="col-lg-12 connectedSortable">
					<div class="alert alert-warning alert-dismissible" role="alert">
						<strong>Alerta!</strong><br/>
						Lembre-se que, o <b>Grupo de Evento</b> poderá possuir critérios de desempate também.<br/>
						Caso você escolha um ou mais critérios nesta tela, os critérios de desempate do Grupo de Evento <strong>serão desconsiderados!</strong>
					</div>
				</section>
				<br/>
				@if(
					\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
					\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
				)
					<section class="col-lg-6 connectedSortable">
						<div class="box box-primary">
							<div class="box-header">
								<h3 class="box-title">Relacionar Critério de Desempate</h3>
							</div>
							<!-- form start -->
							<form method="post" action="{{url("/evento/".$evento->id."/criteriodesempate/add")}}">
								<div class="box-body">
									<div class="form-group">
										<label for="criterio_desempate_id">Critério de Desempate</label>
										<select name="criterio_desempate_id" id="criterio_desempate_id" class="form-control width-100">
											<option value="">--- Selecione ---</option>
											@foreach($criterios_desempate as $criterio_desempate)
												<option value="{{$criterio_desempate->id}}">{{$criterio_desempate->id}} - {{$criterio_desempate->name}} @if($criterio_desempate->sm_code) [{{$criterio_desempate->sm_code}}] @endif</option>
											@endforeach
										</select>
									</div>
									<div class="form-group">
										<label for="tipo_torneio_id">Tipo de Torneio</label>
										<select name="tipo_torneio_id" id="tipo_torneio_id" class="form-control width-100">
											<option value="">--- Selecione ---</option>
											@foreach($tipos_torneio as $tipo_torneio)
												<option value="{{$tipo_torneio->id}}">{{$tipo_torneio->id}} - {{$tipo_torneio->name}}</option>
											@endforeach
										</select>
									</div>
									<div class="form-group">
										<label for="softwares_id">Software</label>
										<select name="softwares_id" id="softwares_id" class="form-control width-100">
											<option value="">--- Selecione ---</option>
											@foreach($softwares as $software)
												<option value="{{$software->id}}">{{$software->id}} - {{$software->name}}</option>
											@endforeach
										</select>
									</div>
									<div class="form-group">
										<label for="prioridade">Prioridade</label>
										<input name="prioridade" id="prioridade" class="form-control" type="number" />
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
				<section class="col-lg-6 connectedSortable">
					<div class="box box-primary">
						<div class="box-header">
							<h3 class="box-title">Critérios de Desempate</h3>
						</div>
						<!-- form start -->
							<div class="box-body">
								<table id="tabela_criterio_desempate" class="table-responsive table-condensed table-striped" style="width: 100%">
									<thead>
										<tr>
											<th>#</th>
											<th>Nome</th>
											<th>Tipo de Torneio</th>
											<th>Software</th>
											<th>Prior.</th>
											<th width="20%">Opções</th>
										</tr>
									</thead>
									<tbody>
										@foreach($evento->criterios()->orderBy("tipo_torneio_id","ASC")->orderBy("softwares_id","ASC")->orderBy("prioridade","ASC")->get() as $criterio_desempate)
											<tr>
												<td>{{$criterio_desempate->criterio->id}}</td>
												<td>{{$criterio_desempate->criterio->name}}</td>
												<td>{{$criterio_desempate->tipo_torneio->name}}</td>
												<td>{{$criterio_desempate->software->name}}</td>
												<td>{{$criterio_desempate->prioridade}}</td>
												<td>
													@if(
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
														\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
													)
														<a class="btn btn-danger" href="{{url("/evento/".$evento->id."/criteriodesempate/remove/".$criterio_desempate->id)}}" role="button"><i class="fa fa-times"></i></a>
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
			

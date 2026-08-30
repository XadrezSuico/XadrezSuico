				<br/>
				<section class="col-lg-12 connectedSortable">
					<div class="alert alert-danger alert-dismissible" role="alert">
						<strong>Alerta!</strong><br/>
						Esta aba se destina apenas ao <strong>caso de necessitar de uma categoria específica para este evento</strong>. As categorias aqui cadastradas <strong>não serão replicadas</strong> a qualquer outro Evento ou então Grupo de Evento.<br/>
						Obs: O cadastro da categoria aqui <strong>não retira a necessidade de efetuar a relação</strong> da mesma na aba "Categorias Relacionadas".
					</div>
				</section>
				<br/>
				<section class="col-lg-12 connectedSortable">
					@if(
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
					)
						<div class="box box-primary collapsed-box">
							<div class="box-header">
								<h3 class="box-title">Nova Categoria</h3>
								<div class="pull-right box-tools">
									<button type="button" class="btn btn-primary btn-sm pull-right" data-widget="collapse" data-toggle="tooltip" title="" style="margin-right: 5px;" data-original-title="Collapse">
										<i class="fa fa-plus"></i></button>
								</div>
							</div>
							<!-- form start -->
							<form method="post" action="{{url("/evento/".$evento->id."/categorias/new")}}">
								<div class="box-body">
									<div class="form-group">
										<label for="name">Nome</label>
										<input name="name" id="name" class="form-control" type="text" />
									</div>
									<div class="form-group">
										<label for="idade_minima">Idade Mínima (Em anos)</label>
										<input name="idade_minima" id="idade_minima" class="form-control" type="number" step="1" min="0" />
									</div>
									<div class="form-group">
										<label for="idade_maxima">Idade Máxima (Em anos)</label>
										<input name="idade_maxima" id="idade_maxima" class="form-control" type="number" step="1" min="0" />
									</div>
									<div class="form-group">
										<label for="cat_code">Código Categoria (Padrão Swiss-Manager)</label>
										<input name="cat_code" id="cat_code" class="form-control" type="text" maxlength="10" />
										<small>Exemplo: Para Sub-08, utilizar <strong>U08</strong>.</small>
									</div>
									<div class="form-group">
										<label for="code">Código Grupo (Deve ser único em cada evento, para evitar problemas de processamento do resultado)</label>
										<input name="code" id="code" class="form-control" type="text" maxlength="10" />
										<small>Este código pode ser diferente de acordo com a sua forma de controle. Mas vale saber: é esta a informação que será utilizada para identificação da categoria quando ocorrer o processamento do resultado, e por isso é importante que esteja preenchida no Swiss-Manager e também que seja única para cada categoria.</small>
									</div>
									<div class="form-group">
										<label><input type="checkbox" id="nao_classificar" name="nao_classificar"> Não Classificar Categoria</label>
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
							<h3 class="box-title">Categorias</h3>
						</div>
						<!-- form start -->
							<div class="box-body">
								<table id="tabela_categoria" class="table-responsive table-condensed table-striped" style="width: 100%">
									<thead>
										<tr>
											<th>#</th>
											<th>Nome</th>
											<th>Classificar?</th>
											<th width="20%">Opções</th>
										</tr>
									</thead>
									<tbody>
										@foreach($evento->categorias_cadastradas->all() as $categoria)
											<tr>
												<td>{{$categoria->id}}</td>
												<td>{{$categoria->name}}</td>
												<td>@if(!$categoria->nao_classificar) Sim @else Não @endif</td>
												<td>
													@if(
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
														\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
													)
														<a class="btn btn-success" href="{{url("/evento/".$evento->id."/categorias/dashboard/".$categoria->id)}}" role="button"><i class="fa fa-dashboard"></i></a>
														@if($categoria->isDeletavel()) <a class="btn btn-danger" href="{{url("/evento/".$evento->id."/categorias/delete/".$categoria->id)}}" role="button"><i class="fa fa-times"></i></a> @endif
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
			

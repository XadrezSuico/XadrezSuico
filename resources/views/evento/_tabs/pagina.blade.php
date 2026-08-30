				<br/>
				<section class="col-lg-12 connectedSortable">
					<div class="alert alert-warning alert-dismissible" role="alert">
						<strong>Aviso!</strong><br/>
						Caso queira personalizar a página de inscrição do evento, será possível adicionar um texto e também uma imagem, não sendo itens obrigatórios.
					</div>
					<div class="box box-primary">
						<div class="box-header">
							<h3 class="box-title">Página</h3>
						</div>
						<!-- form start -->

					@if(
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
					)
						<form method="post" action="{{url("/evento/".$evento->id."/pagina")}}" enctype="multipart/form-data">
					@endif
							<div class="box-body">
								<div class="form-group">
									<label for="imagem">Imagem</label>
									@if($evento->pagina)
										@if($evento->pagina->imagem)
											<br/><img src="data:image/png;base64, {!!$evento->pagina->imagem!!}" width="100%" style="max-width: 400px"/>
											@if(\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() || \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) || \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7]))
												<label><input type="checkbox" name="remover_imagem" /> Remover Imagem?</label>
											@endif
										@endif
									@endif
									@if(\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() || \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) || \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7]))
										<input type="file" id="imagem" name="imagem">
										@if ($errors->has('imagem'))
											<span class="help-block">
												<strong>{{ $errors->first('imagem') }}</strong>
											</span>
										@endif
									@endif
								</div>

								<div class="form-group">
									<label for="texto">Texto</label>
									<textarea class="form-control" id="texto" name="texto" placeholder="Texto sobre o Evento" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif >@if($evento->pagina){{$evento->pagina->texto}}@endif</textarea>
								</div>
							</div>
							<!-- /.box-body -->

							<div class="box-footer">
								<button type="submit" class="btn btn-success" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif >Enviar</button>
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
			

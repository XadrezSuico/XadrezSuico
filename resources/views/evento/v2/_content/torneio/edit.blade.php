<!-- Main row -->
<div class="v2-tournament-page-actions">
    <a class="btn btn-primary" href="{{ url('/evento/' . $torneio->evento->id . '/torneios/new') }}"><i class="fa fa-plus" aria-hidden="true"></i> Novo torneio</a>
</div>
@if(session('status'))<div class="alert alert-success" role="status">{{ session('status') }}</div>@endif
@if($errors->any())<div class="alert alert-danger" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

<div class="row">
  <!-- Left col -->
  <section class="col-lg-6 connectedSortable">
		<div class="box box-primary" id="inscricao">
			<div class="box-header">
				<h3 class="box-title">Editar Torneio</h3>
			</div>
			<!-- form start -->
					<form method="post">
				<div class="box-body">
					<div class="form-group">
						<label for="name">Nome</label>
						<input name="name" id="name" class="form-control" type="text" value="{{$torneio->name}}" />
					</div>
					<div class="form-group">
						<label for="tipo_torneio_id">Tipo de Torneio</label>
						<select id="tipo_torneio_id" name="tipo_torneio_id" class="form-control">
							<option value="">-- Selecione --</option>
							@foreach($tipos_torneio as $tipo_torneio)
								<option value="{{$tipo_torneio->id}}">{{$tipo_torneio->name}}</option>
							@endforeach
						</select>
                        <div class="form-group">
                            <label for="softwares_id">Software</label>
                            <select id="softwares_id" name="softwares_id" class="form-control">
                                <option value="">-- Selecione --</option>
                                @foreach($softwares as $software)
                                    <option value="{{$software->id}}">{{$software->name}}</option>
                                @endforeach
                            </select>
                        </div>
					</div>
					<div class="form-group">
						<label>Template de Torneio</label>
						<input class="form-control" type="text" value="@if($torneio->template) {{$torneio->template->name}} @else Sem Template de Torneio @endif" disabled="disabled" />
					</div>
                    @if($torneio->software->isChessCom())
                        <div class="form-group">
                            <label for="chesscom_tournament_slug">Chess.com: Slug do Torneio (apenas depois que o torneio finaliza):</label>
                            <input id="chesscom_tournament_slug" name="chesscom_tournament_slug" class="form-control" type="text" value="{{$torneio->getConfig("chesscom_tournament_slug",true)}}" />
                        </div>
                    @endif
				</div>
				<!-- /.box-body -->

				<div class="box-footer">
					<button type="submit" class="btn btn-success">Enviar</button>
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
				</div>
					</form>
		</div>

  </section>
  <section class="col-lg-6 connectedSortable">
        @if($torneio->tipo_torneio->id != 3 || ($torneio->tipo_torneio->id == 3 AND $torneio->categorias()->count() == 0))
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Nova Relação de Categoria</h3>
                </div>
                <!-- form start -->
                <form method="post" action="{{url("/evento/".$torneio->evento->id."/torneios/".$torneio->id."/categoria/add")}}">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="categoria_id">Categoria</label>
                            <select name="categoria_id" id="categoria_id" class="form-control">
                                <option value="">--- Selecione ---</option>
                                @foreach($categorias as $categoria)
                                    <option value="{{$categoria->id}}">{{$categoria->id}} - {{$categoria->name}}</option>
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
        @endif
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title">Categorias</h3>
			</div>
			<!-- form start -->
				<div class="box-body">
					<div class="js-tournament-categories" data-batch-url="{{ route('evento.torneios.categoria.batch', [$evento->id, $torneio->id]) }}">
                        <form class="js-category-batch-form" method="post" action="{{ route('evento.torneios.categoria.batch', [$evento->id, $torneio->id]) }}">@csrf</form>
                        <div class="v2-category-bulk-toolbar">
                            <div><strong class="js-category-count" aria-live="polite">0 selecionadas</strong> <button type="button" class="btn btn-link js-clear-category-selection" disabled>Limpar seleção</button></div>
                            <label for="category-target-tournament">Migrar para o torneio</label>
                            <select id="category-target-tournament" class="form-control js-category-target">
                                <option value="">Selecione o destino</option>
                                @foreach($evento->torneios()->where('id', '!=', $torneio->id)->orderBy('name')->get() as $target)
                                    <option value="{{ $target->id }}">#{{ $target->id }} — {{ $target->name }}</option>
                                @endforeach
                            </select>
                            <div class="btn-group" role="group" aria-label="Ações nas categorias selecionadas">
                                <button type="button" class="btn btn-warning js-transfer-categories" disabled><i class="fa fa-exchange" aria-hidden="true"></i> Migrar selecionadas</button>
                                <button type="button" class="btn btn-danger js-delete-categories" disabled><i class="fa fa-trash" aria-hidden="true"></i> Excluir selecionadas</button>
                            </div>
                        </div>
                        <table data-dashboard-managed="true" id="tabela" class="v2-tournament-category-table table-responsive table-condensed table-striped" style="width: 100%">
						<thead>
							<tr>
								<th class="v2-no-export"><input type="checkbox" class="js-select-all-categories" aria-label="Selecionar categorias do resultado filtrado" title="Selecionar categorias do resultado filtrado"></th>
                                <th>#</th>
								<th>Nome</th>
								<th width="20%">Opções</th>
							</tr>
						</thead>
						<tbody>
							@foreach($torneio->categorias->all() as $categoria)
								<tr>
									<td><input type="checkbox" class="js-select-category" value="{{ $categoria->id }}" aria-label="Selecionar {{ $categoria->categoria->name }}"></td>
                                    <td>{{$categoria->categoria->id}}</td>
									<td>{{$categoria->categoria->name}}</td>
									<td>
										<a class="btn btn-warning" href="{{url("/evento/".$torneio->evento->id."/torneios/".$torneio->id."/categoria/transfer/".$categoria->id)}}" role="button" title="Trocar categoria de torneio" aria-label="Trocar categoria de torneio"><i class="fa fa-exchange" aria-hidden="true"></i></a>
										<button type="button" class="btn btn-danger js-remove-category" data-category-link="{{ $categoria->id }}" data-category-name="{{ $categoria->categoria->name }}" title="Excluir categoria deste torneio" aria-label="Excluir categoria deste torneio"><i class="fa fa-trash" aria-hidden="true"></i></button>
									</td>
								</tr>
							@endforeach
						</tbody>
          			</table>
                    </div>
				</div>
				<!-- /.box-body -->
		</div>

  </section>
  <!-- /.Left col -->
</div>
<!-- /.row (main row) -->

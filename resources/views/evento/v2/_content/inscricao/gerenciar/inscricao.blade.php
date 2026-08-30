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
<!-- Main row -->
<ul class="nav nav-pills">
  <!--<li role="presentation"><a href="/evento/inscricao/{{$evento->id}}">Nova Inscrição</a></li>-->
  <li role="presentation"><a href="/evento/inscricao/{{$evento->id}}/confirmacao">Confirmar Inscrições</a></li>
</ul>
<div class="row">
  <!-- Left col -->
  <section class="col-lg-12 connectedSortable">
	<!-- general form elements -->
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title">Evento: {{$evento->name}}</h3>
			<div class="pull-right box-tools">
			</div>
		</div>

		<div class="box-body">
			<strong>Categorias:</strong><br/>
			@foreach($evento->categorias->all() as $categoria)
				{{$categoria->categoria->name}}, 
			@endforeach<br/>
			<strong>Cidade:</strong> {{$evento->cidade->name}}<br/>
			<strong>Local:</strong> {{$evento->local}}<br/>
			<strong>Data:</strong> {{$evento->getDataInicio()}}<br/>
			<strong>Maiores informações em:</strong> <a href="{{$evento->link}}" target="_blank">{{$evento->link}}</a><br/>
		</div>
	</div>
	<div class="box box-primary" id="naoPossuiCadastro">
		<div class="box-header">
			<h3 class="box-title">Cadastro de Novo Enxadrista</h3>
		</div>
	  <!-- form start -->
			<div class="box-body">
				<div class="form-group">
					<label for="name">Nome Completo *</label>
					<input type="text" name="name" class="form-control" id="name" placeholder="Insira o Nome Completo do(a) Enxadrista" required="required">
				</div>
				<div class="form-group">
					<label for="born">Data de Nascimento *</label>
					<input type="text" name="born" class="form-control" id="born" placeholder="Insira a Data de Nascimento do(a) Enxadrista" required="required">
				</div>
				<div class="form-group">
					<label for="sexos_id">Sexo *</label>
					<select id="sexos_id" name="sexos_id" class="form-control">
						<option value="">--- Selecione ---</option>
						@foreach($sexos as $sexo)
							<option value="{{$sexo->id}}">{{$sexo->name}}</option>
						@endforeach
					</select>
				</div>
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="cbx_id">ID CBX</label>
							<input name="cbx_id" id="cbx_id" class="form-control" type="text" />
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="fide_id">ID FIDE</label>
							<input name="fide_id" id="fide_id" class="form-control" type="text" />
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="lbx_id">ID LBX</label>
							<input name="lbx_id" id="lbx_id" class="form-control" type="text" />
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="email">E-mail </label>
							<input name="email" id="email" class="form-control" type="text" />
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="celular">Celular </label>
							<input name="celular" id="celular" class="form-control" type="text" />
							<button type="button" id="celular_brasileiro" disabled="disabled" class="btn btn-success">Celular Brasileiro</button>
							<button type="button" id="celular_paraguaio" class="btn btn-success">Celular Paraguaio</button>
							<button type="button" id="celular_argentino" class="btn btn-success">Celular Argentino</button>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="enxadrista_cidade_id">Cidade *</label>
					<select id="enxadrista_cidade_id" class="cidade_id form-control">
						<option value="">--- Selecione uma cidade ---</option>
					</select><br/>
                    <button id="cidadeNaoCadastradaEnxadrista" class="btn btn-success">A cidade não está cadastrada</button>
				</div>
				<div class="form-group">
					<label for="enxadrista_clube_id">Clube</label>
					<select id="enxadrista_clube_id" class="clube_id form-control">
						<option value="">--- Você pode escolher um clube ---</option>
					</select><br/>
                    <button id="clubeNaoCadastradoEnxadrista" class="btn btn-success">O clube não está cadastrado</button>
				</div>

			</div>
			<!-- /.box-body -->

			<div class="box-footer">
				<button id="cadastrarEnxadrista" class="btn btn-success">Enviar</button>
			</div>
	</div>


	<div class="box box-primary" id="inscricao">
		<div class="box-header">
			<h3 class="box-title">Inscrição</h3>
		</div>
	  <!-- form start -->
			<div class="box-body">
				<div class="form-group">
					<label for="enxadrista_id">Enxadrista *</label>
					<select id="enxadrista_id" class="form-control">
						<option value="">--- Selecione um enxadrista ---</option>
					</select>
				</div>
				<div class="form-group">
					<label for="categoria_id">Categoria *</label>
					<select id="categoria_id" class="form-control">
						<option value="">--- Selecione primeiramente um Enxadrista ---</option>
					</select>
				</div>
				<div class="form-group">
					<label for="cidade_id">Cidade *</label>
					<select id="cidade_id" class="cidade_id form-control">
						<option value="">--- Selecione uma cidade ---</option>
					</select>
                    <button id="cidadeNaoCadastradaInscricao" class="btn btn-success">A cidade não está cadastrada</button>
				</div>
				<div class="form-group">
					<label for="clube_id">Clube *</label>
					<select id="clube_id" class="clube_id form-control">
						<option value="">--- Você pode escolher um clube ---</option>
					</select>
                    <button id="clubeNaoCadastradoInscricao" class="btn btn-success">O clube não está cadastrado</button>
				</div>
				@foreach($evento->campos() as $campo)
					<div class="form-group">
						<label for="campo_personalizado_{{$campo->id}}">{{$campo->question}} @if($campo->is_required) * @endif </label>
						<select id="campo_personalizado_{{$campo->id}}" class="campo_personalizado form-control">
							<option value="">--- Selecione uma opção ---</option>
							@foreach($campo->opcoes->all() as $opcao)
								<option value="{{$opcao->id}}">{{$opcao->response}}</option>
							@endforeach
						</select>
					</div>
				@endforeach
				<div class="form-group">
					<label><input type="checkbox" id="confirmado"> Inscrição Confirmada</label>
				</div>
				<div class="form-group">
					<label><input type="checkbox" id="atualizar_cadastro"> Atualizar Cadastro</label>
				</div>
			</div>
			<!-- /.box-body -->

			<div class="box-footer">
				<button id="enviarInscricao" class="btn btn-success">Enviar Inscrição</button>
			</div>
	</div>

  </section>
  <!-- /.Left col -->
</div>
<!-- /.row (main row) -->

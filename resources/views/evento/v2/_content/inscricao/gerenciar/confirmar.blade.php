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
  <li role="presentation"><a href="/evento/inscricao/{{$evento->id}}">Efetuar Nova Inscrição</a></li>
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
	<div class="box box-primary" id="inscricao">
		<div class="box-header">
			<h3 class="box-title">Confirmar Pré-Inscrição</h3>
		</div>
	  <!-- form start -->
			<div class="box-body">
				<div class="form-group">
					<label for="inscricao_id">Enxadrista Pré-Inscrito *</label>
					<select id="inscricao_id" class="form-control">
						<option value="">--- Selecione um enxadrista pré-inscrito ---</option>
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
				<div class="form-group">
					<label><input type="checkbox" id="atualizar_cadastro"> Atualizar Cadastro</label>
				</div>
			</div>
			<!-- /.box-body -->

			<div class="box-footer">
				<button id="confirmarInscricao" class="btn btn-success">Confirmar Pré-Inscrição</button>
			</div>
	</div>

  </section>
  <!-- /.Left col -->
</div>
<!-- /.row (main row) -->

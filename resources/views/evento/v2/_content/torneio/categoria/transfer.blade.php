<!-- Main row -->
<div class="row">
  <!-- Left col -->
  <section class="col-lg-12 connectedSortable">
		<div class="box box-primary" id="inscricao">
			<div class="box-header">
				<h3 class="box-title">Transferir Categoria</h3>
			</div>
			<!-- form start -->
			<form method="post">
				<div class="box-body">
                    <div class="alert alert-warning alert-dismissible" role="alert">
                        <strong>Alerta!</strong><br/>
                        Esta função serve para transferir uma categoria de um torneio para outro. Com isso, as inscrições desta categoria que estão neste torneio também serão migradas.
                    </div>

					<div class="form-group">
                        <label>Categoria: </label> #{{$categoria->categoria->id}} - {{$categoria->categoria->name}}
                    </div>
					<div class="form-group">
						<label for="tournament_id">Torneio para Transferência da Categoria</label>
						<select id="tournament_id" name="tournament_id" class="form-control">
							<option value="">-- Selecione --</option>
							@foreach($torneio->evento->torneios()->where([["id","!=",$torneio->id]])->get() as $outro_torneio)
								<option value="{{$outro_torneio->id}}"> #{{$outro_torneio->id}} - {{$outro_torneio->name}} </option>
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
  <!-- /.Left col -->
</div>
<!-- /.row (main row) -->

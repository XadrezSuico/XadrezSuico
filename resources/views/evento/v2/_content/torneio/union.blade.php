<!-- Main row -->
<div class="row">
  <!-- Left col -->
  <section class="col-lg-12 connectedSortable">
		<div class="box box-primary" id="inscricao">
			<div class="box-header">
				<h3 class="box-title">Unir Torneios</h3>
			</div>
			<!-- form start -->
			<form method="post">
				<div class="box-body">
                    <div class="alert alert-warning alert-dismissible" role="alert">
                        <strong>Alerta!</strong><br/>
                        Esta função serve para efetuar a união entre dois torneios. As categorias e inscrições do "Torneio a Ser Unido ao Torneio Base" são movidas para o "Torneio Base" e o "Torneio a Ser Unido ao Torneio Base" é excluído.<br/>
                        <strong>Tome cuidado com esta função, visto que não é possível voltar às configurações anteriores!</strong>
                    </div>

					<div class="form-group">
                        @php($i=1)
                        @php($categorias_torneio_base_count = $torneio->categorias()->count())
						<label for="name">Torneio Base: </label> #{{$torneio->id}} - {{$torneio->name}} (Categorias: @foreach($torneio->categorias->all() as $categoria) {{$categoria->categoria->name}} @if($categorias_torneio_base_count > $i++), @endif @endforeach )
					</div>
					<div class="form-group">
						<label for="torneio_a_ser_unido">Torneio a Ser Unido ao Torneio Base</label>
						<select id="torneio_a_ser_unido" name="torneio_a_ser_unido" class="form-control">
							<option value="">-- Selecione --</option>
							@foreach($torneios as $Torneio)
                                @php($j=1)
                                @php($categorias_torneio_a_ser_unido_count = $torneio->categorias()->count())
								<option value="{{$Torneio->id}}"> #{{$Torneio->id}} - {{$Torneio->name}}  (Categorias: @foreach($Torneio->categorias->all() as $categoria) {{$categoria->categoria->name}} @if($categorias_torneio_a_ser_unido_count > $j++), @endif @endforeach ) </option>
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

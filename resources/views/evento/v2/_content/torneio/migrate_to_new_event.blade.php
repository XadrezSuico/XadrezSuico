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
                        Esta função serve para tornar um torneio específico em um evento novo.
                        Ao completar a migração não será possível trazer esse torneio novamente para dentro deste evento, então, use com cuidado.
                    </div>

					<div class="form-group">
                        @php($i=1)
                        @php($categorias_torneio_base_count = $torneio->categorias()->count())
						<strong>Torneio: </strong> => #{{$torneio->id}} - {{$torneio->name}} (Categorias: @foreach($torneio->categorias->all() as $categoria) {{$categoria->categoria->name}} @if($categorias_torneio_base_count > $i++), @endif @endforeach )
                        <hr/>
                        <strong>Novo Evento: </strong> {{$torneio->evento->name}} - {{$torneio->name}}

                    </div>

				</div>
				<!-- /.box-body -->

				<div class="box-footer">
					<a href="{{url("/evento/dashboard/".$evento->id."?tab=torneio")}}" class="btn btn-success btn-lg mr-2">Não, não migrar e voltar à lista de torneios do evento</a>
					<a href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/migrate_to_new_event/execute")}}" class="btn btn-danger">Migrar para Novo Evento</a>
				</div>
			</form>
		</div>

  </section>
  <!-- /.Left col -->
</div>
<!-- /.row (main row) -->

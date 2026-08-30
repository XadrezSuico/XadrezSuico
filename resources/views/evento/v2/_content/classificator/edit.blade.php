<!-- Main row -->
	<ul class="nav nav-pills">
		<li role="presentation"><a href="/evento/{{$evento->id}}/classificator/new">Novo Classificador</a></li>
	</ul>
	<div class="row">
  <section class="col-lg-12 connectedSortable">


		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title">Editar Classificador</h3>
			</div>
			<!-- form start -->
			<form method="post">
				<div class="box-body">
					<div class="form-group">
						<label for="event_or_event_group">Grupo de Evento ou Evento?</label>
                        <select name="event_or_event_group" id="event_or_event_group" class="form-control width-100">
                            <option value="event">Evento</option>
                            <option value="event_group">Grupo de Evento</option>
                        </select>
                    </div>
					<div class="form-group" id="event_classificator_id_group">
						<label for="event_classificator_id">Evento que Classifica a Este</label>
                        <select name="event_classificator_id" id="event_classificator_id" class="form-control width-100">
                            <option value="">-- Selecione --</option>
                            @foreach($evento->all() as $evento_class)
                                <option value="{{$evento_class->id}}">{{$evento_class->id}} - {{$evento_class->name}}</option>
                            @endforeach
                        </select>
                    </div>
					<div class="form-group" id="event_group_classificator_id_group">
						<label for="event_group_classificator_id">Grupo de Evento que Classifica a Este</label>
                        <select name="event_group_classificator_id" id="event_group_classificator_id" class="form-control width-100">
                            <option value="">-- Selecione --</option>
                            @foreach($evento->grupo_evento->all() as $grupo_evento_class)
                                <option value="{{$grupo_evento_class->id}}">{{$grupo_evento_class->id}} - {{$grupo_evento_class->name}}</option>
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

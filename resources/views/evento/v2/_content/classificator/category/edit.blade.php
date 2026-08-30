<!-- Main row -->
	<ul class="nav nav-pills">
		<li role="presentation"><a href="{{url("/evento/" . $evento->id . "/classificator/category/new")}}">Novo Vínculo de Categoria</a></li>
	</ul>
	<div class="row">
  <section class="col-lg-12 connectedSortable">


		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title">Editar Vínculo de Categoria</h3>
			</div>
			<!-- form start -->
					<form method="post">
				<div class="box-body">
					<div class="form-group">
						<label for="category_id">Categoria Base</label>
                        <select name="category_id" id="category_id" class="form-control width-100">
                            @foreach($evento->classificator_getCategories() as $category)
                                <option value="{{$category->id}}">{{$category->id}} - {{$category->name}}</option>
                            @endforeach
                        </select>
                    </div>
					<div class="form-group">
						<label for="category_classificator_id">Categoria deste Evento</label>
                        <select name="category_classificator_id" id="category_classificator_id" class="form-control width-100">
                            @foreach($evento->categorias_cadastradas->all() as $category)
                                <option value="{{$category->id}}">{{$category->id}} - {{$category->name}}</option>
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

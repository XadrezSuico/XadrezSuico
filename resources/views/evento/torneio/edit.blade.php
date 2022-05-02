@extends('adminlte::page')

@section("title", 'Evento #'.$torneio->evento->id." - Dashboard de Torneio")

@section('content_header')
  <h1>Evento #{{$torneio->evento->id}} ({{$torneio->evento->name}}) - Dashboard de Torneio</h1>
@stop


@section('css')
	<style>
		.display-none, .displayNone{
			display: none;
		}
	</style>
@endsection

@section("content")
<!-- Main row -->
<ul class="nav nav-pills">
  <li role="presentation"><a href="/evento/dashboard/{{$torneio->evento->id}}?tab=torneio">Voltar a Lista de Torneios</a></li>
  <li role="presentation"><a href="/evento/{{$torneio->evento->id}}/torneios/new">Novo Torneio</a></li>
</ul>
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
					<table id="tabela" class="table-responsive table-condensed table-striped" style="width: 100%">
						<thead>
							<tr>
								<th>#</th>
								<th>Nome</th>
								<th width="20%">Opções</th>
							</tr>
						</thead>
						<tbody>
							@foreach($torneio->categorias->all() as $categoria)
								<tr>
									<td>{{$categoria->categoria->id}}</td>
									<td>{{$categoria->categoria->name}}</td>
									<td>
										<a class="btn btn-danger" href="{{url("/evento/".$torneio->evento->id."/torneios/".$torneio->id."/categoria/remove/".$categoria->id)}}" role="button">Remover Relação</a>
									</td>
								</tr>
							@endforeach
						</tbody>
          			</table>
				</div>
				<!-- /.box-body -->
		</div>

  </section>
  <!-- /.Left col -->
</div>
<!-- /.row (main row) -->

@endsection

@section("js")
<!-- Morris.js charts -->
<script type="text/javascript" src="{{url("/js/jquery.mask.min.js")}}"></script>
<script type="text/javascript">
  $(document).ready(function(){
		$("#categoria_id").select2();
		$("#tipo_torneio_id").select2().val({{$torneio->tipo_torneio_id}}).change();
		$("#softwares_id").select2().val({{$torneio->softwares_id}}).change();
		$("#tabela").DataTable({
				responsive: true,
		});
  });
</script>
@endsection

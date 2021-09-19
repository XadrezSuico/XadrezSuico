@extends('adminlte::page')

@section("title", "Grupo de Evento #".$grupo_evento->id." (".$grupo_evento->name.") >> Dashboard de Categoria")

@section('content_header')
  <h1>Grupo de Evento #{{$grupo_evento->id}} ({{$grupo_evento->name}}) >> Dashboard de Categoria</h1>
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
		<li role="presentation"><a href="/grupoevento/dashboard/{{$grupo_evento->id}}?tab=categoria">Voltar a Lista de Categorias na Dashboard de Grupo de Evento</a></li>
	</ul>
	<div class="row">
  <section class="col-lg-6 connectedSortable">


		<div class="box box-primary" id="inscricao">
			<div class="box-header">
				<h3 class="box-title">Editar Categoria</h3>
			</div>
			<!-- form start -->
					<form method="post">
				<div class="box-body">
					<div class="form-group">
						<label for="name">Nome</label>
						<input name="name" id="name" class="form-control" type="text" value="{{$categoria->name}}" />
					</div>
					<div class="form-group">
						<label for="name">Idade Mínima (Em anos)</label>
						<input name="idade_minima" id="idade_minima" class="form-control" type="text" value="{{$categoria->idade_minima}}" />
					</div>
					<div class="form-group">
						<label for="name">Idade Máxima (Em anos)</label>
						<input name="idade_maxima" id="idade_maxima" class="form-control" type="text" value="{{$categoria->idade_maxima}}" />
					</div>
					<div class="form-group">
						<label for="name">Código Categoria (Padrão Swiss-Manager)</label>
						<input name="cat_code" id="cat_code" class="form-control" type="text" value="{{$categoria->cat_code}}" />
						<small>Exemplo: Para Sub-08, utilizar <strong>U08</strong>.</small>
					</div>
					<div class="form-group">
						<label for="name">Código Grupo (Deve ser único em cada evento, para evitar problemas de processamento do resultado)</label>
						<input name="code" id="code" class="form-control" type="text" value="{{$categoria->code}}" />
						<small>Este código pode ser diferente de acordo com a sua forma de controle. Mas vale saber: é esta a informação que será utilizada para identificação da categoria quando ocorrer o processamento do resultado, e por isso é importante que esteja preenchida no Swiss-Manager e também que seja única para cada categoria.</small>
					</div>
					<div class="form-group">
						<label for="name">Quantos premiam desta categoria?</label>
						<input name="quantos_premiam" id="quantos_premiam" class="form-control" type="text" value="{{$categoria->quantos_premiam}}" />
						<small>Informe neste campo quantos enxadristas premiam nesta categoria.</small>
					</div>
                    @if($categoria->grupo_evento->classificador)
                        <div class="form-group">
                            <label for="categoria_classificadora_id">Categoria Classificadora ({{$categoria->grupo_evento->classificador->name}})</label>
                            <select name="categoria_classificadora_id" id="categoria_classificadora_id" class="form-control width-100">
                                <option value="">--- Você pode selecionar uma categoria ---</option>
                                @foreach($categoria->grupo_evento->classificador->categorias->all() as $cc)
                                    <option value="{{$cc->id}}">{{$cc->id}} - {{$cc->name}}</option>
                                @endforeach
                            </select>
                            <small><strong>IMPORTANTE!</strong> Essa configuração serve para caso tenha um grupo de evento que classifica para este grupo de evento. Aqui vai a Categoria do Grupo de Evento Classificador que classifica para esta Categoria.</small>
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
		<!-- Sexos da Categoria -->
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title">Relacionar Sexo</h3>
			</div>
			<!-- form start -->
			<form method="post" action="{{url("/grupoevento/".$grupo_evento->id."/categorias/".$categoria->id."/sexo/add")}}">
				<div class="box-body">
					<div class="form-group">
						<label for="sexos_id">Sexo</label>
						<select name="sexos_id" id="sexos_id" class="form-control">
							<option value="">--- Selecione ---</option>
							@foreach($sexos as $sexo)
								<option value="{{$sexo->id}}">{{$sexo->id}} - {{$sexo->name}}</option>
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
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title">Sexos</h3>
			</div>
			<!-- form start -->
				<div class="box-body">
					<table id="tabela_sexos" class="table-responsive table-condensed table-striped" style="width: 100%">
						<thead>
							<tr>
								<th>#</th>
								<th>Nome</th>
								<th width="20%">Opções</th>
							</tr>
						</thead>
						<tbody>
							@foreach($categoria->sexos()->orderBy("sexos_id","ASC")->get() as $sexo)
								<tr>
									<td>{{$sexo->sexo->id}}</td>
									<td>{{$sexo->sexo->name}}</td>
									<td>
										<a class="btn btn-danger" href="{{url("/grupoevento/".$grupo_evento->id."/categorias/".$categoria->id."/sexo/remove/".$sexo->id)}}" role="button"><i class="fa fa-times"></i></a>
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
		$("#sexos_id").select2();
		$("#tabela_sexos").DataTable();

        @if($categoria->classificadora)
		    $("#categoria_classificadora_id").select2();
			$("#categoria_classificadora_id").val([{{$categoria->classificadora->id}}]).change();
        @endif
  });
</script>
@endsection

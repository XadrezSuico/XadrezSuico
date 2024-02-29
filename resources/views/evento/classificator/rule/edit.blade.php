@extends('adminlte::page')

@section("title", "Evento #".$evento->id." (".$evento->name.") >> XadrezSuíço Classificador >> Vínculo de Categorias >> Editar")
@section('content_header')
  <h1>Evento #{{$evento->id}} ({{$evento->name}}) >> XadrezSuíço Classificador >> Vínculo de Categorias >> Editar</h1>
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
		<li role="presentation"><a href="/evento/dashboard/{{$evento->id}}?tab=classificator">Voltar a Lista de Classificadores na Dashboard de Evento</a></li>
		<li role="presentation"><a href="{{url("/evento/".$evento->id."/classificator/".$event_classificates->id."/rule/new")}}">Nova Regra</a></li>
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
						<label for="type">Tipo de Regra</label>
                        <select name="type" id="type" class="form-control width-100">
                            <option value="" selected>-- Selecione --</option>
                            @foreach(\App\Enum\ClassificationTypeRule::list() as $key => $type)
                                <option value="{{$key}}">{{$type["name"]}}</option>
                            @endforeach
                        </select>
                    </div>
					<div class="form-group" id="value_block">
						<label for="value">Valor</label>
						<input name="value" id="value" class="form-control" type="text" value="{{$event_classificate_rule->value}}"/>
                    </div>
					<div class="form-group" id="event_block">
						<label for="event_id">Evento</label>
                        <select name="event_id" id="event_id" class="form-control width-100">
                            <option value="">-- Selecione --</option>
                            @foreach(\App\Evento::all() as $event_item)
                                <option value="{{$event_item->id}}">#{{$event_item->id}} - {{$event_item->name}}</option>
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

@endsection

@section("js")
<!-- Morris.js charts -->
<script type="text/javascript" src="{{url("/js/jquery.mask.min.js")}}"></script>
<script type="text/javascript">
  $(document).ready(function(){
        $("#type").select2();
        $("#event_id").select2();


        $("#type").val(["{{$event_classificate_rule->type}}"]).change();
        $("#event_id").val([{{$event_classificate_rule->event_id}}]).change();


        $("#type").on("select2:select",function(){
            checkTypeSelected(true);
        });

        checkTypeSelected(false);
  });


  function checkTypeSelected(clear){
    switch($("#type").val()){
        case "position":
        case "position-absolute":
        case "place-by-quantity":
            $("#value_block").show("fast");
            $("#event_block").hide("fast");

            if(clear) $("#event_block select").val("").change();
        break;
        case "pre-classificate":
            $("#value_block").hide("fast");
            $("#event_block").show("fast");

            if(clear) $("#value_block input").val("");
            break;
        default:
            $("#value_block").hide("fast");
            $("#event_block").hide("fast");

            if(clear) {
                $("#value_block input").val("");
                $("#event_block select").val("").change();
            }
    }
  }
</script>
@endsection

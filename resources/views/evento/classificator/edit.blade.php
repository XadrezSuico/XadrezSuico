@extends('adminlte::page')

@section("title", "Evento #".$evento->id." (".$evento->name.") >> Classificador >> Editar")
@section('content_header')
  <h1>Evento #{{$evento->id}} ({{$evento->name}}) >> Classificador >> Editar</h1>
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

@endsection

@section("js")
<!-- Morris.js charts -->
<script type="text/javascript" src="{{url("/js/jquery.mask.min.js")}}"></script>
<script type="text/javascript">

    $(document).ready(function(){
        $("#event_classificator_id").select2();
        $("#event_group_classificator_id").select2();
        $("#event_or_event_group").select2();

        $("#event_or_event_group").on("select2:select",()=>{
            checkEventOrEventGroup();
        });

        @if($event_classificate->event_classificator_id)
            $("#event_classificator_id").val([{{$event_classificate->event_classificator_id}}]).change();
            $("#event_or_event_group").val('event').change();
        @endif
        @if($event_classificate->event_group_classificator_id)
            $("#event_group_classificator_id").val([{{$event_classificate->event_group_classificator_id}}]).change();
            $("#event_or_event_group").val('event_group').change();
        @endif

        checkEventOrEventGroup();

    });

    async function checkEventOrEventGroup() {
        return new Promise((resolve, reject)=>{

            const eventClassificator = $("#event_classificator_id_group");
            const eventGroupClassificator = $("#event_group_classificator_id_group");

            eventClassificator.hide(100);
            eventGroupClassificator.hide(100);

            setTimeout(() => {
                if ($("#event_or_event_group").val() === "event") {
                    eventClassificator.show(100);
                } else {
                    eventGroupClassificator.show(100);
                }

                resolve();
            }, 200);
        });
    }
</script>
@endsection

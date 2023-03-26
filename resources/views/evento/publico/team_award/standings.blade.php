@extends('adminlte::page')

@section('title', 'Evento #'.$event->id.' - Classificação de Times')

@section('content_header')
    <h1>Evento #{{$event->id}} ({{$event->name}}) - Classificação de Times</h1>
@stop

@section('content')
	@if (session('status'))
		<div class="alert alert-success">
				{{ session('status') }}
		</div>
	@endif
    @if(\Illuminate\Support\Facades\Auth::check())
        <ul class="nav nav-pills">
            <li role="presentation"><a href="{{url("/evento/dashboard/".$event->id)}}">Voltar à Dashboard de Evento</a></li>
        </ul>
	@endif
    <div class="box">
        <div class="box-body">
			<div class="form-group">
                <label for="team_awards_id">Premiações</label>
                <select id="team_awards_id" name="team_awards_id" class="form-control">
                    <option value=""> -- Selecione uma Premiação antes de acessar a Lista de Resultados --</option>
                    @foreach($event->event_team_awards->all() as $event_team_award)
                        <option value="{{$event_team_award->id}}">{{$event_team_award->name}}</option>
                    @endforeach
                </select>
            </div>
            <button id="acessar" type="button" class="btn btn-success">Acessar Lista de Resultados de Classificação de Times</button>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
		</div>
	</div>
@endsection
@section("js")
<script type="text/javascript">
    $(document).ready(function(){
        $("#categoria_id").select2();
    });
    $("#acessar").on("click",function(){
        @if(\Illuminate\Support\Facades\Auth::check())
            location.href = "{{url("/evento/".$event->id."/team_awards/")}}/".concat($("#team_awards_id").val()).concat("/results");
        @else
            location.href = "{{url("/evento/".$event->id."/team_awards/")}}/".concat($("#team_awards_id").val()).concat("/results");
        @endif
    });
</script>
@endsection

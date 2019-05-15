@extends('adminlte::page')

@section('title', 'Grupo de Evento #'.$grupo_evento->id.' - Resultados')

@section('content_header')
    <h1>Grupo de Evento #{{$grupo_evento->id}} ({{$grupo_evento->name}}) - Resultados</h1>
@stop

@section('content')
	@if (session('status'))
		<div class="alert alert-success">
				{{ session('status') }}
		</div>
	@endif
    @if(\Illuminate\Support\Facades\Auth::check())
        <ul class="nav nav-pills">
            <li role="presentation"><a href="{{url("/grupoevento/")}}">Voltar à Lista de Grupos de Eventos</a></li>
        </ul>
	@endif
    <div class="box">
        <div class="box-body">
			<div class="form-group">
                <label for="categoria_id">Categoria</label>
                <select id="categoria_id" name="categoria_id" class="form-control">
                    <option value=""> -- Selecione uma Categoria antes de acessar a Lista de Resultados Gerais --</option>
                    @foreach($grupo_evento->categorias->all() as $categoria)
                        @if(!$categoria->nao_classificar) <option value="{{$categoria->categoria->id}}">{{$categoria->categoria->name}}</option> @endif
                    @endforeach
                </select>
            </div>
            <button id="acessar" type="button" class="btn btn-success">Acessar Lista de Resultados Gerais</button>
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
            location.href = "{{url("/grupoevento/".$grupo_evento->id."/resultados")}}/".concat($("#categoria_id").val());
        @else
            location.href = "{{url("/grupoevento/".$grupo_evento->id."/resultados")}}/".concat($("#categoria_id").val());
        @endif
    });
</script>
@endsection
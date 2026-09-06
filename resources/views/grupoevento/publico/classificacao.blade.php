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
                <label for="categoria_id">Classificação</label>
                <select id="categoria_id" name="categoria_id" class="form-control">
                    <option value=""> -- Selecione uma classificação antes de acessar a Lista de Resultados --</option>
                    @if($grupo_evento->classificaIndividualGeral())
                        <option value="geral">Geral (cross-categoria)</option>
                    @endif
                    @if($grupo_evento->classificaIndividualPorCategoria())
                        @foreach($grupo_evento->categorias()->where([["nao_classificar","=",0]])->get() as $categoria)
                            @if(!$categoria->nao_classificar) <option value="{{$categoria->id}}">{{$categoria->name}}</option> @endif
                        @endforeach
                    @endif
                </select>
            </div>
            <button id="acessar" type="button" class="btn btn-success">Acessar Lista de Resultados</button>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
		</div>
	</div>
@endsection
@section("js")
<script type="text/javascript">
    $(document).ready(function(){
        $("#categoria_id").select2();
        @if($grupo_evento->classificaIndividualGeral() && !$grupo_evento->classificaIndividualPorCategoria())
            $("#categoria_id").val('geral').trigger('change');
        @endif
    });
    $("#acessar").on("click",function(){
        var selecionado = $("#categoria_id").val();
        if(!selecionado){
            return;
        }
        location.href = "{{url("/grupoevento/".$grupo_evento->id."/resultados")}}/".concat(selecionado);
    });
</script>
@endsection

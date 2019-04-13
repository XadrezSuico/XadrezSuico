@extends('adminlte::page')

@section('title', 'Evento #'.$evento->id.' - Resultados')

@section('content_header')
    <h1>Evento #{{$evento->id}} ({{$evento->name}}) - Resultados</h1>
@stop

@section('content')
	@if (session('status'))
		<div class="alert alert-success">
				{{ session('status') }}
		</div>
	@endif
    <div class="box">
        <div class="box-body">
			<div class="form-group">
                <label for="categoria_id">Categoria</label>
                <select id="categoria_id" name="categoria_id" class="form-control">
                    <option value=""> -- Selecione uma Categoria antes de acessar a Lista de Resultados --</option>
                    @foreach($evento->categorias->all() as $categoria)
                        <option value="{{$categoria->categoria->id}}">{{$categoria->categoria->name}}</option>
                    @endforeach
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
    });
    $("#acessar").on("click",function(){
        @if(\Illuminate\Support\Facades\Auth::check())
            location.href = "{{url("/evento/".$evento->id."/resultados")}}/".concat($("#categoria_id").val()).concat("/interno");
        @else
            location.href = "{{url("/evento/".$evento->id."/resultados")}}/".concat($("#categoria_id").val());
        @endif
    });
</script>
@endsection
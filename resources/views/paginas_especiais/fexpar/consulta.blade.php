@extends('adminlte::page')

@section('title', 'Vínculo Federativo - Consulta')

@section('content_header')
    <h1>Vínculo Federativo - Consulta</h1>
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
                <label for="uuid_consulta"># (ID) da Consulta</label>
                <input type="text" id="uuid_consulta" name="uuid_consulta" class="form-control" placeholder="________-____-____-____-____________" />
                <hr/>
                <small>IMPORTANTE! O # (ou ID) da Consulta fica abaixo do # do Vínculo, conforme é possível observar abaixo:</small><br/>
                <img src="{{asset("/img/_fexpar/exemplo_id_consulta.png")}}" style="max-width: 100%" />
            </div>
            <button id="acessar" type="button" class="btn btn-success">Acessar Consulta</button>
		</div>
	</div>
@endsection
@section("js")
<script type="text/javascript" src="{{url("/js/jquery.mask.min.js")}}"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $("#uuid_consulta").mask("HHHHHHHH-HHHH-HHHH-HHHH-HHHHHHHHHHHH", {
            'translation': {
                H: {
                    pattern: /[A-Fa-f0-9]/
                }
            },
            placeholder: "________-____-____-____-____________"
        });
    });
    $("#acessar").on("click",function(){
        if($("#uuid_consulta").val().length === 36){
            location.href = "{{url("/especiais/fexpar/vinculos/consulta")}}/".concat($("#uuid_consulta").val());
        }else{
            alert("Há caracteres faltando neste ID informado.");
        }
    });
    var enter = false;
    $("#uuid_consulta").on("keyup",function(k){
        if(k.which === 13){
            if(enter){
                enter = false;
            }else{
                if($("#uuid_consulta").val().length === 36){
                    location.href = "{{url("/especiais/fexpar/vinculos/consulta")}}/".concat($("#uuid_consulta").val());
                }else{
                    alert("Há caracteres faltando neste ID informado.");
                }
                enter = true;
            }
        }
    });
</script>
@endsection

@extends('adminlte::page')

@section('title', 'Rating')

@section('content_header')
    <h1>Rating</h1>
@stop

@section('content')
	@if (session('status'))
		<div class="alert alert-success">
				{{ session('status') }}
		</div>
	@endif
    <div class="box">
        <div class="box-body">
			<div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                <label for="tipo_ratings_id">Tipo de Rating</label>
                <select id="tipo_ratings_id" name="tipo_ratings_id" class="form-control">
                    <option value=""> -- Selecione um Tipo de Rating antes de acessar a Lista de Ratings --</option>
                    @foreach($tipos_rating as $tipo_rating)
                        <option value="{{$tipo_rating->id}}">{{$tipo_rating->name}}</option>
                    @endforeach
                </select>
            </div>
            <button id="acessar" type="button" class="btn btn-success">Acessar Lista de Ratings</button>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
		</div>
	</div>
@endsection
@section("js")
<script type="text/javascript">
    $(document).ready(function(){
        $("#tipo_ratings_id").select2();
    });
    $("#acessar").on("click",function(){
        location.href = "{{url("/rating/list")}}/".concat($("#tipo_ratings_id").val());
    });
</script>
@endsection
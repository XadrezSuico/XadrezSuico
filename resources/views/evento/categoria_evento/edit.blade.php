@extends('adminlte::page')

@section("title", "Evento #".$evento->id." (".$evento->name.") >> Dashboard de Categoria")
@section('content_header')
  <h1>Evento #{{$evento->id}} ({{$evento->name}}) >> Dashboard de Categoria</h1>
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
		<li role="presentation"><a href="/evento/dashboard/{{$evento->id}}?tab=categoria">Voltar a Lista de Categorias na Dashboard de Evento</a></li>
	</ul>
	<div class="row">
        <section section class="col-lg-12 connectedSortable">


            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Editar Categoria</h3>
                </div>
                <!-- form start -->
                <form method="post">
                    <div class="box-body">
                        <div class="form-group">
                            <label>Categoria: </label> {{$categoria->categoria->id}} - {{$categoria->categoria->name}}<br/>
                            @if(
                                env("XADREZSUICOPAG_URI",null) &&
                                env("XADREZSUICOPAG_SYSTEM_ID",null) &&
                                env("XADREZSUICOPAG_SYSTEM_TOKEN",null) &&
                                \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobalbyPerfil([1,10,11]) &&
                                $evento->xadrezsuicopag_uuid != ""
                            )
                                @if(
                                    $xadrezsuicopag_controller
                                )
                                    @php($xadrezsuicopag_category_request = $xadrezsuicopag_controller->factory("categories")->list($evento->xadrezsuicopag_uuid))

                                    @if(
                                        $xadrezsuicopag_category_request->ok == 1
                                    )
                                        <label for="category_xadrezsuicopag_uuid">XadrezSuíçoPAG: Categoria</label>
                                        <select name="xadrezsuicopag_uuid" id="category_xadrezsuicopag_uuid" class="form-control width-100">
                                            <option value="">--- Sem Categoria no XadrezSuíçoPAG ---</option>
                                            @foreach($xadrezsuicopag_category_request->categories as $xadrezsuicopag_category)
                                                <option value="{{$xadrezsuicopag_category->uuid}}">{{$xadrezsuicopag_category->uuid}} - {{$xadrezsuicopag_category->name}}</option>
                                            @endforeach
                                        </select>
                                        <small><strong>IMPORTANTE!</strong> Apenas selecione uma categoria do XadrezSuíçoPAG caso esta necessite pagamento.</small>
                                    @endif
                                @endif
                            @endif
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
		$("#category_xadrezsuicopag_uuid").select2();
        @if($categoria->xadrezsuicopag_uuid)
		    $("#category_xadrezsuicopag_uuid").val("{{$categoria->xadrezsuicopag_uuid}}").change();
        @endif
    });
</script>
@endsection

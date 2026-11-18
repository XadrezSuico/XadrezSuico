@extends('layouts.v2.catalog')

@section("title", "Editar Clube")

@section('page-header')
    Editar Clube
@endsection




@section('catalog-content')
<!-- Main row -->
<ul class="v2-catalog-nav">
  <li role="presentation"><a href="/clube">Voltar a Lista de Clubes</a></li>
  <li role="presentation"><a href="/clube/new">Novo Clube</a></li>
</ul>
<div class="v2-catalog-grid">
  <!-- Left col -->
  <section class="v2-catalog-column">
	<div class="v2-catalog-panel" id="inscricao">
		<div class="v2-catalog-panel-header">
			<h3 class="v2-catalog-panel-title">Editar Clube</h3>
		</div>
	  <!-- form start -->
      <form method="post" class="v2-catalog-form">
				<div class="v2-catalog-panel-body">
					<div class="v2-catalog-field">
						<label for="name">Nome</label>
						<input name="name" id="name" class="v2-catalog-input" type="text" value="{{ old('name', $clube->getName(false)) }}" />
					</div>
					<div class="v2-catalog-field">
						<label for="abbr">Abreviação</label>
						<input name="abbr" id="abbr" class="v2-catalog-input" type="text" maxlength="3" value="{{ old('abbr', $clube->abbr) }}" />
					</div>
					<div class="v2-catalog-field">
						<label for="cidade_id">Cidade</label>
						<select id="cidade_id" name="cidade_id" class="v2-catalog-input">
							<option value="">--- Selecione uma cidade ---</option>
							@foreach($cidades as $cidade)
								<option value="{{$cidade->id}}">{{$cidade->id}} - {{$cidade->name}}</option>
							@endforeach
						</select>
					</div>
                    @if(env("ENTITY_DOMAIN",null) == "fexpar.com.br")
                        <div class="v2-catalog-field">
                            <label><input type="checkbox" name="is_fexpar___clube_filiado" @if($clube->is_fexpar___clube_filiado) checked="checked" @endif /> É clube filiado?</label>
                        </div>
                        <div class="v2-catalog-field">
                            <label><input type="checkbox" name="is_fexpar___clube_valido_vinculo_federativo" @if($clube->is_fexpar___clube_valido_vinculo_federativo) checked="checked" @endif /> É clube válido para vinculo federativo?</label>
                        </div>
                    @endif
				</div>
			<!-- /.v2-catalog-panel-body -->

				<div class="v2-catalog-panel-footer">
					<button type="submit" class="btn btn-success"><i class="fa fa-check" aria-hidden="true"></i> Salvar</button>
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
		$("#cidade_id").select2().val([{{$clube->cidade_id}}]).change();
  });
</script>
@endsection

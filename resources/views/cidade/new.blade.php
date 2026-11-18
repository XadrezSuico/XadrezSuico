@extends('layouts.v2.catalog')

@section("title", "Nova Cidade")

@section('page-header')
    Nova Cidade
@endsection




@section('catalog-content')
<!-- Main row -->
<ul class="v2-catalog-nav">
  <li role="presentation"><a href="/cidade">Voltar a Lista de Cidades</a></li>
</ul>
<div class="v2-catalog-grid">
  <!-- Left col -->
  <section class="v2-catalog-column">
	<div class="v2-catalog-panel" id="inscricao">
		<div class="v2-catalog-panel-header">
			<h3 class="v2-catalog-panel-title">Nova Cidade</h3>
		</div>
	  <!-- form start -->
        <form method="post" class="v2-catalog-form">
			<div class="v2-catalog-panel-body">
				<div class="v2-catalog-field">
					<label for="name">Nome</label>
					<input name="name" id="name" class="v2-catalog-input" type="text" value="{{ old('name') }}" />
				</div>
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
		
  });
</script>
@endsection

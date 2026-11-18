@extends('layouts.v2.catalog')

@section("title", 'Clube #'.$clube_base->id." - Unir Clubes")

@section('page-header')
    Clube #{{$clube_base->id}} ({{$clube_base->name}}) - Unir Clubes
@endsection




@section('catalog-content')
<!-- Main row -->
<ul class="v2-catalog-nav">
  <li role="presentation"><a href="/clube">Voltar a Lista de Clubes</a></li>
</ul>
<div class="v2-catalog-grid">
  <!-- Left col -->
  <section class="v2-catalog-column">
		<div class="v2-catalog-panel" id="inscricao">
			<div class="v2-catalog-panel-header">
				<h3 class="v2-catalog-panel-title">Unir Clubes</h3>
			</div>
			<!-- form start -->
			<form method="post" class="v2-catalog-form">
				<div class="v2-catalog-panel-body">
                    <div class="alert alert-warning alert-dismissible" role="alert">
                        <strong>Alerta!</strong><br/>
                        Esta função serve para efetuar a união entre dois clubes.
                        Todas as inscrições, vínculos e enxadristas do "Clube a ser Unido ao Clube Base" serão importados para o "Clube Base", e por fim, o "Clube a ser Unido ao Clube Base" será excluído.<br/>
                        <strong>Tome cuidado com esta função, visto que não é possível voltar às configurações anteriores!</strong>
                    </div>

					<div class="v2-catalog-field">
						<label for="name">Clube Base: </label> #{{$clube_base->id}} - {{$clube_base->getName()}} ({{$clube_base->getPlace()}})
					</div>
					<div class="v2-catalog-field">
						<label for="clube_a_ser_unido">Clube a Ser Unido ao Clube Base</label>
						<select id="clube_a_ser_unido" name="clube_a_ser_unido" class="v2-catalog-input">
							<option value="">-- Selecione --</option>
							@foreach($clubes as $clube)
								<option value="{{$clube->id}}"> #{{$clube->id}} - - {{$clube->getName()}} ({{$clube->getPlace()}})</option>
							@endforeach
						</select>
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
		$("#clube_a_ser_unido").select2();
    });
</script>
@endsection

@extends('adminlte::page')

@section("title", "Nova Rede")

@section('content_header')
  <h1>Efetuar Nova Inscrição</h1>
  <ol class="breadcrumb">
    <li><a href="{{url("/")}}"><i class="fa fa-dashboard"></i> Home</a></li>
    <li><a href="{{url("/senha")}}"><i class="fa fa-network"></i> Rede</a></li>
    <li><a href="#"><i class="fa fa-network"></i> Listar Redes</a></li>
	<li class="active"><i class="fa fa-plus"></i> Nova Rede</li>
  </ol>
@stop


@section('css')
	<style>
		.display-none, .displayNone{
			display: none;
		}
	</style>
@endsection

@section("content")
<div class="modal fade modal-danger" id="alerts" tabindex="-1" role="dialog" aria-labelledby="alerts">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title">ERRO!</h4>
	  </div>
	  <div class="modal-body">
		<span id="alertsMessage"></span>
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
	  </div>
	</div>
  </div>
</div>
<!-- Main row -->
<ul class="nav nav-pills">
  <li role="presentation"><a href="/senha">Listar Todas</a></li>
</ul>
<div class="row">
  <!-- Left col -->
  <section class="col-lg-12 connectedSortable">
	<!-- general form elements -->
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title">Você já possui cadastro?</h3>
			<div class="pull-right box-tools">
				<button type="button" class="btn btn-primary btn-sm pull-right" data-widget="collapse" data-toggle="tooltip" title="" style="margin-right: 5px;" data-original-title="Abrir/Fechar">
					<i class="fa fa-minus"></i></button>
			</div>
		</div>
	  <!-- form start -->
	  <form role="form" method="post">
			<div class="box-body">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<div class="form-group @if ($errors->has('name')) has-error @endif">
					<label for="name">Nome</label>
					<input type="text" name="name" class="form-control" id="name" placeholder="Insira o Nome da Rede" required="required">
					@if ($errors->has('name'))
						<label class="control-label" for="name"><i class="fa fa-times-circle-o"></i> {{ $errors->first('name') }}</label>
					@endif
				</div>
			</div>
			<!-- /.box-body -->

			<div class="box-footer">
				<button type="submit" class="btn btn-success">Enviar</button>
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
<script src="{{url("vendor/raphael/raphael.min.js")}}"></script>
<script src="{{url("plugins/morris/morris.min.js")}}"></script>
<!-- Sparkline -->
<script src="{{url("plugins/sparkline/jquery.sparkline.min.js")}}"></script>
<!-- jvectormap -->
<script src="{{url("plugins/jvectormap/jquery-jvectormap-1.2.2.min.js")}}"></script>
<script src="{{url("plugins/jvectormap/jquery-jvectormap-world-mill-en.js")}}"></script>
<!-- jQuery Knob Chart -->
<script src="{{url("plugins/knob/jquery.knob.js")}}"></script>
<!-- daterangepicker -->
<script src="{{url("vendor/moment/moment.min.js")}}"></script>
<script src="{{url("plugins/daterangepicker/daterangepicker.js")}}"></script>
<!-- datepicker -->
<script src="{{url("plugins/datepicker/bootstrap-datepicker.js")}}"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="{{url("plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js")}}"></script>
<!-- Slimscroll -->
<script src="{{url("plugins/slimScroll/jquery.slimscroll.min.js")}}"></script>
<!-- FastClick -->
<script src="{{url("plugins/fastclick/fastclick.js")}}"></script>
<script type="text/javascript" src="{{url("/js/jquery.mask.min.js")}}"></script>
<script type="text/javascript">

  $(document).ready(function(){
		
  });
</script>
@endsection

@extends('adminlte::page')

@section("title", "ERRO!")

@section('content_header')
  <h1>ERRO!</h1>
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
                <span id="alertsMessage">As inscrições para este evento devem ser feitas apenas com o link divulgado (Inscrições Privadas apenas via link).</span>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
			</div>
		</div>
  </div>
</div>
<div class="row">
  <!-- Left col -->
  <section class="col-lg-12 connectedSortable">
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
	$("#alerts").modal();
  });
</script>
@endsection

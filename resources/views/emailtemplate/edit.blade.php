@extends('adminlte::page')

@section("title", "Editar Template de E-mail")

@section('content_header')
  <h1>Editar Template de E-mail</h1>
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

    @if($email_template->grupo_evento)
        <li role="presentation"><a href="/grupoevento/dashboard/{{$email_template->grupo_evento->id}}?tab=email_template">Voltar a Dashboard de Grupo de Evento</a></li>
    @else
        @if($email_template->evento)
            <li role="presentation"><a href="/evento/dashboard/{{$email_template->evento->id}}?tab=email_template">Voltar a Dashboard de Evento</a></li>
        @else
            <li role="presentation"><a href="/emailtemplate">Voltar a Lista de Templates de E-mail</a></li>
        @endif
    @endif
</ul>
<div class="row">
  <!-- Left col -->
  <section class="col-lg-12 connectedSortable">
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title">Editar Template de E-mail</h3>
		</div>
	  <!-- form start -->
        <form method="post">
			<div class="box-body">
				<div class="form-group">
					<label for="name">Nome</label>
					<input name="name" id="name" class="form-control" type="text" value="{{$email_template->name}}" />
				</div>
				<div class="form-group">
					<label for="name">Tipo de E-mail</label><br/>
					{{$email_template->email_type}} - {{$email_template->getEmailType()}}
				</div>
				<div class="form-group">
					<label for="subject">Assunto do E-mail</label>
					<input name="subject" id="subject" class="form-control" type="text" value="{{$email_template->subject}}" />
				</div>
				<div class="form-group">
					<label for="message">Mensagem do E-mail</label>
                    <textarea name="message" id="message" class="form-control">{!!$email_template->message!!}</textarea>
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
<script type="text/javascript" src="{{url("/vendor/bower/ckeditor/ckeditor.js")}}"></script>
<script type="text/javascript">
  $(document).ready(function(){
    CKEDITOR.replace('message');
  });
</script>
@endsection

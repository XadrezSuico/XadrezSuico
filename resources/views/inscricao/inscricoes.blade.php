@extends('adminlte::page')

@section("title", "Visualizar Lista de Inscrições")

@section('content_header')
  <h1>Visualizar Lista de Inscrições</h1>
  </ol>
@stop


@section('css')
	<style>
		.display-none, .displayNone{
			display: none;
		}

		.box-title.evento{
			font-size: 2.5rem;
			font-weight: bold;
		}
	</style>
@endsection

@section("content")

<!-- Main row -->
<ul class="nav nav-pills">
  <li role="presentation"><a href="/inscricao/{{$evento->id}}">Voltar ao Formulário de Nova Inscrição</a></li>
  @if($evento->estaRecebendoConfirmacaoPublica()) <li role="presentation"><a href="/inscricao/{{$evento->id}}/confirmacao">Voltar ao Formulário de Confirmação</a></li> @endif
</ul>
<div class="row">
  <!-- Left col -->
  <section class="col-lg-12 connectedSortable">
	<!-- general form elements -->
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title evento">Evento: {{$evento->name}}</h3>
			<div class="pull-right box-tools">
			</div>
		</div>

		<div class="box-body">
			@if(!$evento->inscricoes_encerradas())
                <a href="{{url("/inscricao/".$evento->id)}}" class="btn btn-lg btn-info btn-block">
                    Voltar ao Formulário de Inscrição
                </a><br/>
            @endif
            @if($evento->hasTorneiosEmparceiradosByXadrezSuico())
                <a href="{{url("/evento/acompanhar/".$evento->id)}}" class="btn btn-lg btn-primary btn-block">
                    Acompanhe o Emparceiramento do Torneio
                </a><br/>
            @endif
			@if($evento->pagina)
				@if($evento->pagina->imagem) <div style="width: 100%; text-align: center;"><img src="data:image/png;base64, {!!$evento->pagina->imagem!!}" width="100%" style="max-width: 800px"/></div> <br/> @endif
				@if($evento->pagina->texto) {!!$evento->pagina->texto!!} <br/> @endif
				@if($evento->pagina->imagem || $evento->pagina->texto) <hr/> @endif
			@endif
			<strong>Categorias:</strong><br/>
			@foreach($evento->categorias->all() as $categoria)
				{{$categoria->categoria->name}},
			@endforeach<br/>
			<strong>Cidade:</strong> {{$evento->cidade->name}}<br/>
			<strong>Local:</strong> {{$evento->local}}<br/>
			<strong>Data:</strong>
            @if($evento->getDataInicio() == $evento->getDataFim())
                {{$evento->getDataInicio()}}
            @else
                {{$evento->getDataInicio()}} - {{$evento->getDataFim()}}
            @endif<br/>
			<strong>Maiores informações em:</strong> <a href="{{$evento->link}}" target="_blank">{{$evento->link}}</a><br/>
			@if($evento->maximo_inscricoes_evento)
				<hr/>
				<strong>Total de Inscritos até o presente momento:</strong> {{$evento->quantosInscritos()}}.<br/>
				<strong>Limite de Inscritos:</strong> {{$evento->maximo_inscricoes_evento}}.<br/>
				<hr/>
			@endif
			@if($evento->getDataFimInscricoesOnline()) <h3><strong>Inscrições antecipadas até:</strong> {{$evento->getDataFimInscricoesOnline()}}.</h3>@endif
            @if($evento->is_lichess_integration)
                Informações do Lichess.org são atualizadas a cada 6 horas.
            @endif
        </div>
	</div>
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title">Lista de Inscrições</h3>
			<div class="pull-right box-tools">
			</div>
		</div>

		<div class="box-body">
			<table id="tabela" class="table-responsive table-condensed table-striped" style="width: 100%">
                <thead>
                    <tr>
                        <th>Código Enxadrista</th>
                        <th>Nome do Enxadrista</th>
                        @if($evento->data_inicio <= date("Y-m-d")) <th>Confirmado?</th> @endif
                        <th>Data de Nascimento</th>
                        @if($evento->is_lichess_integration)
                            <th>Usuário Lichess.org</th>
                            <th>Inscrito Lichess.org?</th>
                            <th>Rating Lichess.org</th>
                            <th>Posição Inicial (Parcial)</th>
                        @endif
                        @if($evento->is_chess_com)
                            <th>Usuário Chess.com</th>
                        @endif
                        @if($evento->tipo_rating)
                            <th>Rating</th>
                        @endif
                        @if($evento->usa_fide)
                            <th>FIDE</th>
                        @endif
                        @if($evento->usa_lbx)
                            <th>LBX</th>
                        @endif
                        @if($evento->usa_cbx)
                            <th>CBX</th>
                        @endif
                        <th>Categoria Inscrição</th>
                        <th>Cidade</th>
                        <th>Clube</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($evento->getInscricoes() as $inscricao)
                        <tr>
                            <td>{{$inscricao->enxadrista->id}}</td>
                            <td>{{$inscricao->enxadrista->getNomePublico()}}</td>
                            @if($evento->data_inicio <= date("Y-m-d")) <td> @if($inscricao->confirmado) Sim @endif </td> @endif
                            <td>{{$inscricao->enxadrista->getNascimentoPublico()}}</td>
                            @if($evento->is_lichess_integration)
                                <td>@if($inscricao->lichess_username) {{$inscricao->lichess_username}} @else - @endif</td>
                                <td>@if($inscricao->is_lichess_found) Sim @else <strong><span style="color:red">Não</span></strong>@endif</td>
                                <td>@if($inscricao->lichess_rating) {{$inscricao->lichess_rating}} @else - @endif</td>
                                <td>@if($inscricao->start_position) {{$inscricao->start_position}} @else - @endif</td>
                            @endif
                            @if($evento->is_chess_com)
                                <td>@if($inscricao->chess_com_username) {{$inscricao->chess_com_username}} @else - @endif</td>
                            @endif
                            @if($evento->tipo_rating)
                                <td>{{$inscricao->enxadrista->ratingParaEvento($evento->id,true)}}</td>
                            @endif
                            @if($evento->usa_fide)
                                <td>{{$inscricao->enxadrista->showRating(0, $evento->tipo_modalidade, $evento->getConfig("fide_sequence"))}}</td>
                            @endif
                            @if($evento->usa_lbx)
                                <td>{{$inscricao->enxadrista->showRating(2, $evento->tipo_modalidade)}}</td>
                            @endif
                            @if($evento->usa_cbx)
                                <td>{{$inscricao->enxadrista->showRating(1, $evento->tipo_modalidade)}}</td>
                            @endif
                            <td>{{$inscricao->categoria->name}}</td>
                            <td>{{$inscricao->getCidade()}}</td>
                            <td>@if($inscricao->clube) {{$inscricao->clube->getName()}} @else - @endif</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
		</div>
	</div>

  </section>
  <!-- /.Left col -->
</div>
<!-- /.row (main row) -->

@endsection

@section("js")
@foreach(array(
    "https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js",
    "https://cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js",
    "https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js",
    "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js",
    "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js",
    "https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js",
    "https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"
    ) as $url)
<script type="text/javascript" src="{{$url}}"></script>
@endforeach
<script type="text/javascript">
    $(document).ready(function(){
        $("#tabela").DataTable({
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            ordering: false,
            paging: false
        });
    });
</script>
@endsection

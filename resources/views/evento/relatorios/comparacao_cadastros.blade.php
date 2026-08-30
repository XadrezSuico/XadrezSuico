@extends('adminlte::page')

@section("title", "Comparação de Cadastros - ".$evento->name)

@section('content_header')
  <h1>Comparação de Cadastros</h1>
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

		.parecer-badge {
			display: inline-block;
			padding: 4px 8px;
			border-radius: 3px;
			font-size: 12px;
			font-weight: bold;
			white-space: nowrap;
		}

		.parecer-confere {
			background-color: #00a65a;
			color: #fff;
		}

		.parecer-verificar {
			background-color: #f39c12;
			color: #fff;
		}

		.parecer-nao-confere,
		.parecer-nao-integrado {
			background-color: #dd4b39;
			color: #fff;
		}

		.legenda-parecer {
			margin-bottom: 15px;
		}

		.legenda-parecer .parecer-badge {
			margin-right: 8px;
		}
	</style>
@endsection

@section("content")

<ul class="nav nav-pills">
  <li role="presentation"><a href="/evento/dashboard/{{$evento->id}}">Voltar à Dashboard de Evento</a></li>
</ul>

<div class="row">
  <section class="col-lg-12 connectedSortable">
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title evento">Evento: {{$evento->name}}</h3>
		</div>
	</div>

	@if(!$evento->calcula_cbx && !$evento->calcula_fide)
		<div class="box box-warning">
			<div class="box-body">
				<p>Este relatório só está disponível para eventos com cálculo de rating CBX e/ou FIDE configurado.</p>
			</div>
		</div>
	@else
		<div class="box box-default">
			<div class="box-header">
				<h3 class="box-title">Legenda</h3>
			</div>
			<div class="box-body legenda-parecer">
				<span class="parecer-badge parecer-nao-confere">Não confere</span>
				<span class="parecer-badge parecer-nao-integrado">Nome não integrado</span>
				<span class="parecer-badge parecer-verificar">Verificar</span>
				<span class="parecer-badge parecer-confere">Confere</span>
				<p style="margin-top: 10px; margin-bottom: 0;">
					Compara o nome do cadastro local com o nome integrado da entidade (CBX/FIDE), quando o ID estiver informado.
					Registros com maior divergência aparecem primeiro.
				</p>
			</div>
		</div>

		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title">Inscrições</h3>
			</div>
			<div class="box-body">
				<table id="tabela" class="table-responsive table-condensed table-striped" style="width: 100%">
					<thead>
						<tr>
							<th class="display-none">Ordem</th>
							<th>ID Inscrição</th>
							<th>ID Enxadrista</th>
							<th>Nome do Enxadrista</th>
							@if($evento->calcula_cbx)
								<th>ID CBX</th>
								<th>Nome CBX</th>
								<th>Parecer CBX</th>
							@endif
							@if($evento->calcula_fide)
								<th>ID FIDE</th>
								<th>Nome FIDE</th>
								<th>Parecer FIDE</th>
							@endif
						</tr>
					</thead>
					<tbody>
						@foreach($linhas as $linha)
							<tr>
								<td class="display-none">{{ $linha['nivel_ordenacao'] }}</td>
								<td>{{ $linha['inscricao_id'] }}</td>
								<td>{{ $linha['enxadrista_id'] }}</td>
								<td>{{ $linha['nome'] }}</td>
								@if($evento->calcula_cbx)
									<td>{{ $linha['cbx'] ? $linha['cbx']['id'] : '-' }}</td>
									<td>{{ $linha['cbx'] ? $linha['cbx']['nome'] : '-' }}</td>
									<td>
										@if($linha['cbx'])
											@php
												$classe = 'parecer-nao-confere';
												if ($linha['cbx']['parecer'] === 'Confere') {
													$classe = 'parecer-confere';
												} elseif ($linha['cbx']['parecer'] === 'Verificar') {
													$classe = 'parecer-verificar';
												} elseif ($linha['cbx']['parecer'] === 'Nome não integrado') {
													$classe = 'parecer-nao-integrado';
												}
											@endphp
											<span class="parecer-badge {{ $classe }}" title="{{ $linha['cbx']['detalhe'] }}">
												{{ $linha['cbx']['parecer'] }}
											</span>
										@else
											-
										@endif
									</td>
								@endif
								@if($evento->calcula_fide)
									<td>{{ $linha['fide'] ? $linha['fide']['id'] : '-' }}</td>
									<td>{{ $linha['fide'] ? $linha['fide']['nome'] : '-' }}</td>
									<td>
										@if($linha['fide'])
											@php
												$classe = 'parecer-nao-confere';
												if ($linha['fide']['parecer'] === 'Confere') {
													$classe = 'parecer-confere';
												} elseif ($linha['fide']['parecer'] === 'Verificar') {
													$classe = 'parecer-verificar';
												} elseif ($linha['fide']['parecer'] === 'Nome não integrado') {
													$classe = 'parecer-nao-integrado';
												}
											@endphp
											<span class="parecer-badge {{ $classe }}" title="{{ $linha['fide']['detalhe'] }}">
												{{ $linha['fide']['parecer'] }}
											</span>
										@else
											-
										@endif
									</td>
								@endif
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	@endif
  </section>
</div>

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
        if ($("#tabela").length) {
            $("#tabela").DataTable({
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                order: [[0, 'asc']],
                columnDefs: [
                    { targets: 0, visible: false }
                ],
                paging: false
            });
        }
    });
</script>
@endsection

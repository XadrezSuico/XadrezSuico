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

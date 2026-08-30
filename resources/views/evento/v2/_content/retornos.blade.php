@if (session('status'))
		<div class="alert alert-success">
				{{ session('status') }}
		</div>
	@endif
	<div class="box">
		<div class="box-header">
			<h3 class="box-title">Retorno do Processamento da Classificação</h3>
		</div>
        <div class="box-body">
            @php($i=1)
            @foreach($retornos as $linha)
                {{$i++}} - {!!$linha!!} <br/>
            @endforeach
		</div>
	</div>

@if (session('status'))
		<div class="alert alert-success">
				{{ session('status') }}
		</div>
	@endif
	<ul class="nav nav-pills">
		<li role="presentation"><a href="{{url("/evento/dashboard/".$evento->id."?tab=torneio")}}">Listar Todos os Torneios</a></li>
	</ul>

    <div class="box">
        <div class="box-body">
            @if($torneio->getCountCriteriosNaoManuais() == 0)
                <div class="alert alert-warning">
                    <strong>ALERTA!</strong><br/>
                    O evento e o grupo de evento não possui critérios de desempate vinculados. Isso pode gerar problemas na importação.
                </div>
            @endif
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="arquivo">Arquivo de Resultados (TXT)</label>
                    <input type="file" id="arquivo" name="arquivo">
                </div>
                <button type="submit" class="btn btn-success">Enviar</button>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
            </form>
		</div>
	</div>

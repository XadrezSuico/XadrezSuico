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
  <section class="col-lg-8 connectedSortable">
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
  <section class="col-lg-4 connectedSortable">

	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title">Tags do Sistema</h3>
		</div>
        <div class="box-body">
            Existem algumas tags que você pode inserir no template de e-mail para que, quando o e-mail for enviado, estas tags sejam substituídas por informações do sistema.<br/>
            Para utilizar é necessário apenas adicionar a tag completa, que está em negrito. Mas fique atento, visto que existem tags que parecem iguais, mas tem propósitos diferentes.
            <h3>Inscrição</h3>
            <ul>
                <li><strong>{inscricao.id}</strong> - ID da Inscrição</li>
            </ul>

            <h3>Categoria da Inscrição</h3>
            <ul>
                <li><strong>{categoria.id}</strong> - ID da Categoria</li>
                <li><strong>{categoria.name}</strong> - Nome da Categoria</li>
            </ul>

            <h3>Cidade de Vínculo da Inscrição</h3>
            <ul>
                <li><strong>{cidade.id}</strong> - ID da Cidade</li>
                <li><strong>{cidade.name}</strong> - Nome da Cidade</li>
            </ul>

            <h3>Estado da Cidade de Vínculo da Inscrição</h3>
            <ul>
                <li><strong>{cidade.estado.id}</strong> - ID do Estado</li>
                <li><strong>{cidade.estado.name}</strong> - Nome do Estado</li>
                <li><strong>{cidade.estado.uf}</strong> - UF do Estado</li>
            </ul>

            <h3>País do Estado da Cidade de Vínculo da Inscrição</h3>
            <ul>
                <li><strong>{cidade.estado.pais.id}</strong> - ID do País</li>
                <li><strong>{cidade.estado.pais.name}</strong> - Nome do País</li>
                <li><strong>{cidade.estado.pais.iso}</strong> - <a href="https://www.sport-histoire.fr/pt/Geografia/Codigos_ISO_Paises.php" target="_blank">Código ISO</a> do País</li>
            </ul>

            <h3>Clube da Inscrição</h3>
            <ul>
                <li><strong>{clube.id}</strong> - ID do Clube</li>
                <li><strong>{clube.name}</strong> - Nome do Clube</li>
            </ul>

            <h3>Cidade do Clube</h3>
            <ul>
                <li><strong>{clube.cidade.id}</strong> - ID da Cidade</li>
                <li><strong>{clube.cidade.name}</strong> - Nome da Cidade</li>
            </ul>

            <h3>Estado da Cidade de Vínculo do Clube</h3>
            <ul>
                <li><strong>{clube.cidade.estado.id}</strong> - ID do Estado</li>
                <li><strong>{clube.cidade.estado.name}</strong> - Nome do Estado</li>
                <li><strong>{clube.cidade.estado.uf}</strong> - UF do Estado</li>
            </ul>

            <h3>País do Estado da Cidade de Vínculo do Clube</h3>
            <ul>
                <li><strong>{clube.cidade.estado.pais.id}</strong> - ID do País</li>
                <li><strong>{clube.cidade.estado.pais.name}</strong> - Nome do País</li>
                <li><strong>{clube.cidade.estado.pais.iso}</strong> - <a href="https://www.sport-histoire.fr/pt/Geografia/Codigos_ISO_Paises.php" target="_blank">Código ISO</a> do País</li>
            </ul>

            <h3>Torneio da Inscrição</h3>
            <ul>
                <li><strong>{torneio.id}</strong> - ID do Torneio</li>
                <li><strong>{torneio.name}</strong> - Nome do Torneio</li>
            </ul>

            <h3>Evento da Inscrição</h3>
            <ul>
                <li><strong>{evento.id}</strong> - ID do Evento</li>
                <li><strong>{evento.name}</strong> - Nome do Evento</li>
                <li><strong>{evento.data.inicio}</strong> - Data de Início do Evento</li>
                <li><strong>{evento.data.fim}</strong> - Data de Fim do Evento</li>
                <li><strong>{evento.data.inscricoes}</strong> - Prazo Final para Inscrições (Data e Hora)</li>
                <li><strong>{evento.local}</strong> - Local do Evento</li>
                <li><strong>{evento.link}</strong> - Link de Maiores Informações do Evento</li>
                <li><strong>{evento.lichess.team}</strong> - Lichess.org - Link do Time</li>
                <li><strong>{evento.lichess.tournament}</strong> - Lichess.org - Link do Torneio</li>
            </ul>
            <h3>Cidade do Evento</h3>
            <ul>
                <li><strong>{evento.cidade.id}</strong> - ID da Cidade</li>
                <li><strong>{evento.cidade.name}</strong> - Nome da Cidade</li>
            </ul>

            <h3>Estado da Cidade do Evento</h3>
            <ul>
                <li><strong>{evento.cidade.estado.id}</strong> - ID do Estado</li>
                <li><strong>{evento.cidade.estado.name}</strong> - Nome do Estado</li>
                <li><strong>{evento.cidade.estado.uf}</strong> - UF do Estado</li>
            </ul>

            <h3>País do Estado da Cidade do Evento</h3>
            <ul>
                <li><strong>{evento.cidade.estado.pais.id}</strong> - ID do País</li>
                <li><strong>{evento.cidade.estado.pais.name}</strong> - Nome do País</li>
                <li><strong>{evento.cidade.estado.pais.iso}</strong> - <a href="https://www.sport-histoire.fr/pt/Geografia/Codigos_ISO_Paises.php" target="_blank">Código ISO</a> do País</li>
            </ul>

            <h3>Grupo de Evento</h3>
            <ul>
                <li><strong>{grupoevento.id}</strong> - ID do Grupo de Evento</li>
                <li><strong>{grupoevento.name}</strong> - Nome do Grupo de Evento</li>
                <li><strong>{grupoevento.regulamento}</strong> - Link do Regulamento do Grupo de Evento</li>
            </ul>

            <h3>Enxadrista da Inscrição</h3>
            <ul>
                <li><strong>{enxadrista.id}</strong> - ID do Enxadrista</li>
                <li><strong>{enxadrista.name}</strong> - Nome do Enxadrista</li>
                <li><strong>{enxadrista.firstname}</strong> - Primeiros Nomes do Enxadrista</li>
                <li><strong>{enxadrista.lastname}</strong> - Sobrenome do Enxadrista (conforme orientação da LBX)</li>
                <li><strong>{enxadrista.born}</strong> - Data de Nascimento do Enxadrista</li>
            </ul>

            <h3>Dados da CBX do Enxadrista da Inscrição</h3>
            <ul>
                <li><strong>{enxadrista.cbx.id}</strong> - ID do Enxadrista na CBX</li>
                <li><strong>{enxadrista.cbx.name}</strong> - Nome do Enxadrista na CBX</li>
            </ul>

            <h3>Dados da FIDE do Enxadrista da Inscrição</h3>
            <ul>
                <li><strong>{enxadrista.fide.id}</strong> - ID do Enxadrista na FIDE</li>
                <li><strong>{enxadrista.fide.name}</strong> - Nome do Enxadrista na FIDE</li>
            </ul>

            <h3>Dados da LBX do Enxadrista da Inscrição</h3>
            <ul>
                <li><strong>{enxadrista.lbx.id}</strong> - ID do Enxadrista na LBX</li>
                <li><strong>{enxadrista.lbx.name}</strong> - Nome do Enxadrista na LBX</li>
            </ul>

            <h3>Dados do Chess.com do Enxadrista da Inscrição</h3>
            <ul>
                <li><strong>{enxadrista.chess_com.username}</strong> - Username do Enxadrista no Chess.com</li>
            </ul>

            <h3>Dados do Lichess.org do Enxadrista da Inscrição</h3>
            <ul>
                <li><strong>{enxadrista.lichess.username}</strong> - Username do Enxadrista no Lichess.org</li>
            </ul>

            <h3>Sexo do Enxadrista da Inscrição</h3>
            <ul>
                <li><strong>{enxadrista.sexo.name}</strong> - Sexo do Enxadrista</li>
                <li><strong>{enxadrista.sexo.abbr}</strong> - Abreviação do Sexo do Enxadrista</li>
            </ul>

            <h3>País de Nascimento do Enxadrista da Inscrição</h3>
            <ul>
                <li><strong>{enxadrista.pais.id}</strong> - ID do País</li>
                <li><strong>{enxadrista.pais.name}</strong> - Nome do País</li>
                <li><strong>{enxadrista.pais.iso}</strong> - <a href="https://www.sport-histoire.fr/pt/Geografia/Codigos_ISO_Paises.php" target="_blank">Código ISO</a> do País</li>
            </ul>

            <h3>Cidade de Vinculo do Enxadrista</h3>
            <ul>
                <li><strong>{enxadrista.cidade.id}</strong> - ID da Cidade</li>
                <li><strong>{enxadrista.cidade.name}</strong> - Nome da Cidade</li>
            </ul>

            <h3>Estado da Cidade de Vinculo do Enxadrista</h3>
            <ul>
                <li><strong>{enxadrista.cidade.estado.id}</strong> - ID do Estado</li>
                <li><strong>{enxadrista.cidade.estado.name}</strong> - Nome do Estado</li>
                <li><strong>{enxadrista.cidade.estado.uf}</strong> - UF do Estado</li>
            </ul>

            <h3>País do Estado da Cidade de Vinculo do Enxadrista</h3>
            <ul>
                <li><strong>{enxadrista.cidade.estado.pais.id}</strong> - ID do País</li>
                <li><strong>{enxadrista.cidade.estado.pais.name}</strong> - Nome do País</li>
                <li><strong>{enxadrista.cidade.estado.pais.iso}</strong> - <a href="https://www.sport-histoire.fr/pt/Geografia/Codigos_ISO_Paises.php" target="_blank">Código ISO</a> do País</li>
            </ul>

            <h3>Clube do Enxadrista</h3>
            <ul>
                <li><strong>{enxadrista.clube.id}</strong> - ID do Clube</li>
                <li><strong>{enxadrista.clube.name}</strong> - Nome do Clube</li>
            </ul>

            <h3>Cidade do Clube do Enxadrista</h3>
            <ul>
                <li><strong>{enxadrista.clube.cidade.id}</strong> - ID da Cidade</li>
                <li><strong>{enxadrista.clube.cidade.name}</strong> - Nome da Cidade</li>
            </ul>

            <h3>Estado da Cidade de Vínculo do Clube do Enxadrista</h3>
            <ul>
                <li><strong>{enxadrista.clube.cidade.estado.id}</strong> - ID do Estado</li>
                <li><strong>{enxadrista.clube.cidade.estado.name}</strong> - Nome do Estado</li>
                <li><strong>{enxadrista.clube.cidade.estado.uf}</strong> - UF do Estado</li>
            </ul>

            <h3>País do Estado da Cidade de Vínculo do Clube do Enxadrista</h3>
            <ul>
                <li><strong>{enxadrista.clube.cidade.estado.pais.id}</strong> - ID do País</li>
                <li><strong>{enxadrista.clube.cidade.estado.pais.name}</strong> - Nome do País</li>
                <li><strong>{enxadrista.clube.cidade.estado.pais.iso}</strong> - <a href="https://www.sport-histoire.fr/pt/Geografia/Codigos_ISO_Paises.php" target="_blank">Código ISO</a> do País</li>
            </ul>
        </div>
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

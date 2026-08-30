@if (session('status'))
		<div class="alert alert-success">
				{{ session('status') }}
		</div>
	@endif
	<div class="box">
        <div class="box-body">
            <div class="alert alert-warning" role="alert">
                <h3><strong>Opa! Recomendamos verificar as seguintes informações antes de efetuar a sua importação:</strong></h3>
                <ul>
                    <li>O relatório que você recebeu as informações é de título "RELATÓRIO DE DELEGAÇÕES";</li>
                    <li>Os dados a serem importados é referente às inscrições para este torneio;</li>
                    <li>As duas primeiras linhas do relatório foram removidas, mantendo apenas os dados de cabeçalho da planilha, contendo as seguintes informações:
                        <ul>
                            <li>FUNÇÃO</li>
                            <li>DELEGAÇÃO/EQUIPE</li>
                            <li>CATEGORIA DE MODALIDADE</li>
                            <li>MODALIDADE</li>
                            <li>NAIPE</li>
                            <li>CATEGORIA</li>
                            <li>NOME</li>
                            <li>CPF / DOC. ESTR.</li>
                            <li>RG</li>
                            <li>DATA NASCIMENTO</li>
                            <li>EMAIL</li>
                            <li>TELEFONE</li>
                        </ul>
                    </li>
                    <li>Nem todos os campos são obrigatórios, mas é necessário estar aparecendo todos os campos no relatório.</li>
                    <li>Caso o relatório atenda todos estes requisitos, é possível importá-lo. Tenha um bom trabalho.</li>
                </ul>
            </div>

			<form method="post" enctype="multipart/form-data">
				<div class="form-group">
					<label for="arquivo">Arquivo de Exportação (XLSX)</label>
					<input type="file" id="arquivo" name="arquivo">
				</div>
				<button type="submit" class="btn btn-success">Enviar</button>
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
			</form>
		</div>
	</div>

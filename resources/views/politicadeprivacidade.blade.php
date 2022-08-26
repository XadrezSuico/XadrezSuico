@extends('adminlte::page')

@section('title', 'Política de Privacidade')

@section('content_header')
    <h1>Política de Privacidade</h1>
@stop

@section('content')
    <h3>A. Introdução</h3>
    <p>A privacidade dos visitantes e usuários de nossa plataforma ({{url("/")}}) é muito importante para nós, e estamos comprometidos em protegê-la.
        Esta política explica o que faremos com suas informações pessoais.</p>

    <h3>B. O que coletamos?</h3>
    <p>B.1 - Durante o cadastro ou atualização de cadastro do enxadrista, nós coletamos os seguintes dados: </p>
    <ul>
        <li>Nome Completo;</li>
        <li>Data de Nascimento;</li>
        <li>Sexo;</li>
        <li>País de Nascimento;</li>
        <li>Um ou mais documentos dentre estes: RG, CPF, Passaporte;</li>
        <li>E-mail;</li>
        <li>Celular e País do mesmo;</li>
        <li>ID de Cadastro na Entidade Confederação Brasileira de Xadrez (CBX);</li>
        <li>ID de Cadastro na Entidade Federação Internacional de Xadrez (FIDE);</li>
        <li>ID de Cadastro na Entidade Liga Brasileira de Xadrez (LBX);</li>
        <li>Ratings (pontuação) dos cadastros nas Entidades de cada uma das modalidades: Relâmpago, Rápido e Convencional (Esta coleta é efetuada automaticamente);</li>
        <li>Cidade, Estado/Província e País de Vínculo;</li>
        <li>Clube de Vínculo;</li>
    </ul>
    <p>B.2 - Além destes dados, durante o processo de inscrição podemos coletar os seguintes dados:</p>
    <ul>
        <li>Cidade, Estado/Província e País de Vínculo;</li>
        <li>Clube de Vínculo;</li>
        <li>Informações adicionais, como por exemplo: Se o mesmo é comerciário? Se é copeliano? dentre outras informações;</li>
    </ul>
    <br/>
    <h3>C. Qual o tratamento dos dados que coletamos?</h3>
    <p>Os dados que coletamos são utilizados para:</p>
    <ul>
        <li>Identificação e Garantia de Cadastro Único do(a) Enxadrista: Há uma validação que considera alguns dados, como Documento e Nome, que fazem com que o enxadrista não acabe efetuando cadastros duplicados dentro do sistema.</li>
        <li>Gerenciamento de Torneio: Os dados são utilizados para que o enxadrista possa ser inscrito na categoria correta e também para identificação do enxadrista dentro dos torneios realizados com os dados da plataforma.</li>
        <li>Divulgação de Resultado dos Torneios: Os seguintes dados podem ser usados para a divulgação dos resultados dos torneios: Nome completo; Data de nascimento; Cidade; Estado; País; Clube; IDs nas Entidades e seus devidos Ratings.</li>
        <li>Contato com o Enxadrista: Os dados de contato podem ser usados para contato com o(a) enxadrista caso seja necessário, como por exemplo, para solicitar dados para envio de premiação ou então para divulgar outro evento que o(a) mesmo(a) pode participar.</li>
        <li>Repasse de Informações para Entidades/Empresas Parceiras: Alguns dados podem ser enviados para Entidades/Empresas parceiras, conforme o evento que está sendo efetuado, visto que podem ocorrer eventos realizados por algum parceiro e seja necessário encaminhar algum dado para os mesmos para contato devido à algum motivo necessário. Os dados que podem ser enviados são: Nome completo; Data de nascimento; Celular e País do Celular; E-mail; Cidade/Estado/País de Vínculo; Clube de Vínculo; e alguma informação adicional que pode existir no formulário de inscrição.</li>
    </ul>
    <br/>
    <h3>D. Do que não consta nesta política de privacidade</h3>
    <p>Considerando que podem existir pontos que esta política de privacidade não comtemple, para estes casos serão aplicados o que consta nas regras da Lei Brasileira nº 13.709, de 14 de Agosto de 2018.</p>

    <h3>E. Do aceite</h3>
    <p>Considerando os dados e tratamentos dispostos neste documento e a aplicação dos mesmos, o aceite é efetuado durante o processo de cadastro no sistema de forma completa, não sendo possível aceitar parcialmente o presente.</p>

    <h3>F. Da plataforma</h3>
    <p>O XadrezSuíço é uma plataforma de código aberto, onde é possível qualquer indivíduo efetuar o download e fazer a sua própria implementação. Além disso, você pode ter acesso ao código-fonte para auditar como a plataforma funciona e conhecer como é operada. Para isso acesse o repositório da mesma no github: <a href="https://github.com/xadrezsuico/xadrezsuico">https://github.com/xadrezsuico/xadrezsuico</a>.</p>

    <h3>G. Da atualização do presente documento</h3>
    <p>Caso ocorra alguma atualização no presente documento, o mesmo será encaminhado por e-mail o qual aguardará retorno de não aceite durante o período de 30 dias corridos, o qual, caso não haja manifestação, será considerado como aceito. Para aceites após a data de publicação, entra em vigor assim que efetuado o aceite.</p>
    <br/>
    <p>{{env("CIDADE_POLITICA_PRIVACIDADE")}}, {{env("DATA_ALTERACAO_POLITICA_PRIVACIDADE")}}.</p>
@stop

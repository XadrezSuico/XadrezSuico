<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Pagina;

class SeedPaginaTableAddTermosDeUso extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Pagina::where([["uuid","=","ca6f1c00-3868-43fe-9258-0da65acefb0a"]])->count() > 0){
            $pagina = Pagina::where([["uuid","=","ca6f1c00-3868-43fe-9258-0da65acefb0a"]])->first();
        }else{
            $pagina = new Pagina;
            $pagina->uuid = "ca6f1c00-3868-43fe-9258-0da65acefb0a";
        }

        $pagina->title = "Termos de Uso do XadrezSuíço";
        $pagina->texto = "<h3>A. Condições Gerais</h3>
            <p>Este documento contém os Termos de Uso da plataforma de gerenciamento de Circuito de Xadrez XadrezSuíço, cuja aceitação plena e integral requisito
                para todos os seus Usuários. Eles incluem, além dos termos gerais, as políticas de responsabilidade, de privacidade e confidencialidade, a licença
                de uso do conteúdo, e as informações sobre como reportar violações.</p>
            <p> Usuário deverá ler e aceitar todas as condições aqui estabelecidas antes de seu cadastro na plataforma. Todas as contribuições são bem-vindas,
                desde que respeitadas as condições aqui expressas.</p>
            <p> A plataforma XadrezSuíço é uma plataforma virtual de gerenciamento de Circuitos de Xadrez, que atua no pré e pós-evento. No pré-evento atua com
                principalmente o gerenciamento de inscrições e exportação de arquivos para importação em softwares de Emparceiramento de Xadrez, e já no pós-evento,
                atua no recebimento do resultado obtido pelos enxadristas e com isto, efetua processamentos para principalmente efetuar o cálculo de pontuação final
                do circuito e seus critérios de desempate.</p>
            <h3>B. Da Atualização deste Documento</h3>
            <p> Este documento poderá ser atualizado, valendo a partir de 48 horas após a sua publicação. Acesse esta página com alguma frequência para ficar a par deste documento.</p>
            <br/>
            [CIDADE DOS TERMOS], [DATA DE ALTERAÇÃO DOS TERMOS]";
        $pagina->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

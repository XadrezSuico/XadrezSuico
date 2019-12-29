<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WhatsNewController extends Controller
{
    public function index(){
        $news = array();

        // Novidades versão 0.0.2.1
        $new_0021_betha["name"] = "0.0.2.1 Beta";
        $new_0021_betha["news"] = array();
        $new_0021_betha["news"][] = "Correção em bug relativo a forma de armazenamento da imagem e descrição do evento.";
        $new_0021_betha["news"][] = "Agora é possível visualizar a lista de inscrições do evento de forma pública, porém, é necessário que a opção 'Permite a visualização da lista de inscrições de forma pública?' esteja selecionada - Issue #12";
        $new_0021_betha["news"][] = "A lista de Enxadristas agora é carregada de acordo com a demanda, assim deixando a lista mais rápida para ser acessada. - Issue #5";
        $new_0021_betha["news"][] = "Agora é possível definir que um evento só é permitido a realização de inscrições pelo link (Inscrições Privadas). No caso, há uma informação a mais no link que libera ou não a inscrição para este evento. - Issue #22";
        $new_0021_betha["news"][] = "Agora é possível gerar lista de Enxadristas inscritos em determinado evento. - Issue #17";
        $new_0021_betha["news"][] = "Os ratings FIDE, CBX e LBX agora são divididos pelos seus tipos: Relâmpago, Rápido e Convencional. De acordo com a seleção do tipo de modalidade escolhido no cadastro de evento será apresentado o rating de acordo com o tipo de modalidade escolhida. - Issue #8";
        $new_0021_betha["news"][] = "A classificação de evento está corrigida, agora quando é finalizada a mesma retorna ao Dashboard de Evento - Já a classificação geral, a mesma já está com a classificação sendo realizada através de solicitações AJAX - Issue #11";
        $news[] = $new_0021_betha;

        // Novidades versão 0.0.2.0
        $new_0020_betha["name"] = "0.0.2.0 Beta";
        $new_0020_betha["news"] = array();
        $new_0020_betha["news"][] = "Agora é possível definir uma imagem e um texto de apresentação do evento. - Issue #3";
        $news[] = $new_0020_betha;

        // Novidades versão 0.0.1.2
        $new_0012_betha["name"] = "0.0.1.2 Beta";
        $new_0012_betha["news"] = array();
        $new_0012_betha["news"][] = "Agora o email é validado quando é inserido em um cadastro de enxadrista a fim de garantir que o email é válido. Em breve apresentará uma mensagem de erro. - Issue #21";
        $new_0012_betha["news"][] = "Corrigido bug que permitia que um enxadrista se recadastrasse caso utilizasse mais espaços entre os nomes.";
        $new_0012_betha["news"][] = "Corrigido bug na visualização da classificação do Grupo de Evento, onde não aparecia os campos para Download da lista e também de pesquisa.";
        $news[] = $new_0012_betha;

        // Novidades versão 0.0.1.1
        $new_0011_betha["name"] = "0.0.1.1 Beta";
        $new_0011_betha["news"] = array();
        $new_0011_betha["news"][] = "A página de 'O que há de novo?' agora possui link no menu para usuários logados.";
        $new_0011_betha["news"][] = "Grupo de Evento - Agora é possível definir que a pontuação geral do enxadrista é a sua pontuação adquirida durante as etapas que participou.<br/>
        Para isso, é necessário configurar o Grupo de Evento, selecionando a opção <strong>'A pontuação do enxadrista será composta pelos seus resultados?'</strong> e salvando a edição do Grupo de Evento.<br/>
        Após isso, caso já existam eventos classificados, <strong>será necessário reclassificá-los</strong>, antes de classificar o Grupo de Evento. - Issue #26";
        $news[] = $new_0011_betha;

        // Novidades versão 0.0.1.0

        $new_0010_betha["name"] = "0.0.1.0 Beta";
        $new_0010_betha["news"][] = "Versão inicial do sistema em beta.";
        $news[] = $new_0010_betha;

        return view("whatsnew",compact("news"));

    }
}

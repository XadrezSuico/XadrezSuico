<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WhatsNewController extends Controller
{
    public function index(){
        $news = array();

        // Novidades versão 0.0.1.0

        $new_0010_betha["name"] = "0.0.1.0 Beta";
        $new_0010_betha["news"][] = "Versão inicial do sistema em beta.";
        $news[] = $new_0010_betha;

        // Novidades versão 0.0.1.1
        $new_0011_betha["name"] = "0.0.1.1 Beta";
        $new_0011_betha["news"] = array();
        $new_0011_betha["news"][] = "A página de 'O que há de novo?' agora possui link no menu para usuários logados.";
        $new_0011_betha["news"][] = "Grupo de Evento - Agora é possível definir que a pontuação geral do enxadrista é a sua pontuação adquirida durante as etapas que participou.<br/>
        Para isso, é necessário configurar o Grupo de Evento, selecionando a opção <strong>'A pontuação do enxadrista será composta pelos seus resultados?'</strong> e salvando a edição do Grupo de Evento.<br/>
        Após isso, caso já existam eventos classificados, <strong>será necessário reclassificá-los</strong>, antes de classificar o Grupo de Evento.";
        $news[] = $new_0011_betha;

        return view("whatsnew",compact("news"));

    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WhatsNewController extends Controller
{
    public function index(){
        $news = array();

        // Novidades versão 0.0.1

        $new_001_betha["name"] = "Versão 0.0.1 Beta";
        $new_001_betha["news"][] = "Versão inicial do sistema em beta.";
        $news[] = $new_001_betha;

        return view("whatsnew",compact("news"));

    }
}

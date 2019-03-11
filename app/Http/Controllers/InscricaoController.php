<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Inscricao;
use App\Torneio;

class InscricaoController extends Controller
{
    public function inscricao($id){
        $torneio = Torneio::find($id);
        if($torneio){
            return view("inscricao.inscricao",compact("torneio"));
        }
        return false;
    }
}

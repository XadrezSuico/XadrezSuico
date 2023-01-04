<?php

namespace App\Http\Controllers\API\Event;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Evento;

class EventController extends Controller
{
    public function get($uuid){
        if($uuid){
            if(Evento::where([["uuid","=",$uuid]])->count() == 0){
                return response()->json(["ok"=>0,"error"=>1,"message"=>"Evento não encontrado","httpcode"=>404],404);
            }
            $evento = Evento::where([["uuid","=",$uuid]])->first();

            $retorno["ok"] = 1;
            $retorno["error"] = 0;
            $retorno["event"] = array();
            $retorno["event"]["info"] = array();
            $retorno["event"]["tabs"] = array();

            $retorno["event"]["info"]["title"] = $evento->name;
            $retorno["event"]["info"]["date"] = ($evento->getDataInicio() == $evento->getDataFim()) ? $evento->getDataInicio() : $evento->getDataInicio()." - ".$evento->getDataFim();
            $retorno["event"]["info"]["banner"] = null;
            $retorno["event"]["info"]["short_description"] = "descrição curta do evento";
            $retorno["event"]["info"]["long_description"] = "descrição longa do evento";
            $retorno["event"]["info"]["city"] = $evento->cidade->getName();
            $retorno["event"]["info"]["time_control"] = $evento->getTimeControl();

            $retorno["event"]["info"]["timeline"] = array();
            if($evento->data_limite_inscricoes_abertas){
                $retorno["event"]["info"]["timeline"][] = [
                    "datetime" => $evento->getDataFimInscricoesOnline(),
                    "text" => "Fim das Inscrições Online",
                    "is_expected" => false
                ];
            }


            return response()->json($retorno);
        }
        return response()->json(["ok"=>0,"error"=>1,"message"=>"Evento não encontrado","httpcode"=>404],404);
    }
}

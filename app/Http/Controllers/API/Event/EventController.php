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
            $retorno["event"]["uuid"] = $evento->uuid;
            $retorno["event"]["info"] = array();
            $retorno["event"]["tabs"] = array();

            $retorno["event"]["tabs"][] = "home";
            if(!$evento->is_inscricoes_bloqueadas) $retorno["event"]["tabs"][] = "register";
            if($evento->e_permite_visualizar_lista_inscritos_publica) $retorno["event"]["tabs"][] = "players";

            $retorno["event"]["info"]["title"] = $evento->name;
            $retorno["event"]["info"]["date"] = ($evento->getDataInicio() == $evento->getDataFim()) ? $evento->getDataInicio() : $evento->getDataInicio()." - ".$evento->getDataFim();
            $retorno["event"]["info"]["banner"] = url("/api/v1/event/banner/".$evento->uuid);
            $retorno["event"]["info"]["short_description"] = "";
            $retorno["event"]["info"]["long_description"] = "";
            $retorno["event"]["info"]["city"] = $evento->cidade->getName();
            $retorno["event"]["info"]["place"] = $evento->local;
            $retorno["event"]["info"]["link"] = ($evento->link) ? $evento->link : null;
            $retorno["event"]["info"]["time_control"] = $evento->getTimeControl();

            $retorno["event"]["info"]["limits"] = $evento->getAPILimits();

            $retorno["event"]["info"]["long_description"] = "";
            if($evento->local){
                $retorno["event"]["info"]["long_description"] .= "<strong>Local:</strong> ".$evento->local."<br/>";
            }
            if($evento->link){
                $retorno["event"]["info"]["long_description"] .= "<strong>Maiores Informações:</strong> <a href='".$evento->link."'>".$evento->link."</a><br/>";
            }
            if($evento->pagina){
                $retorno["event"]["info"]["long_description"] .= $evento->pagina->texto;
            }
            if($evento->hasLimits()){
                $retorno["event"]["info"]["long_description"] .= "<strong>Total de Inscritos:</strong> ".$retorno["event"]["info"]["limits"]["total"]."<br/>";
                $retorno["event"]["info"]["long_description"] .= "<strong>Limite Máximo de Inscritos:</strong> ".$retorno["event"]["info"]["limits"]["limit"]."<br/>";
            }

            foreach($evento->getTimelineItems() as $item){
                $retorno["event"]["info"]["timeline"][] = [
                    "datetime" => $item["datetime"],
                    "text" => $item["text"],
                    "is_expected" => $item["is_expected"]
                ];
            }

            if($evento->inscricoes_encerradas()){
                $retorno["event"]["info"]["is_registering"] = false;
                if($evento->is_inscricoes_bloqueadas){
                    $retorno["event"]["info"]["registering_status"] = "O evento não permite inscrições no momento.";
                }elseif($evento->inscricoes_encerradas(false,true)){
                    $retorno["event"]["info"]["registering_status"] = "Prazo para Inscrição Encerrado.";
                }elseif($evento->estaLotado()){
                    $retorno["event"]["info"]["registering_status"] = "O evento chegou ao limite de inscritos.";
                }else{
                    $retorno["event"]["info"]["registering_status"] = "Inscrições encerradas.";
                }
            }else{
                $retorno["event"]["info"]["is_registering"] = true;
                $retorno["event"]["info"]["registering_status"] = "Recebendo inscrições.";
            }

            $retorno["event"]["info"]["is_paid"] = false;
            if($evento->isPaid()){
                $retorno["event"]["info"]["is_paid"] = true;
            }

            $retorno["event"]["info"]["is_lichess"] = false;
            if($evento->is_lichess){
                $retorno["event"]["info"]["is_lichess"] = true;
            }
            $retorno["event"]["info"]["is_lichess_integration"] = false;
            if($evento->is_lichess_integration){
                $retorno["event"]["info"]["is_lichess_integration"] = true;
            }

            $retorno["event"]["info"]["is_chess_com"] = false;
            if($evento->is_chess_com){
                $retorno["event"]["info"]["is_chess_com"] = true;
            }

            $retorno["event"]["info"]["is_xadrezsuico_rating"] = false;
            if($evento->tipo_rating){
                $retorno["event"]["info"]["is_xadrezsuico_rating"] = true;
            }

            $retorno["event"]["info"]["is_use_fide"] = false;
            if($evento->usa_fide){
                $retorno["event"]["info"]["is_use_fide"] = true;
            }

            $retorno["event"]["info"]["is_use_cbx"] = false;
            if($evento->usa_cbx){
                $retorno["event"]["info"]["is_use_cbx"] = true;
            }

            $retorno["event"]["info"]["is_use_lbx"] = false;
            if($evento->usa_lbx){
                $retorno["event"]["info"]["is_use_lbx"] = true;
            }

            $retorno["event"]["info"]["custom_fields"] = array();
            foreach($evento->getPublicCustomFields() as $custom_field){
                $retorno["event"]["info"]["custom_fields"][] = $custom_field->toAPIObject();
            }
            $retorno["event"]["categories"] = array();
            foreach($evento->categorias->all() as $category){
                $retorno["event"]["categories"][] = $category->categoria->toAPIObject();
            }


            return response()->json($retorno);
        }
        return response()->json(["ok"=>0,"error"=>1,"message"=>"Evento não encontrado","httpcode"=>404],404);
    }
}

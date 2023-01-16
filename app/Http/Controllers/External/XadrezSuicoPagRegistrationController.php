<?php

namespace App\Http\Controllers\External;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class XadrezSuicoPagRegistrationController extends Controller
{
    public function register($inscricao){
        if($inscricao->torneio->evento->isPaid()){
            if($inscricao->categoria->isPaid($inscricao->torneio->evento->id)){
                $body = array();
                $body["inscricao_uuid"] = $inscricao->uuid;
                $body["event_uuid"] = $inscricao->torneio->evento->xadrezsuicopag_uuid;
                $body["category_uuid"] = $inscricao
                                            ->categoria
                                            ->eventos()
                                            ->where([["evento_id","=",$inscricao->torneio->evento->id]])
                                            ->first()
                                            ->xadrezsuicopag_uuid;

                $body["player"] = array();
                $body["player"]["id"] = $inscricao->enxadrista->id;
                $body["player"]["name"] = $inscricao->enxadrista->name;
                $body["player"]["email"] = $inscricao->enxadrista->email;
                $body["player"]["phone"] = $inscricao->enxadrista->celular;
                $body["player"]["city"] = $inscricao->cidade->getName();
                $body["player"]["club"] = ($inscricao->clube) ? $inscricao->clube->getFullName() : "";

                $body["notification_uri"] = env("APP_URL")."/api/v1/xadrezsuicopag/callback/".$inscricao->uuid;

                if(env("APP_ENV","local") != "production") {
                    $client = new \GuzzleHttp\Client(["verify"=>false]);
                }else{
                    $client = new \GuzzleHttp\Client();
                }
                $response = $client->request('post', env("XADREZSUICOPAG_URI")."/api/v1/system/registration/new", [
                    'headers' => [
                        "system_id" => env("XADREZSUICOPAG_SYSTEM_ID"),
                        "system_token" => env("XADREZSUICOPAG_SYSTEM_TOKEN")
                    ],
                    'json' => $body
                ]);

                if($response->getStatusCode() < 300){
                    $json = json_decode($response->getBody());
                    if($json->ok == 1){
                        $xadrezsuicopag_obj = array();
                        $xadrezsuicopag_obj["uuid"] = $json->registration->uuid;
                        $xadrezsuicopag_obj["link"] = $json->link;
                    }else{
                        $xadrezsuicopag_obj = array();
                        $xadrezsuicopag_obj["ok"] = $json->ok;
                        $xadrezsuicopag_obj["error"] = $json->error;
                        $xadrezsuicopag_obj["message"] = $json->message;
                    }
                }else{
                    $xadrezsuicopag_obj = array();
                    $xadrezsuicopag_obj["ok"] = 0;
                    $xadrezsuicopag_obj["error"] = 1;
                    $xadrezsuicopag_obj["message"] = "HTTP Code: ".$response->getStatusCode();
                }

                $inscricao->payment_info = json_encode($xadrezsuicopag_obj);
                $inscricao->save();
            }
        }
    }
    public function get($registration_uuid){

        if(env("APP_ENV","local") != "production") {
            $client = new \GuzzleHttp\Client(["verify"=>false]);
        }else{
            $client = new \GuzzleHttp\Client();
        }
        $response = $client->request('get', env("XADREZSUICOPAG_URI")."/api/v1/system/registration/get/".$registration_uuid, [
            'headers' => [
                "system_id" => env("XADREZSUICOPAG_SYSTEM_ID"),
                "system_token" => env("XADREZSUICOPAG_SYSTEM_TOKEN")
            ]
        ]);

        if($response->getStatusCode() < 300){
            $json = json_decode($response->getBody());
            if($json->ok == 1){
                return $json;
            }else{
                return ["ok"=>0,"error"=>1,"message"=>"Motivo Externo (XadrezSuíçoPAG): ".$json->message];
            }
        }
        return ["ok"=>0,"error"=>1,"message"=>"Motivo: Código HTTP XadrezSuíçoPAG Incorreto: ".$json->message];
    }
}

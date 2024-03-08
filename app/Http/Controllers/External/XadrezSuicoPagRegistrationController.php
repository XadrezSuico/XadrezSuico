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
                    $client = new \GuzzleHttp\Client(["verify"=>false,'http_errors' => false]);
                }else{
                    $client = new \GuzzleHttp\Client(['http_errors' => false]);
                }
                $response = $client->request('post', env("XADREZSUICOPAG_URI")."/api/v1/system/registration/new", [
                    'headers' => [
                        "System-Id" => env("XADREZSUICOPAG_SYSTEM_ID"),
                        "System-Token" => env("XADREZSUICOPAG_SYSTEM_TOKEN")
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
    public function get($registration_uuid)
    {

        if (env("APP_ENV", "local") != "production") {
            $client = new \GuzzleHttp\Client(["verify" => false, 'http_errors' => false]);
        } else {
            $client = new \GuzzleHttp\Client(['http_errors' => false]);
        }
        $response = $client->request('get', env("XADREZSUICOPAG_URI") . "/api/v1/system/registration/get/" . $registration_uuid, [
            'headers' => [
                "System-Id" => env("XADREZSUICOPAG_SYSTEM_ID"),
                "System-Token" => env("XADREZSUICOPAG_SYSTEM_TOKEN")
            ]
        ]);

        if ($response->getStatusCode() < 300) {
            $json = json_decode($response->getBody());
            if ($json->ok == 1) {
                return $json;
            } else {
                return ["ok" => 0, "error" => 1, "message" => "Motivo Externo (XadrezSuíçoPAG): " . $json->message];
            }
        }
        $json = json_decode($response->getBody());
        return ["ok" => 0, "error" => 1, "message" => "Motivo: Código HTTP XadrezSuíçoPAG Incorreto: " . $json->message];
    }
    public function delete($registration_uuid)
    {

        if (env("APP_ENV", "local") != "production") {
            $client = new \GuzzleHttp\Client(["verify" => false, 'http_errors' => false]);
        } else {
            $client = new \GuzzleHttp\Client(['http_errors' => false]);
        }
        Log::debug("XadrezSuicoPagRegistrationController::delete URL: ". env("XADREZSUICOPAG_URI") . "/api/v1/system/registration/delete/" . $registration_uuid);
        $response = $client->request('get', env("XADREZSUICOPAG_URI") . "/api/v1/system/registration/delete/" . $registration_uuid, [
            'headers' => [
                "System-Id" => env("XADREZSUICOPAG_SYSTEM_ID"),
                "System-Token" => env("XADREZSUICOPAG_SYSTEM_TOKEN")
            ]
        ]);

        if ($response->getStatusCode() < 300) {
            $json = json_decode($response->getBody(),true);
            if ($json->ok == 1) {
                return $json;
            } else {
                return ["ok" => 0, "error" => 1, "message" => "Motivo Externo (XadrezSuíçoPAG): " . $json->message];
            }
        }
        $json = json_decode($response->getBody());
        return ["ok" => 0, "error" => 1, "message" => "Motivo: Código HTTP XadrezSuíçoPAG Incorreto (". $response->getStatusCode()."): " . json_encode($json)];
    }
}

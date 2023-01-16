<?php

namespace App\Http\Controllers\External;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class XadrezSuicoPagCategoryController extends Controller
{
    public function list($event_uuid){

        if(env("APP_ENV","local") != "production") {
            $client = new \GuzzleHttp\Client(["verify"=>false]);
        }else{
            $client = new \GuzzleHttp\Client();
        }
        $response = $client->request('get', env("XADREZSUICOPAG_URI")."/api/v1/system/categories/list/".$event_uuid, [
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
    public function get($event_uuid,$category_uuid){

        if(env("APP_ENV","local") != "production") {
            $client = new \GuzzleHttp\Client(["verify"=>false]);
        }else{
            $client = new \GuzzleHttp\Client();
        }
        $response = $client->request('get', env("XADREZSUICOPAG_URI")."/api/v1/system/categories/get/".$event_uuid."/".$category_uuid, [
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

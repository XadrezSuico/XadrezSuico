<?php

namespace App\Http\Controllers\External;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Log;

class XadrezSuicoPagCategoryController extends Controller
{
    public function list($event_uuid){

        $headers = [
            "System-Id" => env("XADREZSUICOPAG_SYSTEM_ID"),
            "System-Token" => env("XADREZSUICOPAG_SYSTEM_TOKEN")
        ];

        Log::debug("headers: ".json_encode($headers));

        if(env("APP_ENV","local") != "production") {
            $client = new \GuzzleHttp\Client([
                "verify"=>false,
                'http_errors' => false,
                'headers' => $headers,
            ]);
        }else{
            $client = new \GuzzleHttp\Client([
                'http_errors' => false,
                'headers' => $headers,
            ]);
        }

        $tapMiddleware = \GuzzleHttp\Middleware::tap(function ($request) {
            print_r($request);
        });

        $uri = trim(env("XADREZSUICOPAG_URI")."/api/v1/system/categories/list/".$event_uuid);

        Log::debug("XadrezSuicoPag_Uri_Request: ".$uri);

        $response = $client->request('get', $uri);

        if($response->getStatusCode() < 300){
            $json = json_decode($response->getBody());
            if($json->ok == 1){
                return $json;
            }else{
                return ["ok"=>0,"error"=>1,"message"=>"Motivo Externo (XadrezSuíçoPAG): ".$json->message];
            }
        }
        $json = json_decode($response->getBody());
        return ["ok"=>0,"error"=>1,"message"=>"Motivo: Código HTTP XadrezSuíçoPAG Incorreto: ".$json->message];
    }
    public function get($event_uuid,$category_uuid){

        if(env("APP_ENV","local") != "production") {
            $client = new \GuzzleHttp\Client([
                "verify"=>false,
                'http_errors' => false,
                'headers' => [
                    "System-Id" => env("XADREZSUICOPAG_SYSTEM_ID"),
                    "System-Token" => env("XADREZSUICOPAG_SYSTEM_TOKEN")
                ]
            ]);
        }else{
            $client = new \GuzzleHttp\Client([
                'http_errors' => false,
                'headers' => [
                    "System-Id" => env("XADREZSUICOPAG_SYSTEM_ID"),
                    "System-Token" => env("XADREZSUICOPAG_SYSTEM_TOKEN")
                ]
            ]);
        }
        $response = $client->request('get', env("XADREZSUICOPAG_URI")."/api/v1/system/categories/get/".$event_uuid."/".$category_uuid, [
            'headers' => [
                "System-Id" => env("XADREZSUICOPAG_SYSTEM_ID"),
                "System-Token" => env("XADREZSUICOPAG_SYSTEM_TOKEN")
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
        $json = json_decode($response->getBody());
        return ["ok"=>0,"error"=>1,"message"=>"Motivo: Código HTTP XadrezSuíçoPAG Incorreto: ".$json->message];
    }
}

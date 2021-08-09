<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class LichessIntegrationController extends Controller
{
    public function getSwissResults($tournament_id){
        $client = new Client;
        $response = $client->get("https://lichess.org/api/swiss/".$tournament_id."/results");
        $html = (string) $response->getBody();
        if($response->getStatusCode() == 200){
            return array("ok"=>1,"error"=>0,"data"=>$html);
        }
        return array("ok"=>0,"error"=>1,"message"=>"Torneio não encontrado.");
    }
}

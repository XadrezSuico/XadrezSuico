<?php

namespace App\Http\Controllers\Integration\Online;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

use Log;

class ChessComIntegrationController extends Controller
{
    public function getTournament($slug){
        $client = new Client(['http_errors' => false]);
        $response = $client->get("https://api.chess.com/pub/tournament/".$slug);
        $html = (string) $response->getBody();
        if($response->getStatusCode() == 200){
            return array("ok"=>1,"error"=>0,"data"=>$html);
        }
        return array("ok"=>0,"error"=>1,"message"=>"Torneio não encontrado.");
    }
    public function getRound($slug,$round_number){
        $client = new Client(['http_errors' => false]);
        $response = $client->get("https://api.chess.com/pub/tournament/".$slug."/".$round_number);
        $html = (string) $response->getBody();
        if($response->getStatusCode() == 200){
            return array("ok"=>1,"error"=>0,"data"=>$html);
        }
        return array("ok"=>0,"error"=>1,"message"=>"Torneio/Rodada não encontrado.");
    }
}

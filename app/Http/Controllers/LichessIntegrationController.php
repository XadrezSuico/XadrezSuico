<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

use Log;

class LichessIntegrationController extends Controller
{
    public function getUserData($token){
        $user_response = \Httpful\Request::get('https://lichess.org/api/account')
                ->expectsJson()
                ->addHeader('Authorization', "Bearer " . $token)
                ->send();
        if ($user_response->code == 200) {
            return array("ok"=>1,"error"=>0,"data"=>$user_response->body);
        }
        return array("ok"=>0,"error"=>1,"message"=>"Torneio não encontrado.");
    }
    public function getSwissResults($tournament_id){
        $client = new Client;
        $response = $client->get("https://lichess.org/api/swiss/".$tournament_id."/results");
        $html = (string) $response->getBody();
        if($response->getStatusCode() == 200){
            return array("ok"=>1,"error"=>0,"data"=>$html);
        }
        return array("ok"=>0,"error"=>1,"message"=>"Torneio não encontrado.");
    }
    public function getTeamMembers($team_id){
        $response = \Httpful\Request::get("https://lichess.org/api/team/".$team_id."/users")
                ->addHeader('Authorization', "Bearer " . env("LICHESS_TOKEN",""))
                ->send();
        if($response->code == 200){
            return array("ok"=>1,"error"=>0,"data"=>$response->body);
        }
        return array("ok"=>0,"error"=>1,"message"=>"Time não encontrado.");
    }


    public function removeMemberFromTeam($team_id,$user_id){
        Log::debug("removeMemberFromTeam: "."https://lichess.org/team/".trim($team_id)."/kick/".trim($user_id));
        $response = \Httpful\Request::post("https://lichess.org/team/".trim($team_id)."/kick/".trim($user_id))
            ->addHeader('Authorization', "Bearer " . env("LICHESS_TOKEN",""))
            ->send();
            Log::debug("removeMemberFromTeam-code: ".$response->code);
        if($response->code == 200){
            Log::debug("removeMemberFromTeam-body: ".$response->raw_body);
            return array("ok"=>1,"error"=>0);
        }
        return array("ok"=>0,"error"=>1,"message"=>"Algo deu errado.");
    }
}

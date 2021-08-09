<?php

namespace App\Http\Controllers;

use App\Torneio;
use App\Evento;
use App\Inscricao;

class CronController extends Controller
{
    public function index()
    {
        EmailController::sendScheduledEmails();
        $this->evento_check_players_in();
        $this->evento_players_not_in_lichess_schedule_email();
    }

    public function evento_check_players_in(){
        $torneios = Torneio::whereHas("evento",function($q1){
            $q1->whereNotNull("is_lichess_integration");
            $q1->whereNotNull("lichess_tournament_id");
        })
        ->where(function($q1){
            $q1->whereNull("lichess_last_update");
            $q1->orWhere([["lichess_last_update",">=",date("Y-m-d H:i:s",time() - (60*60*6))]]);
        })
        ->limit(5)
        ->get();
        foreach($torneios as $torneio){
            echo 1;
            $lichess_integration_controller = new LichessIntegrationController;
            $retorno = $lichess_integration_controller->getSwissResults($torneio->evento->lichess_tournament_id);
            if($retorno["ok"] == 1){
                foreach(explode("\n",$retorno["data"]) as $lichess_player_raw){
                    $lichess_player = json_decode(trim($lichess_player_raw));
                    if($lichess_player){
                        $inscricao_count = $torneio->inscricoes()->whereHas("enxadrista",function($q1) use ($lichess_player){
                            $q1->where([
                                ["lichess_username","=",$lichess_player->username]
                            ]);
                        })->count();
                        if($inscricao_count > 0){
                            $inscricao = $torneio->inscricoes()->whereHas("enxadrista",function($q1) use ($lichess_player){
                                $q1->where([
                                    ["lichess_username","=",$lichess_player->username]
                                ]);
                            })->first();

                            $inscricao->is_lichess_found = true;
                            $inscricao->save();
                        }
                    }
                }
            }
            $torneio->lichess_last_update = date("Y-m-d H:i:s");
            $torneio->save();
        }  
    }

    public function evento_players_not_in_lichess_schedule_email(){
        if(date("H:i") == "06:00"){
            $eventos = Evento::where([
                ["is_lichess_integration","=",true],
                ["data_limite_inscricoes_abertas",">=",date("Y-m-d H:i:s")],
            ])->get();
            foreach($eventos as $evento){
                foreach($evento->torneios->all() as $torneio){
                    foreach($torneio->inscricoes()->where([
                        ["is_lichess_found","=",false]
                    ])->get() as $inscricao){
                        // EMAIL PARA O ENXADRISTA SOLICITANTE
                        $text = "Olá " . $inscricao->enxadrista->name . "!<br/>";
                        $text .= "Você está recebendo este email para pois efetuou inscrição no Evento '" . $inscricao->torneio->evento->name . "', porém <strong>ainda não se inscreveu no torneio do Lichess.org</strong>.<br/>";
                        $text .= "Você necessita efetuar a inscrição, pois sem efetuar a inscrição junto ao Torneio do Lichess.org, você não poderá jogar o torneio e inclusive terá sua inscrição cancelada.<br/>";
                        if($evento->orientacao_pos_inscricao != NULL){
                            $text .= "<strong>Segue Orientações Pós-Inscrição:</strong><br/>";
                            $text .= $evento->orientacao_pos_inscricao . "<hr/>";
                        }
                        $text .= "Lembre-se: Você tem até :".$inscricao->torneio->evento->getDataFimInscricoesOnline()." para efetuar estes passos, pois senão terá sua inscrição cancelada e não poderá jogar o evento.<br/>";
                        EmailController::scheduleEmail(
                            $inscricao->enxadrista->email,
                            $evento->name . " - IMPORTANTE! - Inscrição no Torneio do Lichess.org - Enxadrista: " . $inscricao->enxadrista->name,
                            $text,
                            $inscricao->enxadrista
                        );
                    }
                }
            }
        }  
    }
}

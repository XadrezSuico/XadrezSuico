<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;

use App\Torneio;
use App\Evento;
use App\Enxadrista;
use App\Inscricao;

use App\Enum\EmailType;


class CronController extends Controller
{
    public function index()
    {
        EmailController::sendScheduledEmails();
        $this->evento_check_players_in();
        $this->evento_players_not_in_lichess_schedule_email();
        $this->generateUuidOnInscricao();
        $this->generateConvertLichessChessComToLowerOnEnxadrista();
    }

    public function evento_check_players_in(){
        $torneios = Torneio::whereHas("evento",function($q1){
            $q1->whereNotNull("is_lichess_integration");
            $q1->whereNotNull("lichess_tournament_id");
        })
        ->where(function($q1){
            $q1->whereNull("lichess_last_update");
            $q1->orWhere([["lichess_last_update","<=",date("Y-m-d H:i:s",time() - (60*60*6))]]);
        })
        ->limit(5)
        ->get();
        foreach($torneios as $torneio){
            echo 1;
            $lichess_integration_controller = new LichessIntegrationController;
            $retorno = $lichess_integration_controller->getSwissResults($torneio->evento->lichess_tournament_id);
            if($retorno["ok"] == 1){
                $torneio->setAllInscricoesNotFound();
                foreach(explode("\n",$retorno["data"]) as $lichess_player_raw){
                    $lichess_player = json_decode(trim($lichess_player_raw));
                    if($lichess_player){
                        $inscricao_count = $torneio->inscricoes()->whereHas("enxadrista",function($q1) use ($lichess_player){
                            $q1->where([
                                ["lichess_username","=",mb_strtolower($lichess_player->username)]
                            ]);
                        })->count();
                        if($inscricao_count > 0){
                            $inscricao = $torneio->inscricoes()->whereHas("enxadrista",function($q1) use ($lichess_player){
                                $q1->where([
                                    ["lichess_username","=",mb_strtolower($lichess_player->username)]
                                ]);
                            })->first();
                            $inscricao->disableLogging();

                            if(!$inscricao->is_lichess_found && ($inscricao->is_lichess_found != $inscricao->is_last_lichess_found)){

                                // EMAIL PARA O ENXADRISTA SOLICITANTE
                                EmailController::schedule(
                                    $inscricao->enxadrista->email,
                                    $inscricao,
                                    EmailType::ConfirmacaoInscricaoLichess,
                                    $inscricao->enxadrista
                                );
                            }

                            $inscricao->is_lichess_found = true;
                            $inscricao->is_last_lichess_found = true;
                            $inscricao->save();
                        }else{
                            $inscricao->is_lichess_found = false;
                            $inscricao->is_last_lichess_found = false;
                        }
                    }
                }
            }
            $torneio->lichess_last_update = date("Y-m-d H:i:s");
            $torneio->save();
        }
    }

    public function evento_players_not_in_lichess_schedule_email(){
        if(date("H:i") == "08:00"){
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
                        EmailController::schedule(
                            $inscricao->enxadrista->email,
                            $inscricao,
                            EmailType::AvisoNecessidadeInscricaoLichess,
                            $inscricao->enxadrista
                        );
                    }
                }
            }
        }
    }

    public function generateUuidOnInscricao(){
        foreach(Inscricao::whereNull("uuid")->get() as $inscricao){
            $inscricao->uuid = Str::uuid();
            $inscricao->disableLogging();
            $inscricao->save();
        }
    }
    public function generateConvertLichessChessComToLowerOnEnxadrista(){
        foreach(Enxadrista::whereNotNull("lichess_username")->get() as $enxadrista){
            $enxadrista->lichess_username = mb_strtolower($enxadrista->lichess_username);
            $enxadrista->disableLogging();
            $enxadrista->save();
        }
        foreach (Enxadrista::whereNotNull("chess_com_username")->get() as $enxadrista) {
            $enxadrista->chess_com_username = mb_strtolower($enxadrista->chess_com_username);
            $enxadrista->disableLogging();
            $enxadrista->save();
        }

    }
}

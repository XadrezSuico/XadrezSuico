<?php

namespace App\Http\Controllers\Process;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Controllers\Process\RatingDiaController;

use App\Helper\RatingEloHelper;

use App\TipoRating;
use App\Rating;
use App\Evento;
use App\MovimentacaoRating;

use Log;


class RatingController extends Controller
{
    public static function calculate($evento_id){
        $rating_dia_controller = new RatingDiaController;


        $evento = Evento::find($evento_id);
        if($evento){
            // Verifica se o evento permite que seja efetuado o cálculo
            if($evento->is_rating_calculate_enabled){
                // verifica se o evento possui tipo de rating
                if($evento->tipo_rating()){
                    $tipo_rating = $evento->tipo_rating();
                    if($tipo_rating){
                        $tipo_rating = $tipo_rating->first()->tipo_rating;
                    }

                    // roda todos os torneios para o processo de cálculo
                    foreach($evento->torneios->all() as $torneio){
                        $inscricoes = $torneio->inscricoes()->where([["desconsiderar_pontuacao_geral","=",false],["confirmado","=",true]])->get();

                        // percorre todas as inscrições confirmadas e presentes e cria o RatingDia da mesma.
                        foreach($inscricoes as $inscricao){
                            $rating_dia_controller->getRatingDia($tipo_rating->id, $inscricao->enxadrista->id,date("Y-m-d",strtotime($evento->data_inicio) - (60*60*24)));
                        }

                        // percorre as rodadas para calculo
                        foreach($torneio->rodadas()->orderBy("numero","ASC")->get() as $rodada){
                            echo "Rdd".$rodada->numero."\n";
                            // percorre os emparceiramentos
                            foreach($rodada->emparceiramentos->all() as $emparceiramento){
                                if($emparceiramento->inscricao_a && $emparceiramento->inscricao_b){
                                    if($rodada->numero == 1){
                                        $ratingdia_a = $rating_dia_controller->getRatingDia($tipo_rating->id, $emparceiramento->inscricao_A->enxadrista->id,date("Y-m-d",strtotime($evento->data_inicio) - (60*60*24)));
                                        $ratingdia_b = $rating_dia_controller->getRatingDia($tipo_rating->id, $emparceiramento->inscricao_B->enxadrista->id,date("Y-m-d",strtotime($evento->data_inicio) - (60*60*24)));

                                        $emparceiramento->rating_a = $ratingdia_a->value;
                                        $emparceiramento->rating_b = $ratingdia_b->value;

                                        // print_r($emparceiramento);
                                    }else{
                                        $rodada_ant_num = $rodada->numero;

                                        while($rodada_ant_num > 1 && (!$emparceiramento->rating_a || !$emparceiramento->rating_b)){
                                            $rodada_ant_num--;

                                            // TO DO BUSCAR RATING DA ÚLTIMA RODADA
                                            $rodada_anterior = $torneio->rodadas()->where([["numero","=",$rodada_ant_num]])->first();
                                            if(!$emparceiramento->rating_a){
                                                $emparceiramento_a_rodada_anterior = $rodada_anterior
                                                    ->emparceiramentos()
                                                    ->where(function($q1) use ($emparceiramento){
                                                        $q1->where([["inscricao_a","=",$emparceiramento->inscricao_a]]);
                                                        $q1->orWhere([["inscricao_b","=",$emparceiramento->inscricao_a]]);
                                                    })
                                                    ->where([["rodadas_id","=",$rodada_anterior->id]])
                                                    ->first();
                                                // print_r($emparceiramento_a_rodada_anterior);
                                                if($emparceiramento_a_rodada_anterior){
                                                    if($emparceiramento->inscricao_a == $emparceiramento_a_rodada_anterior->inscricao_a){
                                                        $emparceiramento->rating_a = $emparceiramento_a_rodada_anterior->rating_a_final;
                                                    }else{
                                                        $emparceiramento->rating_a = $emparceiramento_a_rodada_anterior->rating_b_final;
                                                    }
                                                }
                                            }

                                            if(!$emparceiramento->rating_b){
                                                $emparceiramento_b_rodada_anterior = $rodada_anterior
                                                    ->emparceiramentos()
                                                    ->where(function($q1) use ($emparceiramento){
                                                        $q1->where([["inscricao_a","=",$emparceiramento->inscricao_b]]);
                                                        $q1->orWhere([["inscricao_b","=",$emparceiramento->inscricao_b]]);
                                                    })
                                                    ->where([["rodadas_id","=",$rodada_anterior->id]])
                                                    ->first();
                                                if($emparceiramento_b_rodada_anterior){
                                                    if($emparceiramento->inscricao_b == $emparceiramento_b_rodada_anterior->inscricao_a){
                                                        $emparceiramento->rating_b = $emparceiramento_b_rodada_anterior->rating_a_final;
                                                    }else{
                                                        $emparceiramento->rating_b = $emparceiramento_b_rodada_anterior->rating_b_final;
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    if($emparceiramento->rating_a && $emparceiramento->rating_b){
                                        Log::debug("Emparceiramento: ".json_encode($emparceiramento));
                                        $elo = RatingEloHelper::generateElo(
                                            $emparceiramento->rating_a,
                                            $emparceiramento->rating_b,
                                            $emparceiramento->inscricao_A->enxadrista->KParaEvento($evento->id),
                                            $emparceiramento->inscricao_B->enxadrista->KParaEvento($evento->id)
                                        );

                                        $emparceiramento->rating_a_if_win = $elo["a"]['1.0'];
                                        $emparceiramento->rating_a_if_drw = $elo["a"]['0.5'];
                                        $emparceiramento->rating_a_if_los = $elo["a"]['0.0'];

                                        $emparceiramento->rating_b_if_win = $elo["b"]['1.0'];
                                        $emparceiramento->rating_b_if_drw = $elo["b"]['0.5'];
                                        $emparceiramento->rating_b_if_los = $elo["b"]['0.0'];

                                        if(!$emparceiramento->is_wo_a && !$emparceiramento->is_wo_b){
                                            $emparceiramento->rating_a_mov = $elo["a"][($emparceiramento->resultado_a)];
                                            $emparceiramento->rating_b_mov = $elo["b"][($emparceiramento->resultado_b)];
                                        }else{
                                            $emparceiramento->rating_a_mov = 0;
                                            $emparceiramento->rating_b_mov = 0;
                                        }

                                        $emparceiramento->rating_a_final = $emparceiramento->rating_a + $emparceiramento->rating_a_mov;
                                        $emparceiramento->rating_b_final = $emparceiramento->rating_b + $emparceiramento->rating_b_mov;

                                        $emparceiramento->save();
                                    }else{
                                        if($emparceiramento->rating_a){
                                            $emparceiramento->rating_a_if_win = 0;
                                            $emparceiramento->rating_a_if_drw = 0;
                                            $emparceiramento->rating_a_if_los = 0;
                                            $emparceiramento->rating_a_mov = 0;
                                            $emparceiramento->rating_a_final = $emparceiramento->rating_a;
                                        }
                                        if($emparceiramento->rating_b){
                                            $emparceiramento->rating_b_if_win = 0;
                                            $emparceiramento->rating_b_if_drw = 0;
                                            $emparceiramento->rating_b_if_los = 0;
                                            $emparceiramento->rating_b_mov = 0;
                                            $emparceiramento->rating_b_final = $emparceiramento->rating_b;
                                        }
                                        $emparceiramento->save();
                                    }
                                }else{
                                    if($emparceiramento->inscricao_a){
                                        if($rodada->numero == 1){
                                            $ratingdia_a = $rating_dia_controller->getRatingDia($tipo_rating->id, $emparceiramento->inscricao_A->enxadrista->id,date("Y-m-d",strtotime($evento->data_inicio) - (60*60*24)));

                                            $emparceiramento->rating_a = $ratingdia_a->value;

                                            // print_r($emparceiramento);
                                        }else{
                                            $rodada_ant_num = $rodada->numero;

                                            while($rodada_ant_num > 1 && !$emparceiramento->rating_a){
                                                $rodada_ant_num--;

                                                // TO DO BUSCAR RATING DA ÚLTIMA RODADA
                                                $rodada_anterior = $torneio->rodadas()->where([["numero","=",$rodada_ant_num]])->first();
                                                if(!$emparceiramento->rating_a){
                                                    $emparceiramento_a_rodada_anterior = $rodada_anterior
                                                        ->emparceiramentos()
                                                        ->where(function($q1) use ($emparceiramento){
                                                            $q1->where([["inscricao_a","=",$emparceiramento->inscricao_a]]);
                                                            $q1->orWhere([["inscricao_b","=",$emparceiramento->inscricao_a]]);
                                                        })
                                                        ->where([["rodadas_id","=",$rodada_anterior->id]])
                                                        ->first();
                                                    // print_r($emparceiramento_a_rodada_anterior);
                                                    if($emparceiramento_a_rodada_anterior){
                                                        if($emparceiramento->inscricao_a == $emparceiramento_a_rodada_anterior->inscricao_a){
                                                            $emparceiramento->rating_a = $emparceiramento_a_rodada_anterior->rating_a_final;
                                                        }else{
                                                            $emparceiramento->rating_a = $emparceiramento_a_rodada_anterior->rating_b_final;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        if($emparceiramento->rating_a){
                                            $emparceiramento->rating_a_if_win = 0;
                                            $emparceiramento->rating_a_if_drw = 0;
                                            $emparceiramento->rating_a_if_los = 0;
                                            $emparceiramento->rating_a_mov = 0;
                                            $emparceiramento->rating_a_final = $emparceiramento->rating_a;
                                        }
                                        $emparceiramento->save();
                                    }

                                    if($emparceiramento->inscricao_b){
                                        if($rodada->numero == 1){
                                            $ratingdia_b = $rating_dia_controller->getRatingDia($tipo_rating->id, $emparceiramento->inscricao_B->enxadrista->id,date("Y-m-d",strtotime($evento->data_inicio) - (60*60*24)));

                                            $emparceiramento->rating_b = $ratingdia_b->value;
                                        }else{
                                            $rodada_ant_num = $rodada->numero;

                                            while($rodada_ant_num > 1 && !$emparceiramento->rating_b){
                                                $rodada_ant_num--;

                                                if(!$emparceiramento->rating_b){
                                                    $emparceiramento_b_rodada_anterior = $rodada_anterior
                                                        ->emparceiramentos()
                                                        ->where(function($q1) use ($emparceiramento){
                                                            $q1->where([["inscricao_a","=",$emparceiramento->inscricao_b]]);
                                                            $q1->orWhere([["inscricao_b","=",$emparceiramento->inscricao_b]]);
                                                        })
                                                        ->where([["rodadas_id","=",$rodada_anterior->id]])
                                                        ->first();
                                                    if($emparceiramento_b_rodada_anterior){
                                                        if($emparceiramento->inscricao_b == $emparceiramento_b_rodada_anterior->inscricao_a){
                                                            $emparceiramento->rating_b = $emparceiramento_b_rodada_anterior->rating_a_final;
                                                        }else{
                                                            $emparceiramento->rating_b = $emparceiramento_b_rodada_anterior->rating_b_final;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        if($emparceiramento->rating_b){
                                            $emparceiramento->rating_b_if_win = 0;
                                            $emparceiramento->rating_b_if_drw = 0;
                                            $emparceiramento->rating_b_if_los = 0;
                                            $emparceiramento->rating_b_mov = 0;
                                            $emparceiramento->rating_b_final = $emparceiramento->rating_b;
                                        }
                                        $emparceiramento->save();
                                    }
                                }
                            }

                            if($rodada->isUltimaRodada()){
                                foreach($inscricoes as $inscricao){
                                    $movimentacao_total = 0;

                                    foreach($inscricao->emparceiramentos_a->all() as $emparceiramento){
                                        $movimentacao_total += $emparceiramento->rating_a_mov;
                                    }
                                    foreach($inscricao->emparceiramentos_b->all() as $emparceiramento){
                                        $movimentacao_total += $emparceiramento->rating_b_mov;
                                    }

                                    $temRating = $inscricao->enxadrista->temRating($torneio->evento->id);
                                    if($temRating){
                                        $rating = $temRating["rating"];

                                        $movimentacao = MovimentacaoRating::where([
                                            ["ratings_id", "=", $rating->id],
                                            ["torneio_id", "=", $torneio->id],
                                        ])->first();
                                        if ($movimentacao) {
                                            // echo "Apagando movimentação de rating deste torneio. <br/>";
                                            // $retornos[] = date("d/m/Y H:i:s") . " - Apagando movimentação de rating deste torneio.";
                                            $movimentacao->delete();
                                        }

                                        $movimentacao = new MovimentacaoRating;
                                        $movimentacao->tipo_ratings_id = $rating->tipo_ratings_id;
                                        $movimentacao->ratings_id = $rating->id;
                                        $movimentacao->torneio_id = $torneio->id;
                                        $movimentacao->inscricao_id = $inscricao->id;
                                        $movimentacao->valor = $movimentacao_total;
                                        $movimentacao->is_inicial = false;
                                        $movimentacao->save();


                                        activity("rating__move_calculate")
                                            ->performedOn($movimentacao)
                                            ->log("Movimentação de Rating calculada para o Evento #".$evento->id." e Rating #".$movimentacao->rating->id);

                                        // $retornos[] = date("d/m/Y H:i:s") . " - Movimentação salva. Calculando e atualizando rating do enxadrista.";
                                        $rating->calcular();
                                    }
                                }
                            }
                        }


                    }
                }
            }
        }
        // exit();
    }

    public static function checkMovesWithoutInscricao(){
        foreach(MovimentacaoRating::whereDoesntHave("inscricao")->where([["is_inicial","=",false]])->get() as $movimentacao){
            if($movimentacao->rating){
                activity("rating__check_moves")
                    ->performedOn($movimentacao)
                    ->withProperties(['rating' => $movimentacao->rating->id])
                    ->log("Cron checkMovesWithoutInscricao: Movimentação sem inscrição. Movimentação excluída.");

                $rating = $movimentacao->rating;

                $movimentacao->delete();

                $rating->calcular();
            }else{
                activity("rating__check_moves")
                    ->performedOn($movimentacao)
                    ->log("Cron checkMovesWithoutInscricao: Movimentação sem inscrição. Movimentação excluída.");
                $movimentacao->delete();
            }
        }
    }

    public static function checkDoubleRatings(){
        foreach(Rating::whereNotNull("tipo_ratings_id")->get() as $rating){
            if(Rating::where([["id","=",$rating->id]])->count() > 0){
                if(Rating::where([
                        ["tipo_ratings_id","=",$rating->tipo_ratings_id],
                        ["enxadrista_id","=",$rating->enxadrista_id]
                    ])->count() > 0){

                    $ratings = Rating::where([
                        ["tipo_ratings_id","=",$rating->tipo_ratings_id],
                        ["enxadrista_id","=",$rating->enxadrista_id]
                    ])->get();

                    $correct_rating = null;
                    $rating_double = false;
                    foreach($ratings as $rtg){
                        if($rtg->movimentacoes()->count() > 1){
                            if(!$correct_rating && !$rating_double){
                                $correct_rating = $rtg;
                            }else{
                                $correct_rating = null;
                                $rating_double = true;
                            }
                        }
                    }

                    if(!$correct_rating && !$rating_double){
                        activity("rating__check_double_ratings")
                            ->performedOn($rating)
                            ->log("Cron checkDoubleRatings: Removendo ratings duplicados");
                        foreach($ratings as $rtg){
                            if($rtg->id != $rating->id){
                                foreach($rtg->movimentacoes->all() as $movimentacao){
                                    $movimentacao->delete();
                                }
                                $rtg->delete();
                            }
                        }
                    }else{
                        if($correct_rating){
                            activity("rating__check_double_ratings")
                                ->performedOn($correct_rating)
                                ->log("Cron checkDoubleRatings: Removendo ratings duplicados");
                            foreach($ratings as $rtg){
                                if($rtg->id != $correct_rating->id){
                                    foreach($rtg->movimentacoes->all() as $movimentacao){
                                        $movimentacao->delete();
                                    }
                                    $rtg->delete();
                                }
                            }
                        }
                    }
                }else{
                    activity("rating__check_double_ratings")
                        ->performedOn($rating)
                        ->log("Cron checkDoubleRatings: Rating inexistente, provavelmente já removido.");
                }
            }else{
                activity("rating__check_double_ratings")
                    ->performedOn($rating)
                    ->log("Cron checkDoubleRatings: Rating único - não necessário remover duplicidade.");
            }
        }
    }
}

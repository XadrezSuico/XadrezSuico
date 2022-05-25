<?php

namespace App\Http\Controllers\Process;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\TipoRating;
use App\MovimentacaoRating;

use App\Enxadrista;
use App\RatingDia;
use App\Rating;


class RatingDiaController extends Controller
{
    public function getRatingDia($tipo_rating_id, $enxadrista_id, $date){
        $tipo_rating = TipoRating::find($tipo_rating_id);
        if($tipo_rating){
            $enxadrista = Enxadrista::find($enxadrista_id);
            if($enxadrista){
                return $this->generateRatingDia($tipo_rating_id, $enxadrista_id, $date);
            }
            return -2;
        }
        return -1;
    }

    public function generateRatingDia($tipo_rating_id, $enxadrista_id, $date){
        $tipo_rating = TipoRating::find($tipo_rating_id);
        if($tipo_rating){
            $enxadrista = Enxadrista::find($enxadrista_id);
            if($enxadrista){
                $tem_rating = $enxadrista->temRating(null, $tipo_rating->id);
                if($tem_rating["ok"] == 1){
                    $rating = $tem_rating["rating"];

                    $rating_dia_count = RatingDia::where([["date","=",$date],["ratings_id","=",$tem_rating["rating"]->id]])->count();
                    if($rating_dia_count == 0){
                        $rating_dia = new RatingDia;
                        $rating_dia->ratings_id = $rating->id;
                        $rating_dia->date = $date;
                    }else{
                        $rating_dia = RatingDia::where([["date","=",$date],["ratings_id","=",$tem_rating["rating"]->id]])->first();
                    }


                    $total = 0;
                    foreach($rating->movimentacoes()
                    ->where(function($q1) use ($date) {
                        $q1->whereHas("torneio",function($q1) use ($date) {
                            $q1->whereHas("evento",function($q2) use ($date) {
                                $q2->where([
                                    ["data_fim","<=",$date],
                                    ["is_rating_calculate_enabled","=",true]
                                ]);
                            });
                        })
                        ->orWhere([["is_inicial","=",true]]);
                    })
                    ->where([["ratings_id","=",$rating->id]])
                    ->get() as $movimentacao){
                        $total += $movimentacao->valor;
                    }

                    $rating_dia->value = $total;
                    $rating_dia->save();
                }else{
                    $rating = new Rating;
                    $rating->tipo_ratings_id = $tipo_rating->id;
                    $rating->enxadrista_id = $enxadrista->id;
                    $rating->valor = $tem_rating["regra"]->inicial;
                    $rating->save();

                    $movimentacao = new MovimentacaoRating;
                    $movimentacao->tipo_ratings_id = $rating->tipo_ratings_id;
                    $movimentacao->ratings_id = $rating->id;
                    $movimentacao->valor = $tem_rating["regra"]->inicial;
                    $movimentacao->is_inicial = true;
                    $movimentacao->save();



                    $rating_dia = new RatingDia;
                    $rating_dia->ratings_id = $rating->id;
                    $rating_dia->date = $date;
                    $rating_dia->value = $rating->valor;
                    $rating_dia->save();
                }

                return $rating_dia;
            }
        }
        return false;
    }
}

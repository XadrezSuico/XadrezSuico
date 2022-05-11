<?php

namespace App\Http\Controllers\Process;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\TipoRating;

class RatingDiaController extends Controller
{
    public function getRatingDia($tipo_rating_id, $enxadrista_id, $date){
        $tipo_rating = TipoRating::find($tipo_rating_id);
        if($tipo_rating){
            $enxadrista = Enxadrista::find($enxadrista_id);
            if($enxadrista){
                $rating = $enxadrista->getRating(null,$tipo_rating->id);
                if($rating->rating == null){
                    $this->generateRatingDia($tipo_rating_id, $enxadrista_id, $date);
                    $rating = $enxadrista->getRating(null,$tipo_rating->id);

                    $rating_dia = new RatingDia;
                    $rating_dia->ratings_id = $rating->rating->id;
                    $rating_dia->date = $date;
                    $rating_dia->value = $rating->rating->valor;
                    $rating_dia->save();
                }else{
                    $rating_dia_count = RatingDia::where([["date","=",$date]])->count();
                    if($rating_dia_count == 0){
                        $rating_dia = new RatingDia;
                        $rating_dia->ratings_id = $rating->rating->id;
                        $rating_dia->date = $date;
                    }else{
                        $rating_dia = RatingDia::where([["date","=",$date]])->first();
                    }

                    $total = 0;
                    foreach($rating->rating->movimentacoes
                        ->whereHas("torneio",function($q1) use ($date) {
                            $q1->whereHas("evento",function($q2) use ($date) {
                                $q2->where([
                                    ["data_fim","<=",$date],
                                    ["is_rating_calculate_enabled","=",true]
                                ]);
                            });
                        })
                    ->get() as $movimentacao){
                        $total += $movimentacao->valor;
                    }

                    $rating_dia->valor = $total;
                    $rating_dia->save();
                }

                return $rating_dia;
            }
        }
    }

    public function generateRatingDia($tipo_rating_id, $enxadrista_id, $date){
        $tipo_rating = TipoRating::find($tipo_rating_id);
        if($tipo_rating){
            $enxadrista = Enxadrista::find($enxadrista_id);
            if($enxadrista){
                $rating = $enxadrista->getRating($tipo_rating->id);
                if($rating){

                }else{
                    $rating = new Rating;
                    $rating->tipo_ratings_id = $tipo_rating->id;
                    $rating->enxadristas_id = $enxadrista->id;

                    $rating->valor = $enxadrista->getRating(null,$tipo_rating->id)->inicial;

                    $rating->save();
                }
            }
        }
    }
}

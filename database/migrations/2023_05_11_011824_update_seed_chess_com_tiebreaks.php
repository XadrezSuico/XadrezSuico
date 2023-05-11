<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\CriterioDesempate;

class UpdateSeedChessComTiebreaks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tiebreaks = [
            [
                "name" => "Chess.com - Buchholz com corte do pior resultado",
                "code" => "chesscom-buchholz-cut-1",
                "old_code" => "chesscom-buchholz_cut_1",
            ],
            [
                "name" => "Chess.com - Buchholz Totais",
                "code" => "chesscom-buchholz",
                "old_code" => "chesscom-buchholz",
            ],
            [
                "name" => "Chess.com - Sonneborn-Berger",
                "code" => "chesscom-sonneborn_berger",
                "old_code" => "chesscom-sonneborn-berger",
            ],
            [
                "name" => "Chess.com - Confronto Direto",
                "code" => "chesscom-direct_encounter",
                "old_code" => "chesscom-direct-encounter",
            ],
            [
                "name" => "Chess.com - Maior Número de Vitórias",
                "code" => "chesscom-the_greater_number_of_wins_including_forfeits",
                "old_code" => "chesscom-the-greater-number-of-wins-including-forfeits",
            ],
            [
                "name" => "Chess.com - Maior Número de Vitórias de Negras",
                "code" => "chesscom-number_of_wins_with_black_pieces",
                "old_code" => "chesscom-number-of-wins-with-black-pieces",
            ],
            [
                "name" => "Chess.com - AROC 1",
                "code" => "chesscom-aroc_1",
                "old_code" => "chesscom-aroc-1",
            ],
        ];

        foreach($tiebreaks as $tiebreak){
            if(CriterioDesempate::where([["is_chess_com","=",true],["code","=",$tiebreak["old_code"]]])->count() > 0){
                $criterio_desempate = CriterioDesempate::where([["is_chess_com","=",true],["code","=",$tiebreak["old_code"]]])->first();
                $criterio_desempate->code = $tiebreak["code"];
                $criterio_desempate->is_chess_com = true;
                $criterio_desempate->is_sm = false;
                $criterio_desempate->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SeedCriteriosDesempateSM extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::Table('criterio_desempate')->insert(array('name' => "Pts.  points (game-points)", "code" => "", "sm_code" => "1"));
        DB::Table('criterio_desempate')->insert(array('name' => "Buchholz Tie-Breaks (all Results)", "code" => "", "sm_code" => "2"));
        DB::Table('criterio_desempate')->insert(array('name' => "Buchholzwertung (1 Streichresultat)", "code" => "", "sm_code" => "3"));
        DB::Table('criterio_desempate')->insert(array('name' => "Buchholz Tie-Breaks (without two results=middle Tie-Breaks)", "code" => "", "sm_code" => "4"));
        DB::Table('criterio_desempate')->insert(array('name' => "Manually input in field rankcorr. in player-dialog", "code" => "", "sm_code" => "5"));
        DB::Table('criterio_desempate')->insert(array('name' => "Manually input in field rankcorr. in team-dialog", "code" => "", "sm_code" => "6"));
        DB::Table('criterio_desempate')->insert(array('name' => "Sonneborn-Berger-Tie-Break (with real points)", "code" => "", "sm_code" => "7"));
        DB::Table('criterio_desempate')->insert(array('name' => "Fide Tie-Break ", "code" => "", "sm_code" => "8"));
        DB::Table('criterio_desempate')->insert(array('name' => "Fide Tie-Break (fine)", "code" => "", "sm_code" => "9"));
        DB::Table('criterio_desempate')->insert(array('name' => "rating average of the opponents", "code" => "", "sm_code" => "10"));
        DB::Table('criterio_desempate')->insert(array('name' => "The results of the players in the same point", "code" => "", "sm_code" => "11"));
        DB::Table('criterio_desempate')->insert(array('name' => "The greater number of victories", "code" => "", "sm_code" => "12"));
        DB::Table('criterio_desempate')->insert(array('name' => "Matchpoints (2 for wins, 1 for Draws, 0 for Losses)", "code" => "", "sm_code" => "13"));
        DB::Table('criterio_desempate')->insert(array('name' => "The results of the teams in then same point group according to Matchpoints", "code" => "", "sm_code" => "14"));
        DB::Table('criterio_desempate')->insert(array('name' => "Board Tie-Breaks of the whole tournament", "code" => "", "sm_code" => "15"));
        DB::Table('criterio_desempate')->insert(array('name' => "Buchholz Tie-Breaks (sum of team-points of the opponents and own points)", "code" => "", "sm_code" => "16"));
        DB::Table('criterio_desempate')->insert(array('name' => "Buchholz Tie-Breaks (with the real points)", "code" => "", "sm_code" => "17"));
        DB::Table('criterio_desempate')->insert(array('name' => "Carasaxa Tie-Breaks", "code" => "", "sm_code" => "18"));
        DB::Table('criterio_desempate')->insert(array('name' => "Sonneborn-Berger Tie-Break (with modified points, analogous to Buchholz Tie-Break)", "code" => "", "sm_code" => "19"));
        DB::Table('criterio_desempate')->insert(array('name' => "rating average of the opponents (without one result)", "code" => "", "sm_code" => "20"));
        DB::Table('criterio_desempate')->insert(array('name' => "rating performance", "code" => "", "sm_code" => "21"));
        DB::Table('criterio_desempate')->insert(array('name' => "Buchholz Tie-Breaks (sum of team-points of the opponents)", "code" => "", "sm_code" => "22"));
        DB::Table('criterio_desempate')->insert(array('name' => "Sum of the ratings of the opponents (whithout one result)", "code" => "", "sm_code" => "23"));
        DB::Table('criterio_desempate')->insert(array('name' => "The BSV-Board-Tie-Break", "code" => "", "sm_code" => "24"));
        DB::Table('criterio_desempate')->insert(array('name' => "Sum of Buchholz-Tie-Breaks (all Results)", "code" => "", "sm_code" => "25"));
        DB::Table('criterio_desempate')->insert(array('name' => "For imported tournaments (Tie-break 1)", "code" => "", "sm_code" => "26"));
        DB::Table('criterio_desempate')->insert(array('name' => "For imported tournaments (Tie-break 2)", "code" => "", "sm_code" => "27"));
        DB::Table('criterio_desempate')->insert(array('name' => "Buchholz-Tie-Breaks (all Results (special))", "code" => "", "sm_code" => "28"));
        DB::Table('criterio_desempate')->insert(array('name' => "Buchholz-Tie-Breaks (without two results=middle Tie-Breaks (special))", "code" => "", "sm_code" => "29"));
        DB::Table('criterio_desempate')->insert(array('name' => "Buchholz-Tie-Breaks (all Results with real points)", "code" => "", "sm_code" => "30"));
        DB::Table('criterio_desempate')->insert(array('name' => "Buchholz-Tie-Breaks (without two results with real points)", "code" => "", "sm_code" => "31"));
        DB::Table('criterio_desempate')->insert(array('name' => "For imported tournaments (Tie-break 1)", "code" => "", "sm_code" => "32"));
        DB::Table('criterio_desempate')->insert(array('name' => "Fide Tie-Break (no points for dropped players)", "code" => "", "sm_code" => "33"));
        DB::Table('criterio_desempate')->insert(array('name' => "Buchholz-Tie-Breaks (without two results=middle Tie-Breaks)", "code" => "", "sm_code" => "34"));
        DB::Table('criterio_desempate')->insert(array('name' => "FIDE-Sonneborn-Berger-Tie-Break", "code" => "", "sm_code" => "35"));
        DB::Table('criterio_desempate')->insert(array('name' => "rating average of the opponents (variabel with parameter)", "code" => "", "sm_code" => "36"));
        DB::Table('criterio_desempate')->insert(array('name' => "Buchholz Tie-Breaks (variabel with parameter)", "code" => "", "sm_code" => "37"));
        DB::Table('criterio_desempate')->insert(array('name' => "Points (game-points) + 1 point for each won match.", "code" => "", "sm_code" => "38"));
        DB::Table('criterio_desempate')->insert(array('name' => "points (3 for wins, 2 for Draws, 1 for Losses, 0 for Losses forfeit)", "code" => "", "sm_code" => "39"));
        DB::Table('criterio_desempate')->insert(array('name' => "Matchpoints (3 for wins, 1 for Draws, 0 for Losses)", "code" => "", "sm_code" => "40"));
        DB::Table('criterio_desempate')->insert(array('name' => "The better result (½ or 1) against the rating-strongest player", "code" => "", "sm_code" => "41"));
        DB::Table('criterio_desempate')->insert(array('name' => "Points (Game-points + Qualifying-points)", "code" => "", "sm_code" => "42"));
        DB::Table('criterio_desempate')->insert(array('name' => "Play-off Points", "code" => "", "sm_code" => "43"));
        DB::Table('criterio_desempate')->insert(array('name' => "Matchpoints (variabel)", "code" => "", "sm_code" => "44"));
        DB::Table('criterio_desempate')->insert(array('name' => "Koya  Koya-System (Points against player with >= 50% of the points)", "code" => "", "sm_code" => "45"));
        DB::Table('criterio_desempate')->insert(array('name' => "Points (game-points) + Matchpoints (3 for wins, 1 for Draws, 0 for Losses)", "code" => "", "sm_code" => "46"));
        DB::Table('criterio_desempate')->insert(array('name' => "Points (variabel)", "code" => "", "sm_code" => "47"));
        DB::Table('criterio_desempate')->insert(array('name' => "Sum of Matchpoints (variabel)", "code" => "", "sm_code" => "48"));
        DB::Table('criterio_desempate')->insert(array('name' => "Olympiad Matchpoints (2,1,0) (without lowest result)", "code" => "", "sm_code" => "49"));
        DB::Table('criterio_desempate')->insert(array('name' => "Olympiad-Sonneborn-Berger-Tie-Break", "code" => "", "sm_code" => "50"));
        DB::Table('criterio_desempate')->insert(array('name' => "Sonneborn-Berger-Tie-Break(variabel)", "code" => "", "sm_code" => "52"));
        DB::Table('criterio_desempate')->insert(array('name' => "Most black", "code" => "", "sm_code" => "53"));
        DB::Table('criterio_desempate')->insert(array('name' => "Recursive Ratingperformance", "code" => "", "sm_code" => "54"));
        DB::Table('criterio_desempate')->insert(array('name' => "Average Recursive Performance of Opponents", "code" => "", "sm_code" => "55"));
        DB::Table('criterio_desempate')->insert(array('name' => "Olympiad Khanty Mansysk Matchpoints (2,1,0) (without lowest result)", "code" => "", "sm_code" => "56"));
        DB::Table('criterio_desempate')->insert(array('name' => "Olympiad Khanty Mansysk-Sonneborn-Berger-Tie-Break", "code" => "", "sm_code" => "57"));
        DB::Table('criterio_desempate')->insert(array('name' => "Rtg Sum (without lowest rtg) or Progressive Score (especially for the Youth WCC 2011)", "code" => "", "sm_code" => "58"));
        DB::Table('criterio_desempate')->insert(array('name' => "Rating Performance without two results (EM 2011) especially for the Single-EM 2011 in France", "code" => "", "sm_code" => "59"));
        DB::Table('criterio_desempate')->insert(array('name' => "Performance (variable with parameter)", "code" => "", "sm_code" => "60"));
        DB::Table('criterio_desempate')->insert(array('name' => "Arranz System", "code" => "", "sm_code" => "61"));
        DB::Table('criterio_desempate')->insert(array('name' => "Games ascending", "code" => "", "sm_code" => "62"));
        DB::Table('criterio_desempate')->insert(array('name' => "Percent", "code" => "", "sm_code" => "63"));
        DB::Table('criterio_desempate')->insert(array('name' => "Board", "code" => "", "sm_code" => "64"));
        DB::Table('criterio_desempate')->insert(array('name' => "Games descending", "code" => "", "sm_code" => "65"));
        DB::Table('criterio_desempate')->insert(array('name' => "Ranking of their teams", "code" => "", "sm_code" => "66"));
        DB::Table('criterio_desempate')->insert(array('name' => "Sonneborn-Berger-Tie-Break (analog [57] but with all results)", "code" => "", "sm_code" => "67"));
        DB::Table('criterio_desempate')->insert(array('name' => "The greater number of victories (variable)", "code" => "", "sm_code" => "68"));
        DB::Table('criterio_desempate')->insert(array('name' => "Koya Tie-Break (fine)", "code" => "", "sm_code" => "69"));
        DB::Table('criterio_desempate')->insert(array('name' => "Sum Buchholz-Tie Break variable", "code" => "", "sm_code" => "70"));
        DB::Table('criterio_desempate')->insert(array('name' => "Berlin Tie-Break", "code" => "", "sm_code" => "71"));
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

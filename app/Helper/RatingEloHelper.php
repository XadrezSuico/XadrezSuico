<?php
namespace App\Helper;


class RatingEloHelper{

    public static function generateElo($rating_a, $rating_b, $k_a, $k_b){
        /*
         *  Qa = 10^(Ra/400)
            Qb = 10^(Rb/400)

            Ea = Qa/(Qa+Qb)
            Eb = Qb/(Qa+Qb)


            Ra' = Ra + K*(P	- Ea)
         */

        $Qa = 10^($rating_a/400);
        $Qb = 10^($rating_b/400);

        $Ea = $Qa / ($Qa + $Qb);
        $Eb = $Qb / ($Qa + $Qb);

        $modifications = array();

        $modifications["a"][1] = $k_a * (1 - $Ea);
        $modifications["a"][0.5] = $k_a * (0.5 - $Ea);
        $modifications["a"][0] = $k_a * (0 - $Ea);

        $modifications["b"][1] = $k_b * (1 - $Ea);
        $modifications["b"][0.5] = $k_b * (0.5 - $Ea);
        $modifications["b"][0] = $k_b * (0 - $Ea);

        return $modifications;
    }

}

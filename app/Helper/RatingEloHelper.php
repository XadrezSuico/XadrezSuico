<?php
namespace App\Helper;

use Log;

class RatingEloHelper{

    public static function generateElo($rating_a, $rating_b, $k_a = 20, $k_b = 20){
        /*
         *  Qa = 10^(Ra/400)
            Qb = 10^(Rb/400)

            Ea = Qa/(Qa+Qb)
            Eb = Qb/(Qa+Qb)


            Ra' = Ra + K*(P	- Ea)
         */
        Log::debug("Ra: ".$rating_a);
        Log::debug("Rb: ".$rating_b);

        $Qa = pow(10,($rating_a/(float)400));
        $Qb = pow(10,($rating_b/(float)400));

        $Ea = $Qa / ($Qa + $Qb);
        $Eb = $Qb / ($Qa + $Qb);

        $modifications = array();

        $modifications_wo["a"]['1.0'] = $k_a * (1 - $Ea);
        $modifications_wo["a"]['0.5'] = $k_a * (0.5 - $Ea);
        $modifications_wo["a"]['0.0'] = $k_a * (0 - $Ea);

        $modifications_wo["b"]['1.0'] = $k_b * (1 - $Eb);
        $modifications_wo["b"]['0.5'] = $k_b * (0.5 - $Eb);
        $modifications_wo["b"]['0.0'] = $k_b * (0 - $Eb);

        $modifications["a"]['1.0'] = round($k_a * (1 - $Ea));
        $modifications["a"]['0.5'] = round($k_a * (0.5 - $Ea));
        $modifications["a"]['0.0'] = round($k_a * (0 - $Ea));

        $modifications["b"]['1.0'] = round($k_b * (1 - $Eb));
        $modifications["b"]['0.5'] = round($k_b * (0.5 - $Eb));
        $modifications["b"]['0.0'] = round($k_b * (0 - $Eb));

        // Log::debug("Qa: ".$Qa);
        // Log::debug("Qb: ".$Qb);

        // Log::debug("Ea: ".$Ea);
        // Log::debug("Eb: ".$Eb);

        // Log::debug("Modificações sem arredondamento: ".json_encode($modifications_wo));
        // Log::debug("Modificações: ".json_encode($modifications));

        return $modifications;
    }

}

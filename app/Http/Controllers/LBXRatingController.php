<?php

namespace App\Http\Controllers;

use App\Enxadrista;
use App\Http\Util\Util;
use GuzzleHttp\Client;

class LBXRatingController extends Controller
{
    public function updateRatings()
    {
        if(!(date("j") == 1 && date("G") == 0)){
            $enxadristas = Enxadrista::where([
                ["lbx_id", "!=", null],
                ["lbx_last_update", "<", date("Y-m") . "-01 00:00:00"],
            ])
                ->orWhere([
                    ["lbx_id", "!=", null],
                    ["lbx_last_update", "=", null],
                ])
                ->limit(10)
                ->get();
            foreach ($enxadristas as $enxadrista) {
                $this->getRating($enxadrista);
            }
        }
    }

    public static function getRating($enxadrista, $show_text = true, $return_enxadrista = false, $save_rating = true){
        if(env("LBX_RATING_SERVER",false)){
            $codigo_organizacao = 2;


            if($show_text) echo "Enxadrista #" . $enxadrista->id . " - " . $enxadrista->name. "(".$enxadrista->lbx_id.")";

            $client = new Client;
            $response = $client->get(env("LBX_RATING_SERVER")."/rating/search/id/" . $enxadrista->lbx_id);
            $html = (string) $response->getBody();
            // echo $html;

            $json = json_decode($html);
            $json_array = json_decode($html,true);

            if($json){
                if(count($json_array) > 0){
                    $enxadrista->encontrado_lbx = true;

                    $enxadrista->lbx_name = $json->sobrenome.", ".$json->nome;

                    if($json->ratings->STD == 0){
                        if($save_rating) $enxadrista->setRating($codigo_organizacao,0,1800);
                    }else{
                        if($show_text) echo "STD:" . $json->ratings->STD;
                        if($save_rating) $enxadrista->setRating($codigo_organizacao,0,$json->ratings->STD);
                    }

                    if($json->ratings->RPD == 0){
                        if($save_rating) $enxadrista->setRating($codigo_organizacao,1,1800);
                    }else{
                        if($show_text) echo "RPD:" . $json->ratings->RPD;
                        if($save_rating) $enxadrista->setRating($codigo_organizacao,1,$json->ratings->RPD);
                    }

                    if($json->ratings->BTZ == 0){
                        if($save_rating) $enxadrista->setRating($codigo_organizacao,2,1800);
                    }else{
                        if($show_text) echo "BTZ:" . $json->ratings->BTZ;
                        if($save_rating) $enxadrista->setRating($codigo_organizacao,2,$json->ratings->BTZ);
                    }
                }else{
                    if($show_text) echo "Erro count json";
                    $enxadrista->encontrado_lbx = false;
                }
            }else{
                if($show_text) echo "Erro json";
                $enxadrista->encontrado_lbx = false;
            }
            if($save_rating) $enxadrista->lbx_last_update = date("Y-m-d H:i:s");
            if($return_enxadrista){
                return $enxadrista;
            }else{
                $enxadrista->save();
            }
            if($show_text) echo "<hr/>";
        }else{
            if($show_text) echo "Erro env";
        }
    }

}

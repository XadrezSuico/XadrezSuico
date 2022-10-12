<?php

namespace App\Http\Controllers;

use App\Enxadrista;
use App\Http\Util\Util;
use GuzzleHttp\Client;

class FIDERatingController extends Controller
{
    public function updateRatings()
    {
        $enxadristas = Enxadrista::where([
            ["fide_id", "!=", null],
            ["fide_last_update", "<", date("Y-m") . "-01 00:00:00"],
        ])
            ->orWhere([
                ["fide_id", "!=", null],
                ["fide_last_update", "=", null],
            ])
            ->limit(5)
            ->get();
        foreach ($enxadristas as $enxadrista) {
            $this->getRating($enxadrista);
        }
    }

    // public static function getRating($enxadrista, $show_text = true, $return_enxadrista = false, $save_rating = true){
    //     $codigo_organizacao = 0;


    //     if($show_text) echo "Enxadrista #" . $enxadrista->id . " - " . $enxadrista->name;

    //     $client = new Client;
    //     $response = $client->get("http://ratings.fide.com/card.phtml?event=" . $enxadrista->fide_id);
    //     $html = (string) $response->getBody();
    //     // echo $html;

    //     $explode_not_found = explode("Player not found", $html);
    //     if (count($explode_not_found) == 1) {
    //         // continuar o desenvolvimento a partir daqui
    //         $enxadrista->encontrado_fide = true;
    //         $enxadrista->fide_name = FIDERatingController::getName($html);


    //         $explode_table_1 = explode("<table width=100% cellpadding=0 cellspacing=0 align=ceter broder=0>", $html);
    //         if (count($explode_table_1) == 2) {
    //             $explode_table_2 = explode("</table>", $explode_table_1[1]);
    //             if (count($explode_table_2) >= 2) {
    //                 $explode_table_3 = explode("<tr>", $explode_table_2[0]);
    //                 if (count($explode_table_3) == 2) {
    //                     $explode_table_4 = explode("</tr>", $explode_table_3[1]);
    //                     if (count($explode_table_4) == 2) {
    //                         $explode_columns = explode("align=center>", $explode_table_4[0]);

    //                         $std = "<small>std.</small><br>";
    //                         $rpd = "<small>rapid</small><br>";
    //                         $btz = "<small>blitz</small><br>";
    //                         foreach ($explode_columns as $column_brute) {
    //                             $column = explode("</td>", $column_brute);
    //                             if (count($column) == 2) {
    //                                 $exp_std = explode($std, $column[0]);
    //                                 $exp_rpd = explode($rpd, $column[0]);
    //                                 $exp_btz = explode($btz, $column[0]);
    //                                 $rating = Util::numeros($column[0]);

    //                                 if($show_text) echo "Rating: " . $rating;
    //                                 if (count($exp_std) == 2) {
    //                                     if (is_int(intval($rating))) {
    //                                         if (intval($rating) > 0) {
    //                                             if($save_rating) $enxadrista->setRating($codigo_organizacao, 0, intval($rating));
    //                                         }
    //                                     } else {
    //                                         if($show_text) echo "Erro Rating não é inteiro!";
    //                                     }
    //                                 } elseif (count($exp_rpd) == 2) {
    //                                     if (is_int(intval($rating))) {
    //                                         if (intval($rating) > 0) {
    //                                             if($save_rating) $enxadrista->setRating($codigo_organizacao, 1, intval($rating));
    //                                         }
    //                                     } else {
    //                                         if($show_text) echo "Erro Rating não é inteiro!";
    //                                     }
    //                                 } elseif (count($exp_btz) == 2) {
    //                                     if (is_int(intval($rating))) {
    //                                         if (intval($rating) > 0) {
    //                                             if($save_rating) $enxadrista->setRating($codigo_organizacao, 2, intval($rating));
    //                                         }
    //                                     } else {
    //                                         if($show_text) echo "Erro Rating não é inteiro!";
    //                                     }
    //                                 } else {
    //                                     if($show_text) echo "Erro Nenhum tipo de rating encontrado";
    //                                 }
    //                             } else {
    //                                 if($show_text) echo "Erro column";
    //                             }
    //                         }
    //                     } else {
    //                         if($show_text) echo "Erro explode_table_4";
    //                     }
    //                 } else {
    //                     if($show_text) echo "Erro explode_table_3";
    //                 }
    //             } else {
    //                 if($show_text) echo "Erro explode_table_2";
    //             }
    //         } else {
    //             if($show_text) echo "Erro explode_table_1";
    //         }
    //     }else{
    //         $enxadrista->encontrado_fide = false;
    //     }

    //     if($save_rating) $enxadrista->fide_last_update = date("Y-m-d H:i:s");
    //     if($return_enxadrista){
    //         return $enxadrista;
    //     }else{
    //         $enxadrista->save();
    //     }
    //     if($show_text) echo "<hr/>";
    // }

    public static function getRating($enxadrista, $show_text = true, $return_enxadrista = false, $save_rating = true){
        $codigo_organizacao = 0;


        if($show_text) echo "Enxadrista #" . $enxadrista->id . " - " . $enxadrista->name . "(".$enxadrista->fide_id.")";

        $client = new Client;
        $response = $client->get("https://fide-ratings-scraper.herokuapp.com/player/" . $enxadrista->fide_id . "/elo");
        if($show_text) echo "<br/>";
        $html = (string) $response->getBody();

        $json = json_decode($html);
        if(!isset($json->reason)){
            $enxadrista->encontrado_fide = true;
            if(!$return_enxadrista) $enxadrista->save();
            if(isset($json->standard_elo)){
                if(is_numeric($json->standard_elo)){
                    if($show_text) echo "STD:" . $json->standard_elo;
                    if($save_rating) $enxadrista->setRating($codigo_organizacao, 0, intval($json->standard_elo));
                }else{
                    if($show_text) echo "STD: String (".$json->standard_elo.")";
                    if($save_rating) $enxadrista->deleteRating($codigo_organizacao, 0);
                }
            }else{
                if($show_text) echo "STD: Not Found";
                if($save_rating) $enxadrista->deleteRating($codigo_organizacao, 0);
            }
            if($show_text) echo "<br/>";
            if(isset($json->rapid_elo)){
                if(is_numeric($json->rapid_elo)){
                    if($show_text) echo "RPD:" . $json->rapid_elo;
                    if($save_rating) $enxadrista->setRating($codigo_organizacao, 1, intval($json->rapid_elo));
                }else{
                    if($show_text) echo "RPD: String (".$json->rapid_elo.")";
                    if($save_rating) $enxadrista->deleteRating($codigo_organizacao, 1);
                }
            }else{
                if($show_text) echo "RPD: Not Found";
                if($save_rating) $enxadrista->deleteRating($codigo_organizacao, 1);
            }
            if($show_text) echo "<br/>";
            if(isset($json->blitz_elo)){
                if(is_numeric($json->blitz_elo)){
                    if($show_text) echo "BTZ:" . $json->blitz_elo;
                    if($save_rating) $enxadrista->setRating($codigo_organizacao, 2, intval($json->blitz_elo));
                }else{
                    if($show_text) echo "BTZ: String (".$json->blitz_elo.")";
                    if($save_rating) $enxadrista->deleteRating($codigo_organizacao, 2);
                }
            }else{
                if($show_text) echo "BTZ: Not Found";
                if($save_rating) $enxadrista->deleteRating($codigo_organizacao, 2);
            }
        }else{
            $enxadrista->encontrado_fide = false;
            if(!$return_enxadrista) $enxadrista->save();
        }

        if($save_rating) $enxadrista->fide_last_update = date("Y-m-d H:i:s");
        if($return_enxadrista){
            return $enxadrista;
        }else{
            $enxadrista->save();
        }
        if($show_text) echo "<hr/>";
    }

    private static function getName($html){
        $explode = explode("<td bgcolor=#efefef width=230 height=20>",$html);
        if(count($explode) > 1){
            $explode_2 = explode("</td>",$explode[1]);
            if(count($explode_2) > 1){
                $explode_3 = explode("&nbsp;",$explode_2[0]);
                if(count($explode_3) > 1){
                    return trim($explode_3[1]);
                }else{
                    return trim($explode_2[0]);
                }
            }
        }
        return NULL;
    }

}

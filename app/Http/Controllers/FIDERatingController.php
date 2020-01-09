<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Util\Util;
use App\Enxadrista;
use GuzzleHttp\Client;

class FIDERatingController extends Controller
{
    private $codigo_organizacao = 0;
    public function updateRatings(){
        $enxadristas = Enxadrista::where([
            ["fide_id", "!=", NULL],
            ["fide_last_update","<",date("Y-m-d")." 00:00:00"]
        ])
        ->orWhere([
            ["fide_id", "!=", NULL],
            ["fide_last_update","=",NULL]
        ])
        ->limit(3)
        ->get();
        echo count($enxadristas);
        foreach($enxadristas as $enxadrista){
            echo "Enxadrista #".$enxadrista->id." - ".$enxadrista->name;

            $client = new Client;
            $response = $client->get("http://ratings.fide.com/card.phtml?event=".$enxadrista->fide_id);
            $html = (string) $response->getBody();
            // echo $html;

            $explode_not_found = explode("Player not found",$html);
            if(count($explode_not_found) == 1){
                // continuar o desenvolvimento a partir daqui
                $explode_table_1 = explode("<table width=100% cellpadding=0 cellspacing=0 align=ceter broder=0>",$html);
                if(count($explode_table_1) == 2){
                    $explode_table_2 = explode("</table>",$explode_table_1[1]);
                    if(count($explode_table_2) >= 2){
                        $explode_table_3 = explode("<tr>",$explode_table_2[0]);
                        if(count($explode_table_3) == 2){
                            $explode_table_4 = explode("</tr>",$explode_table_3[1]);
                            if(count($explode_table_4) == 2){
                                $explode_columns = explode("align=center>",$explode_table_4[0]);

                                $std = "<small>std.</small><br>";
                                $rpd = "<small>rapid</small><br>";
                                $btz = "<small>blitz</small><br>";
                                foreach($explode_columns as $column_brute){
                                    $column = explode("</td>",$column_brute);
                                    if(count($column) == 2){
                                        $exp_std = explode($std,$column[0]);
                                        $exp_rpd = explode($rpd,$column[0]);
                                        $exp_btz = explode($btz,$column[0]);
                                        $rating = Util::numeros($column[0]);
                                        
                                        echo "Rating: ".$rating;
                                        if(count($exp_std) == 2){
                                            if(is_int(intval($rating))){
                                                if(intval($rating) > 0){
                                                    $enxadrista->setRating($codigo_organizacao,0,intval($rating));
                                                }
                                            }else{
                                                echo "Erro Rating não é inteiro!";
                                            }
                                        }elseif(count($exp_rpd) == 2){
                                            if(is_int(intval($rating))){
                                                if(intval($rating) > 0){
                                                    $enxadrista->setRating($codigo_organizacao,1,intval($rating));
                                                }
                                            }else{
                                                echo "Erro Rating não é inteiro!";
                                            }
                                        }elseif(count($exp_btz) == 2){
                                            if(is_int(intval($rating))){
                                                if(intval($rating) > 0){
                                                    $enxadrista->setRating($codigo_organizacao,2,intval($rating));
                                                }
                                            }else{
                                                echo "Erro Rating não é inteiro!";
                                            }
                                        }else{
                                            echo "Erro Nenhum tipo de rating encontrado";
                                        }
                                    }else{
                                        echo "Erro column";
                                    }
                                }
                            }else{
                                echo "Erro explode_table_4";
                            }
                        }else{
                            echo "Erro explode_table_3";
                        }
                    }else{
                        echo "Erro explode_table_2";
                    }
                }else{
                    echo "Erro explode_table_1";
                }
            }
            $enxadrista->fide_last_update = date("Y-m-d H:i:s");
            $enxadrista->save();
            echo "<hr/>";
        }
    }


}

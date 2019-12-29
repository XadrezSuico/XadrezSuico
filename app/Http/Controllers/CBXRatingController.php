<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Enxadrista;
use GuzzleHttp\Client;

class CBXRatingController extends Controller
{
    public function updateRatings(){
        $enxadristas = Enxadrista::where([
            ["cbx_id", "!=", NULL],
            ["cbx_last_update","<",date("Y-m-d")." 00:00:00"]
        ])
        ->orWhere([
            ["cbx_id", "!=", NULL],
            ["cbx_last_update","=",NULL]
        ])
        ->limit(3)
        ->get();
        echo count($enxadristas);
        foreach($enxadristas as $enxadrista){
            echo "Enxadrista #".$enxadrista->id." - ".$enxadrista->name;
            // $html = file_get_contents("http://cbx.com.br/jogador/".$enxadrista->cbx_id);

            $client = new Client;
            $response = $client->get("http://cbx.com.br/jogador/".$enxadrista->cbx_id);
            $html = (string) $response->getBody();

            $explode_table_1 = explode("Evolução Rating",$html);
            if(count($explode_table_1) == 2){
                $explode_table_2 = explode("</caption>",$explode_table_1[1]);
                if(count($explode_table_2) == 2){
                    $explode_table_3 = explode("</table>",$explode_table_2[1]);
                    if(count($explode_table_3) >= 2){
                        $explode_lines = explode("<tr>",$explode_table_3[0]);

                        $i = 0;
                        foreach($explode_lines as $line_brute){
                            if($i == 2){
                                echo 1;
                                $line = explode("</tr>",$line_brute);
                                $columns = explode('<td align="center">',$line[0]);
                                echo $i."<br>";
                                print_r($columns);
                                if(count($columns) == 5){
                                    $j = 0;

                                    $std = 2;
                                    $rpd = 3;
                                    $btz = 4;

                                    foreach($columns as $column_brute){
                                        $column = explode("</td>",$column_brute);
                                        if(count($column) == 2){
                                            $rating = $column[0];
                                            switch($j){
                                                case $std:
                                                    echo "Rating STD: ".$rating;
                                                    $enxadrista->setRating(1,0,$rating);
                                                    break;

                                                case $rpd:
                                                    echo "Rating RPD: ".$rating;
                                                    $enxadrista->setRating(1,1,$rating);
                                                    break;

                                                case $btz:
                                                    echo "Rating BTZ: ".$rating;
                                                    $enxadrista->setRating(1,2,$rating);
                                                    break;
                                            }
                                        }else{
                                            echo "Erro column";
                                        }
                                        $j++;
                                    }
                                }else{
                                    echo "Erro columns ".count($columns);
                                }
                            }
                            $i++;
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
            $enxadrista->cbx_last_update = date("Y-m-d H:i:s");
            $enxadrista->save();
            echo "<hr/>";
        }
    }
}

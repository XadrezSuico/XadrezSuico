<?php

namespace App\Http\Controllers;

use App\Enxadrista;
use GuzzleHttp\Client;

class CBXRatingController extends Controller
{

    public function updateRatings()
    {
        $enxadristas = Enxadrista::where([
            ["cbx_id", "!=", null],
            ["cbx_last_update", "<", date("Y-m") . "-01 00:00:00"],
        ])
            ->orWhere([
                ["cbx_id", "!=", null],
                ["cbx_last_update", "=", null],
            ])
            ->limit(5)
            ->get();
        foreach ($enxadristas as $enxadrista) {
            $this->getRating($enxadrista);
        }
    }

    public static function getRating($enxadrista, $show_text = true, $return_enxadrista = false, $save_rating = true){
        $codigo_organizacao = 1;



        if($show_text) echo "Enxadrista #" . $enxadrista->id . " - " . $enxadrista->name;
        // $html = file_get_contents("http://cbx.com.br/jogador/".$enxadrista->cbx_id);

        $client = new Client([
            'http_errors' => false,
        ]);
        $response = $client->get("http://cbx.com.br/jogador/" . $enxadrista->cbx_id);
        if($response->getStatusCode() != 200){
            $html = "";
        }else{
            $html = (string) $response->getBody();
        }

        $not_found = false;

        $nome = CBXRatingController::getName($html);
        if($nome){
            $enxadrista->encontrado_cbx = true;
            $enxadrista->cbx_name = $nome;

            $explode_table_1 = explode("Evolução Rating", $html);
            if (count($explode_table_1) == 2) {
                $explode_table_2 = explode("</caption>", $explode_table_1[1]);
                if (count($explode_table_2) == 2) {
                    $explode_table_3 = explode("</table>", $explode_table_2[1]);
                    if (count($explode_table_3) >= 2) {
                        $explode_lines = explode("<tr>", $explode_table_3[0]);

                        $i = 0;
                        foreach ($explode_lines as $line_brute) {
                            if ($i == 2) {
                                if($show_text) echo 1;
                                $line = explode("</tr>", $line_brute);
                                $columns = explode('<td align="center">', $line[0]);
                                if($show_text) echo $i . "<br>";
                                if($show_text) print_r($columns);
                                if (count($columns) == 5) {
                                    $j = 0;

                                    $std = 2;
                                    $rpd = 3;
                                    $btz = 4;

                                    foreach ($columns as $column_brute) {
                                        $column = explode("</td>", $column_brute);
                                        if (count($column) == 2) {
                                            $rating = $column[0];
                                            switch ($j) {
                                                case $std:
                                                    if($show_text) echo "Rating STD: " . $rating;
                                                    if($save_rating) $enxadrista->setRating($codigo_organizacao, 0, $rating);
                                                    break;

                                                case $rpd:
                                                    if($show_text) echo "Rating RPD: " . $rating;
                                                    if($save_rating) $enxadrista->setRating($codigo_organizacao, 1, $rating);
                                                    break;

                                                case $btz:
                                                    if($show_text) echo "Rating BTZ: " . $rating;
                                                    if($save_rating) $enxadrista->setRating($codigo_organizacao, 2, $rating);
                                                    break;
                                            }
                                        } else {
                                            if($show_text) echo "Erro column";

                                            switch($j){
                                                case $std:
                                                    if($show_text) echo "Rating STD: NF";
                                                    if($save_rating) $enxadrista->deleteRating($codigo_organizacao, 0);
                                                    break;

                                                case $rpd:
                                                    if($show_text) echo "Rating RPD: NF";
                                                    if($save_rating) $enxadrista->deleteRating($codigo_organizacao, 1);
                                                    break;

                                                case $btz:
                                                    if($show_text) echo "Rating BTZ: NF";
                                                    if($save_rating) $enxadrista->deleteRating($codigo_organizacao, 2);
                                                    break;
                                            }
                                        }
                                        $j++;
                                    }
                                } else {
                                    if($show_text) echo "Erro columns " . count($columns);
                                    $not_found = true;
                                }
                            }
                            $i++;
                        }
                    } else {
                        if($show_text) echo "Erro explode_table_3";
                        $not_found = true;
                    }
                } else {
                    if($show_text) echo "Erro explode_table_2";
                    $not_found = true;
                }
            } else {
                if($show_text) echo "Erro explode_table_1";
                $not_found = true;
            }
        } else {
            if($show_text) echo "Erro name";
            $enxadrista->encontrado_cbx = false;
            $not_found = true;
        }

        if($not_found && $save_rating){
            $enxadrista->deleteRating($codigo_organizacao,0);
            $enxadrista->deleteRating($codigo_organizacao,1);
            $enxadrista->deleteRating($codigo_organizacao,2);
        }

        if($save_rating) $enxadrista->cbx_last_update = date("Y-m-d H:i:s");
        if($return_enxadrista){
            return $enxadrista;
        }else{
            $enxadrista->save();
        }
        if($show_text) echo "<hr/>";
    }

    private static function getName($html){
        $explode = explode('<div id="dados-jogador-row1">',$html);
        if(count($explode) > 1){
            $explode_2 = explode('<h2>',$explode[1]);
            if(count($explode_2) > 1){
                $explode_3 = explode('</h2>',$explode_2[1]);
                if(count($explode_3) > 1){
                    if(
                        trim($explode_3[0]) != NULL &&
                        trim($explode_3[0]) != "" &&
                        strlen(trim($explode_3[0])) > 0
                    ){
                        return trim($explode_3[0]);
                    }
                }
            }
        }
        return false;
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Enxadrista;
use App\Clube;
use App\Cidade;
use DateTime;

class ImportController extends Controller
{
    public function importCSVEnxadristas(){
        $meuArray = Array();
        $file = fopen('cadastro.csv', 'r');
        $positions = array();
        $i = 0;
        while (($line = fgetcsv($file)) !== false){
            print_r($line);
            if($i++ == 0){
                $j = 0;
                foreach($line as $title){
                    switch($title){
                        case "Código Enxadrista":
                            $positions["rating_id"] = $j;
                            break;
                        case "Nome":
                            $positions["name"] = $j;
                            break;
                        case "Sobrenome":
                            $positions["lastname"] = $j;
                            break;
                        case "Código Clube":
                            $positions["clube_rating_id"] = $j;
                            break;
                        case "Código Cidade":
                            $positions["cidade_rating_id"] = $j;
                            break;
                        case "Data de Nascimento":
                            $positions["born"] = $j;
                            break;
                    }
                    $j++;
                }
            }else{
                if(isset($line[($positions["rating_id"])])){
                    if($line[($positions["rating_id"])]){
                        if($line[($positions["rating_id"])] != NULL){
                            if($line[($positions["rating_id"])] != ''){
                                $enxadrista = new Enxadrista;
                                $enxadrista->rating_id = $line[($positions["rating_id"])];
                                $enxadrista->name = mb_strtoupper(trim($line[($positions["name"])])." ".trim($line[($positions["lastname"])]));
                                $enxadrista->setBornFromSM($line[($positions["born"])]);

                                if(isset($line[($positions["clube_rating_id"])])){
                                    if($line[($positions["clube_rating_id"])]){
                                        if($line[($positions["clube_rating_id"])] != NULL){
                                            if($line[($positions["clube_rating_id"])] != ''){
                                                $clube = Clube::where([["rating_id","=",$line[($positions["clube_rating_id"])]]])->first();
                                                $enxadrista->clube_id = $clube->id;
                                            }
                                        }
                                    }
                                }

                                $cidade = Cidade::where([["rating_id","=",$line[($positions["cidade_rating_id"])]]])->first();
                                $enxadrista->cidade_id = $cidade->id;
                                $enxadrista->save();
                                echo $enxadrista->id." ".$enxadrista->name."<br/>";
                            }
                        }
                    }
                }
            }
        }
        fclose($file);
    }
}

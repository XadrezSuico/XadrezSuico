<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Enxadrista;
use GuzzleHttp\Client;

class LBXRatingController extends Controller
{
    public function updateRatings(){
        $enxadristas = Enxadrista::where([
            ["lbx_id", "!=", NULL],
            ["lbx_last_update","<",date("Y-m-d")." 00:00:00"]
        ])
        ->orWhere([
            ["lbx_id", "!=", NULL],
            ["lbx_last_update","=",NULL]
        ])
        ->limit(10)
        ->get();
        echo count($enxadristas);
        foreach($enxadristas as $enxadrista){
            $url = env("LBXRATINGSERVER_URL","http://lbx.rating.fexpar.com.br")."/rating/search/id/".$enxadrista->lbx_id;
            echo $url."<br/>";
            $json_file = file_get_contents($url);
            if($json_file){
                $json = json_decode($json_file);
                if($json){
                    // print_r($json);exit();
                    if(count($json) > 0){
                        if(isset($json->ratings->STD)){
                            if($json->ratings->STD == 0){
                                $enxadrista->setRating(2,0,1800);
                            }else{
                                $enxadrista->setRating(2,0,$json->ratings->STD);
                            }
                        }
                        if(isset($json->ratings->RPD)){
                            if($json->ratings->RPD == 0){
                                $enxadrista->setRating(2,1,1800);
                            }else{
                                $enxadrista->setRating(2,1,$json->ratings->RPD);
                            }
                        }
                        if(isset($json->ratings->BTZ)){
                            if($json->ratings->BTZ == 0){
                                $enxadrista->setRating(2,2,1800);
                            }else{
                                $enxadrista->setRating(2,2,$json->ratings->BTZ);
                            }
                        }
                    }else{
                        echo "Erro count($json)";
                    }
                }else{
                    echo "Erro json";
                }
            }else{
                echo "Erro json_file";
            }
            $enxadrista->lbx_last_update = date("Y-m-d H:i:s");
            $enxadrista->save();
            echo "<hr/>";
        }
    }
}

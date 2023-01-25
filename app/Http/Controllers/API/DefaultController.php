<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DefaultController extends Controller
{
    public function default(){
        $defaults = array();

        if(env("PAIS_DEFAULT",null)){
            if(env("PAIS_DEFAULT",null) != ""){
                $defaults["country_default"] = env("PAIS_DEFAULT",null);
            }
        }

        if(env("ESTADO_DEFAULT",null)){
            if(env("ESTADO_DEFAULT",null) != ""){
                $defaults["state_default"] = env("ESTADO_DEFAULT",null);
            }
        }

        if(env("CIDADE_DEFAULT",null)){
            if(env("CIDADE_DEFAULT",null) != ""){
                $defaults["city_default"] = env("CIDADE_DEFAULT",null);
            }
        }

        return response()->json(["ok"=>1,"error"=>0,"defaults"=>$defaults]);
    }
}

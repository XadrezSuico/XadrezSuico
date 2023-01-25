<?php

namespace App\Http\Controllers\API\Location;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Estado;

class StateController extends Controller
{
    public function list($country_id){
        $states = Estado::where([["pais_id","=",$country_id]])->orderBy("nome","ASC")->get();
        $return = array();

        foreach($states as $state){
            $return[] = $state->toAPIObject();
        }

        return response()->json(["ok"=>1,"error"=>0,"states"=>$return]);
    }

    public function get($state_id){
        if(Estado::where([["id","=",$state_id]])->count() > 0){
            $state = Estado::find($state_id);

            return response()->json(["ok"=>1,"error"=>0,"state"=>$state->toAPIObject(true)]);
        }
        return response()->json(["ok"=>0,"error"=>1,"message"=>"Estado não encontrado."]);
    }
}

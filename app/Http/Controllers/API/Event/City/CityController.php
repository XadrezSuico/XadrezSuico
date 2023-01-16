<?php

namespace App\Http\Controllers\API\Event\City;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Cidade;

class CityController extends Controller
{
    public function list($state_id){
        $cities = Cidade::where([["estados_id","=",$state_id]])->orderBy("name","ASC")->get();
        $return = array();

        foreach($cities as $city){
            $return[] = $city->toAPIObject();
        }

        return response()->json(["ok"=>1,"error"=>0,"cities"=>$return]);
    }

    public function get($state_id){
        if(Estado::where([["id","=",$state_id]])->count() > 0){
            $city = Cidade::find($state_id);

            return response()->json(["ok"=>1,"error"=>0,"city"=>$city->toAPIObject(true)]);
        }
        return response()->json(["ok"=>0,"error"=>1,"message"=>"Cidade não encontrada."]);
    }
}

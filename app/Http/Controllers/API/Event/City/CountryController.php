<?php

namespace App\Http\Controllers\API\Event\City;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Pais;

class CountryController extends Controller
{
    // public function search(Request $request){

    // }

    public function list(){
        $countries = Pais::orderBy("nome","ASC")->get();
        $return = array();

        foreach($countries as $country){
            $return[] = $country->toAPIObject();
        }

        return response()->json(["ok"=>1,"error"=>0,"countries"=>$return]);
    }

    public function get($country_id){
        if(Pais::where([["id","=",$country_id]])->count() > 0){
            $country = Pais::find($country_id);

            return response()->json(["ok"=>1,"error"=>0,"country"=>$country->toAPIObject()]);
        }
        return response()->json(["ok"=>0,"error"=>1,"message"=>"País não encontrado."]);
    }
}

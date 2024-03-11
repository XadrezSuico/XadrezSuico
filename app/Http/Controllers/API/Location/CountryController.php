<?php

namespace App\Http\Controllers\API\Location;

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

    public function listSelect2(Request $request)
    {
        $countries = Pais::where(function($q1) use ($request){
            $q1->where([["id","=",$request->q]]);
            $q1->orWhere([["nome", "like", "%".$request->q."%"]]);
            $q1->orWhere([["codigo_iso", "like", "%" . $request->q . "%"]]);
        })
        ->orderBy("nome", "ASC")
        ->limit(30)
        ->get();

        $results = array();

        foreach ($countries as $country) {
            $results[] = array("id" => $country->id, "text" => $country->id . " - " . $country->nome." (".$country->codigo_iso.")");
        }
        return response()->json(["results" => $results, "pagination" => true]);
    }
}

<?php

namespace App\Http\Controllers\API\Event;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Sexo;

class SexController extends Controller
{
    public function list(){
        $sexos = Sexo::all();

        $return = array();
        foreach($sexos as $sexo){
            $return[] = $sexo->toAPIObject();
        }

        return response()->json(["ok"=>1,"error"=>0,"sexes"=>$return]);
    }
}

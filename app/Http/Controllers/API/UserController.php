<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;

class UserController extends Controller
{
    public function __construct(){
        $this->middleware('auth:api');
    }
    public function get(){
        if(!Auth::guard("api")->check()){
            return response()->json(["ok"=>0,"error"=>1,"message"=>"Não Autenticado"]);
        }

        $user = Auth::guard("api")->user();

        return response()->json(["ok"=>1,"error"=>0,"user"=>$user->toAPIObject()]);
    }
}

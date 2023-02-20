<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;

class UserProfileController extends Controller
{
    public function __construct(){
        $this->middleware('auth:api');
    }
    public function list(){
        if(!Auth::guard("api")->check()){
            return response()->json(["ok"=>0,"error"=>1,"message"=>"Não Autenticado"]);
        }

        $user = Auth::guard("api")->user();

        return response()->json(["ok"=>1,"error"=>0,"user_profiles"=>$user->getProfiles(true)]);
    }
    public function check(Request $request){
        if(!Auth::guard("api")->check()){
            return response()->json(["ok"=>0,"error"=>1,"message"=>"Não Autenticado"]);
        }

        $user = Auth::guard("api")->user();

        $not_able_to_admin = false;

        if($request->has("not_able_to_admin")){
            if($request->not_able_to_admin == 1){
                $not_able_to_admin = true;
            }
        }

        return response([
            "ok"=>1,
            "error"=>0,
            "result"=>$user->checkProfile(explode(",",$request->profiles_id),$request->event_id,$request->event_group_id, $not_able_to_admin)
        ]);
    }
}

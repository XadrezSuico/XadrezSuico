<?php

namespace App\Http\Controllers\FEXPAR;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Auth;

use App\Enxadrista;
use App\Vinculo;

use App\Helper\IPHelper;

class GerenciadorVinculosFederativosController extends Controller
{
    public function __construct(){
        $this->middleware("auth");
    }

    public function index(Request $request){
        $user = Auth::user();
        if (
            $user->hasPermissionGlobalbyPerfil([10])
        ) {
            if(env("ENTITY_DOMAIN",NULL) == "fexpar.com.br"){
                $enxadristas = Enxadrista::all();

                $type = null;
                if($request->has("type")){
                    switch($request->input("type")){
                        // apenas vinculados
                        case 1:
                            $type = 1;
                            break;
                        // apenas automatica
                        case 2:
                            $type = 2;
                            break;
                        // apenas manual
                        case 3:
                            $type = 3;
                            break;
                    }
                }

                return view("_fexpar.vinculos.index",compact("enxadristas","type"));
            }
        }
        return abort(403);
    }

    public function edit($enxadrista_id){
        $user = Auth::user();
        if (
            $user->hasPermissionGlobalbyPerfil([10])
        ) {
            if(env("ENTITY_DOMAIN",NULL) == "fexpar.com.br"){
                if(Enxadrista::where([
                    ["id","=",$enxadrista_id],
                ])
                ->count() > 0){
                    $enxadrista = Enxadrista::where([
                        ["id","=",$enxadrista_id],
                    ])
                    ->first();

                    $vinculo = null;
                    if(Vinculo::where([
                        ["enxadrista_id","=",$enxadrista->id],
                        ["ano","=",date("Y")],
                    ])
                    ->count() > 0){
                        $vinculo = Vinculo::where([
                            ["enxadrista_id","=",$enxadrista->id],
                            ["ano","=",date("Y")],
                        ])
                        ->first();
                    }

                    return view("_fexpar.vinculos.edit",compact("enxadrista","vinculo"));
                }
            }
        }
        return abort(403);
    }
    public function edit_post($enxadrista_id, Request $request){
        $user = Auth::user();
        if (
            $user->hasPermissionGlobalbyPerfil([10])
        ) {
            if(env("ENTITY_DOMAIN",NULL) == "fexpar.com.br"){
                if(Enxadrista::where([
                    ["id","=",$enxadrista_id],
                ])
                ->count() > 0){
                    $enxadrista = Enxadrista::where([
                        ["id","=",$enxadrista_id],
                    ])
                    ->first();

                    if(
                        !$request->has("cidade_id") ||
                        !$request->has("clube_id")
                    ){
                        return redirect("/fexpar/vinculos/".$enxadrista->id."/edit");
                    }

                    if(
                        $request->input("cidade_id") == "" ||
                        $request->input("clube_id") == ""
                    ){
                        return redirect("/fexpar/vinculos/".$enxadrista->id."/edit");
                    }

                    $vinculo = null;
                    if(Vinculo::where([
                        ["enxadrista_id","=",$enxadrista->id],
                        ["ano","=",date("Y")],
                    ])
                    ->count() > 0){
                        $vinculo = Vinculo::where([
                            ["enxadrista_id","=",$enxadrista->id],
                            ["ano","=",date("Y")],
                        ])
                        ->first();
                    }else{
                        $vinculo = new Vinculo;
                        $vinculo->enxadrista_id = $enxadrista->id;
                        $vinculo->is_confirmed_manually = true;
                        $vinculo->ano = date("Y");
                        $vinculo->vinculated_at = date("Y-m-d H:i:s");
                    }

                    if($vinculo->is_confirmed_system){
                        $ip = IPHelper::getIp();

                        activity('change_type_vinculo')
                            ->causedBy(Auth::user())
                            ->performedOn($vinculo)
                            ->withProperties(['ip' => $ip])
                            ->log('O usuário em questão efetuou a mudança do tipo de vínculo.');

                        $vinculo->is_confirmed_system = false;
                        $vinculo->system_inscricoes_in_this_club_confirmed = null;

                        $vinculo->is_confirmed_manually = true;
                    }

                    $vinculo->cidade_id = $request->input("cidade_id");
                    $vinculo->clube_id = $request->input("clube_id");

                    $vinculo->events_played = $request->input("events_played");
                    $vinculo->obs = $request->input("obs");
                    $vinculo->save();
                }
            }
            return redirect("/fexpar/vinculos/".$enxadrista->id."/edit");
        }
        return abort(403);
    }

    /*
     *
     *
     * API
     *
     *
     */
    public function searchEnxadristasList($type = 0, Request $request)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobalbyPerfil([10])) {
            return redirect("/");
        }

        $permitido_edicao = true;

        $requisicao = $request->all();

        $enxadristaBorn = new Enxadrista();

        $recordsTotal = Enxadrista::count();
        $enxadristas = Enxadrista::where(function($q1) use ($requisicao, $enxadristaBorn){
            $q1->where([["name", "like", "%" . $requisicao["search"]["value"] . "%"]]);
            $q1->orWhere([["id", "=", $requisicao["search"]["value"]]]);
            $q1->orWhere(function ($q2) use ($requisicao) {
                $q2->whereHas("sexo", function ($q3) use ($requisicao) {
                    $q3->where([["name", "like", "%" . $requisicao["search"]["value"] . "%"]]);
                    $q3->orWhere([["abbr", "like", "%" . $requisicao["search"]["value"] . "%"]]);
                });
            });

            $enxadristaBorn->setBorn($requisicao["search"]["value"]);
            if ($enxadristaBorn->getBorn()) {
                $q1->orWhere([["born", "=", $enxadristaBorn->getBorn()]]);
            }

            $q1->orWhere([["fide_id", "=", $requisicao["search"]["value"]]]);
            $q1->orWhere([["cbx_id", "=", $requisicao["search"]["value"]]]);
            $q1->orWhere([["lbx_id", "=", $requisicao["search"]["value"]]]);
            $q1->orWhere(function ($q2) use ($requisicao) {
                $q2->whereHas("cidade", function ($q3) use ($requisicao) {
                    $q3->where([["name", "like", "%" . $requisicao["search"]["value"] . "%"]]);
                });
            });
            $q1->orWhere(function ($q2) use ($requisicao) {
                $q2->whereHas("clube", function ($q3) use ($requisicao) {
                    $q3->where([["name", "like", "%" . $requisicao["search"]["value"] . "%"]]);
                });
            });
        });

        if($request->has("type")){
            switch($request->input("type")){
                // apenas vinculados
                case 1:
                    $enxadristas->whereHas("vinculos",function($q1) use ($request){
                        $q1->where([["ano","=",date("Y")]]);
                    });
                    break;
                // apenas automatica
                case 2:
                    $enxadristas->whereHas("vinculos",function($q1) use ($request){
                        $q1->where([["is_confirmed_system","=",true],["ano","=",date("Y")]]);
                    });
                    break;
                // apenas manual
                case 3:
                    $enxadristas->whereHas("vinculos",function($q1) use ($request){
                        $q1->where([["is_confirmed_manually","=",true],["ano","=",date("Y")]]);
                    });
                    break;
            }
        }

        switch ($requisicao["order"][0]["column"]) {
            case 1:
                $enxadristas->orderBy("name", mb_strtoupper($requisicao["order"][0]["dir"]));
                break;
            case 2:
                $enxadristas->orderBy("born", mb_strtoupper($requisicao["order"][0]["dir"]));
                break;
            case 3:
                $enxadristas->orderBy("cbx_id", mb_strtoupper($requisicao["order"][0]["dir"]));
                break;
            case 4:
                $enxadristas->orderBy("fide_id", mb_strtoupper($requisicao["order"][0]["dir"]));
                break;
            case 5:
                $enxadristas->orderBy("cidade_id", mb_strtoupper($requisicao["order"][0]["dir"]));
                break;
            case 6:
                $enxadristas->orderBy("clube_id", mb_strtoupper($requisicao["order"][0]["dir"]));
                break;
            default:
                $enxadristas->orderBy("id", mb_strtoupper($requisicao["order"][0]["dir"]));
        }
        $total = count($enxadristas->get());
        $enxadristas->limit($requisicao["length"]);
        $enxadristas->skip($requisicao["start"]);

        $retorno = array("draw" => $requisicao["draw"], "recordsTotal" => $recordsTotal, "recordsFiltered" => $total, "data" => array(), "requisicao" => $requisicao);
        foreach ($enxadristas->get() as $enxadrista) {
            $p = array();
            $p[0] = $enxadrista->id;
            $p[1] = $enxadrista->name;

            $p[2] = $enxadrista->getBorn();

            $p[3] = ($enxadrista->fide_id) ? $enxadrista->fide_id : "";

            $p[4] = ($enxadrista->cbx_id) ? $enxadrista->cbx_id : "";

            $p[5] = "#" . $enxadrista->cidade->id . " - " . $enxadrista->cidade->name;

            if ($enxadrista->clube) {
                $p[6] = $enxadrista->clube->id." - ".$enxadrista->clube->name;
            } else {
                $p[6] = "Não possui clube";
            }

            $p[8] = "";
            if ($permitido_edicao) {
                $p[8] .= '<a href="' . url("/enxadrista/edit/" . $enxadrista->id) . '" title="Editar Enxadrista: ' . $enxadrista->id . ' ' . $enxadrista->name . '" class="btn btn-success btn-sm" data-toggle="tooltip" data-original-title="Editar Enxadrista" target="_blank"><i class="fa fa-edit"></i></a> ';
                $p[8] .= '<a href="' . url("/fexpar/vinculos/" . $enxadrista->id ."/edit") . '" title="Gerenciar Vínculo: ' . $enxadrista->id . ' ' . $enxadrista->name . '" class="btn btn-warning btn-sm" data-toggle="tooltip" data-original-title="Gerenciar Vínculo"><i class="fa fa-anchor"></i></a>';
            }

            $p[7] = "";
            if($enxadrista
                ->vinculos()
                ->where([["ano","=",date("Y")]])
                ->where(function($q1){
                    $q1->where([["is_confirmed_system","=",true]])
                    ->orWhere([["is_confirmed_manually","=",true]]);
                })
                ->count() > 0){

                $vinculo = $enxadrista
                        ->vinculos()
                        ->where([["ano","=",date("Y")]])
                        ->where(function($q1){
                            $q1->where([["is_confirmed_system","=",true]])
                            ->orWhere([["is_confirmed_manually","=",true]]);
                        })->first();

                if($vinculo->is_confirmed_system){
                    $p[7] = "<strong>Sim</strong> - Vínculo <strong>AUTOMÁTICO</strong>";
                }else{
                    $p[7] = "<strong>Sim</strong> - Vínculo <strong>MANUAL</strong>";
                }

                $p[5] .= "<hr/>Vínculo por: <strong>#".$vinculo->cidade->id." - ".$vinculo->cidade->name."</strong>";
                $p[6] .= "<hr/>Vínculo por: <strong>#".$vinculo->clube->id." - ".$vinculo->clube->name."</strong>";

                $p[8] .= '<hr/><a href="' . url("/especiais/fexpar/vinculos/" . $vinculo->uuid) . '" title="Visualização Pública do Vínculo: ' . $enxadrista->id . ' ' . $enxadrista->name . '" class="btn btn-warning btn-sm" data-toggle="tooltip" data-original-title="Visualização Pública Vínculo" target="_blank"><i class="fa fa-eye"></i></a>';
            }elseif($enxadrista->vinculos()->where([["ano","=",date("Y")],["is_confirmed_system","=",false],["is_confirmed_manually","=",false]])->count() > 0){
                $pre_vinculo = $enxadrista->vinculos()->where([["ano","=",date("Y")],["is_confirmed_system","=",false],["is_confirmed_manually","=",false]])->first();
                $p[7] = "<strong>Pré-vinculado</strong>";
                $p[5] .= "<hr/>Pré-Vínculo por: <strong>#".$pre_vinculo->cidade->id." - ".$pre_vinculo->cidade->name."</strong>";
                $p[6] .= "<hr/>Pré-Vínculo por: <strong>#".$pre_vinculo->clube->id." - ".$pre_vinculo->clube->name."</strong>";
            }else{
                $p[7] = "Não";
            }

            $retorno["data"][] = $p;
        }
        return response()->json($retorno);
    }
}

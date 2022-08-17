<?php

namespace App\Http\Controllers\FEXPAR;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Auth;

use App\Enxadrista;

class GerenciadorVinculosFederativosController extends Controller
{
    public function index(){
        $user = Auth::user();
        if (
            $user->hasPermissionGlobalbyPerfil([10])
        ) {
            if(env("ENTITY_DOMAIN",NULL) == "fexpar.com.br"){
                $enxadristas = Enxadrista::all();

                return view("_fexpar.vinculos.index",compact("enxadristas"));
            }
        }
        return abort(404);
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
        $enxadristas = Enxadrista::where([["name", "like", "%" . $requisicao["search"]["value"] . "%"]]);
        $enxadristas->orWhere([["id", "=", $requisicao["search"]["value"]]]);
        $enxadristas->orWhere(function ($q1) use ($requisicao) {
            $q1->whereHas("sexo", function ($q2) use ($requisicao) {
                $q2->where([["name", "like", "%" . $requisicao["search"]["value"] . "%"]]);
                $q2->orWhere([["abbr", "like", "%" . $requisicao["search"]["value"] . "%"]]);
            });
        });

        $enxadristaBorn->setBorn($requisicao["search"]["value"]);
        if ($enxadristaBorn->getBorn()) {
            $enxadristas->orWhere([["born", "=", $enxadristaBorn->getBorn()]]);
        }

        $enxadristas->orWhere([["fide_id", "=", $requisicao["search"]["value"]]]);
        $enxadristas->orWhere([["cbx_id", "=", $requisicao["search"]["value"]]]);
        $enxadristas->orWhere([["lbx_id", "=", $requisicao["search"]["value"]]]);
        $enxadristas->orWhere(function ($q1) use ($requisicao) {
            $q1->whereHas("cidade", function ($q2) use ($requisicao) {
                $q2->where([["name", "like", "%" . $requisicao["search"]["value"] . "%"]]);
            });
        });
        $enxadristas->orWhere(function ($q1) use ($requisicao) {
            $q1->whereHas("clube", function ($q2) use ($requisicao) {
                $q2->where([["name", "like", "%" . $requisicao["search"]["value"] . "%"]]);
            });
        });

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

            $p[7] = "";
            if($enxadrista->vinculos()->where([["ano","=",2022]])->count() > 0){
                if($enxadrista->vinculos()->where([["ano","=",2022],["is_confirmed_system","=",true]])->count() > 0){
                    $p[7] = "<strong>Sim</strong> - Vínculo <strong>AUTOMÁTICO</strong>";
                }else{
                    $p[7] = "<strong>Sim</strong> - Vínculo Manual";
                }
            }else{
                $p[7] = "Não";
            }

            $p[8] = "";
            if ($permitido_edicao) {
                $p[8] .= '<a href="' . url("/enxadrista/edit/" . $enxadrista->id) . '" title="Editar Enxadrista: ' . $enxadrista->id . ' ' . $enxadrista->name . '" class="btn btn-success btn-sm" data-toggle="tooltip" data-original-title="Editar Enxadrista"><i class="fa fa-edit"></i></a> ';
                $p[8] .= '<a href="' . url("/fexpar/vinculos/" . $enxadrista->id ."/edit") . '" title="Gerenciar Vínculo: ' . $enxadrista->id . ' ' . $enxadrista->name . '" class="btn btn-warning btn-sm" data-toggle="tooltip" data-original-title="Gerenciar Vínculo"><i class="fa fa-anchor"></i></a>';
            }

            $retorno["data"][] = $p;
        }
        return response()->json($retorno);
    }
}

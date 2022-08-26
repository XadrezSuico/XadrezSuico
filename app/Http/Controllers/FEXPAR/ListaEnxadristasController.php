<?php

namespace App\Http\Controllers\FEXPAR;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Enxadrista;

class ListaEnxadristasController extends Controller
{
    public function todos(){
        if(env("ENTITY_DOMAIN",NULL) == "fexpar.com.br"){
            return view("paginas_especiais.fexpar.todos_enxadristas");
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

        switch ($requisicao["order"][0]["column"]) {
            case 1:
                $enxadristas->orderBy("name", mb_strtoupper($requisicao["order"][1]["dir"]));
                break;
            case 2:
                $enxadristas->orderBy("born", mb_strtoupper($requisicao["order"][2]["dir"]));
                break;
            case 3:
                $enxadristas->orderBy("cbx_id", mb_strtoupper($requisicao["order"][3]["dir"]));
                break;
            case 4:
                $enxadristas->orderBy("fide_id", mb_strtoupper($requisicao["order"][4]["dir"]));
                break;
            case 5:
                $enxadristas->orderBy("cidade_id", mb_strtoupper($requisicao["order"][5]["dir"]));
                break;
            case 6:
                $enxadristas->orderBy("clube_id", mb_strtoupper($requisicao["order"][6]["dir"]));
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

            $p[2] = $enxadrista->getNascimentoPublico();

            $p[3] = ($enxadrista->cbx_id) ? $enxadrista->cbx_id : "";

            $p[4] = ($enxadrista->fide_id) ? $enxadrista->fide_id : "";

            $p[5] = "#" . $enxadrista->cidade->id . " - " . $enxadrista->cidade->name;

            if ($enxadrista->clube) {
                $p[6] = $enxadrista->getClubePublico();
            } else {
                $p[6] = "Não possui clube";
            }

            $retorno["data"][] = $p;
        }
        return response()->json($retorno);
    }

}

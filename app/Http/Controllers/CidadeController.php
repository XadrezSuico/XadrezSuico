<?php

namespace App\Http\Controllers;

use App\Cidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CidadeController extends Controller
{
    public function __construct()
    {
        return $this->middleware("auth");
    }
    public function index()
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobalbyPerfil([1, 2, 8])) {
            return redirect("/");
        }

        // $cidades = Cidade::all();
        // return view('cidade.index', compact("cidades"));
        return view('cidade.index');
    }
    function new () {
        $user = Auth::user();
        if (!$user->hasPermissionGlobalbyPerfil([1, 2, 8])) {
            return redirect("/");
        }

        return view('cidade.new');
    }
    public function new_post(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobalbyPerfil([1, 2, 8])) {
            return redirect("/");
        }

        $cidade = new Cidade;
        $cidade->name = $request->input("name");
        $cidade->save();
        return redirect("/cidade/edit/" . $cidade->id);
    }
    public function edit($id)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobalbyPerfil([1, 2, 8])) {
            return redirect("/");
        }

        $cidade = Cidade::find($id);
        return view('cidade.edit', compact("cidade"));
    }
    public function edit_post($id, Request $request)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobalbyPerfil([1, 2, 8])) {
            return redirect("/");
        }

        $cidade = Cidade::find($id);
        $cidade->name = $request->input("name");
        $cidade->save();
        return redirect("/cidade/edit/" . $cidade->id);
    }
    public function delete($id)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobalbyPerfil([1, 2, 8])) {
            return redirect("/");
        }

        $cidade = Cidade::find($id);

        if ($cidade->isDeletavel()) {
            $cidade->delete();
        }
        return redirect("/cidade");
    }


    public function buscaCidade($estados_id)
    {
        $cidades = Cidade::where([
            ["estados_id", "=", $estados_id],
        ])->get();
        $results = array();
        foreach ($cidades as $cidade) {
            $results[] = array("id" => $cidade->id, "text" => $cidade->name);
        }
        return response()->json(["results" => $results, "pagination" => true]);
    }

    public function searchListByEstado($estados_id, Request $request)
    {
        $cidades = Cidade::where([
            ["estados_id", "=", $estados_id],
        ])
        ->where(function($q1) use ($request){
            $q1->where([
                ["id", "like", "%" . $request->input("q") . "%"],
            ])
            ->orWhere([
                ["name", "like", "%" . $request->input("q") . "%"],
            ]);
        })
        ->get();
        $results = array();
        foreach ($cidades as $cidade) {
            $results[] = array("id" => $cidade->id, "text" => $cidade->name);
        }
        return response()->json(["results" => $results, "pagination" => true]);
    }

    /*
     *
     *
     * API
     *
     *
     */
    public function searchList($type = 0, Request $request)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobalbyPerfil([1, 2, 8])) {
            return redirect("/");
        }

        $requisicao = $request->all();

        $recordsTotal = Cidade::count();
        $cidades = Cidade::where([["name", "like", "%" . $requisicao["search"]["value"] . "%"]]);
        $cidades->orWhere([["id", "=", $requisicao["search"]["value"]]]);
        $cidades->orWhere([["ibge_id", "=", $requisicao["search"]["value"]]]);
        $cidades->orWhere(function ($q1) use ($requisicao) {
            $q1->whereHas("estado", function ($q2) use ($requisicao) {
                $q2->where([["id", "like", "%" . $requisicao["search"]["value"] . "%"]]);
                $q2->orWhere([["nome", "like", "%" . $requisicao["search"]["value"] . "%"]]);
                $q2->orWhere([["abbr", "like", "%" . $requisicao["search"]["value"] . "%"]]);
                $q2->orWhere(function($q3) use ($requisicao){
                    $q3->whereHas("pais", function ($q4) use ($requisicao) {
                        $q4->where([["name", "like", "%" . $requisicao["search"]["value"] . "%"]]);
                        $q4->orWhere([["codigo_iso", "like", "%" . $requisicao["search"]["value"] . "%"]]);
                    });
                });
            });
        });

        switch ($requisicao["order"][0]["column"]) {
            case 1:
                $cidades->orderBy("name", mb_strtoupper($requisicao["order"][0]["dir"]));
                break;
            default:
                $cidades->orderBy("id", mb_strtoupper($requisicao["order"][0]["dir"]));
        }
        $total = count($cidades->get());
        $cidades->limit($requisicao["length"]);
        $cidades->skip($requisicao["start"]);

        $retorno = array("draw" => $requisicao["draw"], "recordsTotal" => $recordsTotal, "recordsFiltered" => $total, "data" => array(), "requisicao" => $requisicao);
        foreach ($cidades->get() as $cidade) {
            $p = array();
            $p[0] = $cidade->id;
            $p[1] = $cidade->name;
            $p[2] = $cidade->estado->nome;
            $p[3] = $cidade->estado->pais->nome;


            $p[4] = '<a href="' . url("/cidade/edit/" . $cidade->id) . '" title="Editar Cidade: ' . $cidade->id . ' ' . $cidade->name . '" class="btn btn-default btn-sm" data-toggle="tooltip" data-original-title="Editar Enxadrista"><i class="fa fa-edit"></i></a>';
            if($cidade->isDeletavel()){
                $p[4] .= '<a href="' . url("/cidade/delete/" . $cidade->id) . '" title="Deletar Cidade: ' . $cidade->id . ' ' . $cidade->name . '" class="btn btn-danger btn-sm" data-toggle="tooltip" data-original-title="Editar Enxadrista"><i class="fa fa-times"></i></a>';
            }

            $retorno["data"][] = $p;
        }
        return response()->json($retorno);
    }

}

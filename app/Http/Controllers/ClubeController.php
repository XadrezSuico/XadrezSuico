<?php

namespace App\Http\Controllers;

use App\Cidade;
use App\Clube;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClubeController extends Controller
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

        $clubes = Clube::all();
        return view('clube.index', compact("clubes"));
    }
    function new () {
        $user = Auth::user();
        if (!$user->hasPermissionGlobalbyPerfil([1, 2, 8])) {
            return redirect("/");
        }

        $cidades = Cidade::all();
        return view('clube.new', compact("cidades"));
    }
    public function new_post(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobalbyPerfil([1, 2, 8])) {
            return redirect("/");
        }

        $clube = new Clube;
        $clube->name = $request->input("name");
        $clube->cidade_id = $request->input("cidade_id");

        if(env("ENTITY_DOMAIN",null) == "fexpar.com.br"){
            $clube->is_fexpar___clube_filiado = $request->has("is_fexpar___clube_filiado");
            $clube->is_fexpar___clube_valido_vinculo_federativo = $request->has("is_fexpar___clube_valido_vinculo_federativo");
        }

        $clube->save();
        return redirect("/clube/edit/" . $clube->id);
    }
    public function edit($id)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobalbyPerfil([1, 2, 8])) {
            return redirect("/");
        }

        $clube = Clube::find($id);
        $cidades = Cidade::all();
        return view('clube.edit', compact("clube", "cidades"));
    }
    public function edit_post($id, Request $request)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobalbyPerfil([1, 2, 8])) {
            return redirect("/");
        }

        $clube = Clube::find($id);
        $clube->name = $request->input("name");
        $clube->cidade_id = $request->input("cidade_id");

        if(env("ENTITY_DOMAIN",null) == "fexpar.com.br"){
            $clube->is_fexpar___clube_filiado = $request->has("is_fexpar___clube_filiado");
            $clube->is_fexpar___clube_valido_vinculo_federativo = $request->has("is_fexpar___clube_valido_vinculo_federativo");
        }
        $clube->save();
        return redirect("/clube/edit/" . $clube->id);
    }
    public function delete($id)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobalbyPerfil([1, 2, 8])) {
            return redirect("/");
        }

        $clube = Clube::find($id);

        if ($clube->isDeletavel()) {
            $clube->delete();
        }
        return redirect("/clube");
    }


    public function union($id)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobalbyPerfil([1, 2, 8])) {
            return redirect("/clube");
        }

        $clube_base = Clube::find($id);
        $clubes = Clube::where([["id", "!=", $id]])->get();
        return view('clube.union', compact("clube_base", "clubes"));
    }
    public function union_post($id, Request $request)
    {
        if (!$request->has("clube_a_ser_unido")) {
            return redirect()->back();
        } elseif ($request->input("clube_a_ser_unido") == "") {
            return redirect()->back();
        }

        $user = Auth::user();
        if (!$user->hasPermissionGlobalbyPerfil([1, 2, 8])) {
            return redirect("/clube");
        }

        $clube_base = Clube::find($id);
        $clube_a_ser_unido = Clube::find($request->input("clube_a_ser_unido"));

        if ($clube_base && $clube_a_ser_unido) {

            activity("club_union")
            ->causedBy(Auth::user())
            ->performedOn($clube_a_ser_unido)
            ->withProperties(['clube_base' => $clube_base,"clube_a_ser_unido"=>$clube_a_ser_unido])
            ->log('Unificação de Clube realizada.');


            foreach($clube_a_ser_unido->inscricoes->all() as $inscricao){
                $inscricao->clube_id = $clube_base->id;
                $inscricao->save();
            }

            foreach($clube_a_ser_unido->enxadristas->all() as $enxadrista){
                $enxadrista->clube_id = $clube_base->id;
                $enxadrista->save();
            }

            foreach($clube_a_ser_unido->vinculos->all() as $vinculo){
                $vinculo->clube_id = $clube_base->id;
                $vinculo->save();
            }

            foreach($clube_a_ser_unido->team_scores->all() as $team_score){
                $team_score->clubs_id = $clube_base->id;
                $team_score->save();
            }

            $clube_a_ser_unido->delete();

            return redirect("/clube");
        }

        return redirect()->back();
    }

    public function searchList(Request $request)
    {
        if($request->has("is_fexpar___clube_valido_vinculo_federativo")){
            $clubes = Clube::where([["is_fexpar___clube_valido_vinculo_federativo","=",true]])
            ->where(function($q1) use ($request) {
                $q1->where([
                    ["name", "like", "%" . $request->input("q") . "%"],
                ])->orWhere(function ($q) use ($request) {
                    $q->whereHas("cidade", function ($Q) use ($request) {
                        $Q->where([
                            ["name", "like", "%" . $request->input("q") . "%"],
                        ]);
                    });
                });
            })
            ->limit(30)
            ->get();
        }else{
            $clubes = Clube::where([
                ["name", "like", "%" . $request->input("q") . "%"],
            ])->orWhere(function ($q) use ($request) {
                $q->whereHas("cidade", function ($Q) use ($request) {
                    $Q->where([
                        ["name", "like", "%" . $request->input("q") . "%"],
                    ]);
                });
            })
            ->limit(30)
            ->get();
        }
        $results = array();
        if(!$request->has("is_fexpar___clube_valido_vinculo_federativo")) $results[] = array("id" => -1, "text" => "Sem Clube");
        foreach ($clubes as $clube) {
            $results[] = array("id" => $clube->id, "text" => $clube->getFullName());
        }
        return response()->json(["results" => $results, "pagination" => true]);
    }

}

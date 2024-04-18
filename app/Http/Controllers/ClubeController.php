<?php

namespace App\Http\Controllers;

use App\Cidade;
use App\Clube;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

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
        if($request->has("abbr")){
            if($request->input("abbr") != null && $request->input("abbr") != ""){
                $validator = Validator::make($request->all(), [
                    'abbr' => 'required|string|max:3',
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator->errors());
                }

                if (Clube::where([["abbr", "=", mb_strtoupper($request->input("abbr"))]])->count() > 0) {
                    $clube_abbr = Clube::where([["abbr", "=", mb_strtoupper($request->input("abbr"))]])->first();
                    $messageBag = new MessageBag;
                    $messageBag->add("type", "danger");
                    $messageBag->add("alerta", "Já há outro clube com essa mesma abreviação: {$clube_abbr->id} - " . $clube_abbr->getName());

                    return redirect()->back()->withErrors($messageBag);
                }
                $clube->abbr = mb_strtoupper($request->input("abbr"));
            }
        }
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
        if($request->has("abbr")){
            if ($request->input("abbr") != null && $request->input("abbr") != ""){
                if(Clube::where([["id","!=",$clube->id],["abbr","=", mb_strtoupper($request->input("abbr"))]])->count() > 0) {
                    $clube_abbr = Clube::where([["id", "!=", $clube->id], ["abbr", "=", mb_strtoupper($request->input("abbr"))]])->first();
                    $messageBag = new MessageBag;
                    $messageBag->add("type", "danger");
                    $messageBag->add("alerta", "Já há outro clube com essa mesma abreviação: {$clube_abbr->id} - ". $clube_abbr->getName());

                    return redirect()->back()->withErrors($messageBag);
                }
                $clube->abbr = mb_strtoupper($request->input("abbr"));
            }else{
                $clube->abbr = null;
            }
        }else{
            $clube->abbr = null;
        }
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
                })->orWhere(function ($q) use ($request) {
                    if (count(explode("#", $request->q)) == 2) {
                        $id = explode("#", $request->q)[1];

                        $q->where([["id", "=", $id]]);
                    }
                })->orWhere([
                    ["abbr","like", "%" . $request->input("q") . "%"]
                ]);
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
            })->orWhere(function ($q) use ($request) {
                if(count(explode("#",$request->q)) == 2){
                    $id = explode("#", $request->q)[1];

                    $q->where([["id","=",$id]]);
                }
            })->orWhere([
                ["abbr", "like", "%" . $request->input("q") . "%"]
            ])
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

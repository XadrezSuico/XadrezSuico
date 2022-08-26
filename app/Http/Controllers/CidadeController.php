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

        $cidades = Cidade::all();
        return view('cidade.index', compact("cidades"));
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

    public function searchList($estados_id, Request $request)
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

}

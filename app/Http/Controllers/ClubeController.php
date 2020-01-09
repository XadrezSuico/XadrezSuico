<?php

namespace App\Http\Controllers;

use App\Cidade;
use App\Clube;
use Illuminate\Http\Request;

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

}

<?php

namespace App\Http\Controllers;

use App\Sexo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SexoController extends Controller
{
    public function __construct()
    {
        return $this->middleware("auth");
    }
    public function index()
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobalbyPerfil([1, 2])) {
            return redirect("/");
        }

        $sexos = Sexo::all();
        return view('sexo.index', compact("sexos"));
    }
    function new () {
        return view('sexo.new');
    }
    public function new_post(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobalbyPerfil([1, 2])) {
            return redirect("/");
        }

        $sexo = new Sexo;
        $sexo->name = $request->input("name");
        $sexo->abbr = $request->input("abbr");
        $sexo->save();
        return redirect("/sexo/edit/" . $sexo->id);
    }
    public function edit($id)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobalbyPerfil([1, 2])) {
            return redirect("/");
        }

        $sexo = Sexo::find($id);
        return view('sexo.edit', compact("sexo"));
    }
    public function edit_post($id, Request $request)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobalbyPerfil([1, 2])) {
            return redirect("/");
        }

        $sexo = Sexo::find($id);
        $sexo->name = $request->input("name");
        $sexo->abbr = $request->input("abbr");
        $sexo->save();
        return redirect("/sexo/edit/" . $sexo->id);
    }
    public function delete($id)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobalbyPerfil([1, 2])) {
            return redirect("/");
        }

        $sexo = Sexo::find($id);

        if ($sexo->isDeletavel()) {
            $sexo->delete();
        }
        return redirect("/sexo");
    }

}

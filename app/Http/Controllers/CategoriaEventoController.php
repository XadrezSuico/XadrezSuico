<?php

namespace App\Http\Controllers;

use App\Categoria;
use App\CategoriaSexo;
use App\Evento;
use App\Sexo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoriaEventoController extends Controller
{
    public function __construct()
    {
        return $this->middleware("auth");
    }
    // public function index($evento_id)
    // {
    //     $evento = Evento::find($evento_id);
    //     $categorias = $evento->categorias_cadastradas->all();
    //     return view('evento.categoria.index', compact("categorias", "evento"));
    // }
    // function new ($evento_id) {
    //     $evento = Evento::find($evento_id);
    //     return view('evento.categoria.new', compact("evento"));
    // }
    public function new_post($evento_id, Request $request)
    {
        $evento = Evento::find($evento_id);
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/" . $evento->id);
        }

        $categoria = new Categoria;
        $categoria->name = $request->input("name");
        $categoria->idade_minima = $request->input("idade_minima");
        $categoria->idade_maxima = $request->input("idade_maxima");
        $categoria->code = $request->input("code");
        $categoria->cat_code = $request->input("cat_code");
        $categoria->evento_id = $evento->id;
        $categoria->save();

        return redirect("/evento/" . $evento->id . "/categorias/dashboard/" . $categoria->id);
    }
    public function edit($evento_id, $id)
    {
        $evento = Evento::find($evento_id);
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/" . $evento->id);
        }
        $categoria = Categoria::find($id);
        $sexos = Sexo::all();
        return view('evento.categoria.edit', compact("categoria", "sexos", "evento"));
    }
    public function edit_post($evento_id, $id, Request $request)
    {
        $evento = Evento::find($evento_id);
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/" . $evento->id);
        }

        $categoria = Categoria::find($id);
        $categoria->name = $request->input("name");
        $categoria->idade_minima = $request->input("idade_minima");
        $categoria->idade_maxima = $request->input("idade_maxima");
        $categoria->code = $request->input("code");
        $categoria->cat_code = $request->input("cat_code");

        if ($request->has("categoria_classificadora_id")) {
            if ($request->input("categoria_classificadora_id") != "") {
                $categoria->categoria_classificadora_id = $request->input("categoria_classificadora_id");
            } else {
                $categoria->categoria_classificadora_id = null;
            }
        } else {
            $categoria->categoria_classificadora_id = null;
        }
        $categoria->save();

        return redirect("/evento/" . $evento->id . "/categorias/dashboard/" . $categoria->id);
    }
    public function delete($evento_id, $id)
    {
        $evento = Evento::find($evento_id);
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/" . $evento->id);
        }
        $categoria = Categoria::find($id);

        if ($categoria->isDeletavel()) {
            $categoria->delete();
        }
        return redirect("/evento/dashboard/" . $evento->id . "?tab=categoria");
    }

    public function sexo_add($evento_id, $id, Request $request)
    {
        $evento = Evento::find($evento_id);
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/" . $evento->id);
        }
        $categoria = Categoria::find($id);

        $categoria_sexo = new CategoriaSexo;
        $categoria_sexo->categoria_id = $id;
        $categoria_sexo->sexos_id = $request->input("sexos_id");
        $categoria_sexo->save();
        return redirect("/evento/" . $evento->id . "/categorias/dashboard/" . $categoria->id);
    }
    public function sexo_remove($evento_id, $id, $categoria_sexo_id)
    {
        $evento = Evento::find($evento_id);
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/" . $evento->id);
        }
        $categoria = Categoria::find($id);

        $categoria_sexo = CategoriaSexo::find($categoria_sexo_id);
        $categoria_sexo->delete();
        return redirect("/evento/" . $evento->id . "/categorias/dashboard/" . $categoria->id);
    }
}

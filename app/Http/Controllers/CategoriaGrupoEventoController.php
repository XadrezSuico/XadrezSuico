<?php

namespace App\Http\Controllers;

use App\Categoria;
use App\CategoriaSexo;
use App\CategoriaTorneioTemplate;
use App\GrupoEvento;
use App\Sexo;
use App\TorneioTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoriaGrupoEventoController extends Controller
{
    public function __construct()
    {
        return $this->middleware("auth");
    }
    public function index($grupo_evento_id)
    {
        $grupo_evento = GrupoEvento::find($grupo_evento_id);
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionGroupEventByPerfil($grupo_evento->id, [7])
        ) {
            return redirect("/grupoevento/dashboard/".$grupo_evento->id);
        }


        $categorias = $grupo_evento->categorias->all();
        return view('grupoevento.categoria.index', compact("categorias", "grupo_evento"));
    }
    function new ($grupo_evento_id) {
        $grupo_evento = GrupoEvento::find($grupo_evento_id);
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionGroupEventByPerfil($grupo_evento->id, [7])
        ) {
            return redirect("/grupoevento/dashboard/".$grupo_evento->id);
        }


        return view('grupoevento.categoria.new', compact("grupo_evento"));
    }
    public function new_post($grupo_evento_id, Request $request)
    {
        $grupo_evento = GrupoEvento::find($grupo_evento_id);
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionGroupEventByPerfil($grupo_evento->id, [7])
        ) {
            return redirect("/grupoevento/dashboard/".$grupo_evento->id);
        }



        $categoria = new Categoria;
        $categoria->name = $request->input("name");
        $categoria->idade_minima = $request->input("idade_minima");
        $categoria->idade_maxima = $request->input("idade_maxima");
        $categoria->code = $request->input("code");
        $categoria->cat_code = $request->input("cat_code");
        if ($request->has("nao_classificar")) {
            $categoria->nao_classificar = true;
        }


        if ($request->has("quantos_premiam")) {
            if ($request->input("quantos_premiam") != "") {
                $categoria->quantos_premiam = $request->input("quantos_premiam");
            }
        }

        $categoria->grupo_evento_id = $grupo_evento->id;
        $categoria->save();

        return redirect("/grupoevento/" . $grupo_evento->id . "/categorias/dashboard/" . $categoria->id);
    }
    public function edit($grupo_evento_id, $id)
    {
        $grupo_evento = GrupoEvento::find($grupo_evento_id);
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionGroupEventByPerfil($grupo_evento->id, [7])
        ) {
            return redirect("/grupoevento/dashboard/".$grupo_evento->id);
        }


        $categoria = Categoria::find($id);
        $sexos = Sexo::all();
        return view('grupoevento.categoria.edit', compact("categoria", "sexos", "grupo_evento"));
    }
    public function edit_post($grupo_evento_id, $id, Request $request)
    {
        $grupo_evento = GrupoEvento::find($grupo_evento_id);
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionGroupEventByPerfil($grupo_evento->id, [7])
        ) {
            return redirect("/grupoevento/dashboard/".$grupo_evento->id);
        }



        $categoria = Categoria::find($id);
        $categoria->name = $request->input("name");
        $categoria->idade_minima = $request->input("idade_minima");
        $categoria->idade_maxima = $request->input("idade_maxima");
        $categoria->code = $request->input("code");
        $categoria->cat_code = $request->input("cat_code");
        if ($request->has("nao_classificar")) {
            $categoria->nao_classificar = true;
        } else {
            $categoria->nao_classificar = false;
        }

        if ($request->has("categoria_classificadora_id")) {
            if ($request->input("categoria_classificadora_id") != "") {
                $categoria->categoria_classificadora_id = $request->input("categoria_classificadora_id");
            } else {
                $categoria->categoria_classificadora_id = null;
            }
        } else {
            $categoria->categoria_classificadora_id = null;
        }

        if ($request->has("quantos_premiam")) {
            if ($request->input("quantos_premiam") != "") {
                $categoria->quantos_premiam = $request->input("quantos_premiam");
            } else {
                $categoria->quantos_premiam = null;
            }
        } else {
            $categoria->quantos_premiam = null;
        }

        $categoria->save();

        return redirect("/grupoevento/" . $grupo_evento->id . "/categorias/dashboard/" . $categoria->id);
    }
    public function delete($grupo_evento_id, $id)
    {
        $grupo_evento = GrupoEvento::find($grupo_evento_id);
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionGroupEventByPerfil($grupo_evento->id, [7])
        ) {
            return redirect("/grupoevento/dashboard/".$grupo_evento->id);
        }


        $categoria = Categoria::find($id);

        if ($categoria->isDeletavel()) {
            $categoria->delete();
        }
        return redirect("/grupoevento/dashboard/" . $grupo_evento->id . "?tab=categoria");
    }

    public function sexo_add($grupo_evento_id, $id, Request $request)
    {
        $grupo_evento = GrupoEvento::find($grupo_evento_id);
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionGroupEventByPerfil($grupo_evento->id, [7])
        ) {
            return redirect("/grupoevento/dashboard/".$grupo_evento->id);
        }


        $categoria = Categoria::find($id);

        $categoria_sexo = new CategoriaSexo;
        $categoria_sexo->categoria_id = $id;
        $categoria_sexo->sexos_id = $request->input("sexos_id");
        $categoria_sexo->save();
        return redirect("/grupoevento/" . $grupo_evento->id . "/categorias/dashboard/" . $categoria->id);
    }
    public function sexo_remove($grupo_evento_id, $id, $categoria_sexo_id)
    {
        $grupo_evento = GrupoEvento::find($grupo_evento_id);
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionGroupEventByPerfil($grupo_evento->id, [7])
        ) {
            return redirect("/grupoevento/dashboard/".$grupo_evento->id);
        }


        $categoria = Categoria::find($id);

        $categoria_sexo = CategoriaSexo::find($categoria_sexo_id);
        $categoria_sexo->delete();
        return redirect("/grupoevento/" . $grupo_evento->id . "/categorias/dashboard/" . $categoria->id);
    }



    public function create_template($grupo_evento_id, $id, Request $request)
    {
        $grupo_evento = GrupoEvento::find($grupo_evento_id);
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionGroupEventByPerfil($grupo_evento->id, [7])
        ) {
            return redirect("/grupoevento/dashboard/" . $grupo_evento->id);
        }



        $categoria = Categoria::find($id);

        if($categoria->torneios_template()->count() > 0) {
            $messageBag = new MessageBag;
            $messageBag->add("type", "danger");
            $messageBag->add("alerta", "A categoria já está relacionada à um Template de Torneio, então não é possível criar um template a partir dela!");
            return redirect("/grupoevento/dashboard/" . $grupo_evento->id . "/?tab=categoria")->with($messageBag);
        }

        $torneio_template = new TorneioTemplate;
        $torneio_template->grupo_evento_id = $grupo_evento->id;
        $torneio_template->name = $categoria->name;
        $torneio_template->save();

        $categoria_torneio_template = new CategoriaTorneioTemplate;
        $categoria_torneio_template->categoria_id = $categoria->id;
        $categoria_torneio_template->torneio_template_id = $torneio_template->id;
        $categoria_torneio_template->save();

        $messageBag = new MessageBag;
        $messageBag->add("type", "success");
        $messageBag->add("alerta", "Template de Torneio criado a partir de uma Categoria com sucesso!" );

        return redirect("/grupoevento/" . $grupo_evento->id . "/torneiotemplates/dashboard/" . $torneio_template->id)->with($messageBag);
    }
}

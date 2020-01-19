<?php

namespace App\Http\Controllers;

use App\CampoPersonalizado;
use App\GrupoEvento;
use App\Opcao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CampoPersonalizadoGrupoEventoController extends Controller
{
    public function __construct()
    {
        return $this->middleware("auth");
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
        

        $campo = new CampoPersonalizado;
        $campo->name = $request->input("name");
        $campo->question = $request->input("question");
        if ($request->has("type")) {
            if ($request->input("type") != "") {
                $campo->type = $request->input("type");
            }
        }

        if ($request->has("validator")) {
            if ($request->input("validator") != "") {
                $campo->validator = $request->input("campo_validator");
            }
        }

        $campo->grupo_evento_id = $grupo_evento->id;
        $campo->save();

        return redirect("/grupoevento/" . $grupo_evento->id . "/campos/dashboard/" . $campo->id);
    }
    public function dashboard($grupo_evento_id, $id)
    {
        $grupo_evento = GrupoEvento::find($grupo_evento_id);
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionGroupEventByPerfil($grupo_evento->id, [7])
        ) {
            return redirect("/grupoevento/dashboard/".$grupo_evento->id);
        }
        
        $campo = CampoPersonalizado::find($id);
        $user = Auth::user();
        return view('grupoevento.campo.dashboard', compact("campo", "sexos", "grupo_evento", "user"));
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
        

        $campo = CampoPersonalizado::find($id);
        $campo->name = $request->input("name");
        $campo->question = $request->input("question");
        if ($request->has("type")) {
            if ($request->input("type") != "") {
                $campo->type = $request->input("type");
            } else {
                $campo->type = null;
            }
        } else {
            $campo->type = null;
        }
        if ($request->has("validator")) {
            if ($request->input("validator") != "") {
                $campo->validator = $request->input("validator");
            } else {
                $campo->validator = null;
            }
        } else {
            $campo->validator = null;
        }
        if ($request->has("is_active")) {
            $campo->is_active = true;
        } else {
            $campo->is_active = false;
        }
        $campo->save();

        return redirect("/grupoevento/" . $grupo_evento->id . "/campos/dashboard/" . $campo->id);
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
        
        $campo = CampoPersonalizado::find($id);

        if ($campo->isDeletavel()) {
            $campo->delete();
        }
        return redirect("/grupoevento/dashboard/" . $grupo_evento->id . "?tab=campo_personalizado");
    }

    public function opcao_add($grupo_evento_id, $id, Request $request)
    {
        $grupo_evento = GrupoEvento::find($grupo_evento_id);
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionGroupEventByPerfil($grupo_evento->id, [7])
        ) {
            return redirect("/grupoevento/dashboard/".$grupo_evento->id);
        }
        
        $campo = CampoPersonalizado::find($id);

        $opcao = new Opcao;
        $opcao->campo_personalizados_id = $id;
        $opcao->name = $request->input("name");
        $opcao->response = $request->input("response");
        $opcao->value = $request->input("value");
        $opcao->save();
        return redirect("/grupoevento/" . $grupo_evento->id . "/campos/dashboard/" . $campo->id);
    }
    public function opcao_remove($grupo_evento_id, $id, $opcaos_id)
    {
        $grupo_evento = GrupoEvento::find($grupo_evento_id);
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionGroupEventByPerfil($grupo_evento->id, [7])
        ) {
            return redirect("/grupoevento/dashboard/".$grupo_evento->id);
        }
        
        $campo = CampoPersonalizado::find($id);

        $opcao = Opcao::find($opcaos_id);
        $opcao->delete();
        return redirect("/grupoevento/" . $grupo_evento->id . "/campos/dashboard/" . $campo->id);
    }
}

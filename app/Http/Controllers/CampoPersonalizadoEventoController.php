<?php

namespace App\Http\Controllers;

use App\CampoPersonalizado;
use App\Evento;
use App\Opcao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CampoPersonalizadoEventoController extends Controller
{
    public function __construct()
    {
        return $this->middleware("auth");
    }
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

        $campo->evento_id = $evento->id;
        $campo->save();

        return redirect("/evento/" . $evento->id . "/campos/dashboard/" . $campo->id);
    }
    public function dashboard($evento_id, $id)
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
        
        $campo = CampoPersonalizado::find($id);
        $user = Auth::user();
        return view('evento.campo.dashboard', compact("campo", "sexos", "evento", "user"));
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
        if ($request->has("is_required")) {
            $campo->is_required = true;
        } else {
            $campo->is_required = false;
        }
        $campo->save();

        return redirect("/evento/" . $evento->id . "/campos/dashboard/" . $campo->id);
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
        
        $campo = CampoPersonalizado::find($id);

        if ($campo->isDeletavel()) {
            $campo->delete();
        }
        return redirect("/evento/dashboard/" . $evento->id . "?tab=campo_personalizado");
    }

    public function opcao_add($evento_id, $id, Request $request)
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
        
        $campo = CampoPersonalizado::find($id);

        $opcao = new Opcao;
        $opcao->campo_personalizados_id = $id;
        $opcao->name = $request->input("name");
        $opcao->response = $request->input("response");
        $opcao->value = $request->input("value");
        $opcao->save();
        return redirect("/evento/" . $evento->id . "/campos/dashboard/" . $campo->id);
    }
    public function opcao_remove($evento_id, $id, $opcaos_id)
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
        
        $campo = CampoPersonalizado::find($id);

        $opcao = Opcao::find($opcaos_id);
        $opcao->delete();
        return redirect("/evento/" . $evento->id . "/campos/dashboard/" . $campo->id);
    }
}

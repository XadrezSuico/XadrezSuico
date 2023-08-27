<?php

namespace App\Http\Controllers;

use App\PerfilUser;
use App\Evento;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct()
    {
        return $this->middleware("auth");
    }

    public function index()
    {
        $user = Auth::user();
        if (!($user->hasPermissionGlobal() || $user->hasPermissionGroupEventsByPerfil([7]))) {
            return redirect("/");
        }

        $users = User::all();
        return view("usuario.index", compact("users"));
    }
    function new () {
        $user = Auth::user();
        if (!($user->hasPermissionGlobal() || $user->hasPermissionGroupEventsByPerfil([7]))) {
            return redirect("/");
        }

        return view("usuario.new");
    }
    public function newPost(Request $request)
    {
        $user = Auth::user();
        if (!($user->hasPermissionGlobal() || $user->hasPermissionGroupEventsByPerfil([7]))) {
            return redirect("/");
        }

        $requisicao = $request->all();
        $validator = \Validator::make($requisicao, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);
        return redirect("/usuario/edit/" . $user->id);
    }
    public function edit($id)
    {
        $user = Auth::user();
        if (!($user->hasPermissionGlobal() || $user->hasPermissionGroupEventsByPerfil([7]))) {
            return redirect("/");
        }

        $user = User::find($id);
        return view("usuario.edit", compact("user"));
    }
    public function editPost(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobal()) {
            return redirect("/");
        }

        $requisicao = $request->all();
        $user = User::find($id);
        if ($user->email != $request->input("email")) {
            $validator = \Validator::make($requisicao, [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors());
            }
        } else {
            $validator = \Validator::make($requisicao, [
                'name' => 'required|string|max:255',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors());
            }
        }
        $user->name = $request->input("name");
        $user->email = $request->input("email");
        $user->save();
        return redirect("/usuario/edit/" . $user->id);
    }
    public function password($id, Request $request)
    {
        $USER = Auth::user();
        if (!$USER->hasPermissionGlobalbyPerfil([1]) && $USER->id != $id) {
            return redirect("/");
        }
        $user = User::find($id);
        $ok = 0;
        if ($request->has("ok")) {
            $ok = $request->input("ok");
        }

        return view("usuario.password", compact("user", "ok", "USER"));
    }
    public function passwordPost(Request $request, $id)
    {
        $USER = Auth::user();
        if (!$USER->hasPermissionGlobalbyPerfil([1]) && $USER->id != $id) {
            return redirect("/");
        }
        $requisicao = $request->all();
        $validator = \Validator::make($requisicao, [
            'password' => 'required|string|min:6|confirmed',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }
        $user = User::find($id);
        $user->password = bcrypt($request->input('password'));
        $user->save();

        if (!$USER->hasPermissionGlobalbyPerfil([1])) {
            return redirect("/usuario/password/" . $user->id . "?ok=1");
        }

        return redirect("/usuario/edit/" . $user->id);
    }
    public function delete($id)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobalbyPerfil([1])) {
            return redirect("/");
        }

        $user = User::find($id);
        $user->delete();
        return redirect("/usuario");
    }

    public function perfil_add($id, Request $request)
    {
        $user = Auth::user();
        if (!($user->hasPermissionGlobal() || $user->hasPermissionGroupEventsByPerfil([7]))) {
            return redirect()->back();
        }
        if (!$user->hasPermissionGlobal() && $user->hasPermissionGroupEventsByPerfil([7])) {
            if (
                $request->input("perfils_id") == 3 ||
                $request->input("perfils_id") == 4 ||
                $request->input("perfils_id") == 5
            ) {
                $evento = Evento::find($request->input("evento_id"));
                if (!$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])) {
                    return redirect()->back();
                }
            } elseif (
                $request->input("perfils_id") == 6 ||
                $request->input("perfils_id") == 7
            ) {
                if (!$user->hasPermissionGroupEventByPerfil($request->input("grupo_evento_id"), [7])) {
                    return redirect()->back();
                }
            } else {
                return redirect()->back();
            }
        }
        $perfil_user = new PerfilUser;
        $perfil_user->users_id = $id;
        $perfil_user->perfils_id = $request->input("perfils_id");
        if (
            $request->input("perfils_id") == 3 ||
            $request->input("perfils_id") == 4 ||
            $request->input("perfils_id") == 5
        ) {
            $perfil_user->evento_id = $request->input("evento_id");
        } elseif (
            $request->input("perfils_id") == 6 ||
            $request->input("perfils_id") == 7
        ) {
            $perfil_user->grupo_evento_id = $request->input("grupo_evento_id");
        }
        $perfil_user->save();
        return redirect("/usuario/edit/" . $id);
    }
    public function perfil_remove($id, $perfil_users_id)
    {
        $user = Auth::user();
        $perfil_user = PerfilUser::find($perfil_users_id);
        if (!($user->hasPermissionGlobal() || $user->hasPermissionGroupEventsByPerfil([7]))) {
            return redirect()->back();
        }
        if (!$user->hasPermissionGlobal() && $user->hasPermissionGroupEventsByPerfil([7])) {
            if (
                $perfil_user->perfils_id == 3 ||
                $perfil_user->perfils_id == 4 ||
                $perfil_user->perfils_id == 5
            ) {
                $evento = Evento::find($perfil_user->evento_id);
                if (!$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])) {
                    return redirect()->back();
                }
            } elseif (
                $perfil_user->perfils_id == 6 ||
                $perfil_user->perfils_id == 7
            ) {
                if (!$user->hasPermissionGroupEventByPerfil($perfil_user->grupo_evento_id, [7])) {
                    return redirect()->back();
                }
            } else {
                return redirect()->back();
            }
        }
        $perfil_user->delete();
        return redirect("/usuario/edit/" . $id);
    }
}

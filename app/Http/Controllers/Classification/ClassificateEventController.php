<?php

namespace App\Http\Controllers\Classification;

use App\Classification\EventClassificate;
use App\Evento;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassificateEventController extends Controller
{
    public function new($event_id)
    {
        $user = Auth::user();
        $evento = Evento::find($event_id);

        if (
            (
                !$user->hasPermissionGlobal() &&
                !$user->hasPermissionEventByPerfil($evento->id, [3, 4]) &&
                !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [6, 7])
            )
            ||
            !$user->hasPermissionEventByPerfil($evento->id, [14,15])
        ) {
            return redirect("/");
        }

        return view("evento.classificator.new", compact("evento"));
    }
    public function new_post($event_id, Request $request)
    {
        $user = Auth::user();
        $evento = Evento::find($event_id);

        if (
            (
                !$user->hasPermissionGlobal() &&
                !$user->hasPermissionEventByPerfil($evento->id, [3, 4]) &&
                !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [6, 7])
            )
            ||
            !$user->hasPermissionEventByPerfil($evento->id, [14, 15])
        ) {
            return redirect("/");
        }

        $event_classificate = new EventClassificate;
        $event_classificate->event_id = $evento->id;

        $event_classificate->event_classificator_id = $request->event_classificator_id;
        $event_classificate->save();

        return redirect("/evento/" . $evento->id . "/classificator/edit/" . $event_classificate->id);
    }
    public function edit($event_id, $id)
    {
        $user = Auth::user();
        $evento = Evento::find($event_id);

        if (
            (
                !$user->hasPermissionGlobal() &&
                !$user->hasPermissionEventByPerfil($evento->id, [3, 4]) &&
                !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [6, 7])
            )
            ||
            !$user->hasPermissionEventByPerfil($evento->id, [14, 15])
        ) {
            return redirect("/");
        }

        if ($evento->event_classificators()->where([["id", "=", $id]])->count() == 0) {
            return redirect()->back();
        }

        $event_classificate = $evento->event_classificators()->where([["id", "=", $id]])->first();

        return view("evento.classificator.edit", compact("evento", "event_classificate"));
    }
    public function edit_post($event_id, $id, Request $request)
    {
        $user = Auth::user();
        $evento = Evento::find($event_id);

        if (
            (
                !$user->hasPermissionGlobal() &&
                !$user->hasPermissionEventByPerfil($evento->id, [3, 4]) &&
                !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [6, 7])
            )
            ||
            !$user->hasPermissionEventByPerfil($evento->id, [14, 15])
        ) {
            return redirect("/");
        }

        if ($evento->event_classificators()->where([["id", "=", $id]])->count() == 0) {
            return redirect()->back();
        }

        $event_classificate = $evento->event_classificators()->where([["id", "=", $id]])->first();

        $event_classificate->event_classificator_id = $request->event_classificator_id;
        $event_classificate->save();

        return redirect("/evento/" . $evento->id . "/classificator/edit/" . $event_classificate->id);
    }
}

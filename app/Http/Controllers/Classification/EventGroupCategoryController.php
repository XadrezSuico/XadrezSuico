<?php

namespace App\Http\Controllers\Classification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classification\EventClassificate;
use App\Classification\EventClassificateCategory;
use App\GrupoEvento;
use Illuminate\Support\Facades\Auth;

class EventGroupCategoryController extends Controller
{
    public function __construct()
    {
        return $this->middleware("auth");
    }
    public function new($group_event_id)
    {
        $user = Auth::user();
        $grupo_evento = GrupoEvento::find($group_event_id);

        if (
            (
                !$user->hasPermissionGlobal() &&
                !$user->hasPermissionGroupEventByPerfil($grupo_evento->id, [6, 7])
            )
            // ||
            // !$user->hasPermissionEventByPerfil($grupo_evento->id, [14, 15])
        ) {
            return redirect("/");
        }

        return view("grupoevento.classificator.category.new", compact("grupo_evento"));
    }
    public function new_post($group_event_id, Request $request)
    {
        $user = Auth::user();
        $grupo_evento = GrupoEvento::find($group_event_id);

        if (
            (
                !$user->hasPermissionGlobal() &&
                !$user->hasPermissionGroupEventByPerfil($grupo_evento->id, [6, 7])
            )
            // ||
            // !$user->hasPermissionEventByPerfil($grupo_evento->id, [14, 15])
        ) {
            return redirect("/");
        }

        $event_classificate_category = new EventClassificateCategory;
        $event_classificate_category->category_id = $request->category_id;
        $event_classificate_category->category_classificator_id = $request->category_classificator_id;
        $event_classificate_category->save();

        return redirect("/grupoevento/" . $grupo_evento->id . "/classificator/category/edit/" . $event_classificate_category->id);
    }
    public function edit($group_event_id, $id)
    {
        $user = Auth::user();
        $grupo_evento = GrupoEvento::find($group_event_id);

        if (
            (
                !$user->hasPermissionGlobal() &&
                !$user->hasPermissionGroupEventByPerfil($grupo_evento->id, [6, 7])
            )
            // ||
            // !$user->hasPermissionEventByPerfil($grupo_evento->id, [14, 15])
        ) {
            return redirect("/");
        }

        if (EventClassificateCategory::where([["id", "=", $id]])->count() == 0) {
            return redirect()->back();
        }

        $event_classificate_category = EventClassificateCategory::find($id);

        $found = false;

        $obj = $this;

        if (!$event_classificate_category->category_classificator->grupo_evento) {
            return redirect()->back();
        }
        if ($event_classificate_category->category_classificator->grupo_evento->id != $grupo_evento->id) {
            return redirect()->back();
        }

        return view("grupoevento.classificator.category.edit", compact("grupo_evento", "event_classificate_category"));
    }
    public function edit_post($group_event_id, $id, Request $request)
    {
        $user = Auth::user();
        $grupo_evento = GrupoEvento::find($group_event_id);

        if (
            (
                !$user->hasPermissionGlobal() &&
                !$user->hasPermissionGroupEventByPerfil($grupo_evento->id, [6, 7])
            )
            // ||
            // !$user->hasPermissionEventByPerfil($grupo_evento->id, [14, 15])
        ) {
            return redirect("/");
        }

        if (EventClassificateCategory::where([["id", "=", $id]])->count() == 0) {
            return redirect()->back();
        }

        $event_classificate_category = EventClassificateCategory::find($id);


        if (!$event_classificate_category->category_classificator->grupo_evento) {
            return redirect()->back();
        }
        if ($event_classificate_category->category_classificator->grupo_evento->id != $grupo_evento->id) {
            return redirect()->back();
        }

        $event_classificate_category->category_id = $request->category_id;
        $event_classificate_category->category_classificator_id = $request->category_classificator_id;
        $event_classificate_category->save();

        return redirect("/grupoevento/" . $grupo_evento->id . "/classificator/category/edit/" . $event_classificate_category->id);
    }
}

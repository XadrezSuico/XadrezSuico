<?php

namespace App\Http\Controllers\Classification;

use App\Classification\EventClassificateRule;
use App\Enum\ClassificationType;
use App\Enum\ClassificationTypeRule;
use App\Enum\ClassificationTypeRuleConfig;
use App\Evento;
use App\GrupoEvento;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassificateEventRuleController extends Controller
{
    public function __construct()
    {
        return $this->middleware("auth");
    }
    public function new($type, $element_id, $event_classificates_id)
    {
        $user = Auth::user();
        if($type == "event"){
            $element = Evento::find($element_id);
        }else{
            $element = GrupoEvento::find($element_id);
        }


        if($element->event_classificates()->where([["id","=",$event_classificates_id]])->count() == 0) {
            return redirect()->back();
        }
        $event_classificates = $element->event_classificates()->where([["id","=",$event_classificates_id]])->first();

        if($type == "event"){
            if (
                (
                    !$user->hasPermissionGlobal() &&
                    !$user->hasPermissionEventByPerfil($element->id, [3, 4]) &&
                    !$user->hasPermissionGroupEventByPerfil($element->grupo_evento->id, [6, 7])
                )
                ||
                !$user->hasPermissionEventByPerfil($event_classificates->event->id, [14, 16])
            ) {
                return redirect("/");
            }
        }else{

            if (
                (
                    !$user->hasPermissionGlobal() &&
                    !$user->hasPermissionEventByPerfilByGroupEvent($element->id, [3, 4]) &&
                    !$user->hasPermissionGroupEventByPerfil($element->id, [6, 7])
                )
                ||
                !$user->hasPermissionEventByPerfil($event_classificates->event->id, [14, 16])
            ) {
                return redirect("/");
            }
        }

        $evento = $type === "event" ? $element : null;

        return view("evento.v2.classificator.rule.new", compact("event_classificates", "element", "evento"));
    }
    public function new_post($type, $element_id, $event_classificates_id, Request $request)
    {
        $user = Auth::user();
        if ($type == "event") {
            $element = Evento::find($element_id);
        } else {
            $element = GrupoEvento::find($element_id);
        }

        if ($element->event_classificates()->where([["id", "=", $event_classificates_id]])->count() == 0) {
            return redirect()->back();
        }
        $event_classificates = $element->event_classificates()->where([["id", "=", $event_classificates_id]])->first();

        if ($type == "event") {
            if (
                (
                    !$user->hasPermissionGlobal() &&
                    !$user->hasPermissionEventByPerfil($element->id, [3, 4]) &&
                    !$user->hasPermissionGroupEventByPerfil($element->grupo_evento->id, [6, 7])
                )
                ||
                !$user->hasPermissionEventByPerfil($event_classificates->event->id, [14, 16])
            ) {
                return redirect("/");
            }
        } else {

            if (
                (
                    !$user->hasPermissionGlobal() &&
                    !$user->hasPermissionEventByPerfilByGroupEvent($element->id, [3, 4]) &&
                    !$user->hasPermissionGroupEventByPerfil($element->id, [6, 7])
                )
                ||
                !$user->hasPermissionEventByPerfil($event_classificates->event->id, [14, 16])
            ) {
                return redirect("/");
            }
        }

        $event_classificate_rule = new EventClassificateRule;
        $event_classificate_rule->event_classificates_id = $event_classificates->id;

        $event_classificate_rule->type = $request->type;

        if(
            in_array($request->type,[ClassificationTypeRule::POSITION, ClassificationTypeRule::POSITION_ABSOLUTE, ClassificationTypeRule::PLACE_BY_QUANTITY, ClassificationTypeRule::CLASSIFICATE_BY_START_POSITION])
        ){
            $event_classificate_rule->value = $request->value;
        }else{
            $event_classificate_rule->event_id = $request->event_id;
        }
        $event_classificate_rule->save();

        foreach(ClassificationTypeRuleConfig::list() as $key => $r_type){
            if($request->has("config_{$key}")) {
                if ($r_type["type"] == "boolean") {
                    $event_classificate_rule->setConfig($key, $r_type["type"], $request->has("config_{$key}"));
                } else {
                    if($request->input("config_{$key}") != ""){
                        $event_classificate_rule->setConfig($key, $r_type["type"], $request->input("config_{$key}"));
                    }
                }
            }
        }

        return redirect("/classificator/".$type."/". $element_id."/".$event_classificates->id."/rule/edit/" . $event_classificate_rule->id);
    }
    public function edit($type, $element_id, $event_classificates_id, $id)
    {
        $user = Auth::user();
        if ($type == "event") {
            $element = Evento::find($element_id);
        } else {
            $element = GrupoEvento::find($element_id);
        }

        if ($element->event_classificates()->where([["id", "=", $event_classificates_id]])->count() == 0) {
            return redirect()->back();
        }
        $event_classificates = $element->event_classificates()->where([["id", "=", $event_classificates_id]])->first();


        if ($type == "event") {
            if (
                (
                    !$user->hasPermissionGlobal() &&
                    !$user->hasPermissionEventByPerfil($element->id, [3, 4]) &&
                    !$user->hasPermissionGroupEventByPerfil($element->grupo_evento->id, [6, 7])
                )
                ||
                !$user->hasPermissionEventByPerfil($event_classificates->event->id, [14, 16])
            ) {
                return redirect("/");
            }
        } else {

            if (
                (
                    !$user->hasPermissionGlobal() &&
                    !$user->hasPermissionEventByPerfilByGroupEvent($element->id, [3, 4]) &&
                    !$user->hasPermissionGroupEventByPerfil($element->id, [6, 7])
                )
                ||
                !$user->hasPermissionEventByPerfil($event_classificates->event->id, [14, 16])
            ) {
                return redirect("/");
            }
        }


        if ($event_classificates->rules()->where([["id", "=", $id]])->count() == 0) {
            return redirect()->back();
        }

        $event_classificate_rule = $event_classificates->rules()->where([["id", "=", $id]])->first();

        $evento = $type === "event" ? $element : null;

        return view("evento.v2.classificator.rule.edit", compact("event_classificates", "element", "event_classificate_rule", "evento"));
    }
    public function edit_post($type, $element_id, $event_classificates_id, $id, Request $request)
    {
        $user = Auth::user();
        if ($type == "event") {
            $element = Evento::find($element_id);
        } else {
            $element = GrupoEvento::find($element_id);
        }

        if ($element->event_classificates()->where([["id", "=", $event_classificates_id]])->count() == 0) {
            return redirect()->back();
        }
        $event_classificates = $element->event_classificates()->where([["id", "=", $event_classificates_id]])->first();

        if ($type == "event") {
            if (
                (
                    !$user->hasPermissionGlobal() &&
                    !$user->hasPermissionEventByPerfil($element->id, [3, 4]) &&
                    !$user->hasPermissionGroupEventByPerfil($element->grupo_evento->id, [6, 7])
                )
                ||
                !$user->hasPermissionEventByPerfil($event_classificates->event->id, [14, 16])
            ) {
                return redirect("/");
            }
        } else {

            if (
                (
                    !$user->hasPermissionGlobal() &&
                    !$user->hasPermissionEventByPerfilByGroupEvent($element->id, [3, 4]) &&
                    !$user->hasPermissionGroupEventByPerfil($element->id, [6, 7])
                )
                ||
                !$user->hasPermissionEventByPerfil($event_classificates->event->id, [14, 16])
            ) {
                return redirect("/");
            }
        }

        if ($event_classificates->rules()->where([["id", "=", $id]])->count() == 0) {
            return redirect()->back();
        }

        $event_classificate_rule = $event_classificates->rules()->where([["id", "=", $id]])->first();

        $event_classificate_rule->type = $request->type;

        if (
            in_array($request->type, [ClassificationTypeRule::POSITION, ClassificationTypeRule::POSITION_ABSOLUTE, ClassificationTypeRule::PLACE_BY_QUANTITY, ClassificationTypeRule::CLASSIFICATE_BY_START_POSITION])
        ) {
            $event_classificate_rule->value = $request->value;
        } else {
            $event_classificate_rule->event_id = $request->event_id;
        }
        $event_classificate_rule->save();

        foreach (ClassificationTypeRuleConfig::list() as $key => $r_type) {
            if ($request->has("config_{$key}")) {
                if ($r_type["type"] == "boolean") {
                    $event_classificate_rule->setConfig($key, $r_type["type"], $request->has("config_{$key}"));
                } else {
                    if ($request->input("config_{$key}") != "") {
                        $event_classificate_rule->setConfig($key, $r_type["type"], $request->input("config_{$key}"));
                    } else {
                        if ($event_classificate_rule->hasConfig($key)) {
                            $event_classificate_rule->removeConfig($key);
                        }
                    }
                }
            } else {
                if ($event_classificate_rule->hasConfig($key)) {
                    $event_classificate_rule->removeConfig($key);
                }
            }
        }

        return redirect("/classificator/" . $type ."/" . $element_id . "/" . $event_classificates->id."/rule/edit/" . $event_classificate_rule->id);
    }
}

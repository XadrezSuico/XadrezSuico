<?php

namespace App\Http\Controllers\Classification;

use App\Classification\EventClassificateRule;
use App\Enum\ClassificationType;
use App\Enum\ClassificationTypeRule;
use App\Enum\ClassificationTypeRuleConfig;
use App\Evento;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassificateEventRuleController extends Controller
{
    public function __construct()
    {
        return $this->middleware("auth");
    }
    public function new($event_id, $event_classificates_id)
    {
        $user = Auth::user();
        $evento = Evento::find($event_id);

        if($evento->event_classificates()->where([["id","=",$event_classificates_id]])->count() == 0) {
            return redirect()->back();
        }
        $event_classificates = $evento->event_classificates()->where([["id","=",$event_classificates_id]])->first();

        if (
            (
                !$user->hasPermissionGlobal() &&
                !$user->hasPermissionEventByPerfil($evento->id, [3, 4]) &&
                !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [6, 7])
            )
            ||
            !$user->hasPermissionEventByPerfil($event_classificates->event->id, [14, 16])
        ) {
            return redirect("/");
        }

        return view("evento.classificator.rule.new", compact("evento", "event_classificates"));
    }
    public function new_post($event_id, $event_classificates_id, Request $request)
    {
        $user = Auth::user();
        $evento = Evento::find($event_id);

        if ($evento->event_classificates()->where([["id", "=", $event_classificates_id]])->count() == 0) {
            return redirect()->back();
        }
        $event_classificates = $evento->event_classificates()->where([["id", "=", $event_classificates_id]])->first();

        if (
            (
                !$user->hasPermissionGlobal() &&
                !$user->hasPermissionEventByPerfil($evento->id, [3, 4]) &&
                !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [6, 7])
            )
            ||
            !$user->hasPermissionEventByPerfil($event_classificates->event->id, [14, 16])
        ) {
            return redirect("/");
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

        foreach(ClassificationTypeRuleConfig::list() as $key => $type){
            if($request->has("config_{$key}")) {
                if ($type["type"] == "boolean") {
                    $event_classificate_rule->setConfig($key, $type["type"], $request->has("config_{$key}"));
                } else {
                    if($request->input("config_{$key}") != ""){
                        $event_classificate_rule->setConfig($key, $type["type"], $request->input("config_{$key}"));
                    }
                }
            }
        }

        return redirect("/evento/" . $evento->id . "/classificator/".$event_classificates->id."/rule/edit/" . $event_classificate_rule->id);
    }
    public function edit($event_id, $event_classificates_id, $id)
    {
        $user = Auth::user();
        $evento = Evento::find($event_id);

        if ($evento->event_classificates()->where([["id", "=", $event_classificates_id]])->count() == 0) {
            return redirect()->back();
        }
        $event_classificates = $evento->event_classificates()->where([["id", "=", $event_classificates_id]])->first();

        if (
            (
                !$user->hasPermissionGlobal() &&
                !$user->hasPermissionEventByPerfil($evento->id, [3, 4]) &&
                !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [6, 7])
            )
            ||
            !$user->hasPermissionEventByPerfil($event_classificates->event->id, [14, 16])
        ) {
            return redirect("/");
        }


        if ($event_classificates->rules()->where([["id", "=", $id]])->count() == 0) {
            return redirect()->back();
        }

        $event_classificate_rule = $event_classificates->rules()->where([["id", "=", $id]])->first();

        return view("evento.classificator.rule.edit", compact("evento", "event_classificates", "event_classificate_rule"));
    }
    public function edit_post($event_id, $event_classificates_id, $id, Request $request)
    {
        $user = Auth::user();
        $evento = Evento::find($event_id);

        if ($evento->event_classificates()->where([["id", "=", $event_classificates_id]])->count() == 0) {
            return redirect()->back();
        }
        $event_classificates = $evento->event_classificates()->where([["id", "=", $event_classificates_id]])->first();

        if (
            (
                !$user->hasPermissionGlobal() &&
                !$user->hasPermissionEventByPerfil($evento->id, [3, 4]) &&
                !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [6, 7])
            )
            ||
            !$user->hasPermissionEventByPerfil($event_classificates->event->id, [14, 16])
        ) {
            return redirect("/");
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

        foreach (ClassificationTypeRuleConfig::list() as $key => $type) {
            if ($request->has("config_{$key}")) {
                if ($type["type"] == "boolean") {
                    $event_classificate_rule->setConfig($key, $type["type"], $request->has("config_{$key}"));
                } else {
                    if ($request->input("config_{$key}") != "") {
                        $event_classificate_rule->setConfig($key, $type["type"], $request->input("config_{$key}"));
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

        return redirect("/evento/" . $evento->id . "/classificator/".$event_classificates->id."/rule/edit/" . $event_classificate_rule->id);
    }
}

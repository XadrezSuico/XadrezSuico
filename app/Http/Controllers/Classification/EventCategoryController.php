<?php

namespace App\Http\Controllers\Classification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classification\EventClassificate;
use App\Evento;
use App\Categoria;
use App\Classification\EventClassificateCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;

class EventCategoryController extends Controller
{
    public function __construct()
    {
        return $this->middleware("auth");
    }
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
            // ||
            // !$user->hasPermissionEventByPerfil($evento->id, [14, 15])
        ) {

            $messageBag = new MessageBag;
            $messageBag->add("type", "danger");
            $messageBag->add("alerta", "Você não possui privilégios para acesso a essa funcionalidade.");
            return redirect("/home")->withErrors($messageBag);
        }

        return view("evento.v2.classificator.category.new", compact("evento"));
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
            // ||
            // !$user->hasPermissionEventByPerfil($evento->id, [14, 15])
        ) {
            $messageBag = new MessageBag;
            $messageBag->add("type", "danger");
            $messageBag->add("alerta", "Você não possui privilégios para acesso a essa funcionalidade.");
            return redirect("/home")->withErrors($messageBag);
        }

        $event_classificate_category = new EventClassificateCategory;
        $event_classificate_category->category_id = $request->category_id;
        $event_classificate_category->category_classificator_id = $request->category_classificator_id;
        $event_classificate_category->save();

        return redirect("/evento/" . $evento->id . "/classificator/category/edit/" . $event_classificate_category->id);
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
            // ||
            // !$user->hasPermissionEventByPerfil($evento->id, [14, 15])
        ) {
            $messageBag = new MessageBag;
            $messageBag->add("type", "danger");
            $messageBag->add("alerta", "Você não possui privilégios para acesso a essa funcionalidade.");
            return redirect("/home")->withErrors($messageBag);
        }

        if (EventClassificateCategory::where([["id","=",$id]])->count() == 0) {
            $messageBag = new MessageBag;
            $messageBag->add("type", "danger");
            $messageBag->add("alerta", "Classificador não encontrado.");
            return redirect()->back()->withErrors($messageBag);
        }

        $event_classificate_category = EventClassificateCategory::find($id);

        $found = false;

        $obj = $this;

        if (!$event_classificate_category->category_classificator->evento) {
            $messageBag = new MessageBag;
            $messageBag->add("type", "danger");
            $messageBag->add("alerta", "Classificador não possui evento para classificar.");
            return redirect()->back()->withErrors($messageBag);
        }
        if ($event_classificate_category->category_classificator->evento->id != $evento->id) {
            $messageBag = new MessageBag;
            $messageBag->add("type", "danger");
            $messageBag->add("alerta", "A categoria não pertence ao Evento Classificador.");
            return redirect()->back()->withErrors($messageBag);
        }

        return view("evento.v2.classificator.category.edit", compact("evento", "event_classificate_category"));
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
            // ||
            // !$user->hasPermissionEventByPerfil($evento->id, [14, 15])
        ) {
            $messageBag = new MessageBag;
            $messageBag->add("type", "danger");
            $messageBag->add("alerta", "Você não possui privilégios para acesso a essa funcionalidade.");
            return redirect("/home")->withErrors($messageBag);
        }

        if (EventClassificateCategory::where([["id", "=", $id]])->count() == 0) {
            $messageBag = new MessageBag;
            $messageBag->add("type", "danger");
            $messageBag->add("alerta", "Classificador não encontrado.");
            return redirect()->back()->withErrors($messageBag);
        }

        $event_classificate_category = EventClassificateCategory::find($id);

        if (!$event_classificate_category->category_classificator->evento) {
            $messageBag = new MessageBag;
            $messageBag->add("type", "danger");
            $messageBag->add("alerta", "Classificador não possui evento para classificar.");
            return redirect()->back()->withErrors($messageBag);
        }
        if ($event_classificate_category->category_classificator->evento->id != $evento->id) {
            $messageBag = new MessageBag;
            $messageBag->add("type", "danger");
            $messageBag->add("alerta", "A categoria não pertence ao Evento Classificador.");
            return redirect()->back()->withErrors($messageBag);
        }

        $event_classificate_category->category_id = $request->category_id;
        $event_classificate_category->category_classificator_id = $request->category_classificator_id;
        $event_classificate_category->save();

        return redirect("/evento/" . $evento->id . "/classificator/category/edit/" . $event_classificate_category->id);
    }
}

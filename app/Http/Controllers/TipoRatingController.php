<?php

namespace App\Http\Controllers;

use App\TipoRating;
use App\TipoRatingRegras;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;

class TipoRatingController extends Controller
{
    public function __construct()
    {
        return $this->middleware("auth");
    }
    public function index()
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobalbyPerfil([1, 2])) {
            return redirect("/");
        }

        $tipos_rating = TipoRating::all();
        return view("tiporating.index", compact("tipos_rating"));
    }
    function new () {
        $user = Auth::user();
        if (!$user->hasPermissionGlobalbyPerfil([1, 2])) {
            return redirect("/");
        }

        return view('tiporating.new');
    }
    public function new_post(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobalbyPerfil([1, 2])) {
            return redirect("/");
        }

        $tipo_rating = new TipoRating;
        $tipo_rating->name = $request->input("name");
        $tipo_rating->save();
        return redirect("/tiporating/dashboard/" . $tipo_rating->id);
    }
    public function dashboard($id)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobalbyPerfil([1, 2])) {
            return redirect("/");
        }

        $tipo_rating = TipoRating::find($id);
        return view('tiporating.dashboard', compact("tipo_rating"));
    }
    public function dashboard_post($id, Request $request)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobalbyPerfil([1, 2])) {
            return redirect("/");
        }

        $tipo_rating = TipoRating::find($id);
        $tipo_rating->name = $request->input("name");
        $tipo_rating->save();
        return redirect("/tiporating/dashboard/" . $tipo_rating->id);
    }
    public function delete($id)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobalbyPerfil([1, 2])) {
            return redirect("/");
        }

        $tipo_rating = TipoRating::find($id);

        if ($tipo_rating->isDeletavel()) {
            $tipo_rating->delete();
        }
        return redirect("/tiporating");
    }

    public function regra_add($id, Request $request)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobalbyPerfil([1, 2])) {
            return redirect("/");
        }

        if (!$request->has("idade_minima") && !$request->has("idade_maxima")) {
            $messageBag = new MessageBag;
            $messageBag->add("alerta", "É necessário definir ao menos uma idade mínima ou máxima, ou então as duas, para a regra em questão.");
            $messageBag->add("type", "danger");
            return redirect()->back()->withErrors($messageBag);
        } elseif ($request->input("idade_minima") == "" && $request->input("idade_maxima") == "") {
            $messageBag = new MessageBag;
            $messageBag->add("alerta", "É necessário definir ao menos uma idade mínima ou máxima, ou então as duas, para a regra em questão.");
            $messageBag->add("type", "danger");
            return redirect()->back()->withErrors($messageBag);
        }

        $tipo_rating_regra = new TipoRatingRegras;
        $tipo_rating_regra->tipo_ratings_id = $id;
        if ($request->has("idade_minima")) {
            if ($request->input("idade_minima") != null) {
                $tipo_rating_regra->idade_minima = $request->input("idade_minima");
            }
        }

        if ($request->has("idade_maxima")) {
            if ($request->input("idade_maxima") != null) {
                $tipo_rating_regra->idade_maxima = $request->input("idade_maxima");
            }
        }

        $tipo_rating_regra->inicial = $request->input("inicial");
        $tipo_rating_regra->k = $request->input("k");
        $tipo_rating_regra->save();
        return redirect("/tiporating/dashboard/" . $id);
    }
    public function regra_remove($id, $tipo_rating_regra_id)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobalbyPerfil([1, 2])) {
            return redirect("/");
        }

        $tipo_rating_regra = TipoRatingRegras::find($tipo_rating_regra_id);
        $tipo_rating_regra->delete();
        return redirect("/tiporating/dashboard/" . $id);
    }
}

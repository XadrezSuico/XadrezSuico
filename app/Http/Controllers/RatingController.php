<?php

namespace App\Http\Controllers;

use App\Rating;
use App\TipoRating;

use Illuminate\Http\Request;

class RatingController extends Controller
{
    // Controller público!

    public function index()
    {
        $tipos_rating = TipoRating::all();
        return view("rating.index", compact("tipos_rating"));
    }
    function list($tipo_rating_id) {
        if(TipoRating::where([["id","=",$tipo_rating_id]])->count() == 0){
            return abort(404);
        }
        $tipo_rating = TipoRating::find($tipo_rating_id);
        return view("rating.list", compact("tipo_rating"));
    }
    public function view($tipo_rating_id, $rating_id)
    {
        if(TipoRating::where([["id","=",$tipo_rating_id]])->count() == 0){
            return abort(404);
        }
        $tipo_rating = TipoRating::find($tipo_rating_id);
        if(Rating::where([["id","=",$rating_id]])->count() == 0){
            return abort(404);
        }
        $rating = Rating::find($rating_id);

        $enxadrista = $rating->enxadrista;
        return view("rating.view", compact("tipo_rating", "rating", "enxadrista"));
    }

    /*
     *
     *
     * API
     *
     *
     */
    public function searchRatingList($tipo_ratings_id, Request $request)
    {
        if(TipoRating::where([["id","=",$tipo_ratings_id]])->count() == 0){
            return abort(404);
        }
        $tipo_rating = TipoRating::find($tipo_ratings_id);

        $requisicao = $request->all();

        $search = $tipo_rating->ratings()->where(function($q1) use ($requisicao){
            $q1->where([
                ["id","=",$requisicao["search"]["value"]]
            ]);
        })
        ->orWhere(function($q1) use ($requisicao){
            $q1->whereHas("enxadrista", function($q2) use ($requisicao){
                $q2->where([["id", "=", $requisicao["search"]["value"]]]);
                $q2->orWhere([["name", "like", "%".$requisicao["search"]["value"]."%"]]);
            });
        });

        switch ($requisicao["order"][0]["column"]) {
            case 1:
                $search->leftJoin("enxadrista","enxadrista.id","ratings.enxadrista_id");
                $search->orderBy("enxadrista.id", mb_strtoupper($requisicao["order"][0]["dir"]));
                $search->orderBy("enxadrista.name", mb_strtoupper($requisicao["order"][0]["dir"]));
                break;
            case 2:
                $search->leftJoin("enxadrista","enxadrista.id","ratings.enxadrista_id");
                $search->orderBy("enxadrista.born", mb_strtoupper($requisicao["order"][0]["dir"]));
                break;
            default:
                $search->orderBy("id", mb_strtoupper($requisicao["order"][0]["dir"]));
        }
        $total = $search->count();
        $search->limit($requisicao["length"]);
        $search->skip($requisicao["start"]);

        $recordsTotal = $tipo_rating->ratings()->count();

        $retorno = array("draw" => $requisicao["draw"], "recordsTotal" => $recordsTotal, "recordsFiltered" => $total, "data" => array(), "requisicao" => $requisicao);
        foreach ($search->get() as $rating) {
            $p = array();
            $p[0] = $rating->id;
            $p[1] = "#".$rating->enxadrista->getId()." - ".$rating->enxadrista->getNomePublico();

            $p[2] = $rating->enxadrista->getNascimentoPublico();

            $p[3] = $rating->valor;

            $p[4] = '<a class="btn btn-default" href="'.url("/rating/".$tipo_rating->id."/view/".$rating->id).'" role="button">Visualizar</a>';

            $retorno["data"][] = $p;
        }
        return response()->json($retorno);
    }
}

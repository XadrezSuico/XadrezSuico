<?php

namespace App\Http\Controllers;

use App\Rating;
use App\TipoRating;

class RatingController extends Controller
{
    // Controller público!

    public function index()
    {
        $tipos_rating = TipoRating::all();
        return view("rating.index", compact("tipos_rating"));
    }
    function list($tipo_rating_id) {
        $tipo_rating = TipoRating::find($tipo_rating_id);
        return view("rating.list", compact("tipo_rating"));
    }
    public function view($tipo_rating_id, $rating_id)
    {
        $tipo_rating = TipoRating::find($tipo_rating_id);
        $rating = Rating::find($rating_id);
        $enxadrista = $rating->enxadrista;
        return view("rating.view", compact("tipo_rating", "rating", "enxadrista"));
    }
}

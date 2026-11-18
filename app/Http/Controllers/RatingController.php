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
        $tipo_rating = TipoRating::findOrFail($tipo_rating_id);
        $rating = $tipo_rating->ratings()->whereHas('enxadrista')->findOrFail($rating_id);
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

        $request->validate([
            'search' => 'sometimes|array', 'search.value' => 'nullable|string|max:255',
            'order' => 'sometimes|array', 'order.0.column' => 'sometimes|integer|min:0|max:4',
            'order.0.dir' => 'sometimes|in:asc,desc',
            'length' => 'sometimes|integer|min:-1|max:1000',
            'start' => 'sometimes|integer|min:0', 'draw' => 'sometimes|integer|min:0',
        ]);
        $term = (string) $request->input('search.value', '');
        $search = $tipo_rating->ratings()->whereHas('enxadrista');
        if ($term !== '') {
            $search->where(function ($query) use ($term) {
                $query->where('ratings.id', $term)->orWhereHas('enxadrista', function ($player) use ($term) {
                    $player->where('id', $term)->orWhere('name', 'like', '%' . $term . '%');
                });
            });
        }
        $columns = [0 => 'id', 1 => 'enxadrista_id', 3 => 'valor'];
        $column = $columns[(int) $request->input('order.0.column', 0)] ?? 'id';
        $search->orderBy($column, $request->input('order.0.dir', 'asc'));
        $total = $search->count();
        $length = (int) $request->input('length', 10);
        $search->limit($length === -1 ? 1000 : max(1, $length));
        $search->skip((int) $request->input('start', 0));
        $recordsTotal = $tipo_rating->ratings()->whereHas('enxadrista')->count();
        $retorno = ['draw' => (int) $request->input('draw', 0), 'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $total, 'data' => [], 'requisicao' => $request->all()];
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

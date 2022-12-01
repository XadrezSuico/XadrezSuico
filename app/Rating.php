<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Traits\LogsActivity;

class Rating extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];

    public function enxadrista()
    {
        return $this->belongsTo("App\Enxadrista", "enxadrista_id", "id");
    }
    public function tipo_rating()
    {
        return $this->belongsTo("App\TipoRating", "tipo_ratings_id", "id");
    }
    public function movimentacoes()
    {
        return $this->hasMany("App\MovimentacaoRating", "ratings_id", "id");
    }
    public function dias()
    {
        return $this->hasMany("App\RatingDia", "ratings_id", "id");
    }

    public function calcular()
    {
        $this->valor = 0;
        foreach ($this->movimentacoes->all() as $movimentacao) {
            $this->valor += $movimentacao->valor;
        }
        $this->save();


        activity("rating__calculate_update")
            ->performedOn($this)
            ->log("Rating atualizado.");
    }

    public function getMovimentacoes(){
        $movimentacoes = array();

        $primeira_movimentacao = $this->movimentacoes()->where([["is_inicial","=",true]])->first();
        if($primeira_movimentacao){
            $movimentacoes[] = $primeira_movimentacao;
        }

        $movimentacoes_rating = $this->movimentacoes()->with(['torneio' => function ($q1) {
                                        $q1->with(["evento" => function($q2){
                                            $q2->orderBy('data_fim', 'ASC');
                                        }]);
                                    }])
                                    ->where([["is_inicial","=",0]])
                                    ->get();

        foreach($movimentacoes_rating as $movimentacao_rating){
            $movimentacoes[] = $movimentacao_rating;
        }

        return $movimentacoes;
    }
}

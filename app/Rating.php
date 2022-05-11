<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
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
    }
}

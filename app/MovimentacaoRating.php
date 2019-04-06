<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovimentacaoRating extends Model
{
    public function torneio() {
        return $this->belongsTo("App\Torneio","torneio_id","id");
    }
    public function inscricao() {
        return $this->belongsTo("App\Inscricao","inscricao_id","id");
    }
    public function tipo_rating() {
        return $this->belongsTo("App\TipoRating","tipo_ratings_id","id");
    }
    public function rating() {
        return $this->belongsTo("App\Rating","ratings_id","id");
    }
}

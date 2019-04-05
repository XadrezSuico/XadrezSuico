<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    public function enxadrista(){
        return $this->belongsTo("App\Enxadrista","enxadrista_id","id");
    }
    public function tipo_rating(){
        return $this->belongsTo("App\TipoRating","tipo_ratings_id","id");
    }
    public function movimentacoes(){
        return $this->hasMany("App\MovimentacaoRating","ratings_id","id");
    }

    public function calcular(){
        $this->valor = 0;
        foreach($this->movimentacoes->all() as $movimentacao){
            $this->valor += $movimentacao->valor;
        }
        $this->save();
    }
}

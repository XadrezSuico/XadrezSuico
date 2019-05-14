<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pontuacao extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'pontuacao';

    public static function getPontuacaoByEvento($evento_id,$posicao){
        $pontuacao = Pontuacao::where([
            ["evento_id","=",$evento_id],
            ["posicao","=",$posicao]
        ])->first();
        if($pontuacao) return $pontuacao->pontuacao;
        return NULL;
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Pontuacao extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];
    
    
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'pontuacao';

    public static function getPontuacaoByEvento($evento_id,$posicao){
        $evento = Evento::find($evento_id);
        $pontuacao = Pontuacao::where([
            ["evento_id","=",$evento_id],
            ["posicao","=",$posicao]
        ])->first();
        if(!$pontuacao){
            $pontuacao = Pontuacao::where([
                ["grupo_evento_id","=",$evento->grupo_evento_id],
                ["posicao","=",$posicao]
            ])->first();
        }
        if($pontuacao) return $pontuacao->pontuacao;
        return NULL;
    }
}

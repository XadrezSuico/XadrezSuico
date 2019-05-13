<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\InscricaoCriterioDesempate;

class CriterioDesempate extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'criterio_desempate';

    // public static function isCriterio($tag){
    //     $criterio = CriterioDesempate::where()
    // }

    public static function criterios_evento(){
        return CriterioDesempate::where([["is_geral","=",false]]);
    }
    public static function criterios_grupo_evento(){
        return CriterioDesempate::where([["is_geral","=",true]]);
    }

    public function valor_criterio($inscrito_id){
        $desempate = InscricaoCriterioDesempate::where([
            ["criterio_desempate_id","=",$this->id],
            ["inscricao_id","=",$inscrito_id],
        ])->first();
        if($desempate) return $desempate;
        return false;
    }

    public function sort_desempate($inscrito_a, $inscrito_b){
        $desempate_a = $this->valor_criterio($inscrito_a->id);
        $desempate_b = $this->valor_criterio($inscrito_b->id);
        // echo $desempate_a, $desempate_b;
        if($desempate_a->valor < $desempate_b->valor){
            return 1;
        }elseif($desempate_a->valor > $desempate_b->valor){
            return -1;
        }
        return 0;
    }
}

<?php

namespace App;

use App\InscricaoCriterioDesempate;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class CriterioDesempate extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'criterio_desempate';

    // public static function isCriterio($tag){
    //     $criterio = CriterioDesempate::where()
    // }

    public static function criterios_evento()
    {
        return CriterioDesempate::where([["is_geral", "=", false]]);
    }
    public static function criterios_grupo_evento()
    {
        return CriterioDesempate::where([["is_geral", "=", true]]);
    }

    public function valor_criterio($inscrito_id)
    {
        $desempate = InscricaoCriterioDesempate::where([
            ["criterio_desempate_id", "=", $this->id],
            ["inscricao_id", "=", $inscrito_id],
        ])->first();
        if ($desempate) {
            return $desempate;
        }

        return false;
    }
    public function valor_criterio_geral($enxadrista_id, $grupo_evento_id, $categoria_id)
    {
        return $this->valor_desempate_geral($enxadrista_id, $grupo_evento_id, $categoria_id);
    }

    public function valor_desempate_geral($enxadrista_id, $grupo_evento_id, $categoria_id)
    {
        // echo $enxadrista_id, $grupo_evento_id, $categoria_id,$this->id;
        $desempate = EnxadristaCriterioDesempateGeral::where([
            ["enxadrista_id", "=", $enxadrista_id],
            ["grupo_evento_id", "=", $grupo_evento_id],
            ["categoria_id", "=", $categoria_id],
            ["criterio_desempate_id", "=", $this->id],
        ])->first();
        if ($desempate) {
            return $desempate;
        }

        return false;
    }

    public function sort_desempate($inscrito_a, $inscrito_b)
    {
        $desempate_a = $this->valor_criterio($inscrito_a->id);
        $desempate_b = $this->valor_criterio($inscrito_b->id);
        // echo $desempate_a, $desempate_b;
        if ($desempate_a && !$desempate_b) {
            return 1;
        } elseif (!$desempate_a && $desempate_b) {
            return 0;
        } elseif ($desempate_a && $desempate_b) {

            if ($desempate_a->valor < $desempate_b->valor) {
                return 1;
            } elseif ($desempate_a->valor > $desempate_b->valor) {
                return -1;
            }

        }
        return 0;
    }

    public function sort_desempate_geral($pontuacao_a, $pontuacao_b)
    {
        $desempate_a = $this->valor_criterio_geral($pontuacao_a->enxadrista->id, $pontuacao_a->grupo_evento_id, $pontuacao_a->categoria_id);
        $desempate_b = $this->valor_criterio_geral($pontuacao_b->enxadrista->id, $pontuacao_b->grupo_evento_id, $pontuacao_b->categoria_id);
        // echo $desempate_a, $desempate_b;
        if ($desempate_a->valor < $desempate_b->valor) {
            return 1;
        } elseif ($desempate_a->valor > $desempate_b->valor) {
            return -1;
        }
        return 0;
    }
}

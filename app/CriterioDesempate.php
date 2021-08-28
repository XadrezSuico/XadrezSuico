<?php

namespace App;

use App\InscricaoCriterioDesempate;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

use Log;

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
    public function valor_criterio_visualizacao($inscrito_id)
    {
        $desempate = $this->valor_criterio($inscrito_id);
        if ($desempate) {
            switch($this->internal_code){
                case "TT3_1":
                    switch($desempate->valor){
                        case 2.0:
                            return "1º";
                            break;
                        case 1.0:
                            return "2º";
                            break;
                        default:
                            return "-";
                    }
                    break;
                case "TT3_2":
                    switch($desempate->valor){
                        case 2.0:
                            return "3º";
                            break;
                        case 1.0:
                            return "4º";
                            break;
                        default:
                            return "-";
                    }
                    break;
                default:
                    return $desempate;
            }
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
        Log::debug("Comparação de critérios (".$this->name.") entre ".$inscrito_a->id." e ".$inscrito_b->id);
        if ($desempate_a && !$desempate_b) {
            Log::debug("critério B não existe");
            return 1;
        } elseif (!$desempate_a && $desempate_b) {
            Log::debug("critério A não existe");
            return 0;
        } elseif ($desempate_a && $desempate_b) {

            if ($desempate_a->valor < $desempate_b->valor) {
            Log::debug("critério A < B");
                return 1;
            } elseif ($desempate_a->valor > $desempate_b->valor) {
                Log::debug("critério A > B");
                return -1;
            }

        }
        Log::debug("critérios não encontrados");
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

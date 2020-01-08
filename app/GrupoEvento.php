<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use App\CriterioDesempateGrupoEventoGeral;

class GrupoEvento extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];
    
    
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'grupo_evento';

    public function eventos() {
        return $this->hasMany("App\Evento","grupo_evento_id","id");
    }

    public function torneios() {
        return $this->torneios_template();
    }

    public function torneios_template() {
        return $this->hasMany("App\TorneioTemplate","grupo_evento_id","id");
    }

    public function categorias() {
        return $this->hasMany("App\Categoria","grupo_evento_id","id");
    }

    public function criterios() {
        return $this->hasMany("App\CriterioDesempateGrupoEvento","grupo_evento_id","id");
    }

    public function criterios_gerais() {
        return $this->hasMany("App\CriterioDesempateGrupoEventoGeral","grupo_evento_id","id");
    }

    public function tipo_rating() {
        return $this->hasOne("App\TipoRatingGrupoEvento","grupo_evento_id","id");
    }

    public function pontuacoes() {
        return $this->hasMany("App\Pontuacao","grupo_evento_id","id");
    }

    // public function campos() {
    //     return $this->hasMany("App\CampoPersonalizadoGrupoEvento","grupo_evento_id","id");
    // }

    public function campos() {
        return $this->hasMany("App\CampoPersonalizado","grupo_evento_id","id");
    }



    public function isDeletavel(){
        if($this->id != null){
            if(
                $this->eventos()->count() > 0 ||
                $this->torneios()->count() > 0 ||
                $this->categorias()->count() > 0 ||
                $this->criterios()->count() > 0 ||
                $this->criterios_gerais()->count() > 0 ||
                $this->tipo_rating()->count() > 0 ||
                $this->pontuacoes()->count() > 0
            ){
                return false;
            }
            return true;
        }else{
            return false;
        }
    }

    public function getCriteriosGerais(){
        return $this->getCriteriosDesempateGerais();
    }

    public function getCriteriosDesempateGerais(){
        return CriterioDesempateGrupoEventoGeral::where([
            ["grupo_evento_id","=",$this->id]
        ])
        ->whereHas("criterio",function($q1){
            $q1->where([
                ["is_geral","=",true]
            ]);
        })
        ->orderBy("prioridade","ASC")
        ->get();
    }

    public function getEventosClassificacaoGeralPublica(){
        return Evento::where([
            ["grupo_evento_id","=",$this->id],
            ["mostrar_resultados_final","=",true],
        ])
        ->orderBy("data_inicio","ASC")
        ->get();
    }
}

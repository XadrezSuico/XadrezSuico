<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GrupoEvento extends Model
{
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
        return $this->hasMany("App\TorneioTemplateGrupoEvento","grupo_evento_id","id");
    }

    public function categorias() {
        return $this->hasMany("App\CategoriaGrupoEvento","grupo_evento_id","id");
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
}

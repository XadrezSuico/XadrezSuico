<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TorneioTemplate extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'torneio_template';

    public function categorias() {
        return $this->hasMany("App\CategoriaTorneioTemplate","torneio_template_id","id");
    }

    public function torneios() {
        return $this->hasMany("App\Torneio","torneio_template_id","id");
    }

    public function grupos_evento() {
        return $this->hasMany("App\TorneioTemplateGrupoEvento","torneio_template_id","id");
    }



    public function isDeletavel(){
        if($this->id != null){
            if(
                $this->categorias()->count() > 0 ||
                $this->torneios()->count() > 0 ||
                $this->grupos_evento()->count() > 0
            ){
                return false;
            }
            return true;
        }else{
            return false;
        }
    }
}

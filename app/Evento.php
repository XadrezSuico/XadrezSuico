<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DateTime;

class Evento extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'evento';

    public function grupo_evento() {
        return $this->belongsTo("App\GrupoEvento","grupo_evento_id","id");
    }

    public function cidade() {
        return $this->belongsTo("App\Cidade","cidade_id","id");
    }

    public function categorias() {
        return $this->hasMany("App\CategoriaEvento","evento_id","id");
    }

    public function torneios() {
        return $this->hasMany("App\Torneio","evento_id","id");
    }
    
    public function getDataInicio(){
        $datetime = DateTime::createFromFormat('Y-m-d', $this->data_inicio);
        if($datetime){
            return $datetime->format("d/m/Y");
        }else
            return false;
    }
    
    public function getDataFim(){
        $datetime = DateTime::createFromFormat('Y-m-d', $this->data_fim);
        if($datetime){
            return $datetime->format("d/m/Y");
        }else
            return false;
    }
}

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
    
    public function getDataFimInscricoesOnline(){
        $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $this->data_limite_inscricoes_abertas);
        if($datetime){
            return $datetime->format("d/m/Y H:i");
        }else
            return false;
    }
    
    public function inscricoes_encerradas($api = false){
        $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $this->data_limite_inscricoes_abertas);
        if($datetime){
            if($api){
                if($datetime->getTimestamp()+(60*5) >= time()){
                    return false;
                }else{
                    return true;
                }
            }
            if($datetime->getTimestamp() >= time()){
                return false;
            }else{
                return true;
            }
        }else
            return false;
    }

    

    
    public function isDeletavel(){
        if($this->id != null){
            if($this->categorias()->count() > 0 || $this->torneios()->count() > 0){
                return false;
            }
            return true;
        }else{
            return false;
        }
    }
}

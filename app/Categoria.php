<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'categoria';


    public function grupos_evento(){
        return $this->hasMany("App\CategoriaGrupoEvento","categoria_id","id");
    }

    public function torneios_template(){
        return $this->hasMany("App\CategoriaTorneioTemplate","categoria_id","id");
    }

    public function inscricoes(){
        return $this->hasMany("App\Inscricao","categoria_id","id");
    }

    public function torneios(){
        return $this->hasMany("App\CategoriaTorneio","categoria_id","id");
    }

    public function eventos(){
        return $this->hasMany("App\CategoriaEvento","categoria_id","id");
    }

    public function isDeletavel(){
        if($this->id != null){
            if(
                $this->grupos_evento()->count() > 0 ||
                $this->torneios_template()->count() > 0 ||
                $this->inscricoes()->count() > 0 ||
                $this->torneios()->count() > 0 ||
                $this->eventos()->count() > 0
            ){
                return false;
            }
            return true;
        }else{
            return false;
        }
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CampoPersonalizado extends Model
{
    public function eventos(){
        return $this->hasMany("App\CampoPersonalizadoEvento","campo_personalizados_id","id");
    }
    public function grupos_evento(){
        return $this->hasMany("App\CampoPersonalizadoGrupoEvento","campo_personalizados_id","id");
    }
    public function opcoes(){
        return $this->hasMany("App\Opcao","campo_personalizados_id","id");
    }
    public function respostas(){
        return $this->hasMany("App\CampoPersonalizadoOpcaoInscricao","campo_personalizados_id","id");
    }
    public function inscricoes(){
        return $this->respostas();
    }

    public function isDeletavel(){
        if($this->id != null){
            if(
                $this->eventos()->count() > 0 ||
                $this->opcoes()->count() > 0 ||
                $this->respostas()->count() > 0
            ){
                return false;
            }
            return true;
        }else{
            return false;
        }
    }
}

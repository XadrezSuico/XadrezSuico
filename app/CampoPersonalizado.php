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
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CampoPersonalizadoOpcaoInscricao extends Model
{
    public function campo(){
        return $this->belongsTo("App\CampoPersonalizado","campo_personalizados_id", "id");
    }
    public function opcao(){
        return $this->belongsTo("App\Opcao","opcaos_id", "id");
    }
    public function inscricao(){
        return $this->belongsTo("App\Inscricao","inscricao_id", "id");
    }
}

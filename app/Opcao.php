<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Opcao extends Model
{
    public function inscricoes(){
        return $this->belongsTo("App\CampoPersonalizadoOpcaoInscricao","opcaos_id", "id");
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CampoPersonalizadoGrupoEvento extends Model
{
    public function campo(){
        return $this->belongsTo("App\CampoPersonalizado","campo_personalizados_id", "id");
    }
    public function grupo_evento(){
        return $this->belongsTo("App\GrupoEvento","grupo_eventos_id", "id");
    }
}

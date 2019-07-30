<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PerfilUser extends Model
{
    public function perfil(){
        return $this->belongsTo("App\Perfil","perfils_id","id");
    }
    public function user(){
        return $this->belongsTo("App\User","users_id","id");
    }
    public function evento(){
        return $this->belongsTo("App\Evento","evento_id","id");
    }
    public function grupo_evento(){
        return $this->belongsTo("App\GrupoEvento","grupo_evento_id","id");
    }
}

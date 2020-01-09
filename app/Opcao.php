<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Opcao extends Model
{
    public function inscricoes()
    {
        return $this->hasMany("App\CampoPersonalizadoOpcaoInscricao", "opcaos_id", "id");
    }
    public function campo()
    {
        return $this->belongsTo("App\CampoPersonalizado", "campo_personalizados_id", "id");
    }

    public function isDeletavel()
    {
        if ($this->id != null) {
            if (
                $this->inscricoes()->count() > 0
            ) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }
}

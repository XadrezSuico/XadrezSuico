<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CampoPersonalizadoEvento extends Model
{
    public function campo()
    {
        return $this->belongsTo("App\CampoPersonalizado", "campo_personalizados_id", "id");
    }
    public function evento()
    {
        return $this->belongsTo("App\Evento", "evento_id", "id");
    }
}

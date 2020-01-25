<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    public function pais()
    {
        return $this->belongsTo("App\Pais", "pais_id", "id");
    }

    public function cidades()
    {
        return $this->hasMany("App\Cidade", "estados_id", "id");
    }
}

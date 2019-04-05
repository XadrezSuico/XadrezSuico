<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CriterioDesempateEvento extends Model
{

    public function evento(){
        return $this->belongsTo("App\Evento","evento_id","id");
    }

    public function criterio(){
        return $this->belongsTo("App\CriterioDesempate","criterio_desempate_id","id");
    }
}

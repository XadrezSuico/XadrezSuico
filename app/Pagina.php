<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pagina extends Model
{
    public function evento() {
        return $this->belongsTo("App\Evento","evento_id","id");
    }
}

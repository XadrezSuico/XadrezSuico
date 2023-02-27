<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventConfig extends Model
{
    public function event(){
        return $this->belongsTo("App\Evento","evento_id","id");
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventGroupConfig extends Model
{
    public function event_group()
    {
        return $this->belongsTo("App\GrupoEvento", "grupo_evento_id", "id");
    }
}

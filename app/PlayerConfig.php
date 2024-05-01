<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlayerConfig extends Model
{
    public function player(){
        return $this->belongsTo("App\Enxadrista","enxadrista_id","id");
    }
}

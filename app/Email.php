<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    public function enxadrista()
    {
        return $this->belongsTo("App\Enxadrista", "enxadrista_id", "id");
    }
}

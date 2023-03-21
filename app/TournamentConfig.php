<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TournamentConfig extends Model
{
    public function tournament(){
        return $this->belongsTo("App\Torneio","torneio_id","id");
    }
}

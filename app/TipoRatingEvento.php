<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoRatingEvento extends Model
{
    public function evento(){
        return $this->belongsTo("App\Evento","evento_id","id");
    }
    public function tipo_rating(){
        return $this->belongsTo("App\TipoRating","tipo_ratings_id","id");
    }
}

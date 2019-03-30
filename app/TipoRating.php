<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoRating extends Model
{
    public function ratings(){
        return $this->hasMany("App\Rating","tipo_ratings_id","id");
    }
    public function regras(){
        return $this->hasMany("App\TipoRatingRegras","tipo_ratings_id","id");
    }
}

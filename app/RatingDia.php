<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RatingDia extends Model
{
    public function rating(){
        return $this->belongsTo("App\Rating","ratings_id","id");
    }
}

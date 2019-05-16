<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class TipoRatingEvento extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];
    
    
    public function evento(){
        return $this->belongsTo("App\Evento","evento_id","id");
    }
    public function tipo_rating(){
        return $this->belongsTo("App\TipoRating","tipo_ratings_id","id");
    }
}

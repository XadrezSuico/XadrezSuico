<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class TipoRating extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];
    
    
    public function ratings(){
        return $this->hasMany("App\Rating","tipo_ratings_id","id");
    }
    public function regras(){
        return $this->hasMany("App\TipoRatingRegras","tipo_ratings_id","id");
    }
}

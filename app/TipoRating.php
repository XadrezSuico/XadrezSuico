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

    public function regraIdade($idade,$evento){
        $rating_regra = $this->regras()->where(function($q1) use ($idade){
            $q1->where([
                ["idade_minima","<=",$idade],
                ["idade_maxima","=",NULL]
            ]);
            $q1->orWhere([
                ["idade_minima","=",NULL],
                ["idade_maxima",">=",$idade]
            ]);
            $q1->orWhere([
                ["idade_minima","<=",$idade],
                ["idade_maxima",">=",$idade]
            ]);
        })
        ->first();
        return $rating_regra;
    }

    public function showRatingRegraIdade($idade,$evento){
        $rating_regra = $this->regraIdade($idade,$evento);
        if($rating_regra){
            return $rating_regra->inicial;
        }
        return 0;
    }

    public function isDeletavel(){
        if($this->id != null){
            if(
                $this->ratings()->count() > 0 ||
                $this->regras()->count() > 0
            ){
                return false;
            }
            return true;
        }else{
            return false;
        }
    }
}

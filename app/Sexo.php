<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Sexo extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];
    
    
    public function enxadristas() {
        return $this->hasMany("App\Enxadrista","sexos_id","id");
    }



    public function isDeletavel(){
        if($this->id != null){
            if(
                $this->enxadristas()->count() > 0
            ){
                return false;
            }
            return true;
        }else{
            return false;
        }
    }
}

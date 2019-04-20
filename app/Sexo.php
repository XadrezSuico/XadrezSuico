<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sexo extends Model
{
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

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cidade extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'cidade';

    public function enxadristas() {
        return $this->hasMany("App\Enxadrista","cidade_id","id");
    }

    public function inscricoes() {
        return $this->hasMany("App\Inscricao","cidade_id","id");
    }

    public function clubes() {
        return $this->hasMany("App\Clube","cidade_id","id");
    }

    public function eventos() {
        return $this->hasMany("App\Evento","cidade_id","id");
    }



    public function isDeletavel(){
        if($this->id != null){
            if(
                $this->clubes()->count() > 0 ||
                $this->enxadristas()->count() > 0 ||
                $this->inscricoes()->count() > 0 ||
                $this->eventos()->count() > 0
            ){
                return false;
            }
            return true;
        }else{
            return false;
        }
    }

    public function getName(){
        return mb_strtoupper($this->name);
    }
}

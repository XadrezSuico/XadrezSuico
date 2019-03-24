<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DateTime;

class Enxadrista extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'enxadrista';

    
    public function cidade(){
        return $this->belongsTo("App\Cidade","cidade_id","id");
    }
    public function clube(){
        return $this->belongsTo("App\Clube","clube_id","id");
    }
    public function inscricoes() {
        return $this->hasMany("App\Inscricao","enxadrista_id","id");
    }


    public function setBorn($born){
        $datetime = DateTime::createFromFormat('d/m/Y', $born);
        if($datetime){
            $this->born = $datetime->format("Y-m-d");
            return true;
        }else
            return false;
    }
    public function getBorn(){
        $datetime = DateTime::createFromFormat('Y-m-d', $this->born);
        if($datetime){
            return $datetime->format("d/m/Y");
        }else
            return false;
    }
    public function setBornFromSM($born){
        $datetime = DateTime::createFromFormat('d.m.Y', $born);
        if($datetime){
            $this->born = $datetime->format("Y-m-d");
            return true;
        }else
            return false;
    }
    public function getBornToSM(){
        $datetime = DateTime::createFromFormat('Y-m-d', $this->born);
        if($datetime){
            return $datetime->format("d.m.Y");
        }else
            return false;
    }

    public function howOld(){
        $datetime = DateTime::createFromFormat('Y-m-d', $this->born);
        if($datetime){
            return date("Y") - $datetime->format("Y");
        }else
            return false;
    }

    public function estaInscrito($evento_id){
        $enxadrista = $this;
        if($this->whereHas("inscricoes",function($q1) use ($evento_id,$enxadrista) {
            $q1->where([["enxadrista_id","=",$enxadrista->id]]);
            $q1->whereHas("torneio",function($q2) use ($evento_id,$enxadrista) {
                $q2->where([["evento_id","=",$evento_id]]);
            });
        })->count() > 0){
            return true;
        }
        return false;
    }
}

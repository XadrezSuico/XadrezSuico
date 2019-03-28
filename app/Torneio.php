<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Torneio extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'torneio';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id'
    ];



    public function template() {
        return $this->belongsTo("App\TorneioTemplate","torneio_template_id","id");
    }
    public function evento() {
        return $this->belongsTo("App\Evento","evento_id","id");
    }
    public function categorias() {
        return $this->hasMany("App\CategoriaTorneio","torneio_id","id");
    }
    public function inscricoes() {
        return $this->hasMany("App\Inscricao","torneio_id","id");
    }
    
    public function isDeletavel(){
        if($this->id != null){
            if($this->categorias()->count() > 0 || $this->inscricoes()->count() > 0){
                return false;
            }
            return true;
        }else{
            return false;
        }
    }
}

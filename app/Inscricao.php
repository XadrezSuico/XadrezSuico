<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inscricao extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'inscricao';

    public function enxadrista() {
        return $this->belongsTo("App\Enxadrista","enxadrista_id","id");
    }

    public function torneio() {
        return $this->belongsTo("App\Torneio","torneio_id","id");
    }

    public function categoria() {
        return $this->belongsTo("App\Categoria","categoria_id","id");
    }

    public function cidade() {
        return $this->belongsTo("App\Cidade","cidade_id","id");
    }

    public function clube() {
        return $this->belongsTo("App\Clube","clube_id","id");
    }
    
    public function isDeletavel(){
        if($this->id != null){
            return true;
        }else{
            return false;
        }
    }
}

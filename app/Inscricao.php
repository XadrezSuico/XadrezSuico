<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inscricao extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'inscricao';

    public function torneio() {
        return $this->belongsTo("App\Torneio","torneio_id","id");
    }

    public function categoria() {
        return $this->belongsTo("App\Categoria","categoria_id","id");
    }
}

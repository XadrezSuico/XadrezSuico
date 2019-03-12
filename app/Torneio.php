<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Torneio extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'torneio';

    public function categorias() {
        return $this->hasMany("App\CategoriaTorneio","torneio_id","id");
    }
    public function inscricoes() {
        return $this->hasMany("App\Inscricao","torneio_id","id");
    }
}

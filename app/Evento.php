<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'evento';

    public function categorias() {
        return $this->hasMany("App\CategoriaEvento","evento_id","id");
    }

    public function torneios() {
        return $this->hasMany("App\Torneio","evento_id","id");
    }
}

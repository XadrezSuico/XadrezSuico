<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoriaEvento extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'categoria_evento';

    public function categoria() {
        return $this->belongsTo("App\Categoria","categoria_id","id");
    }

    public function evento() {
        return $this->belongsTo("App\Evento","evento_id","id");
    }
}

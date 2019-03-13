<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GrupoEvento extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'grupo_evento';

    public function eventos() {
        return $this->hasMany("App\Evento","grupo_evento_id","id");
    }

    public function torneios() {
        return $this->hasMany("App\TorneioTemplateGrupoEvento","grupo_evento_id","id");
    }

    public function categorias() {
        return $this->hasMany("App\CategoriaGrupoEvento","grupo_evento_id","id");
    }
}

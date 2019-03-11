<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoriaGrupoEvento extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'categoria_grupo_evento';
}

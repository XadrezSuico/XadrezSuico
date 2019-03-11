<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CriterioDesempateGrupoEvento extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'criterio_desempate_grupo_evento';
}

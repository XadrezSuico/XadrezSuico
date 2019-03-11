<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TorneioTemplateGrupoEvento extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'torneio_template_grupo_evento';
}

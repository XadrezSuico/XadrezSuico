<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CriterioDesempateGrupoEventoGeral extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'criterio_desempate_grupo_evento_geral';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'criterio_desempate_id', 'grupo_evento_id'
    ];
}

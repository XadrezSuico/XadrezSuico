<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CriterioDesempateGrupoEvento extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'criterio_desempate_grupo_evento';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'criterio_desempate_id', 'grupo_evento_id'
    ];

    public function grupo_evento(){
        return $this->belongsTo("App\GrupoEvento","grupo_evento_id","id");
    }

    public function criterio(){
        return $this->belongsTo("App\CriterioDesempate","criterio_desempate_id","id");
    }
}

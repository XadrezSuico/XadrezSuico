<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TorneioTemplateGrupoEvento extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'torneio_template_grupo_evento';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'torneio_template_id', 'grupo_evento_id'
    ];

    public function template() {
        return $this->belongsTo("App\TorneioTemplate","torneio_template_id","id");
    }

    public function grupo_evento() {
        return $this->belongsTo("App\GrupoEvento","grupo_evento_id","id");
    }
}

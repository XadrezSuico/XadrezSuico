<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoriaGrupoEvento extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'categoria_grupo_evento';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'categoria_id', 'grupo_evento_id'
    ];

    public function categoria() {
        return $this->belongsTo("App\Categoria","categoria_id","id");
    }

    public function grupo_evento() {
        return $this->belongsTo("App\GrupoEvento","grupo_evento_id","id");
    }
}

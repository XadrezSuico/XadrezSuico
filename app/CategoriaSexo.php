<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoriaSexo extends Model
{    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'categoria_id', 'torneio_id'
    ];

    public function categoria() {
        return $this->belongsTo("App\Categoria","categoria_id","id");
    }

    public function sexo() {
        return $this->belongsTo("App\Sexo","sexos_id","id");
    }
}

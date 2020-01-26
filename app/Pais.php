<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
    public $timestamps = false;

    public function estados()
    {
        return $this->hasMany("App\Estado", "pais_id", "id");
    }
    public function tipo_documentos()
    {
        return $this->hasMany("App\TipoDocumentoPais", "pais_id", "id");
    }
}

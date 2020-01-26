<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    public function documentos()
    {
        return $this->hasMany("App\Documento", "tipo_documentos_id", "id");
    }
    public function paises()
    {
        return $this->hasMany("App\TipoDocumentoPais", "tipo_documentos_id", "id");
    }
}

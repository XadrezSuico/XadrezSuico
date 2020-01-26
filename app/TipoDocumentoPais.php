<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoDocumentoPais extends Model
{
    public function pais()
    {
        return $this->belongsTo("App\Pais", "pais_id", "id");
    }
    public function tipo_documento()
    {
        return $this->belongsTo("App\TipoDocumento", "tipo_documentos_id", "id");
    }
}

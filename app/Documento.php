<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    public function tipo_documento()
    {
        return $this->belongsTo("App\TipoDocumento", "tipo_documentos_id", "id");
    }
    public function enxadrista()
    {
        return $this->belongsTo("App\Enxadrista", "enxadrista_id", "id");
    }
}

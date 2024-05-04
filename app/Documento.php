<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Documento extends Model
{
    public static function boot()
    {
        parent::boot();

        self::creating(function($model){
            if(Documento::where([
                ["tipo_documentos_id","=",$model->tipo_documentos_id],
                ["enxadrista_id", "=", $model->enxadrista_id],
            ])->count() > 0){
                Log::debug("Enxadrista ID {$model->enxadrista_id} já possui documento de Tipo {$model->tipo_documentos_id} - Não é possível inserir um novo");
                return false;
            }
        });

    }
    public function tipo_documento()
    {
        return $this->belongsTo("App\TipoDocumento", "tipo_documentos_id", "id");
    }
    public function enxadrista()
    {
        return $this->belongsTo("App\Enxadrista", "enxadrista_id", "id");
    }
}

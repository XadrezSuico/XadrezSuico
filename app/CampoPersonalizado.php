<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class CampoPersonalizado extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];

    public function evento()
    {
        return $this->belongsTo("App\CampoPersonalizado", "evento_id", "id");
    }
    public function grupo_evento()
    {
        return $this->belongsTo("App\CampoPersonalizado", "grupo_evento_id", "id");
    }
    public function eventos()
    {
        return $this->hasMany("App\CampoPersonalizadoEvento", "campo_personalizados_id", "id");
    }
    public function opcoes()
    {
        return $this->hasMany("App\Opcao", "campo_personalizados_id", "id");
    }
    public function respostas()
    {
        return $this->hasMany("App\CampoPersonalizadoOpcaoInscricao", "campo_personalizados_id", "id");
    }
    public function inscricoes()
    {
        return $this->respostas();
    }

    public function isDeletavel()
    {
        if ($this->id != null) {
            if (
                $this->eventos()->count() > 0 ||
                $this->opcoes()->count() > 0 ||
                $this->respostas()->count() > 0
            ) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

    public function toAPIObject(){
        return [
            "id" => $this->id,
            "public_name" => $this->public_name,
        ];
    }
}

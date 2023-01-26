<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class CampoPersonalizadoOpcaoInscricao extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];

    public function campo()
    {
        return $this->belongsTo("App\CampoPersonalizado", "campo_personalizados_id", "id");
    }
    public function opcao()
    {
        return $this->belongsTo("App\Opcao", "opcaos_id", "id");
    }
    public function inscricao()
    {
        return $this->belongsTo("App\Inscricao", "inscricao_id", "id");
    }

    public function toAPIObject(){
        return [
            "id" => $this->id,
            "public_name" => $this->public_name,
            "value" => $this->opcao->response,
        ];
    }
}

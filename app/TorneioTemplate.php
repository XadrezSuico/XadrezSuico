<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class TorneioTemplate extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'torneio_template';

    public function grupo_evento()
    {
        return $this->belongsTo("App\GrupoEvento", "grupo_evento_id", "id");
    }

    public function tipo()
    {
        return $this->belongsTo("App\TipoTorneio", "tipo_torneio_id", "id");
    }

    public function categorias()
    {
        return $this->hasMany("App\CategoriaTorneioTemplate", "torneio_template_id", "id");
    }

    public function torneios()
    {
        return $this->hasMany("App\Torneio", "torneio_template_id", "id");
    }

    public function isDeletavel()
    {
        if ($this->id != null) {
            if (
                $this->categorias()->count() > 0 ||
                $this->torneios()->count() > 0
            ) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }
}

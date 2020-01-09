<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class CriterioDesempateEvento extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];

    public function evento()
    {
        return $this->belongsTo("App\Evento", "evento_id", "id");
    }

    public function criterio()
    {
        return $this->belongsTo("App\CriterioDesempate", "criterio_desempate_id", "id");
    }
}

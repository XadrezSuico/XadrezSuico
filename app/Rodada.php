<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;


class Rodada extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];

    public function torneio()
    {
        return $this->belongsTo("App\Torneio", "torneio_id", "id");
    }
    public function emparceiramentos()
    {
        return $this->hasMany("App\Emparceiramento", "rodadas_id", "id");
    }
    public function armageddons()
    {
        return $this->hasMany("App\Emparceiramento", "armageddon_rodadas_id", "id");
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class TipoTorneio extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'tipo_torneio';

    public function torneios()
    {
        return $this->hasMany("App\Torneio", "tipo_torneio_id", "id");
    }

    public function templates()
    {
        return $this->hasMany("App\TorneioTemplate", "tipo_torneio_id", "id");
    }

    public function isSwiss(){
        if($this->name == "Suíço") return true;
        return false;
    }

    public function usaPontuacao(){
        if($this->id == 3) return false;
        return true;
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;


class Emparceiramento extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];

    public function rodada()
    {
        return $this->belongsTo("App\Rodada", "rodadas_id", "id");
    }

    public function inscricao_A()
    {
        return $this->belongsTo("App\Inscricao", "inscricao_a", "id");
    }

    public function inscricao_B()
    {
        return $this->belongsTo("App\Inscricao", "inscricao_b", "id");
    }

    public function armageddon_rodada()
    {
        return $this->belongsTo("App\Rodada", "armageddon_rodadas_id", "id");
    }

    public function armageddon_emparceiramento()
    {
        return $this->belongsTo("App\Emparceiramento", "armageddon_emparceiramentos_id", "id");
    }

    public function armageddons()
    {
        return $this->hasMany("App\Emparceiramento", "armageddon_emparceiramentos_id", "id");
    }

    public function getResultadoA(){
        if($this->resultado_a){
            return $this->resultado_a;
        }
        return 0;
    }
    public function getResultadoB(){
        if($this->resultado_b){
            return $this->resultado_b;
        }
        return 0;
    }

}

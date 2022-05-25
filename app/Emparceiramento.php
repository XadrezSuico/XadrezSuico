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
            if($this->resultado_a == 1.0){
                return (!$this->is_wo_b) ? $this->resultado_a : "+";
            }else if($this->resultado_a == 0.5){
                return $this->resultado_a;
            }else{
                return (!$this->is_wo_a) ? $this->resultado_a : "-";
            }
        }else{
            return (!$this->is_wo_a) ? 0 : "-";
        }
        return 0;
    }
    public function getResultadoB(){
        if($this->resultado_b){
            if($this->resultado_b == 1.0){
                return (!$this->is_wo_a) ? $this->resultado_b : "+";
            }else if($this->resultado_b == 0.5){
                return $this->resultado_b;
            }else{
                return (!$this->is_wo_b) ? $this->resultado_b : "-";
            }
        }else{
            return (!$this->is_wo_b) ? 0 : "-";
        }
        return 0;
    }

    public function hasArmageddonsAproved(){
        foreach($this->armageddons->all() as $armageddon){
            if(is_int($armageddon->resultado)){
                return true;
            }
        }
        return false;
    }
}

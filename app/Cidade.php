<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Cidade extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'cidade';

    public function estado()
    {
        return $this->belongsTo("App\Estado", "estados_id", "id");
    }

    public function enxadristas()
    {
        return $this->hasMany("App\Enxadrista", "cidade_id", "id");
    }

    public function inscricoes()
    {
        return $this->hasMany("App\Inscricao", "cidade_id", "id");
    }

    public function clubes()
    {
        return $this->hasMany("App\Clube", "cidade_id", "id");
    }

    public function eventos()
    {
        return $this->hasMany("App\Evento", "cidade_id", "id");
    }

    public function isDeletavel()
    {
        if ($this->id != null) {
            if (
                $this->clubes()->count() > 0 ||
                $this->enxadristas()->count() > 0 ||
                $this->inscricoes()->count() > 0 ||
                $this->eventos()->count() > 0
            ) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

    public function getName()
    {
        if($this->estado){
            $estado = "";
            if($this->estado->abbr){
                 $estado = trim($this->estado->abbr);
            }else{
                 $estado = trim($this->estado->nome);
            }
            if($this->estado->pais){
                $pais = "";
                if($this->estado->pais->codigo_iso){
                    $pais = trim($this->estado->pais->codigo_iso);
                }else{
                    $pais = trim($this->estado->pais->nome);
                }
                return mb_strtoupper(trim($this->name) . "/" . $estado." - ".$pais);
            }else{
                return mb_strtoupper(trim($this->name) . "/" . $estado);
            }
        }else{
            return mb_strtoupper(trim($this->name));
        }
    }
}

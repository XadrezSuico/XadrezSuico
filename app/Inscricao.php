<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Inscricao extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];
    
    
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'inscricao';

    public function enxadrista() {
        return $this->belongsTo("App\Enxadrista","enxadrista_id","id");
    }

    public function torneio() {
        return $this->belongsTo("App\Torneio","torneio_id","id");
    }

    public function categoria() {
        return $this->belongsTo("App\Categoria","categoria_id","id");
    }

    public function cidade() {
        return $this->belongsTo("App\Cidade","cidade_id","id");
    }

    public function clube() {
        return $this->belongsTo("App\Clube","clube_id","id");
    }

    public function criterios_desempate() {
        return $this->hasMany("App\InscricaoCriterioDesempate","inscricao_id","id");
    }
    
    public function isDeletavel(){
        if($this->id != null){
            if($this->criterios_desempate()->count() == 0){
                return true;
            }
        }
        return false;
    }
}

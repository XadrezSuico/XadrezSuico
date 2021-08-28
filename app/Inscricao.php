<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Str;

use Log;


class Inscricao extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'inscricao';


    public static function boot()
    {
        parent::boot();

        self::creating(function($model){
            Log::debug("self::creating");
            if($model->uuid == NULL){
                Log::debug("generating uuid");
                $model->uuid = Str::uuid();
                Log::debug("uuid: ".$model->uuid);
            }
        });

        // self::created(function($model){
        //     // ... code here
        // });

        self::updating(function($model){
            Log::debug("self::updating");
            if($model->uuid == NULL){
                Log::debug("generating uuid");
                $model->uuid = Str::uuid();
                Log::debug("uuid: ".$model->uuid);
            }
        });

        // self::updated(function($model){
        //     // ... code here
        // });

        // self::deleting(function($model){
        //     // ... code here
        // });

        // self::deleted(function($model){
        //     // ... code here
        // });
    }

    public function enxadrista()
    {
        return $this->belongsTo("App\Enxadrista", "enxadrista_id", "id");
    }

    public function torneio()
    {
        return $this->belongsTo("App\Torneio", "torneio_id", "id");
    }

    public function categoria()
    {
        return $this->belongsTo("App\Categoria", "categoria_id", "id");
    }

    public function cidade()
    {
        return $this->belongsTo("App\Cidade", "cidade_id", "id");
    }

    public function clube()
    {
        return $this->belongsTo("App\Clube", "clube_id", "id");
    }

    public function emparceiramentos_a()
    {
        return $this->hasMany("App\Emparceiramento", "inscricao_a", "id");
    }

    public function emparceiramentos_b()
    {
        return $this->hasMany("App\Emparceiramento", "inscricao_b", "id");
    }

    public function criterios_desempate()
    {
        return $this->hasMany("App\InscricaoCriterioDesempate", "inscricao_id", "id");
    }

    public function opcoes()
    {
        return $this->hasMany("App\CampoPersonalizadoOpcaoInscricao", "inscricao_id", "id");
    }

    public function getOpcao($campo_personalizados_id)
    {
        $opcao = $this->opcoes()->where([["campo_personalizados_id", "=", $campo_personalizados_id]])->first();
        return $opcao;
    }

    public function getLichessProcessLink(){
        return url("/inscricao/".$this->uuid."/lichess");
    }

    public function isDeletavel()
    {
        if ($this->id != null) {
            if ($this->criterios_desempate()->count() == 0) {
                return true;
            }
        }
        return false;
    }

    public function getCreatedAt()
    {
        $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $this->created_at);
        if ($datetime) {
            return $datetime->format("d/m/Y H:i:s");
        }
        return false;
    }

    public function getEmparceiramentos()
    {
        $emparceiramentos = array();
        foreach($this->emparceiramentos_a->all() as $emparceiramento){
            $emparceiramentos[] = $emparceiramento;
        }
        foreach($this->emparceiramentos_b->all() as $emparceiramento){
            $emparceiramentos[] = $emparceiramento;
        }
        return $emparceiramentos;
    }
}

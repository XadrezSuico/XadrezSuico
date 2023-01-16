<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Str;


class Cidade extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'cidade';

    public static function boot()
    {
        parent::boot();

        // self::creating(function($model){
        //     if($model->uuid == NULL){
        //         $model->uuid = Str::uuid();
        //     }
        // });

        // self::created(function($model){
        //     // ... code here
        // });

        self::updating(function($model){
            if($model->uuid == NULL){
                $model->uuid = Str::uuid();
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

    public function export($type){
        switch($type){
            case "xadrezsuico":
                return $this->exportXadrezSuico();
        }

        return null;
    }

    public function exportXadrezSuico(){
        $obj = array();

        if($this->uuid == NULL){
            $this->generateUuid();
        }

        $obj["uuid"] = $this->uuid;
        $obj["name"] = $this->getName();

        return $obj;
    }

    public function generateUuid(){
        if($this->uuid == NULL){
            $this->uuid = Str::uuid();
            $this->save();
        }
    }


    public function toAPIObject($include_parent = false){
        if($include_parent){
            return [
                "id" => $this->id,
                "name" => $this->name,

                "state" => $this->estado->toAPIObject($include_parent),
                "state_id" => $this->estado->id,
            ];
        }
        return [
            "id" => $this->id,
            "name" => $this->name,

            "state_id" => $this->estado->id,
        ];
    }
}

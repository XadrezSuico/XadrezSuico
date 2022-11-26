<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Str;


class Rodada extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];

    public static function boot()
    {
        parent::boot();

        self::creating(function($model){
            if($model->uuid == NULL){
                $model->uuid = Str::uuid();
            }
        });

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


    public function isUltimaRodada(){
        $last_round = $this->torneio->rodadas()->orderBy("numero","DESC")->first();
        if($last_round->numero == $this->numero){
            return true;
        }
        return false;
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
        $obj["number"] = $this->numero;

        $obj["pairings"] = array();
        foreach($this->emparceiramentos->all() as $emparceiramento){
            $obj["pairings"][] = $emparceiramento->export("xadrezsuico");
        }

        return $obj;
    }

    public function generateUuid(){
        if($this->uuid == NULL){
            $this->uuid = Str::uuid();
            $this->save();
        }
    }
}

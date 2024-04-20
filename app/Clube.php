<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Str;


class Clube extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'clube';

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

    public function cidade()
    {
        return $this->belongsTo("App\Cidade", "cidade_id", "id");
    }

    public function enxadristas()
    {
        return $this->hasMany("App\Enxadrista", "clube_id", "id");
    }

    public function inscricoes()
    {
        return $this->hasMany("App\Inscricao", "clube_id", "id");
    }

    public function vinculos()
    {
        return $this->hasMany("App\Vinculo", "clube_id", "id");
    }

    public function team_scores()
    {
        return $this->hasMany("App\EventTeamScore", "clubs_id", "id");
    }

    public function isDeletavel()
    {
        if ($this->id != null) {
            if (
                $this->enxadristas()->count() > 0 ||
                $this->inscricoes()->count() > 0 ||
                $this->vinculos()->count() > 0
            ) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

    public function getFullName()
    {
        return $this->getPlace()." - ".$this->getName() ." (ID: ".$this->id.")";
    }
    public function getName()
    {
        if($this->abbr){
            return mb_strtoupper($this->name." [{$this->abbr}]");
        }
        return mb_strtoupper($this->name);
    }

    public function getPlace()
    {
        $retorno = "";
        if($this->cidade){
            $cidade = $this->cidade->name;
            if($this->cidade->estado){
                if($this->cidade->estado->pais){
                    if($this->cidade->estado->pais->codigo_iso){
                        $retorno .= trim($this->cidade->estado->pais->codigo_iso) . " - ";
                    }else{
                        $retorno .= trim($this->cidade->estado->pais->name) . " - ";
                    }
                }
                if($this->cidade->estado->abbr){
                    $retorno .= trim($cidade). "/" .trim($this->cidade->estado->abbr);
                }else{
                    $retorno .= trim($cidade). "/" .trim($this->cidade->estado->name);
                }
            }
        }
        return mb_strtoupper($retorno);
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
                "name" => $this->getFullName(),

                "city" => ($this->cidade) ? $this->cidade->toAPIObject($include_parent) : "",
                "city_id" => ($this->cidade) ? $this->cidade->id : "",
            ];
        }
        return [
            "id" => $this->id,
            "name" => $this->getFullName(),

            "city_id" => ($this->cidade) ? $this->cidade->id : "",
        ];
    }
}

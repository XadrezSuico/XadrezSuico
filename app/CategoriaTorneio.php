<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Str;

class CategoriaTorneio extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'categoria_torneio';

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

    public function categoria()
    {
        return $this->belongsTo("App\Categoria", "categoria_id", "id");
    }

    public function torneio()
    {
        return $this->belongsTo("App\Torneio", "torneio_id", "id");
    }

    public function getPremiados(){
        $quantos_premiam = 3;
        if($this->categoria){
            if($this->categoria->quantos_premiam != NULL){
                $quantos_premiam = $this->categoria->quantos_premiam;
            }
        }

        $premiados = array();
        $i = 1;
        foreach($this->torneio->inscricoes()->whereNotNull("posicao")->where([
            ["categoria_id","=",$this->categoria->id],
            ["is_desclassificado","=",false],
            ["desconsiderar_pontuacao_geral","=",false],
            ["desconsiderar_classificado","=",false],
        ])->orderBy("posicao","ASC")->limit($quantos_premiam)->get() as $premiado){
            if($i++ <= $quantos_premiam){
                $premiados[] = $premiado;
            }
        }
        return $premiados;
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
        if($this->categoria->uuid == NULL){
            $this->categoria->generateUuid();
        }

        $obj["uuid"] = $this->uuid;
        $obj["ge_uuid"] = $this->categoria->uuid;
        $obj["name"] = $this->categoria->name;
        $obj["abbr"] = $this->categoria->code;

        return $obj;
    }

    public function generateUuid(){
        if($this->uuid == NULL){
            $this->uuid = Str::uuid();
            $this->save();
        }
    }
}

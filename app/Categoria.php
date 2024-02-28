<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Str;

class Categoria extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'categoria';


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

    // AQUELA Categoria CLASSIFICA PARA ESTA Categoria
    public function classificadora()
    {
        return $this->belongsTo("App\Categoria", "categoria_classificadora_id", "id");
    }

    // ESTA Categoria CLASSIFICA PARA AQUELA Categoria
    public function classifica()
    {
        return $this->hasOne("App\Categoria", "categoria_classificadora_id", "id");
    }

    public function grupo_evento()
    {
        return $this->belongsTo("App\GrupoEvento", "grupo_evento_id", "id");
    }
    public function evento()
    {
        return $this->belongsTo("App\Evento", "evento_id", "id");
    }

    public function torneios_template()
    {
        return $this->hasMany("App\CategoriaTorneioTemplate", "categoria_id", "id");
    }

    public function sexos()
    {
        return $this->hasMany("App\CategoriaSexo", "categoria_id", "id");
    }

    public function inscricoes()
    {
        return $this->hasMany("App\Inscricao", "categoria_id", "id");
    }

    public function torneios()
    {
        return $this->hasMany("App\CategoriaTorneio", "categoria_id", "id");
    }

    public function eventos()
    {
        return $this->hasMany("App\CategoriaEvento", "categoria_id", "id");
    }

    public function event_classificators()
    {
        return $this->hasMany("App\Classification\EventClassificateCategory", "category_id", "id");
    }
    public function event_classificates()
    {
        return $this->hasMany("App\Classification\EventClassificateCategory", "category_classificator_id", "id");
    }

    public function getTorneioByEvento($evento)
    {
        $categoria = $this;
        if ($evento) {
            $torneio = Torneio::where([
                ["evento_id", "=", $evento->id],
            ])
                ->whereHas("categorias", function ($q1) use ($categoria) {
                    $q1->where([["categoria_id", "=", $categoria->id]]);
                })
                ->first();
            if ($torneio) {
                return $torneio;
            }
        }
        return false;
    }

    public function isDeletavel()
    {
        if ($this->id != null) {
            if (
                $this->torneios_template()->count() > 0 ||
                $this->inscricoes()->count() > 0 ||
                $this->torneios()->count() > 0 ||
                $this->eventos()->count() > 0
            ) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

    public function generateUuid(){
        if($this->uuid == NULL){
            $this->uuid = Str::uuid();
            $this->save();
        }
    }

    public function isPaid($event_id){
        if($this->eventos()->where([["evento_id","=",$event_id]])->whereNotNull("xadrezsuicopag_uuid")->count() > 0){
            return true;
        }
        return false;
    }

    public function getHowManyStandingPlaces(){
        return 3;
    }

    public function toAPIObject($include_parent = false){
        return [
            "id" => $this->id,
            "name" => $this->name,
        ];
    }
}

<?php

namespace App;

use App\Http\Controllers\InscricaoGerenciarController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
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

    public function getStartingRank($event_id){
        if($this->eventos()->where([["evento_id","=",$event_id]])->count() > 0){
            $event = $this->eventos()->where([["evento_id", "=", $event_id]])->first()->evento;
            $list = array();

            if($event->usa_fide){
                $fide_sequence = $event->getConfig("fide_sequence", true);
                if(!$fide_sequence){
                    $fide_sequence = false;
                }
            }

            foreach($this->inscricoes()->whereHas("torneio", function($q1) use ($event){
                $q1->where([["evento_id","=",$event->id]]);
            })->get() as $inscricao){
                $item = array();

                $item["registration"] = $inscricao;
                $item["ratings"] = array();

                $next_order = 0;
                if($event->usa_lbx){
                    $item["ratings"][$next_order++] = $inscricao->enxadrista->getRating(2,$event->tipo_modalidade);
                }elseif($event->usa_fide) {
                    $item["ratings"][$next_order++] = $inscricao->enxadrista->showRating(0, $event->tipo_modalidade, $fide_sequence);
                }

                if($event->tipo_rating){
                    $item["ratings"][$next_order++] = $inscricao->enxadrista->ratingParaEvento($event->id, true);
                }elseif($event->usa_cbx){
                    $item["ratings"][$next_order++] = $inscricao->enxadrista->getRating(1,$event->tipo_modalidade);
                }


                $list[] = $item;
            }

            usort($list, array("App\Categoria", "cmp_obj"));

            $i = 1;
            foreach($list as $k => $item){
                $list[$k]["position"] = $i++;
            }


            return $list;
        }

        return array();
    }


    public static function cmp_obj($item_a, $item_b)
    {
        if(count($item_a["ratings"]) > 0 || count($item_b["ratings"]) > 0){
            $total_ratings = count($item_a["ratings"]);
            if(count($item_b["ratings"]) > $total_ratings){
                $total_ratings = count($item_b["ratings"]);
            }
            Log::debug("Categoria::cmp_obj - total_ratings = {$total_ratings} (". count($item_a["ratings"]).",". count($item_b["ratings"])." - " . json_encode(array_keys($item_a["ratings"])) . "," . json_encode(array_keys($item_b["ratings"])) . ")");
            for($i = 0; $i < $total_ratings; $i++){
                if (isset($item_a["ratings"][$i]) && !isset($item_b["ratings"][$i])) {
                    return -1;
                } elseif (!isset($item_a["ratings"][$i]) && isset($item_b["ratings"][$i])) {
                    return 1;
                }elseif ($item_a["ratings"][$i] && !$item_b["ratings"][$i]) {
                    return -1;
                } elseif (!$item_a["ratings"][$i] && $item_b["ratings"][$i]) {
                    return 1;
                } elseif ($item_a["ratings"][$i] > $item_b["ratings"][$i]) {
                    return -1;
                } elseif ($item_a["ratings"][$i] < $item_b["ratings"][$i]) {
                    return 1;
                }
            }
        }

        return InscricaoGerenciarController::cmp_obj_alf($item_a["registration"], $item_b["registration"]);
    }

    public function toAPIObject($include_parent = false){
        return [
            "id" => $this->id,
            "name" => $this->name,
        ];
    }
}

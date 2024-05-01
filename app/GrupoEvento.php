<?php

namespace App;

use App\CriterioDesempateGrupoEventoGeral;
use App\Enum\ConfigType;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class GrupoEvento extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'grupo_evento';

    // AQUELE Grupo de Evento CLASSIFICA PARA ESTE Grupo de Evento
    public function classificador()
    {
        return $this->belongsTo("App\GrupoEvento", "grupo_evento_classificador_id", "id");
    }

    // ESTE Grupo de Evento CLASSIFICA PARA AQUELE Grupo de Evento
    public function classifica()
    {
        return $this->hasOne("App\GrupoEvento", "grupo_evento_classificador_id", "id");
    }

    // ESTE Grupo de Evento CLASSIFICA PARA AQUELE Evento
    public function evento_classifica()
    {
        return $this->hasOne("App\Evento", "grupo_evento_classificador_id", "id");
    }

    public function eventos()
    {
        return $this->hasMany("App\Evento", "grupo_evento_id", "id");
    }

    public function torneios()
    {
        return $this->torneios_template();
    }

    public function torneios_template()
    {
        return $this->hasMany("App\TorneioTemplate", "grupo_evento_id", "id");
    }

    public function categorias()
    {
        return $this->hasMany("App\Categoria", "grupo_evento_id", "id");
    }

    public function criterios()
    {
        return $this->hasMany("App\CriterioDesempateGrupoEvento", "grupo_evento_id", "id");
    }

    public function criterios_gerais()
    {
        return $this->hasMany("App\CriterioDesempateGrupoEventoGeral", "grupo_evento_id", "id");
    }

    public function tipo_rating()
    {
        return $this->hasOne("App\TipoRatingGrupoEvento", "grupo_evento_id", "id");
    }

    public function pontuacoes()
    {
        return $this->hasMany("App\Pontuacao", "grupo_evento_id", "id");
    }

    public function email_templates()
    {
        return $this->hasMany("App\EmailTemplate", "grupo_evento_id", "id");
    }
    public function pontuacoes_enxadrista()
    {
        return $this->hasMany("App\PontuacaoEnxadrista", "grupo_evento_id", "id");
    }

    // public function campos() {
    //     return $this->hasMany("App\CampoPersonalizadoGrupoEvento","grupo_evento_id","id");
    // }

    public function campos()
    {
        return $this->hasMany("App\CampoPersonalizado", "grupo_evento_id", "id");
    }
    public function event_team_awards(){
        return $this->hasMany("App\EventTeamAward","event_groups_id","id");
    }

    public function configs()
    {
        return $this->hasMany("App\EventGroupConfig", "grupo_evento_id", "id");
    }

    public function isDeletavel()
    {
        if ($this->id != null) {
            if (
                $this->eventos()->count() > 0 ||
                $this->torneios()->count() > 0 ||
                $this->categorias()->count() > 0 ||
                $this->criterios()->count() > 0 ||
                $this->criterios_gerais()->count() > 0 ||
                $this->tipo_rating()->count() > 0 ||
                $this->pontuacoes()->count() > 0
            ) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

    public function getCriteriosGerais()
    {
        return $this->getCriteriosDesempateGerais();
    }

    public function getCriteriosDesempateGerais()
    {
        return CriterioDesempateGrupoEventoGeral::where([
            ["grupo_evento_id", "=", $this->id],
        ])
            ->whereHas("criterio", function ($q1) {
                $q1->where([
                    ["is_geral", "=", true],
                ]);
            })
            ->orderBy("prioridade", "ASC")
            ->get();
    }

    public function getEventosClassificacaoGeralPublica()
    {
        return Evento::where([
            ["grupo_evento_id", "=", $this->id],
            ["mostrar_resultados_final", "=", true],
        ])
            ->orderBy("data_inicio", "ASC")
            ->get();
    }


    public function enxadristaJaInscritoEmOutroEvento($evento_id, $enxadrista_id){
        $evento = $this->eventos()->where([["id","=",$evento_id]])->first();
        $eventos_count = $this->eventos()->whereHas("torneios",function($q1) use ($enxadrista_id){
            $q1->whereHas("inscricoes",function($q2) use ($enxadrista_id){
                $q2->whereHas("enxadrista",function($q3) use ($enxadrista_id){
                    $q3->where([["id","=",$enxadrista_id]]);
                });
            });
        })
        ->where([
            ["id","!=",$evento_id]
        ])
        ->where(function($q1) use ($evento){
            $q1->where([
                ["data_inicio","=",$evento->data_inicio]
            ]);
            $q1->orWhere([
                ["data_fim","=",$evento->data_fim]
            ]);
            $q1->orWhere([
                ["data_inicio",">=",$evento->data_fim],
                ["data_fim","=<",$evento->data_fim]
            ]);
        })
        ->count();

        if($eventos_count > 0){
            return true;
        }

        return false;
    }

    public function getInscricoes()
    {
        $grupo_evento = $this;
        $inscricoes = Inscricao::whereHas("torneio", function ($q1) use ($grupo_evento) {
            $q1->whereHas("evento",function($q2) use ($grupo_evento) {
                $q2->where([["grupo_evento_id", "=", $grupo_evento->id]]);
            });
        })
            ->join('enxadrista', 'enxadrista.id', '=', 'inscricao.enxadrista_id')
            ->orderBy("torneio_id", "ASC")
            ->orderBy("categoria_id", "ASC")
            ->orderBy("enxadrista.name", "ASC")
            ->get();
        return $inscricoes;
    }

    public function isEvent(){
        return false;
    }

    public function isEventGroup(){
        return true;
    }


    public function classificator_getCategories()
    {
        $categories = array();
        foreach ($this->eventos->all() as $evento) {
            foreach ($evento->event_classificates->all() as $event_classificate) {
                foreach ($event_classificate->event->categorias->all() as $categoria_relacionada) {
                    if(!in_array($categoria_relacionada->categoria,$categories)){
                        $categories[] = $categoria_relacionada->categoria;
                    }
                }
            }
        }
        return $categories;
    }



    public function toAPIObject(){
        return [
            "id" => $this->id,
            "uuid" => $this->uuid,
            "name" => $this->name,
            "xadrezsuicopag_uuid" => $this->xadrezsuicopag_uuid,
        ];
    }




    public function getConfigs()
    {
        return $this->configs->all();
    }

    public function hasConfig($key)
    {
        if ($this->configs()->where([["key", "=", $key]])->count() > 0) {
            return true;
        }
        return false;
    }
    public function getConfig($key, $return_value = false)
    {
        if ($this->hasConfig($key)) {
            if ($return_value) {
                $config = $this->configs()->where([["key", "=", $key]])->first();
                switch ($config->value_type) {
                    case ConfigType::Integer:
                        return $config->integer;
                    case ConfigType::Float:
                        return $config->float;
                    case ConfigType::Decimal:
                        return $config->decimal;
                    case ConfigType::Boolean:
                        return $config->boolean;
                    case ConfigType::String:
                        return $config->string;
                    case ConfigType::Date:
                        return $config->date;
                    case ConfigType::DateTime:
                        return $config->datetime;
                }
            }

            return ["ok" => 1, "error" => 0, "config" => $this->configs()->where([["key", "=", $key]])->first()];
        }
        if ($return_value) return null;

        return ["ok" => 0, "error" => 1, "message" => "Configuração não encontrada."];
    }
    public function removeConfig($key)
    {
        if ($this->hasConfig($key)) {
            $config = $this->configs()->where([["key", "=", $key]])->first();

            $config->delete();

            return ["ok" => 1, "error" => 0];
        }
        return ["ok" => 0, "error" => 1, "message" => "Configuração não encontrada."];
    }

    public function setConfig($key, $type, $value)
    {
        if ($this->hasConfig($key)) {
            $config = $this->configs()->where([["key", "=", $key]])->first();

            if ($config->value_type != $type) {
                return ["ok" => 0, "error" => 1, "message" => "O tipo do campo é diferente - " . $registration_config->value_type . " != " . $type];
            }
        } else {
            $config = new EventGroupConfig;
            $config->grupo_evento_id = $this->id;
            $config->key = $key;
            $config->value_type = $type;
        }

        switch ($type) {
            case ConfigType::Integer:
                $config->integer = $value;
                break;
            case ConfigType::Float:
                $config->float = $value;
                break;
            case ConfigType::Decimal:
                $config->decimal = $value;
                break;
            case ConfigType::Boolean:
                $config->boolean = $value;
                break;
            case ConfigType::String:
                $config->string = $value;
                break;
            case ConfigType::Date:
                $config->date = $value;
                break;
            case ConfigType::DateTime:
                $config->datetime = $value;
                break;
        }

        $config->save();

        return ["ok" => 1, "error" => 0];
    }
}

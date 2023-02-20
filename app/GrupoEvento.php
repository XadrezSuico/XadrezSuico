<?php

namespace App;

use App\CriterioDesempateGrupoEventoGeral;
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

    // public function campos() {
    //     return $this->hasMany("App\CampoPersonalizadoGrupoEvento","grupo_evento_id","id");
    // }

    public function campos()
    {
        return $this->hasMany("App\CampoPersonalizado", "grupo_evento_id", "id");
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



    public function toAPIObject(){
        return [
            "id" => $this->id,
            "uuid" => $this->uuid,
            "name" => $this->name,
            "xadrezsuicopag_uuid" => $this->xadrezsuicopag_uuid,
        ];
    }
}

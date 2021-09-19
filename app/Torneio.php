<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

use DateTime;

class Torneio extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'torneio';

    public function template()
    {
        return $this->belongsTo("App\TorneioTemplate", "torneio_template_id", "id");
    }
    public function evento()
    {
        return $this->belongsTo("App\Evento", "evento_id", "id");
    }
    public function tipo_torneio()
    {
        return $this->belongsTo("App\TipoTorneio", "tipo_torneio_id", "id");
    }
    public function categorias()
    {
        return $this->hasMany("App\CategoriaTorneio", "torneio_id", "id");
    }
    public function inscricoes()
    {
        return $this->hasMany("App\Inscricao", "torneio_id", "id");
    }
    public function rodadas()
    {
        return $this->hasMany("App\Rodada", "torneio_id", "id");
    }

    public function getCountInscritos()
    {
        return $this->inscricoes()->count();
    }
    public function getCountInscritosConfirmados()
    {
        return $this->inscricoes()->where([["confirmado", "=", true]])->count();
    }
    public function getCountInscritosConfirmadosWOs()
    {
        return $this->inscricoes()->where([["confirmado", "=", true], ["desconsiderar_pontuacao_geral", "=", true]])->count();
    }
    public function quantosInscritosPresentes()
    {
        return $this->getCountInscritosConfirmados() - $this->getCountInscritosConfirmadosWOs();
    }
    public function getCountInscritosNaoConfirmados()
    {
        return $this->inscricoes()->where([["confirmado", "=", false]])->count();
    }
    public function getCountLichessConfirmadosnoTorneio()
    {
        return $this->inscricoes()->where([["is_lichess_found", "=", true]])->count();
    }
    public function getCountCriterios()
    {
        if ($this->evento->criterios()->count() > 0) {
            return $this->evento->criterios()->count();
        }

        return $this->evento->grupo_evento->criterios()->count();
    }
    public function getCountCriteriosNaoManuais()
    {
        $torneio = $this;
        if ($this->evento->criterios()->where([["tipo_torneio_id", "=", $this->tipo_torneio_id]])->count() > 0) {
            return $this->evento->criterios()->where([["tipo_torneio_id", "=", $this->tipo_torneio_id]])->whereDoesntHave("criterio", function ($q1) use ($torneio) {
                $q1->where([
                    ["is_manual", "=", true],
                ]);
            })->count();
        }

        return $this->evento->grupo_evento->criterios()->where([["tipo_torneio_id", "=", $this->tipo_torneio_id]])->whereDoesntHave("criterio", function ($q1) use ($torneio) {
            $q1->where([
                ["is_manual", "=", true],
            ]);
        })->count();
    }
    public function getCriterios()
    {
        if ($this->evento->criterios()->where([["tipo_torneio_id", "=", $this->tipo_torneio_id]])->count() > 0) {
            return $this->evento->criterios()->where([["tipo_torneio_id", "=", $this->tipo_torneio_id]])->orderBy("prioridade", "ASC")->whereHas("criterio", function ($q1) {$q1->where([["is_manual", "=", false]]);})->get();
        }

        return $this->evento->grupo_evento->criterios()->where([["tipo_torneio_id", "=", $this->tipo_torneio_id]])->where([["tipo_torneio_id", "=", $this->tipo_torneio_id]])->whereHas("criterio", function ($q1) {$q1->where([["is_manual", "=", false]]);})->orderBy("prioridade", "ASC")->get();
    }
    public function getCriteriosManuais()
    {
        if ($this->evento->criterios()->where([["tipo_torneio_id", "=", $this->tipo_torneio_id]])->count() > 0) {
            return $this->evento->criterios()->where([["tipo_torneio_id", "=", $this->tipo_torneio_id]])->orderBy("prioridade", "ASC")->whereHas("criterio", function ($q1) {$q1->where([["is_manual", "=", true]]);})->get();
        }

        return $this->evento->grupo_evento->criterios()->where([["tipo_torneio_id", "=", $this->tipo_torneio_id]])->where([["tipo_torneio_id", "=", $this->tipo_torneio_id]])->whereHas("criterio", function ($q1) {$q1->where([["is_manual", "=", true]]);})->orderBy("prioridade", "ASC")->get();
    }
    public function getCriteriosTotal()
    {
        if ($this->evento->criterios()->where([["tipo_torneio_id", "=", $this->tipo_torneio_id]])->count() > 0) {
            return $this->evento->criterios()->where([["tipo_torneio_id", "=", $this->tipo_torneio_id]])->orderBy("prioridade", "ASC")->get();
        }

        return $this->evento->grupo_evento->criterios()->where([["tipo_torneio_id", "=", $this->tipo_torneio_id]])->orderBy("prioridade", "ASC")->get();
    }
    public function getLastLichessPlayersUpdate(){
        $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $this->lichess_last_update);
        if ($datetime) {
            return $datetime->format("d/m/Y H:i:s");
        }
        return "Não houve atualização ainda.";
    }
    public function findByTagCategoria($tag)
    {
        return Torneio::whereHas("categorias", function ($q0) use ($tag) {
            $q0->whereHas("categoria", function ($q1) use ($tag) {
                $q1->where([["code", "=", $tag]]);
            });
        })->first();
    }
    public function hasCriteriosDesempateNasInscricoes()
    {
        if ($this->inscricoes()
            ->whereNotNull("pontos")
            ->whereHas("criterios_desempate")
            ->count() > 0) {
            return true;
        }
        return false;
    }

    public function setAllInscricoesNotFound(){
        foreach($this->inscricoes->all() as $inscricao){
            $inscricao->is_lichess_found = false;
            $inscricao->save();
        }
    }

    public function getIsResultadosImportados(){
        if($this->tipo_torneio->id == 3){
            $rodada = $this->rodadas()->where([["numero","=",2]])->first();
            $emparceiramento = $rodada->emparceiramentos()->first();
            if(is_int($emparceiramento->resultado)){
                return "Sim";
            }
        }
        if($this->hasCriteriosDesempateNasInscricoes()){
            return "Sim";
        }
        return "Não";
    }

    public function isDeletavel()
    {
        if ($this->id != null) {
            if ($this->categorias()->count() > 0 || $this->inscricoes()->count() > 0) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }
}

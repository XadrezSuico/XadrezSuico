<?php

namespace App;

use App\Enxadrista;
use App\TipoRatingRegras;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Evento extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'evento';

    public function grupo_evento()
    {
        return $this->belongsTo("App\GrupoEvento", "grupo_evento_id", "id");
    }

    public function cidade()
    {
        return $this->belongsTo("App\Cidade", "cidade_id", "id");
    }

    public function categorias_cadastradas()
    {
        return $this->hasMany("App\Categoria", "evento_id", "id");
    }

    public function categorias()
    {
        return $this->hasMany("App\CategoriaEvento", "evento_id", "id");
    }

    public function torneios()
    {
        return $this->hasMany("App\Torneio", "evento_id", "id");
    }

    public function criterios()
    {
        return $this->hasMany("App\CriterioDesempateEvento", "evento_id", "id");
    }

    public function tipo_rating_interno()
    {
        return $this->hasOne("App\TipoRatingEvento", "evento_id", "id");
    }

    public function pagina()
    {
        return $this->hasOne("App\Pagina", "evento_id", "id");
    }

    public function campos_adicionais()
    {
        return $this->hasMany("App\CampoPersonalizado", "evento_id", "id");
    }

    public function tipo_rating()
    {
        if ($this->tipo_rating_interno()->count() > 0) {
            return $this->tipo_rating_interno();
        } else {
            return $this->grupo_evento->tipo_rating();
        }
    }

    public function campos()
    {
        // if($this->hasMany("App\CampoPersonalizadoEvento","evento_id","id")->count() > 0){
        //     return $this->hasMany("App\CampoPersonalizadoEvento","evento_id","id");
        // }
        // return $this->grupo_evento->campos();
        return CampoPersonalizado::where([["grupo_evento_id", "=", $this->grupo_evento->id]])
            ->orWhere([["evento_id", "=", $this->id]])
            ->get();
    }

    public function campos_obrigatorios()
    {
        // if($this->hasMany("App\CampoPersonalizadoEvento","evento_id","id")->count() > 0){
        //     return $this->hasMany("App\CampoPersonalizadoEvento","evento_id","id");
        // }
        // return $this->grupo_evento->campos();
        return CampoPersonalizado::where([["grupo_evento_id", "=", $this->grupo_evento->id], ["is_required", "=", true]])
            ->orWhere([["evento_id", "=", $this->id], ["is_required", "=", true]])
            ->get();
    }

    public function getDataInicio()
    {
        $datetime = DateTime::createFromFormat('Y-m-d', $this->data_inicio);
        if ($datetime) {
            return $datetime->format("d/m/Y");
        } else {
            return false;
        }

    }

    public function getDataFim()
    {
        $datetime = DateTime::createFromFormat('Y-m-d', $this->data_fim);
        if ($datetime) {
            return $datetime->format("d/m/Y");
        } else {
            return false;
        }

    }

    public function getDataFimInscricoesOnline()
    {
        $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $this->data_limite_inscricoes_abertas);
        if ($datetime) {
            return $datetime->format("d/m/Y H:i");
        } else {
            return false;
        }

    }

    public function inscricoes_encerradas($api = false)
    {
        $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $this->data_limite_inscricoes_abertas);
        if ($datetime) {
            if ($api) {
                if ($datetime->getTimestamp() + (60 * 5) >= time() && !$this->estaLotado()) {
                    return false;
                } else {
                    return true;
                }
            }
            if ($datetime->getTimestamp() >= time() && !$this->estaLotado()) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }

    }

    public function getRegraRating($enxadrista_id)
    {
        $evento = $this;
        $enxadrista = Enxadrista::find($enxadrista_id);
        return TipoRatingRegras::where([
            ["tipo_ratings_id", "=", $evento->tipo_rating->tipo_ratings_id],
        ])
            ->where(function ($q1) use ($evento, $enxadrista) {
                $q1->where([
                    ["idade_minima", "<=", $enxadrista->howOld()],
                    ["idade_maxima", "=", null],
                ]);
                $q1->orWhere([
                    ["idade_minima", "=", null],
                    ["idade_maxima", ">=", $enxadrista->howOld()],
                ]);
                $q1->orWhere([
                    ["idade_minima", "<=", $enxadrista->howOld()],
                    ["idade_maxima", ">=", $enxadrista->howOld()],
                ]);
            })
            ->first();
    }

    public function quantosInscritos()
    {
        $total = 0;
        foreach ($this->torneios->all() as $torneio) {
            $total += $torneio->inscricoes()->count();
        }
        return $total;
    }
    public function quantosInscritosConfirmados()
    {
        $total = 0;
        foreach ($this->torneios->all() as $torneio) {
            $total += $torneio->inscricoes()->where([["confirmado", "=", true]])->count();
        }
        return $total;
    }
    public function quantosInscritosConfirmadosWOs()
    {
        $total = 0;
        foreach ($this->torneios->all() as $torneio) {
            $total += $torneio->inscricoes()->where([["confirmado", "=", true], ["desconsiderar_pontuacao_geral", "=", true]])->count();
        }
        return $total;
    }
    public function quantosInscritosPresentes()
    {
        return $this->quantosInscritosConfirmados() - $this->quantosInscritosConfirmadosWOs();
    }
    public function estaLotado()
    {
        if ($this->maximo_inscricoes_evento) {
            if ($this->maximo_inscricoes_evento > 0) {
                if ($this->maximo_inscricoes_evento <= $this->quantosInscritos()) {
                    return true;
                }
            }
        }

        return false;
    }

    public function enxadristaInscrito($enxadrista_id)
    {
        $total = 0;
        $evento = $this;
        $inscricao = Inscricao::where([
            ["enxadrista_id", "=", $enxadrista_id],
        ])
            ->whereHas("torneio", function ($q1) use ($evento) {
                $q1->where("evento_id", "=", $evento->id);
            })
            ->first();
        if ($inscricao) {
            return $inscricao;
        }

        return false;
    }

    public function getInscricoes()
    {
        $evento = $this;
        $inscricoes = Inscricao::whereHas("torneio", function ($q1) use ($evento) {
            $q1->where([["evento_id", "=", $evento->id]]);
        })
            ->join('enxadrista', 'enxadrista.id', '=', 'inscricao.enxadrista_id')
            ->orderBy("categoria_id", "ASC")
            ->orderBy("enxadrista.name", "ASC")
            ->get();
        return $inscricoes;
    }

    public function gerarToken()
    {
        if ($this->token == null) {
            $this->token = hash("sha1", "xadrezSuico" . time() . $this->id . $this->created_at . rand(0, 2345) . rand(25, rand(852, 658714)));
        }
    }

    public function inscricaoLiberada($token)
    {
        if ($this->e_inscricao_apenas_com_link) {
            if ($this->token == $token) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    public function isDeletavel()
    {
        if ($this->id != null) {
            if ($this->categorias()->count() > 0 || $this->torneios()->count() > 0) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }
}

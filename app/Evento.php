<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use App\TipoRatingRegras;
use App\Enxadrista;
use DateTime;

class Evento extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];
    
    
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'evento';

    public function grupo_evento() {
        return $this->belongsTo("App\GrupoEvento","grupo_evento_id","id");
    }

    public function cidade() {
        return $this->belongsTo("App\Cidade","cidade_id","id");
    }

    public function categorias() {
        return $this->hasMany("App\CategoriaEvento","evento_id","id");
    }

    public function torneios() {
        return $this->hasMany("App\Torneio","evento_id","id");
    }

    public function criterios() {
        return $this->hasMany("App\CriterioDesempateEvento","evento_id","id");
    }

    private function tipo_rating_interno() {
        return $this->hasOne("App\TipoRatingEvento","evento_id","id");
    }

    public function campos() {
        if($this->hasMany("App\CampoPersonalizadoEvento","evento_id","id")->count() > 0){
            return $this->hasMany("App\CampoPersonalizadoEvento","evento_id","id");
        }
        return $this->grupo_evento->campos();
    }

    public function tipo_rating() {
        if($this->tipo_rating_interno()->count() > 0){
            return $this->tipo_rating_interno();
        }else{
            return $this->grupo_evento->tipo_rating();
        }
    }
    
    public function getDataInicio(){
        $datetime = DateTime::createFromFormat('Y-m-d', $this->data_inicio);
        if($datetime){
            return $datetime->format("d/m/Y");
        }else
            return false;
    }
    
    public function getDataFim(){
        $datetime = DateTime::createFromFormat('Y-m-d', $this->data_fim);
        if($datetime){
            return $datetime->format("d/m/Y");
        }else
            return false;
    }
    
    public function getDataFimInscricoesOnline(){
        $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $this->data_limite_inscricoes_abertas);
        if($datetime){
            return $datetime->format("d/m/Y H:i");
        }else
            return false;
    }
    
    public function inscricoes_encerradas($api = false){
        $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $this->data_limite_inscricoes_abertas);
        if($datetime){
            if($api){
                if($datetime->getTimestamp()+(60*5) >= time()){
                    return false;
                }else{
                    return true;
                }
            }
            if($datetime->getTimestamp() >= time()){
                return false;
            }else{
                return true;
            }
        }else
            return false;
    }

    public function getRegraRating($enxadrista_id){
        $evento = $this;
        $enxadrista = Enxadrista::find($enxadrista_id);
        return TipoRatingRegras::where([
                ["tipo_ratings_id","=",$evento->tipo_rating->tipo_ratings_id],
            ])
            ->where(function($q1) use ($evento,$enxadrista){
                $q1->where([
                    ["idade_minima","<=",$enxadrista->howOld()],
                    ["idade_maxima","=",NULL]
                ]);
                $q1->orWhere([
                    ["idade_minima","=",NULL],
                    ["idade_maxima",">=",$enxadrista->howOld()]
                ]);
                $q1->orWhere([
                    ["idade_minima","<=",$enxadrista->howOld()],
                    ["idade_maxima",">=",$enxadrista->howOld()]
                ]);
            })
            ->first();
    }

    public function quantosInscritos(){
        $total = 0;
        foreach($this->torneios->all() as $torneio){
            $total += $torneio->inscricoes()->count();
        }
        return $total;
    }
    public function quantosInscritosConfirmados(){
        $total = 0;
        foreach($this->torneios->all() as $torneio){
            $total += $torneio->inscricoes()->where([["confirmado","=",true]])->count();
        }
        return $total;
    }
    public function quantosInscritosConfirmadosWOs(){
        $total = 0;
        foreach($this->torneios->all() as $torneio){
            $total += $torneio->inscricoes()->where([["confirmado","=",true],["desconsiderar_pontuacao_geral","=",true]])->count();
        }
        return $total;
    }
    public function quantosInscritosPresentes(){
        return $this->quantosInscritosConfirmados() - $this->quantosInscritosConfirmadosWOs();
    }
    
    public function enxadristaInscrito($enxadrista_id){
        $total = 0;
        $evento = $this;
        $inscricao = Inscricao::where([
            ["enxadrista_id","=",$enxadrista_id],
        ])
        ->whereHas("torneio",function($q1) use ($evento){
            $q1->where("evento_id","=",$evento->id);
        })
        ->first();
        if($inscricao) return $inscricao;
        return false;
    }

    
    public function isDeletavel(){
        if($this->id != null){
            if($this->categorias()->count() > 0 || $this->torneios()->count() > 0){
                return false;
            }
            return true;
        }else{
            return false;
        }
    }
}

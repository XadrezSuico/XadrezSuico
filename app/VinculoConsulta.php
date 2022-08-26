<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Str;

use DateTime;

use QrCode;


class VinculoConsulta extends Model
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
    }
    public function vinculo(){
        return $this->belongsTo("App\Vinculo","vinculos_id","id");
    }
    public function enxadrista(){
        return $this->belongsTo("App\Enxadrista","enxadrista_id","id");
    }
    public function cidade(){
        return $this->belongsTo("App\Cidade","cidade_id","id");
    }
    public function clube(){
        return $this->belongsTo("App\Clube","clube_id","id");
    }

    public function getCreatedAt()
    {
        $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $this->created_at);
        if ($datetime) {
            return $datetime->format("d/m/Y H:i:s");
        }
        return false;
    }


    public function getVinculoType(){
        if($this->is_confirmed_system){
            return "Automático";
        }elseif($this->is_confirmed_manually){
            return "Manual";
        }else{
            return "SEM VÍNCULO";
        }
    }


    public function getQrCode(){
        return QrCode::size(150)->generate(url("/especiais/fexpar/vinculos/consulta/".$this->uuid));
    }


    public function eConsultaAmazenadaIgualVinculo(){
        if(
            $this->vinculo->ano == $this->ano &&
            $this->vinculo->cidade_id == $this->cidade_id &&
            $this->vinculo->clube_id == $this->clube_id &&
            $this->vinculo->is_confirmed_system == $this->is_confirmed_system &&
            $this->vinculo->is_confirmed_manually == $this->is_confirmed_manually &&
            $this->vinculo->system_inscricoes_in_this_club_confirmed == $this->system_inscricoes_in_this_club_confirmed &&
            $this->vinculo->events_played == $this->events_played &&
            $this->vinculo->obs == $this->obs
        ){
            return true;
        }
        return false;
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class PerfilUser extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];

    public function perfil()
    {
        return $this->belongsTo("App\Perfil", "perfils_id", "id");
    }
    public function user()
    {
        return $this->belongsTo("App\User", "users_id", "id");
    }
    public function evento()
    {
        return $this->belongsTo("App\Evento", "evento_id", "id");
    }
    public function grupo_evento()
    {
        return $this->belongsTo("App\GrupoEvento", "grupo_evento_id", "id");
    }

    public function toAPIObject(){
        if($this->perfil){
            if($this->perfil->is_for_event){
                if($this->evento){
                    return [
                        "id" => $this->id,
                        "profile" => $this->perfil->toAPIObject(),
                        "event" => $this->evento->toAPIObject(),
                    ];
                }
            }
            if($this->perfil->is_for_event_group){
                if($this->grupo_evento){
                    return [
                        "id" => $this->id,
                        "profile" => $this->perfil->toAPIObject(),
                        "event_group" => $this->grupo_evento->toAPIObject(),
                    ];
                }
            }


            return [
                "id" => $this->id,
                "profile" => $this->perfil->toAPIObject(),
            ];
        }
        return [];
    }
}

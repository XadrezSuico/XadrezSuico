<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Perfil extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];

    public function users()
    {
        return $this->hasMany("App\PerfilUser", "perfils_id", "id");
    }

    public function toAPIObject(){
        return [
            "id"=> $this->id,
            "name"=> $this->name,
            "is_for"=>[
                "event"=> $this->is_for_event,
                "event_group"=> $this->is_for_event_group,
            ]
        ];
    }
}

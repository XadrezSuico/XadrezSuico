<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventTeamScore extends Model
{
    public function event_team_award(){
        return $this->belongsTo("App\EventTeamAward","event_team_awards_id","id");
    }
    public function configs(){
        return $this->hasMany("App\EventTeamAwardConfig","event_team_configs_id","id");
    }
}

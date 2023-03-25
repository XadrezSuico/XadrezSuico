<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventTeamAwardConfig extends Model
{
    public function event_team_award(){
        return $this->belongsTo("App\EventTeamAward","event_team_awards_id","id");
    }
}

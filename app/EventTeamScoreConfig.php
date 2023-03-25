<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventTeamScoreConfig extends Model
{
    public function event_team_score(){
        return $this->belongsTo("App\EventTeamScore","event_team_scores_id","id");
    }
}

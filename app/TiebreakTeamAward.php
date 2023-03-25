<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TiebreakTeamAward extends Model
{
    public function event_team_award(){
        return $this->belongsTo("App\EventTeamAward","event_team_awards_id","id");
    }
    public function tiebreak(){
        return $this->belongsTo("App\CriterioDesempate","tiebreaks_id","id");
    }
}

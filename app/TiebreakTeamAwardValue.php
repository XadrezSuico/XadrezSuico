<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TiebreakTeamAwardValue extends Model
{
    public function event_team_score(){
        return $this->belongsTo("App\EventTeamScore","event_team_scores_id","id");
    }
    public function tiebreak(){
        return $this->belongsTo("App\CriterioDesempate","tiebreaks_id","id");
    }
    public function club(){
        return $this->belongsTo("App\Clube","clubs_id","id");
    }
}

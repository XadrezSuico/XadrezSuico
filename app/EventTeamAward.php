<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventTeamAward extends Model
{
    public function event(){
        return $this->belongsTo("App\Evento","events_id","id");
    }
    public function configs(){
        return $this->hasMany("App\EventTeamAwardConfig","event_team_award_configs_id","id");
    }
    public function scores(){ // table with position/score
        return $this->hasMany("App\EventTeamAwardScore","event_team_award_scores_id","id");
    }
    public function team_scores(){ // team scores
        return $this->hasMany("App\EventTeamScore","event_team_scores_id","id");
    }
}

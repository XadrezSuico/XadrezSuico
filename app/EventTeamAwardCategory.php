<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventTeamAwardCategory extends Model
{
    public function event_team_award(){
        return $this->belongsTo("App\EventTeamAward","event_team_awards_id","id");
    }
    public function category(){
        return $this->belongsTo("App\Categoria","categories_id","id");
    }
}

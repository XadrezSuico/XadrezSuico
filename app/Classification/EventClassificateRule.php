<?php

namespace App\Classification;

use App\Enum\ClassificationTypeRule;
use Illuminate\Database\Eloquent\Model;

class EventClassificateRule extends Model
{
    public function event_classificate()
    {
        return $this->belongsTo("App\Classification\EventClassificate", "event_classificates_id", "id");
    }
    public function event()
    {
        return $this->belongsTo("App\Evento", "event_id", "id");
    }

    public function getRuleName(){
        return ClassificationTypeRule::get($this->type)["name"];
    }
}

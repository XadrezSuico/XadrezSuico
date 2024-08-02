<?php

namespace App\Classification;

use Illuminate\Database\Eloquent\Model;

class EventClassificateRuleConfig extends Model
{
    public function event_classificate_rule()
    {
        return $this->belongsTo("App\Classification\EventClassificateRule", "event_classificate_rules_id", "id");
    }
}

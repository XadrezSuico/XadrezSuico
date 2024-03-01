<?php

namespace App\Classification;

use App\Enum\ClassificationTypeRule;
use App\Inscricao;
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

    public function howMuchClassificated()
    {
        $xzsuic_classificator_rule = $this;

        return Inscricao::whereHas("torneio", function ($q1) use ($xzsuic_classificator_rule) {
            $q1->whereHas("evento", function ($q2) use ($xzsuic_classificator_rule) {
                $q2->where([["id", "=", $xzsuic_classificator_rule->event_classificate->event->id]]);
            });
        })
            ->whereHas("configs", function ($q1) use ($xzsuic_classificator_rule) {
                $q1->where([
                    ["key", "=", "event_classificator_rule_id"],
                    ["integer", "=", $xzsuic_classificator_rule->id],
                ]);
            })
            ->count();
    }
}

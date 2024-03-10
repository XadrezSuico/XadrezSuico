<?php

namespace App\Classification;

use App\Inscricao;
use Illuminate\Database\Eloquent\Model;

class EventClassificate extends Model
{
    public function event()
    {
        return $this->belongsTo("App\Evento", "event_id", "id");
    }

    public function event_classificator()
    {
        return $this->belongsTo("App\Evento", "event_classificator_id", "id");
    }

    public function rules()
    {
        return $this->hasMany("App\Classification\EventClassificateRule", "event_classificates_id", "id");
    }

    public function getRegistrationsClassificated()
    {
        $xzsuic_classificator = $this;

        return Inscricao::whereHas("torneio", function ($q1) use ($xzsuic_classificator) {
            $q1->whereHas("evento", function ($q2) use ($xzsuic_classificator) {
                $q2->where([["id", "=", $xzsuic_classificator->event->id]]);
            });
        })
        ->whereHas("configs", function ($q1) use ($xzsuic_classificator) {
            $q1->where([
                ["key", "=", "event_classificator_id"],
                ["integer", "=", $xzsuic_classificator->event_classificator->id],
            ]);
        })
        ->whereHas("configs", function ($q1) use ($xzsuic_classificator) {
            $q1->where([
                ["key", "=", "event_classificator_rule_id"],
            ]);
            $q1->whereIn("integer",  $xzsuic_classificator->getRulesId());
        })
        ->get();
    }

    public function howMuchClassificated()
    {
        $xzsuic_classificator = $this;

        return Inscricao::whereHas("torneio", function ($q1) use ($xzsuic_classificator) {
            $q1->whereHas("evento", function ($q2) use ($xzsuic_classificator) {
                $q2->where([["id", "=", $xzsuic_classificator->event->id]]);
            });
        })
            ->whereHas("configs", function ($q1) use ($xzsuic_classificator) {
                $q1->where([
                    ["key", "=", "event_classificator_id"],
                    ["integer", "=", $xzsuic_classificator->event_classificator->id],
                ]);
            })
            ->whereHas("configs", function ($q1) use ($xzsuic_classificator) {
                $q1->where([
                    ["key", "=", "event_classificator_rule_id"],
                ]);
                $q1->whereIn("integer",  $xzsuic_classificator->getRulesId());
            })
            ->count();
    }

    public function getRulesId(){
        $ids = array();
        foreach($this->rules->all() as $rule){
            $ids[] = $rule->id;
        }
        return $ids;
    }
}

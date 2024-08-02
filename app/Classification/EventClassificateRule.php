<?php

namespace App\Classification;

use App\Enum\ClassificationTypeRule;
use App\Enum\ClassificationTypeRuleConfig;
use App\Enum\ConfigType;
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

    public function configs()
    {
        return $this->hasMany("App\Classification\EventClassificateRuleConfig", "event_classificate_rules_id", "id");
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






    public function getConfigs()
    {
        return $this->configs->all();
    }

    public function hasConfig($key)
    {
        if ($this->configs()->where([["key", "=", $key]])->count() > 0) {
            return true;
        }
        return false;
    }
    public function getConfig($key, $return_value = false)
    {
        if ($this->hasConfig($key)) {
            if ($return_value) {
                $config = $this->configs()->where([["key", "=", $key]])->first();
                switch ($config->value_type) {
                    case ConfigType::Integer:
                        return $config->integer;
                    case ConfigType::Float:
                        return $config->float;
                    case ConfigType::Decimal:
                        return $config->decimal;
                    case ConfigType::Boolean:
                        return $config->boolean;
                    case ConfigType::String:
                        return $config->string;
                    case ConfigType::Date:
                        return $config->date;
                    case ConfigType::DateTime:
                        return $config->datetime;
                }
            }

            return ["ok" => 1, "error" => 0, "config" => $this->configs()->where([["key", "=", $key]])->first()];
        }
        if ($return_value) return null;

        return ["ok" => 0, "error" => 1, "message" => "Configuração não encontrada."];
    }
    public function removeConfig($key)
    {
        if ($this->hasConfig($key)) {
            $config = $this->configs()->where([["key", "=", $key]])->first();

            $config->delete();

            return ["ok" => 1, "error" => 0];
        }
        return ["ok" => 0, "error" => 1, "message" => "Configuração não encontrada."];
    }

    public function setConfig($key, $type, $value)
    {
        if ($this->hasConfig($key)) {
            $config = $this->configs()->where([["key", "=", $key]])->first();

            if ($config->value_type != $type) {
                return ["ok" => 0, "error" => 1, "message" => "O tipo do campo é diferente - " . $config->value_type . " != " . $type];
            }
        } else {
            $config = new EventClassificateRuleConfig;
            $config->event_classificate_rules_id = $this->id;
            $config->key = $key;
            $config->value_type = $type;
        }

        switch ($type) {
            case ConfigType::Integer:
                $config->integer = $value;
                break;
            case ConfigType::Float:
                $config->float = $value;
                break;
            case ConfigType::Decimal:
                $config->decimal = $value;
                break;
            case ConfigType::Boolean:
                $config->boolean = $value;
                break;
            case ConfigType::String:
                $config->string = $value;
                break;
            case ConfigType::Date:
                $config->date = $value;
                break;
            case ConfigType::DateTime:
                $config->datetime = $value;
                break;
        }

        $config->save();

        return ["ok" => 1, "error" => 0];
    }
}

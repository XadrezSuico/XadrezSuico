<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Enum\ConfigType;

class EventTeamScore extends Model
{
    public function event_team_award(){
        return $this->belongsTo("App\EventTeamAward","event_team_awards_id","id");
    }
    public function club(){
        return $this->belongsTo("App\Clube","clubs_id","id");
    }
    public function configs(){
        return $this->hasMany("App\EventTeamScoreConfig","event_team_scores_id","id");
    }
    public function tiebreaks(){
        return $this->hasMany("App\TiebreakTeamAwardValue","event_team_scores_id","id");
    }


    public function getConfigs(){
        return $this->configs->all();
    }

    public function hasConfig($key){
        if($this->configs()->where([["key","=",$key]])->count() > 0){
            return true;
        }
        return false;
    }
    public function getConfig($key,$return_value = false){
        if($this->hasConfig($key)){
            if($return_value){
                $config = $this->configs()->where([["key","=",$key]])->first();
                switch($config->value_type){
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
                }
            }

            return ["ok"=>1,"error"=>0,"config"=>$this->configs()->where([["key","=",$key]])->first()];
        }
        if($return_value) return null;

        return ["ok"=>0,"error"=>1,"message"=>"Configuração não encontrada."];
    }
    public function removeConfig($key){
        if($this->hasConfig($key)){
            $event_config = $this->configs()->where([["key","=",$key]])->first();

            $event_config->delete();

            return ["ok"=>1,"error"=>0];
        }
        return ["ok"=>0,"error"=>1,"message"=>"Configuração não encontrada."];
    }

    public function setConfig($key,$type,$value){
        if($this->hasConfig($key)){
            $event_config = $this->configs()->where([["key","=",$key]])->first();

            if($event_config->value_type != $type){
                return ["ok"=>0,"error"=>1,"message"=>"O tipo do campo é diferente - ".$event_config->value_type." != ".$type];
            }
        }else{
            $event_config = new EventTeamScoreConfig;
            $event_config->event_team_scores_id = $this->id;
            $event_config->key = $key;
            $event_config->value_type = $type;
        }

        switch($type){
            case ConfigType::Integer:
                $event_config->integer = $value;
                break;
            case ConfigType::Float:
                $event_config->float = $value;
                break;
            case ConfigType::Decimal:
                $event_config->decimal = $value;
                break;
            case ConfigType::Boolean:
                $event_config->boolean = $value;
                break;
            case ConfigType::String:
                $event_config->string = $value;
                break;
        }

        $event_config->save();

        return ["ok"=>1,"error"=>0];
    }
}

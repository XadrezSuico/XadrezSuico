<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Enum\ConfigType;

class EventTeamAward extends Model
{
    public function event(){
        return $this->belongsTo("App\Evento","events_id","id");
    }
    public function configs(){
        return $this->hasMany("App\EventTeamAwardConfig","event_team_awards_id","id");
    }
    public function scores(){ // table with position/score
        return $this->hasMany("App\EventTeamAwardScore","event_team_awards_id","id");
    }
    public function team_scores(){ // team scores
        return $this->hasMany("App\EventTeamScore","event_team_awards_id","id");
    }
    public function categories(){
        return $this->hasMany("App\EventTeamAwardCategory","event_team_awards_id","id");
    }
    public function tiebreaks(){
        return $this->hasMany("App\TiebreakTeamAward","event_team_awards_id","id");
    }

    public function hasPlace($place, $is_points = false, $registration = null){
        if($is_points){
            if($registration){
                if($registration->pontos > 0){
                    return true;
                }
            }
        }elseif($this->scores()->where([["place","=",$place]])->count() > 0){
            return true;
        }
        return false;
    }

    public function getPlace($category, $place = null, $return_value = false, $is_points = false, $registration = null){
        // if we use registration points to get points
        if($this->hasConfig("category_".$category->id."_default_points")){
            return $this->getConfig("category_".$category->id."_default_points",true);
        }
        if($is_points){
            if($registration){
                if($registration->isPresent()){
                    return $registration->pontos;
                }
            }
        }elseif($this->hasPlace($place)){
            if($return_value){
                return $this->scores()->where([["place","=",$place]])->first()->score;
            }
            return $this->scores()->where([["place","=",$place]])->first();
        }
        return false;
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
                $event_config = $this->configs()->where([["key","=",$key]])->first();
                switch($event_config->value_type){
                    case ConfigType::Integer:
                        return $event_config->integer;
                    case ConfigType::Float:
                        return $event_config->float;
                    case ConfigType::Decimal:
                        return $event_config->decimal;
                    case ConfigType::Boolean:
                        return $event_config->boolean;
                    case ConfigType::String:
                        return $event_config->string;
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
            $event_config = new EventTeamAwardConfig;
            $event_config->event_team_awards_id = $this->id;
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

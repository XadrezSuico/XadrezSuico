<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Title extends Model
{
    public function entity(){
        return $this->belongsTo("App\Entity", "entities_id", "id");
    }

    public function toAPIObject($with_parent = false){
        if($with_parent){
            return [
                "id" => $this->id,
                "name" => $this->name,
                "abbr" => $this->abbr,
                "entity" => $this->entity->toAPIObject(),
                "is_for_women" => $this->is_for_women,
                "id_online" => $this->id_online,
            ];
        }
        return [
            "id" => $this->id,
            "name" => $this->name,
            "abbr" => $this->abbr,
            "entity" => null,
            "is_for_women" => $this->is_for_women,
            "id_online" => $this->id_online,
        ];
    }
}

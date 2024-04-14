<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    public function toAPIObject()
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "abbr" => $this->abbr,
            "website" => $this->website,
            "type" => $this->type,
        ];
    }
}

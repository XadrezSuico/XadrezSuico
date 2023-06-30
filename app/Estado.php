<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    public function pais()
    {
        return $this->belongsTo("App\Pais", "pais_id", "id");
    }

    public function cidades()
    {
        return $this->hasMany("App\Cidade", "estados_id", "id");
    }


    public function getName()
    {
        return mb_strtoupper($this->nome);
    }


    public function toAPIObject($include_parent = false){
        if($include_parent){
            return [
                "id" => $this->id,
                "name" => $this->nome,
                "slug" => trim($this->abbr),

                "country" => $this->pais->toAPIObject(),

                "country_id" => $this->pais->id,
            ];
        }
        return [
            "id" => $this->id,
            "name" => $this->nome,
            "slug" => trim($this->abbr),

            "country_id" => $this->pais->id,
        ];
    }
}

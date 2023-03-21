<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RegistrationConfig extends Model
{
    public function registration(){
        return $this->belongsTo("App\Inscricao","inscricao_id","id");
    }
}

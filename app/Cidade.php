<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cidade extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'cidade';

    public function clubes() {
        return $this->hasMany("App/Clube","cidade_id","id");
    }

    public function getName(){
        return mb_strtoupper($this->name);
    }
}

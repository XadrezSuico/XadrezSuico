<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Clube extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'clube';

    public function cidade() {
        return $this->belongsTo("App\Cidade","cidade_id","id");
    }

    public function getName(){
        return mb_strtoupper($this->name);
    }
}

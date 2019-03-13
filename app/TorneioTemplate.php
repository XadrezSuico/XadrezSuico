<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TorneioTemplate extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'torneio_template';

    public function categorias() {
        return $this->hasMany("App\CategoriaTorneioTemplate","torneio_template_id","id");
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoriaTorneioTemplate extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'categoria_torneio_template';
}

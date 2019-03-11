<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoTorneio extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'tipo_torneio';
}

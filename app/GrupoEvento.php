<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GrupoEvento extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'grupo_evento';
}

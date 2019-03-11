<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'evento';
}

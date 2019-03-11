<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pontuacao extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'pontuacao';
}

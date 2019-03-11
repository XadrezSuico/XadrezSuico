<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'categoria';
}

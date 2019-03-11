<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CriterioDesempate extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'criterio_desempate';
}

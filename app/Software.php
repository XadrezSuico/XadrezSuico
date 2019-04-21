<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Software extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'softwares';
}

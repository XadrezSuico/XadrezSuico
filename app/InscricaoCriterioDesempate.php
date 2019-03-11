<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InscricaoCriterioDesempate extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'inscricao_criterio_desempate';
}

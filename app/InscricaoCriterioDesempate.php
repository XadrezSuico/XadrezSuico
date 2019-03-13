<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InscricaoCriterioDesempate extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'inscricao_criterio_desempate';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'criterio_desempate_id', 'inscricao_id'
    ];
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class InscricaoCriterioDesempate extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'inscricao_criterio_desempate';
}

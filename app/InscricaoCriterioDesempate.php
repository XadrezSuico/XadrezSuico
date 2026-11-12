<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class InscricaoCriterioDesempate extends Model
{
    use LogsActivity;
    use \App\Scopes\ExcludesDeletedRegistrations;

    protected function registrationColumns()
    {
        return ['inscricao_id'];
    }

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'inscricao_criterio_desempate';

    public function inscricao(){
        return $this->belongsTo("App\Inscricao","inscricao_id","id");
    }

    public function criterio(){
        return $this->belongsTo("App\CriterioDesempate","criterio_desempate_id","id");
    }
}

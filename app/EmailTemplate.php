<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

use App\Enum\EmailType;

class EmailTemplate extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];


    public function evento()
    {
        return $this->belongsTo("App\Evento", "evento_id", "id");
    }
    public function grupo_evento()
    {
        return $this->belongsTo("App\GrupoEvento", "grupo_evento_id", "id");
    }

    public function getEmailType(){
        $enum = new EmailType;
        return $enum->get($this->email_type);
    }
}

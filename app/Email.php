<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Email extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];
    public function enxadrista()
    {
        return $this->belongsTo("App\Enxadrista", "enxadrista_id", "id");
    }
}

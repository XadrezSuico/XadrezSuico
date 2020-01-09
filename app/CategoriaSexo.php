<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class CategoriaSexo extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];

    public function categoria()
    {
        return $this->belongsTo("App\Categoria", "categoria_id", "id");
    }

    public function sexo()
    {
        return $this->belongsTo("App\Sexo", "sexos_id", "id");
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class CategoriaEvento extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'categoria_evento';

    public function categoria()
    {
        return $this->belongsTo("App\Categoria", "categoria_id", "id");
    }

    public function evento()
    {
        return $this->belongsTo("App\Evento", "evento_id", "id");
    }
}

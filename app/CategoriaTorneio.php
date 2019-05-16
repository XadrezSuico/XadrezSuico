<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class CategoriaTorneio extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];

    
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'categoria_torneio';

    public function categoria() {
        return $this->belongsTo("App\Categoria","categoria_id","id");
    }

    public function torneio() {
        return $this->belongsTo("App\Torneio","torneio_id","id");
    }
}

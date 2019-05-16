<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class CategoriaTorneioTemplate extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];
    
    
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'categoria_torneio_template';

    public function categoria() {
        return $this->belongsTo("App\Categoria","categoria_id","id");
    }

    public function template() {
        return $this->belongsTo("App\TorneioTemplate","torneio_id","id");
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoriaTorneioTemplate extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'categoria_torneio_template';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'categoria_id', 'torneio_template_id'
    ];

    public function categoria() {
        return $this->belongsTo("App\Categoria","categoria_id","id");
    }

    public function template() {
        return $this->belongsTo("App\TorneioTemplate","torneio_id","id");
    }
}

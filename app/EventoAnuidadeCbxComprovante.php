<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventoAnuidadeCbxComprovante extends Model
{
    protected $fillable = [
        'evento_id',
        'enxadrista_id',
        'comprovante_recebido',
    ];

    protected $casts = [
        'comprovante_recebido' => 'boolean',
    ];

    public function evento()
    {
        return $this->belongsTo('App\Evento', 'evento_id', 'id');
    }

    public function enxadrista()
    {
        return $this->belongsTo('App\Enxadrista', 'enxadrista_id', 'id');
    }
}

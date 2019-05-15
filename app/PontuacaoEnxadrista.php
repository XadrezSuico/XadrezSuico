<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PontuacaoEnxadrista extends Model
{
    public function enxadrista(){
        return $this->belongsTo("App\Enxadrista","enxadrista_id","id");
    }
    public function grupo_evento(){
        return $this->belongsTo("App\GrupoEvento","grupo_evento_id","id");
    }
    public function categoria(){
        return $this->belongsTo("App\Categoria","categoria_id","id");
    }
}

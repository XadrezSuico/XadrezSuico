<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Categoria extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'categoria';

    public function grupo_evento()
    {
        return $this->belongsTo("App\GrupoEvento", "grupo_evento_id", "id");
    }
    public function evento()
    {
        return $this->belongsTo("App\Evento", "evento_id", "id");
    }

    public function torneios_template()
    {
        return $this->hasMany("App\CategoriaTorneioTemplate", "categoria_id", "id");
    }

    public function sexos()
    {
        return $this->hasMany("App\CategoriaSexo", "categoria_id", "id");
    }

    public function inscricoes()
    {
        return $this->hasMany("App\Inscricao", "categoria_id", "id");
    }

    public function torneios()
    {
        return $this->hasMany("App\CategoriaTorneio", "categoria_id", "id");
    }

    public function eventos()
    {
        return $this->hasMany("App\CategoriaEvento", "categoria_id", "id");
    }

    public function getTorneioByEvento($evento)
    {
        $categoria = $this;
        if ($evento) {
            $torneio = Torneio::where([
                ["evento_id", "=", $evento->id],
            ])
                ->whereHas("categorias", function ($q1) use ($categoria) {
                    $q1->where([["categoria_id", "=", $categoria->id]]);
                })
                ->first();
            if ($torneio) {
                return $torneio;
            }
        }
        return false;
    }

    public function isDeletavel()
    {
        if ($this->id != null) {
            if (
                $this->torneios_template()->count() > 0 ||
                $this->inscricoes()->count() > 0 ||
                $this->torneios()->count() > 0 ||
                $this->eventos()->count() > 0
            ) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }
}

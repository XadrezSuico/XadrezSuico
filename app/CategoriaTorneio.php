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

    public function categoria()
    {
        return $this->belongsTo("App\Categoria", "categoria_id", "id");
    }

    public function torneio()
    {
        return $this->belongsTo("App\Torneio", "torneio_id", "id");
    }

    public function getPremiados(){
        $quantos_premiam = 3;
        if($this->categoria){
            if($this->categoria->quantos_premiam != NULL){
                $quantos_premiam = $this->categoria->quantos_premiam;
            }
        }

        $premiados = array();
        $i = 1;
        foreach($this->torneio->inscricoes()->whereNotNull("posicao")->where([
            ["categoria_id","=",$this->categoria->id],
            ["is_desclassificado","=",false],
            ["desconsiderar_pontuacao_geral","=",false],
            ["desconsiderar_classificado","=",false],
        ])->orderBy("posicao","ASC")->limit($quantos_premiam)->get() as $premiado){
            if($i++ <= $quantos_premiam){
                $premiados[] = $premiado;
            }
        }
        return $premiados;
    }
}

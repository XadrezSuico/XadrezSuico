<?php

namespace App\Http\Controllers;

use App\Categoria;
use App\Evento;
use App\Inscricao;

class EventoController extends Controller
{
    // public function index(){
    //     $eventos = Evento::all();
    //     return view("evento.index",compact("eventos"));
    // }
    public function classificacao($evento_id)
    {
        $evento = Evento::find($evento_id);
        if ($evento->mostrar_resultados) {
            return view("evento.publico.classificacao", compact("evento"));
        }

        return view("evento.publico.classificacaonao", compact("evento"));
    }
    public function resultados($evento_id, $categoria_id)
    {
        $evento = Evento::find($evento_id);
        $categoria = Categoria::find($categoria_id);
        $torneio = $categoria->getTorneioByEvento($evento);
        $inscricoes = Inscricao::where([
            ["categoria_id", "=", $categoria->id],
            ["confirmado", "=", true],
        ])
            ->whereHas("torneio", function ($q1) use ($evento) {
                $q1->where([
                    ["evento_id", "=", $evento->id],
                ]);
            })
            ->orderBy("posicao", "ASC")
            ->get();
        $criterios = $torneio->getCriterios();
        return view("evento.publico.list", compact("evento", "categoria", "inscricoes", "criterios"));
    }
}

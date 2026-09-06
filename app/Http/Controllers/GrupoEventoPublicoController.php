<?php

namespace App\Http\Controllers;

use App\Categoria;
use App\Enxadrista;
use App\GrupoEvento;
use App\PontuacaoEnxadrista;

class GrupoEventoPublicoController extends Controller
{
    public function classificacao($grupo_evento_id)
    {
        $grupo_evento = GrupoEvento::find($grupo_evento_id);
        return view("grupoevento.publico.classificacao", compact("grupo_evento"));
    }

    public function resultados($grupo_evento_id, $categoria_id)
    {
        $grupo_evento = GrupoEvento::find($grupo_evento_id);
        $eventos = $grupo_evento->getEventosClassificacaoGeralPublica();
        $criterios = $grupo_evento->getCriteriosDesempateGerais();
        $modo_geral = false;
        $categoria = null;

        if ($categoria_id === 'geral') {
            if (!$grupo_evento->classificaIndividualGeral()) {
                return abort(404);
            }
            $modo_geral = true;
            $pontuacoes = PontuacaoEnxadrista::where([
                ["grupo_evento_id", "=", $grupo_evento->id],
            ])
                ->whereNull("categoria_id")
                ->orderBy("posicao", "ASC")
                ->get();
        } else {
            if (!$grupo_evento->classificaIndividualPorCategoria()) {
                return abort(404);
            }
            $categoria = Categoria::find($categoria_id);
            if (!$categoria || $categoria->nao_classificar) {
                return abort(404);
            }
            $pontuacoes = PontuacaoEnxadrista::where([
                ["grupo_evento_id", "=", $grupo_evento->id],
                ["categoria_id", "=", $categoria->id],
            ])
                ->orderBy("posicao", "ASC")
                ->get();
        }

        return view("grupoevento.publico.list", compact("grupo_evento", "eventos", "categoria", "pontuacoes", "criterios", "modo_geral"));
    }

    public function verPontuacaoEnxadrista($grupo_evento_id, $enxadrista_id)
    {
        $grupo_evento = GrupoEvento::find($grupo_evento_id);
        $enxadrista = Enxadrista::find($enxadrista_id);
        if ($grupo_evento && $enxadrista) {
            return view("grupoevento.publico.enxadrista", compact("grupo_evento", "enxadrista"));
        }
        return redirect()->back();
    }
}

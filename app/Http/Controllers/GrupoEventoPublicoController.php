<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GrupoEvento;
use App\Categoria;
use App\PontuacaoEnxadrista;
use App\Enxadrista;

class GrupoEventoPublicoController extends Controller
{
	public function classificacao($grupo_evento_id){
		$grupo_evento = GrupoEvento::find($grupo_evento_id);
		return view("grupoevento.publico.classificacao",compact("grupo_evento"));
	}
	public function resultados($grupo_evento_id,$categoria_id){
		$grupo_evento = GrupoEvento::find($grupo_evento_id);
		$eventos = $grupo_evento->getEventosClassificacaoGeralPublica();
		$categoria = Categoria::find($categoria_id);
		$pontuacoes = PontuacaoEnxadrista::where([
				["grupo_evento_id","=",$grupo_evento->id],
				["categoria_id","=",$categoria->id],
            ])
			->orderBy("posicao","ASC")
		->get();
		$criterios = $grupo_evento->getCriteriosDesempateGerais();
		return view("grupoevento.publico.list",compact("grupo_evento","eventos","categoria","pontuacoes","criterios"));
	}
	public function verPontuacaoEnxadrista($grupo_evento_id,$enxadrista_id){
		$grupo_evento = GrupoEvento::find($grupo_evento_id);
		$enxadrista = Enxadrista::find($enxadrista_id);
		if($grupo_evento && $enxadrista){
			return view("grupoevento.publico.enxadrista",compact("grupo_evento","enxadrista"));
		}
		return redirect()->back();
	}
}

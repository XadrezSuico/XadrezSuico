<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Evento;
use App\Categoria;
use App\Inscricao;

class EventoController extends Controller
{	
	// public function index(){
	// 	$eventos = Evento::all();
	// 	return view("evento.index",compact("eventos"));
	// }
	public function classificacao($evento_id){
		$evento = Evento::find($evento_id);
		if($evento->mostrar_resultados)
			return view("evento.publico.classificacao",compact("evento"));
		return view("evento.publico.classificacaonao",compact("evento"));
	}
	public function resultados($evento_id,$categoria_id){
		$evento = Evento::find($evento_id);
		$categoria = Categoria::find($categoria_id);
		$inscricoes = Inscricao::where([
				["categoria_id","=",$categoria->id],
				["confirmado","=",true]
            ])
            ->whereHas("torneio",function($q1) use ($evento){
                $q1->where([
                    ["evento_id","=",$evento->id]
                ]);
			})
			->orderBy("posicao","ASC")
		->get();
		if($evento->criterios()->count() > 0){
			$criterios = $evento->criterios()->orderBy("prioridade")->get();
		}else{
			$criterios = $evento->grupo_evento->criterios()->orderBy("prioridade")->get();
		}
		return view("evento.publico.list",compact("evento","categoria","inscricoes","criterios"));
	}
}

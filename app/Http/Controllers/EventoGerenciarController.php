<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Evento;
use App\Categoria;
use App\Inscricao;


class EventoGerenciarController extends Controller
{
	public function __construct(){
		return $this->middleware("auth");
	}
	
	public function index(){
		$eventos = Evento::all();
		return view("evento.index",compact("eventos"));
	}

	public function classificar($evento_id){
		$evento = Evento::find($evento_id);
		if($evento){
			foreach($evento->categorias->all() as $categoria){
				CategoriaController::classificar($evento->id,$categoria->categoria->id);
			}
			return redirect("/evento");
		}
    }
    
    public function toggleMostrarClassificacao($evento_id){
		$evento = Evento::find($evento_id);
		if($evento){
			if($evento->mostrar_resultados){
                $evento->mostrar_resultados = false;
            }else{
                $evento->mostrar_resultados = true;
            }
            $evento->save();
		    return redirect("/evento");
		}
	}
	


	public function classificacao($evento_id){
		$evento = Evento::find($evento_id);
		return view("evento.publico.classificacao",compact("evento"));
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

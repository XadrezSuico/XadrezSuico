<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Evento;
use App\Categoria;
use App\Inscricao;
use App\CriterioDesempate;
use App\TipoTorneio;
use App\Software;
use App\TipoRating;
use App\Cidade;


class EventoGerenciarController extends Controller
{
	public function __construct(){
		return $this->middleware("auth");
	}
	
	public function index(){
		$eventos = Evento::all();
		return view("evento.index",compact("eventos"));
	}

	public function edit($id,Request $request){
        $evento = Evento::find($id);
        $categorias = Categoria::all();
        $criterios_desempate = CriterioDesempate::criterios_evento()->get();
        $tipos_torneio = TipoTorneio::all();
        $softwares = Software::all();
        $tipos_rating = TipoRating::all();
        $cidades = Cidade::all();
        if($request->has("tab")){
            $tab = $request->input("tab");
        }else{
            $tab = NULL;    
        }
        return view('evento.edit',compact("evento","categorias","criterios_desempate","tipos_torneio","softwares","tipos_rating","cidades", "tab"));
	}

	public function classificar($evento_id){
		$evento = Evento::find($evento_id);
		if($evento){
			foreach($evento->categorias->all() as $categoria){
				CategoriaController::classificar($evento->id,$categoria->categoria->id);
			}
			// return redirect("/evento");
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
		$torneio = $categoria->getTorneioByEvento($evento);
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
		$criterios = $torneio->getCriterios();
		return view("evento.publico.list",compact("evento","categoria","inscricoes","criterios"));
	}
}

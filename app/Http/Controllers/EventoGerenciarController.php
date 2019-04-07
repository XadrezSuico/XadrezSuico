<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Evento;


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
				CategoriaController::classificar($evento->id,$categoria->id);
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
}

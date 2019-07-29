<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Evento;
use App\Categoria;
use App\Inscricao;
use App\CriterioDesempate;
use App\TipoTorneio;
use App\Software;
use App\TipoRating;
use App\Cidade;
use DateTime;


class EventoGerenciarController extends Controller
{
	public function __construct(){
		return $this->middleware("auth");
	}
	
	public function index(){
		$user = Auth::user();
		if(
			!$user->hasPermissionGlobal() && 
			!$user->hasPermissionEventsByPerfil([3,4,5]) && 
			!$user->hasPermissionGroupEventsByPerfil([6])
		){
			return redirect("/");
		}
		$eventos = Evento::all();
		return view("evento.index",compact("eventos"));
	}

	public function edit($id,Request $request){
		$user = Auth::user();
        $evento = Evento::find($id);
		if(
			!$user->hasPermissionGlobal() && 
			!$user->hasPermissionEventByPerfil($id,[3,4]) && 
			!$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[6])
		){
			return redirect("/");
		}
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

	public function edit_post($id,Request $request){
		$user = Auth::user();
		if(
			!$user->hasPermissionGlobal() && 
			!$user->hasPermissionEventByPerfil($id,[4])
		){
			return redirect("/evento/dashboard/".$id);
		}
		$evento = Evento::find($id);
		
        $datetime_data_inicio = DateTime::createFromFormat('d/m/Y', $request->input("data_inicio"));
        $datetime_data_fim = DateTime::createFromFormat('d/m/Y', $request->input("data_fim"));
        $datetime_data_limite_inscricoes_abertas = DateTime::createFromFormat('d/m/Y H:i', $request->input("data_limite_inscricoes_abertas"));

        // CADASTRO DO EVENTO
        $evento->name = $request->input("name");
        $evento->data_inicio = $datetime_data_inicio->format('Y-m-d');
        $evento->data_fim = $datetime_data_fim->format('Y-m-d');
        $evento->local = $request->input("local");
        $evento->cidade_id = $request->input("cidade_id");
        if($request->has("link")){
			$evento->link = $request->input("link");
		}else{
			$evento->link = NULL;
		}
        if($request->has("data_limite_inscricoes_abertas") && $datetime_data_limite_inscricoes_abertas){
			$evento->data_limite_inscricoes_abertas = $datetime_data_limite_inscricoes_abertas->format('Y-m-d H:i');
		}else{
			$evento->data_limite_inscricoes_abertas = NULL;
		}
		if($request->has("usa_fide") && !$request->has("usa_lbx")) $evento->usa_fide = true; else $evento->usa_fide = false;
        if($request->has("usa_cbx")) $evento->usa_cbx = true; else $evento->usa_cbx = false;
        if($request->has("usa_lbx")) $evento->usa_lbx = true; else $evento->usa_lbx = false;
        $evento->save();
		return redirect("/evento/dashboard/".$id);
	}

	public function classificar($evento_id){
		$user = Auth::user();
		if(
			!$user->hasPermissionGlobal() && 
			!$user->hasPermissionEventByPerfil($evento_id,[4])
		){
			return redirect("/");
		}
		$evento = Evento::find($evento_id);
		if($evento){
			foreach($evento->categorias->all() as $categoria){
				CategoriaController::classificar($evento->id,$categoria->categoria->id);
			}
			// return redirect("/evento");
		}
    }
    
    public function toggleMostrarClassificacao($evento_id){
		$user = Auth::user();
		if(
			!$user->hasPermissionGlobal() && 
			!$user->hasPermissionEventByPerfil($evento_id,[4])
		){
			return redirect("/");
		}
		$evento = Evento::find($evento_id);
		if($evento){
			if($evento->mostrar_resultados){
                $evento->mostrar_resultados = false;
            }else{
                $evento->mostrar_resultados = true;
            }
            $evento->save();
		    return redirect("/evento/dashboard/".$evento->id);
		}
	}
    
    public function toggleEventoClassificavel($evento_id){
		$user = Auth::user();
		if(
			!$user->hasPermissionGlobal() && 
			!$user->hasPermissionEventByPerfil($evento_id,[4])
		){
			return redirect("/");
		}
		$evento = Evento::find($evento_id);
		if($evento){
			if($evento->classificavel){
                $evento->classificavel = false;
            }else{
                $evento->classificavel = true;
            }
            $evento->save();
		    return redirect("/evento/dashboard/".$evento->id);
		}
	}
	


	public function classificacao($evento_id){
		$user = Auth::user();
		if(
			!$user->hasPermissionGlobal() && 
			!$user->hasPermissionEventsByPerfil([3,4,5]) && 
			!$user->hasPermissionGroupEventsByPerfil([6])
		){
			return redirect("/");
		}
		$evento = Evento::find($evento_id);
		return view("evento.publico.classificacao",compact("evento"));
	}
	public function resultados($evento_id,$categoria_id){
		$user = Auth::user();
		if(!$user->hasPermissionMain([1,2,3,4,5,6])){
			return redirect("/");
		}
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
		$criterios = $torneio->getCriteriosTotal();
		return view("evento.publico.list",compact("evento","categoria","inscricoes","criterios"));
	}
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use App\Evento;
use App\Categoria;
use App\Inscricao;
use App\CriterioDesempate;
use App\TipoTorneio;
use App\Software;
use App\TipoRating;
use App\TipoRatingEvento;
use App\Cidade;
use App\Pagina;
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
        $evento->tipo_modalidade = $request->input("tipo_modalidade");
        if($request->has("link")){
			$evento->link = $request->input("link");
		}else{
			$evento->link = NULL;
		}
        if($request->has("maximo_inscricoes_evento")){
			if(is_numeric($request->input("maximo_inscricoes_evento"))){
				$evento->maximo_inscricoes_evento = intval($request->input("maximo_inscricoes_evento"));
			}else{
				$evento->maximo_inscricoes_evento = NULL;
			}
		}else{
			$evento->maximo_inscricoes_evento = NULL;
		}
        if($request->has("data_limite_inscricoes_abertas") && $datetime_data_limite_inscricoes_abertas){
			$evento->data_limite_inscricoes_abertas = $datetime_data_limite_inscricoes_abertas->format('Y-m-d H:i');
		}else{
			$evento->data_limite_inscricoes_abertas = NULL;
		}
		if($request->has("e_permite_visualizar_lista_inscritos_publica")) $evento->e_permite_visualizar_lista_inscritos_publica = true; else $evento->e_permite_visualizar_lista_inscritos_publica = false;
		if($request->has("e_inscricao_apenas_com_link")){
			$evento->e_inscricao_apenas_com_link = true;
			if($evento->token == null){
				$evento->gerarToken();
			}
		}else{
			$evento->e_inscricao_apenas_com_link = false;
		}
		if($request->has("usa_fide") && !$request->has("usa_lbx")) $evento->usa_fide = true; else $evento->usa_fide = false;
        if($request->has("usa_cbx")) $evento->usa_cbx = true; else $evento->usa_cbx = false;
		if($request->has("usa_lbx")) $evento->usa_lbx = true; else $evento->usa_lbx = false;
		$evento->save();
		
		if($request->has("tipo_ratings_id")){
			if(
				($evento->tipo_rating_interno && $evento->tipo_rating) || 
				(!$evento->tipo_rating_interno && !$evento->tipo_rating)
			){
				if($request->input("tipo_ratings_id") != ""){
					if(!$evento->tipo_rating_interno){
						$tipo_rating = new TipoRatingEvento;
						$tipo_rating->evento_id = $evento->id;
						$tipo_rating->tipo_ratings_id = $request->input("tipo_ratings_id");
						$tipo_rating->save();
					}else{
						$evento->tipo_rating_interno->tipo_ratings_id = $request->input("tipo_ratings_id");
						$evento->tipo_rating_interno->save();
					}
				}else{
					if($evento->tipo_rating_interno){
						$evento->tipo_rating_interno->delete();
					}
				}
			}
		}

		return redirect("/evento/dashboard/".$id);
	}

	public function edit_pagina_post($id,Request $request){
		$user = Auth::user();
		if(
			!$user->hasPermissionGlobal() && 
			!$user->hasPermissionEventByPerfil($id,[4])
		){
			return redirect("/evento/dashboard/".$id);
		}
		$evento = Evento::find($id);

		if(!$evento->pagina){
			$pagina = new Pagina;
			$pagina->evento_id = $id;
			$pagina->save();

			$evento = Evento::find($id);
		}
		
        // CADASTRO DO EVENTO
		if($request->hasFile('imagem')) {
			if($request->file('imagem')->isValid()) {
				$evento->pagina->imagem = base64_encode(file_get_contents($request->file('imagem')));
			}
		}
        if($request->has("texto")){
			$evento->pagina->texto = $request->input("texto");
		}else{
			$evento->pagina->texto = NULL;
		}
		if($request->has('remover_imagem')) {
			$evento->pagina->imagem = NULL;
		}  
        $evento->pagina->save();
		return redirect("/evento/dashboard/".$id."?tab=pagina");
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

			$messageBag = new MessageBag;
			$messageBag->add("alerta","O evento foi classificado com sucesso!");
			$messageBag->add("type","success");

			return redirect("/evento/dashboard/".$evento->id)->withErrors($messageBag);
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
    public function toggleClassificacaoManual($evento_id){
		$user = Auth::user();
		if(
			!$user->hasPermissionGlobal() && 
			!$user->hasPermissionEventByPerfil($evento_id,[4])
		){
			return redirect("/");
		}
		$evento = Evento::find($evento_id);
		if($evento){
			if($evento->e_resultados_manuais){
                $evento->e_resultados_manuais = false;
            }else{
                $evento->e_resultados_manuais = true;
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

	

    public function visualizar_inscricoes($id){
        $evento = Evento::find($id);
        if($evento){
            return view("evento.inscricoes",compact("evento"));
        }
        return redirect("/evento");
    }
}

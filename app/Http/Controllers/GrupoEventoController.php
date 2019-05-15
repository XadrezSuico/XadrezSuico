<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GrupoEvento;
use App\Categoria;
use App\TorneioTemplate;
use App\TorneioTemplateGrupoEvento;
use App\CategoriaGrupoEvento;
use App\CriterioDesempate;
use App\TipoTorneio;
use App\Software;
use App\CriterioDesempateGrupoEvento;
use App\CriterioDesempateGrupoEventoGeral;
use App\Pontuacao;
use App\TipoRating;
use App\TipoRatingGrupoEvento;
use App\Cidade;

class GrupoEventoController extends Controller
{
	public function __construct(){
		return $this->middleware("auth");
	}
    public function index(){
        $grupos_evento = GrupoEvento::all();
        return view('grupoevento.index',compact("grupos_evento"));
    }
    public function new(){
        return view('grupoevento.new');
    }
    public function new_post(Request $request){
        $grupo_evento = new GrupoEvento;
        $grupo_evento->name = $request->input("name");
        $grupo_evento->save();

        if($request->has("tipo_ratings_id")){
            if($request->input("tipo_ratings_id")){
                $tipo_rating_grupo_evento = new TipoRatingGrupoEvento;
                $tipo_rating_grupo_evento->grupo_evento_id = $grupo_evento->id;
                $tipo_rating_grupo_evento->tipo_ratings_id = $request->input("tipo_ratings_id");
                $tipo_rating_grupo_evento->save();
            }
        }

        return redirect("/grupoevento/dashboard/".$grupo_evento->id);
    }
    public function edit($id){
        $grupo_evento = GrupoEvento::find($id);
        $torneio_templates = TorneioTemplate::all();
        $categorias = Categoria::all();
        $criterios_desempate = CriterioDesempate::criterios_evento()->get();
        $criterios_desempate_geral = CriterioDesempate::criterios_grupo_evento()->get();
        $tipos_torneio = TipoTorneio::all();
        $softwares = Software::all();
        $tipos_rating = TipoRating::all();
        $cidades = Cidade::all();
        return view('grupoevento.edit',compact("grupo_evento","torneio_templates","categorias","criterios_desempate","tipos_torneio","softwares","criterios_desempate_geral","tipos_rating","cidades"));
    }
    public function edit_post($id,Request $request){
        $grupo_evento = GrupoEvento::find($id);
        $grupo_evento->name = $request->input("name");
        $grupo_evento->save();

        if($request->has("tipo_ratings_id")){
            if($request->input("tipo_ratings_id")){
                if(!$grupo_evento->tipo_rating){
                    $tipo_rating_grupo_evento = new TipoRatingGrupoEvento;
                    $tipo_rating_grupo_evento->grupo_evento_id = $grupo_evento->id;
                    $tipo_rating_grupo_evento->tipo_ratings_id = $request->input("tipo_ratings_id");
                    $tipo_rating_grupo_evento->save();
                }else{
                    $grupo_evento->tipo_rating->tipo_ratings_id = $request->input("tipo_ratings_id");
                    $grupo_evento->tipo_rating->save();
                }
            }else{
                if($grupo_evento->tipo_rating){
                    $grupo_evento->tipo_rating->delete();
                }
            }
        }else{
            if($grupo_evento->tipo_rating){
                $grupo_evento->tipo_rating->delete();
            }
        }
        return redirect("/grupoevento/dashboard/".$grupo_evento->id);
    }
    public function delete($id){
        $grupo_evento = GrupoEvento::find($id);
        
        if($grupo_evento->isDeletavel()){
            $grupo_evento->delete();
        }
        return redirect("/grupoevento");
    }

    
    public function torneio_template_add($id,Request $request){
        $torneio_template_grupo_evento = new TorneioTemplateGrupoEvento;
        $torneio_template_grupo_evento->grupo_evento_id = $id;
        $torneio_template_grupo_evento->torneio_template_id = $request->input("torneio_template_id");
        $torneio_template_grupo_evento->save();
        return redirect("/grupoevento/dashboard/".$id."?tab=torneio_template");
    }
    public function torneio_template_remove($id,$torneio_template_grupo_evento_id){
        $torneio_template_grupo_evento = TorneioTemplateGrupoEvento::find($torneio_template_grupo_evento_id);
        $torneio_template_grupo_evento->delete();
        return redirect("/grupoevento/dashboard/".$id."?tab=torneio_template");
    }


    
    public function categoria_add($id,Request $request){
        $categoria_grupo_evento = new CategoriaGrupoEvento;
        $categoria_grupo_evento->grupo_evento_id = $id;
        $categoria_grupo_evento->categoria_id = $request->input("categoria_id");
        $categoria_grupo_evento->save();
        return redirect("/grupoevento/dashboard/".$id."?tab=categoria");
    }
    public function categoria_remove($id,$categoria_grupo_evento_id){
        $categoria_grupo_evento = CategoriaGrupoEvento::find($categoria_grupo_evento_id);
        $categoria_grupo_evento->delete();
        return redirect("/grupoevento/dashboard/".$id."?tab=categoria");
    }
    

    public function criterio_desempate_add($id,Request $request){
        $criterio_desempate_grupo_evento = new CriterioDesempateGrupoEvento;
        $criterio_desempate_grupo_evento->grupo_evento_id = $id;
        $criterio_desempate_grupo_evento->criterio_desempate_id = $request->input("criterio_desempate_id");
        $criterio_desempate_grupo_evento->tipo_torneio_id = $request->input("tipo_torneio_id");
        $criterio_desempate_grupo_evento->softwares_id = $request->input("softwares_id");
        $criterio_desempate_grupo_evento->prioridade = $request->input("prioridade");
        $criterio_desempate_grupo_evento->save();
        return redirect("/grupoevento/dashboard/".$id."?tab=criterio_desempate");
    }
    public function criterio_desempate_remove($id,$cd_grupo_evento_id){
        $criterio_desempate_grupo_evento = CriterioDesempateGrupoEvento::find($cd_grupo_evento_id);
        $criterio_desempate_grupo_evento->delete();
        return redirect("/grupoevento/dashboard/".$id."?tab=criterio_desempate");
    }
    

    public function criterio_desempate_geral_add($id,Request $request){
        $criterio_desempate_grupo_evento_geral = new CriterioDesempateGrupoEventoGeral;
        $criterio_desempate_grupo_evento_geral->grupo_evento_id = $id;
        $criterio_desempate_grupo_evento_geral->criterio_desempate_id = $request->input("criterio_desempate_id");
        $criterio_desempate_grupo_evento_geral->prioridade = $request->input("prioridade");
        $criterio_desempate_grupo_evento_geral->save();
        return redirect("/grupoevento/dashboard/".$id."?tab=criterio_desempate_geral");
    }
    public function criterio_desempate_geral_remove($id,$cd_grupo_evento_geral_id){
        $criterio_desempate_grupo_evento_geral = CriterioDesempateGrupoEventoGeral::find($cd_grupo_evento_geral_id);
        $criterio_desempate_grupo_evento_geral->delete();
        return redirect("/grupoevento/dashboard/".$id."?tab=criterio_desempate_geral");
    }
    

    public function pontuacao_add($id,Request $request){
        $pontuacao = new Pontuacao;
        $pontuacao->grupo_evento_id = $id;
        $pontuacao->posicao = $request->input("posicao");
        $pontuacao->pontuacao = $request->input("pontuacao");
        $pontuacao->save();
        return redirect("/grupoevento/dashboard/".$id."?tab=pontuacao");
    }
    public function pontuacao_remove($id,$pontuacao_id){
        $pontuacao = Pontuacao::find($pontuacao_id);
        $pontuacao->delete();
        return redirect("/grupoevento/dashboard/".$id."?tab=pontuacao");
    }

    

	public function classificar($grupo_evento_id){
		$grupo_evento = GrupoEvento::find($grupo_evento_id);
		if($grupo_evento){
			foreach($grupo_evento->categorias->all() as $categoria){
				CategoriaController::classificar_geral($grupo_evento->id,$categoria->categoria->id);
			}
			// return redirect("/grupoevento/dashboard/".$grupo_evento_id);
		}
    }
}

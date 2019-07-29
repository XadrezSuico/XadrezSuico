<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
use App\Torneio;
use App\Evento;
use App\CategoriaEvento;
use App\CategoriaTorneio;
use DateTime;

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
    public function edit($id,Request $request){
        $user = Auth::user();
        $grupo_evento = GrupoEvento::find($id);
        $torneio_templates = TorneioTemplate::all();
        $categorias = Categoria::all();
        $criterios_desempate = CriterioDesempate::criterios_evento()->get();
        $criterios_desempate_geral = CriterioDesempate::criterios_grupo_evento()->get();
        $tipos_torneio = TipoTorneio::all();
        $softwares = Software::all();
        $tipos_rating = TipoRating::all();
        $cidades = Cidade::all();
        if($request->has("tab")){
            $tab = $request->input("tab");
        }else{
            $tab = NULL;    
        }
        return view('grupoevento.edit',compact("grupo_evento","torneio_templates","categorias","criterios_desempate","tipos_torneio","softwares","criterios_desempate_geral","tipos_rating","cidades", "tab", "user"));
    }
    public function edit_post($id,Request $request){
        $grupo_evento = GrupoEvento::find($id);
        $grupo_evento->name = $request->input("name");
        if($request->has("limite_calculo_geral")){
            if($request->input("limite_calculo_geral") != ""){
                $grupo_evento->limite_calculo_geral = $request->input("limite_calculo_geral");
            }else{
                $grupo_evento->limite_calculo_geral = NULL;
            }
        }else{
            $grupo_evento->limite_calculo_geral = NULL;
        }
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
        if($request->has("nao_classificar")) $categoria_grupo_evento->nao_classificar = true;
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
        $retornos = array();
		$grupo_evento = GrupoEvento::find($grupo_evento_id);
		if($grupo_evento){
			foreach($grupo_evento->categorias->all() as $categoria){
				$retornos = array_merge($retornos,CategoriaController::classificar_geral($grupo_evento->id,$categoria->categoria->id));
			}
			return view("grupoevento.retornos",compact("grupo_evento","retornos"));
		}
    }

    public function evento_new($id,Request $request){
        $grupo_evento = GrupoEvento::find($id);

        
        $datetime_data_inicio = DateTime::createFromFormat('d/m/Y', $request->input("data_inicio"));
        $datetime_data_fim = DateTime::createFromFormat('d/m/Y', $request->input("data_fim"));
        $datetime_data_limite_inscricoes_abertas = DateTime::createFromFormat('d/m/Y H:i', $request->input("data_limite_inscricoes_abertas"));

        // CADASTRO DO EVENTO
        $evento = new Evento;
        $evento->name = $request->input("name");
        $evento->data_inicio = $datetime_data_inicio->format('Y-m-d');
        $evento->data_fim = $datetime_data_fim->format('Y-m-d');
        $evento->local = $request->input("local");
        $evento->cidade_id = $request->input("cidade_id");
        if($request->has("link")) $evento->link = $request->input("link");
        if($request->has("data_limite_inscricoes_abertas") && $datetime_data_limite_inscricoes_abertas) $evento->data_limite_inscricoes_abertas = $datetime_data_limite_inscricoes_abertas->format('Y-m-d H:i');
        if($request->has("usa_fide")) $evento->usa_fide = true;
        if($request->has("usa_cbx")) $evento->usa_cbx = true;
        if($request->has("usa_lbx")) $evento->usa_lbx = true;
        $evento->grupo_evento_id = $grupo_evento->id;
        $evento->save();

        // IMPORTAÇÃO DAS CATEGORIAS
        foreach($grupo_evento->categorias->all() as $categoria){
            $categoria_evento = new CategoriaEvento;
            $categoria_evento->categoria_id = $categoria->categoria->id;
            $categoria_evento->evento_id = $evento->id;
            $categoria_evento->save();
        }

        // IMPORTAÇÃO DOS TORNEIOS A PARTIR DOS TEMPLATES
        foreach($grupo_evento->torneios_template->all() as $torneio_template){
            $torneio = new Torneio;
            $torneio->name = $torneio_template->template->name;
            $torneio->evento_id = $evento->id;
            $torneio->tipo_torneio_id = 1;
            $torneio->torneio_template_id = $torneio_template->template->id;
            $torneio->save();

            // IMPORTAÇÃO DAS CATEGORIAS PARA O TORNEIO A PARTIR DO TEMPLATE
            foreach($torneio_template->template->categorias->all() as $categoria){
                $categoria_torneio = new CategoriaTorneio;
                $categoria_torneio->categoria_id = $categoria->categoria->id;
                $categoria_torneio->torneio_id = $torneio->id;
                $categoria_torneio->save();
            }
        }

        
	    return redirect("/grupoevento/dashboard/".$grupo_evento->id."?tab=evento");
    }
}

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
        return redirect("/grupoevento/dashboard/".$grupo_evento->id);
    }
    public function edit($id){
        $grupo_evento = GrupoEvento::find($id);
        $torneio_templates = TorneioTemplate::all();
        $categorias = Categoria::all();
        $criterios_desempate = CriterioDesempate::all();
        $tipos_torneio = TipoTorneio::all();
        $softwares = Software::all();
        return view('grupoevento.edit',compact("grupo_evento","torneio_templates","categorias","criterios_desempate","tipos_torneio","softwares"));
    }
    public function edit_post($id,Request $request){
        $grupo_evento = GrupoEvento::find($id);
        $grupo_evento->name = $request->input("name");
        $grupo_evento->save();
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
        return redirect("/grupoevento/dashboard/".$id);
    }
    public function torneio_template_remove($id,$torneio_template_grupo_evento_id){
        $torneio_template_grupo_evento = TorneioTemplateGrupoEvento::find($torneio_template_grupo_evento_id);
        $torneio_template_grupo_evento->delete();
        return redirect("/grupoevento/dashboard/".$id);
    }


    
    public function categoria_add($id,Request $request){
        $categoria_grupo_evento = new CategoriaGrupoEvento;
        $categoria_grupo_evento->grupo_evento_id = $id;
        $categoria_grupo_evento->categoria_id = $request->input("categoria_id");
        $categoria_grupo_evento->save();
        return redirect("/grupoevento/dashboard/".$id);
    }
    public function categoria_remove($id,$categoria_grupo_evento_id){
        $categoria_grupo_evento = CategoriaGrupoEvento::find($categoria_grupo_evento_id);
        $categoria_grupo_evento->delete();
        return redirect("/grupoevento/dashboard/".$id);
    }
    

    public function criterio_desempate_add($id,Request $request){
        $criterio_desempate_grupo_evento = new CriterioDesempateGrupoEvento;
        $criterio_desempate_grupo_evento->grupo_evento_id = $id;
        $criterio_desempate_grupo_evento->criterio_desempate_id = $request->input("criterio_desempate_id");
        $criterio_desempate_grupo_evento->tipo_torneio_id = $request->input("tipo_torneio_id");
        $criterio_desempate_grupo_evento->softwares_id = $request->input("softwares_id");
        $criterio_desempate_grupo_evento->prioridade = $request->input("prioridade");
        $criterio_desempate_grupo_evento->save();
        return redirect("/grupoevento/dashboard/".$id);
    }
    public function criterio_desempate_remove($id,$cd_grupo_evento_id){
        $criterio_desempate_grupo_evento = CriterioDesempateGrupoEvento::find($cd_grupo_evento_id);
        $criterio_desempate_grupo_evento->delete();
        return redirect("/grupoevento/dashboard/".$id);
    }
}

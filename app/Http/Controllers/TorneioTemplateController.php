<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TorneioTemplate;
use App\Categoria;
use App\CategoriaTorneioTemplate;
use App\GrupoEvento;

class TorneioTemplateController extends Controller
{
	public function __construct(){
		return $this->middleware("auth");
	}
    // public function index($grupo_evento_id){
    //     $grupo_evento = GrupoEvento::find($grupo_evento_id);

    //     $torneios_template = TorneioTemplate::all();
    //     return view('torneiotemplate.index',compact("torneios_template"));
    // }
    // public function new($grupo_evento_id){
    //     $grupo_evento = GrupoEvento::find($grupo_evento_id);
        
    //     return view('torneiotemplate.new');
    // }
    public function new_post($grupo_evento_id,Request $request){
        $grupo_evento = GrupoEvento::find($grupo_evento_id);
        
        $torneio_template = new TorneioTemplate;
        $torneio_template->name = $request->input("name");
        $torneio_template->grupo_evento_id = $grupo_evento->id;
        $torneio_template->torneio_name = $request->input("torneio_name");
        $torneio_template->save();
        return redirect("/grupoevento/".$grupo_evento->id."/torneiotemplates/dashboard/".$id);
    }
    public function edit($grupo_evento_id,$id){
        $grupo_evento = GrupoEvento::find($grupo_evento_id);
        
        $torneio_template = TorneioTemplate::find($id);
        return view('grupoevento.torneiotemplate.edit',compact("grupo_evento","torneio_template","categorias"));
    }
    public function edit_post($grupo_evento_id,$id,Request $request){
        $grupo_evento = GrupoEvento::find($grupo_evento_id);
        
        $torneio_template = TorneioTemplate::find($id);
        $torneio_template->name = $request->input("name");
        $torneio_template->torneio_name = $request->input("torneio_name");
        $torneio_template->save();
        return redirect("/grupoevento/".$grupo_evento->id."/torneiotemplates/dashboard/".$id);
    }
    public function delete($grupo_evento_id,$id){
        $grupo_evento = GrupoEvento::find($grupo_evento_id);
        
        $torneio_template = TorneioTemplate::find($id);
        
        if($torneio_template->isDeletavel()){
            $torneio_template->delete();
        }
        return redirect("/grupoevento/dashboard/".$grupo_evento->id."?tab=template_torneio");
    }
    public function categoria_add($grupo_evento_id,$id,Request $request){
        $grupo_evento = GrupoEvento::find($grupo_evento_id);
        
        $categoria_torneio_template = new CategoriaTorneioTemplate;
        $categoria_torneio_template->torneio_template_id = $id;
        $categoria_torneio_template->categoria_id = $request->input("categoria_id");
        $categoria_torneio_template->save();
        return redirect("/grupoevento/".$grupo_evento->id."/torneiotemplates/dashboard/".$id);
    }
    public function categoria_remove($grupo_evento_id,$id,$categoria_torneio_id){
        $grupo_evento = GrupoEvento::find($grupo_evento_id);
        
        $categoria_torneio_template = CategoriaTorneioTemplate::find($categoria_torneio_id);
        $categoria_torneio_template->delete();
        return redirect("/grupoevento/".$grupo_evento->id."/torneiotemplates/dashboard/".$id);
    }
}

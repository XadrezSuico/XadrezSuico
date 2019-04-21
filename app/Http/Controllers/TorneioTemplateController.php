<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TorneioTemplate;
use App\Categoria;
use App\CategoriaTorneioTemplate;

class TorneioTemplateController extends Controller
{
	public function __construct(){
		return $this->middleware("auth");
	}
    public function index(){
        $torneios_template = TorneioTemplate::all();
        return view('torneiotemplate.index',compact("torneios_template"));
    }
    public function new(){
        return view('torneiotemplate.new');
    }
    public function new_post(Request $request){
        $torneio_template = new TorneioTemplate;
        $torneio_template->name = $request->input("name");
        $torneio_template->torneio_name = $request->input("torneio_name");
        $torneio_template->save();
        return redirect("/torneiotemplate/dashboard/".$torneio_template->id);
    }
    public function edit($id){
        $torneio_template = TorneioTemplate::find($id);
        $categorias = Categoria::all();
        return view('torneiotemplate.edit',compact("torneio_template","categorias"));
    }
    public function edit_post($id,Request $request){
        $torneio_template = TorneioTemplate::find($id);
        $torneio_template->name = $request->input("name");
        $torneio_template->torneio_name = $request->input("torneio_name");
        $torneio_template->save();
        return redirect("/torneiotemplate/dashboard/".$torneio_template->id);
    }
    public function delete($id){
        $torneio_template = TorneioTemplate::find($id);
        
        if($torneio_template->isDeletavel()){
            $torneio_template->delete();
        }
        return redirect("/torneiotemplate");
    }
    public function categoria_add($id,Request $request){
        $categoria_torneio_template = new CategoriaTorneioTemplate;
        $categoria_torneio_template->torneio_template_id = $id;
        $categoria_torneio_template->categoria_id = $request->input("categoria_id");
        $categoria_torneio_template->save();
        return redirect("/torneiotemplate/dashboard/".$id);
    }
    public function categoria_remove($id,$categoria_torneio_id){
        $categoria_torneio_template = CategoriaTorneioTemplate::find($categoria_torneio_id);
        $categoria_torneio_template->delete();
        return redirect("/torneiotemplate/dashboard/".$id);
    }
}

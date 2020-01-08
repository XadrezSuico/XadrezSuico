<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GrupoEvento;
use App\Evento;
use App\Categoria;
use App\Inscricao;
use App\Sexo;
use App\CategoriaSexo;
use App\Pontuacao;
use App\PontuacaoEnxadrista;
use App\Enxadrista;
use App\EnxadristaCriterioDesempateGeral;

class CategoriaGrupoEventoController extends Controller
{	
    public function __construct(){
		return $this->middleware("auth");
	}
    public function index($grupo_evento_id){
        $grupo_evento = GrupoEvento::find($grupo_evento_id);
        $categorias = $grupo_evento->categorias->all();
        return view('grupoevento.categoria.index',compact("categorias","grupo_evento"));
    }
    public function new($grupo_evento_id){
        $grupo_evento = GrupoEvento::find($grupo_evento_id);
        return view('grupoevento.categoria.new',compact("grupo_evento"));
    }
    public function new_post($grupo_evento_id,Request $request){
        $grupo_evento = GrupoEvento::find($grupo_evento_id);

        $categoria = new Categoria;
        $categoria->name = $request->input("name");
        $categoria->idade_minima = $request->input("idade_minima");
        $categoria->idade_maxima = $request->input("idade_maxima");
        $categoria->code = $request->input("code");
        $categoria->cat_code = $request->input("cat_code");
        if($request->has("nao_classificar")) $categoria->nao_classificar = true;
        $categoria->grupo_evento_id = $grupo_evento->id;
        $categoria->save();

        return redirect("/grupoevento/".$grupo_evento->id."/categorias/dashboard/".$categoria->id);
    }
    public function edit($grupo_evento_id, $id){
        $grupo_evento = GrupoEvento::find($grupo_evento_id);
        $categoria = Categoria::find($id);
        $sexos = Sexo::all();
        return view('grupoevento.categoria.edit',compact("categoria","sexos","grupo_evento"));
    }
    public function edit_post($grupo_evento_id, $id,Request $request){
        $grupo_evento = GrupoEvento::find($grupo_evento_id);

        $categoria = Categoria::find($id);
        $categoria->name = $request->input("name");
        $categoria->idade_minima = $request->input("idade_minima");
        $categoria->idade_maxima = $request->input("idade_maxima");
        $categoria->code = $request->input("code");
        $categoria->cat_code = $request->input("cat_code");
        if($request->has("nao_classificar")){
            $categoria->nao_classificar = true;
        }else{
            $categoria->nao_classificar = false;
        }
        $categoria->save();

        return redirect("/grupoevento/".$grupo_evento->id."/categorias/dashboard/".$categoria->id);
    }
    public function delete($grupo_evento_id, $id){
        $grupo_evento = GrupoEvento::find($grupo_evento_id);
        $categoria = Categoria::find($id);
        
        if($categoria->isDeletavel()){
            $categoria->delete();
        }
        return redirect("/grupoevento/dashboard/".$grupo_evento->id."?tab=categoria");
    }


    

    public function sexo_add($grupo_evento_id,$id,Request $request){
        $grupo_evento = GrupoEvento::find($grupo_evento_id);
        $categoria = Categoria::find($id);

        $categoria_sexo = new CategoriaSexo;
        $categoria_sexo->categoria_id = $id;
        $categoria_sexo->sexos_id = $request->input("sexos_id");
        $categoria_sexo->save();
        return redirect("/grupoevento/".$grupo_evento->id."/categorias/dashboard/".$categoria->id);
    }
    public function sexo_remove($grupo_evento_id,$id,$categoria_sexo_id){
        $grupo_evento = GrupoEvento::find($grupo_evento_id);
        $categoria = Categoria::find($id);

        $categoria_sexo = CategoriaSexo::find($categoria_sexo_id);
        $categoria_sexo->delete();
        return redirect("/grupoevento/".$grupo_evento->id."/categorias/dashboard/".$categoria->id);
    }
}

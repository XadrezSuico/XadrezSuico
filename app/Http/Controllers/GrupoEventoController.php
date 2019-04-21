<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GrupoEvento;

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
        return view('grupoevento.edit',compact("grupo_evento"));
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
}

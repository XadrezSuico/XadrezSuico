<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Enxadrista;
use App\Cidade;
use App\Clube;
use App\Sexo;

class EnxadristaController extends Controller
{
	public function __construct(){
		return $this->middleware("auth");
	}
    public function index(){
        $enxadristas = Enxadrista::all();
        return view('enxadrista.index',compact("enxadristas"));
    }
    public function new(){
        $cidades = Cidade::all();
        $clubes = Clube::all();
        $sexos = Sexo::all();
        return view('enxadrista.new',compact("cidades","clubes","sexos"));
    }
    public function new_post(Request $request){
        $enxadrista = new Enxadrista;
        $enxadrista->name = $request->input("name");
        $enxadrista->setBorn($request->input("born"));
        $enxadrista->cidade_id = $request->input("cidade_id");
        $enxadrista->sexos_id = $request->input("sexos_id");
        if($request->has("clube_id")) if($request->input("clube_id")) $enxadrista->clube_id = $request->input("clube_id");
        if($request->has("email")) if($request->input("email")) $enxadrista->email = $request->input("email");
        if($request->has("cbx_id")) if($request->input("cbx_id")) $enxadrista->cbx_id = $request->input("cbx_id");
        if($request->has("fide_id")) if($request->input("fide_id")) $enxadrista->fide_id = $request->input("fide_id");
        $enxadrista->save();
        return redirect("/enxadrista/edit/".$enxadrista->id);
    }
    public function edit($id){
        $enxadrista = Enxadrista::find($id);
        $cidades = Cidade::all();
        $clubes = Clube::all();
        $sexos = Sexo::all();
        return view('enxadrista.edit',compact("enxadrista","cidades","clubes","sexos"));
    }
    public function edit_post($id,Request $request){
        $enxadrista = Enxadrista::find($id);
        $enxadrista->name = $request->input("name");
        $enxadrista->setBorn($request->input("born"));
        $enxadrista->cidade_id = $request->input("cidade_id");
        $enxadrista->sexos_id = $request->input("sexos_id");
        if($request->has("clube_id")){
            if($request->input("clube_id")){
                $enxadrista->clube_id = $request->input("clube_id");
            }else{
                $enxadrista->clube_id = NULL;
            }
        }else{
            $enxadrista->clube_id = NULL;
        }
        if($request->has("email")){
            if($request->input("email")){
                $enxadrista->email = $request->input("email");
            }else{
                $enxadrista->email = NULL;
            }
        }else{
            $enxadrista->email = NULL;
        }
        if($request->has("cbx_id")){
            if($request->input("cbx_id")){
                $enxadrista->cbx_id = $request->input("cbx_id");
            }else{
                $enxadrista->cbx_id = NULL;
            }
        }else{
            $enxadrista->cbx_id = NULL;
        }
        if($request->has("fide_id")){
            if($request->input("fide_id")){
                $enxadrista->fide_id = $request->input("fide_id");
            }else{
                $enxadrista->fide_id = NULL;
            }
        }else{
            $enxadrista->fide_id = NULL;
        }
        $enxadrista->save();
        return redirect("/enxadrista/edit/".$enxadrista->id);
    }
    public function delete($id){
        $enxadrista = Enxadrista::find($id);
        
        if($enxadrista->isDeletavel()){
            $enxadrista->delete();
        }
        return redirect("/enxadrista");
    }
}

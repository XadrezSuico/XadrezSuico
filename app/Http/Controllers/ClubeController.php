<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Clube;
use App\Cidade;

class ClubeController extends Controller
{
	public function __construct(){
		return $this->middleware("auth");
	}
    public function index(){
        $clubes = Clube::all();
        return view('clube.index',compact("clubes"));
    }
    public function new(){
        $cidades = Cidade::all();
        return view('clube.new',compact("cidades"));
    }
    public function new_post(Request $request){
        $clube = new Clube;
        $clube->name = $request->input("name");
        $clube->cidade_id = $request->input("cidade_id");
        $clube->save();
        return redirect("/clube/edit/".$clube->id);
    }
    public function edit($id){
        $clube = Clube::find($id);
        $cidades = Cidade::all();
        return view('clube.edit',compact("clube","cidades"));
    }
    public function edit_post($id,Request $request){
        $clube = Clube::find($id);
        $clube->name = $request->input("name");
        $clube->cidade_id = $request->input("cidade_id");
        $clube->save();
        return redirect("/clube/edit/".$clube->id);
    }
    public function delete($id){
        $clube = Clube::find($id);
        
        if($clube->isDeletavel()){
            $clube->delete();
        }
        return redirect("/clube");
    }

}

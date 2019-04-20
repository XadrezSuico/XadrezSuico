<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sexo;

class SexoController extends Controller
{
	public function __construct(){
		return $this->middleware("auth");
	}
    public function index(){
        $sexos = Sexo::all();
        return view('sexo.index',compact("sexos"));
    }
    public function new(){
        return view('sexo.new');
    }
    public function new_post(Request $request){
        $sexo = new Sexo;
        $sexo->name = $request->input("name");
        $sexo->abbr = $request->input("abbr");
        $sexo->save();
        return redirect("/sexo/edit/".$sexo->id);
    }
    public function edit($id){
        $sexo = Sexo::find($id);
        return view('cidade.edit',compact("cidade"));
    }
    public function edit_post($id,Request $request){
        $sexo = Sexo::find($id);
        $sexo->name = $request->input("name");
        $sexo->abbr = $request->input("abbr");
        $sexo->save();
        return redirect("/sexo/edit/".$sexo->id);
    }
    public function delete($id){
        $sexo = Sexo::find($id);
        
        if($sexo->isDeletavel()){
            $sexo->delete();
        }
        return redirect("/sexo");
    }

}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cidade;

class CidadeController extends Controller
{
	public function __construct(){
		return $this->middleware("auth");
	}
    public function index(){
        $cidades = Cidade::all();
        return view('cidade.index',compact("cidades"));
    }
    public function new(){
        return view('cidade.new');
    }
    public function new_post(Request $request){
        $cidade = new Cidade;
        $cidade->name = $request->input("name");
        $cidade->save();
        return redirect("/cidade/edit/".$cidade->id);
    }
    public function edit($id){
        $cidade = Cidade::find($id);
        return view('cidade.edit',compact("cidade"));
    }
    public function edit_post($id,Request $request){
        $cidade = Cidade::find($id);
        $cidade->name = $request->input("name");
        $cidade->save();
        return redirect("/cidade/edit/".$cidade->id);
    }
    public function delete($id){
        $cidade = Cidade::find($id);
        
        if($cidade->isDeletavel()){
            $cidade->delete();
        }
        return redirect("/cidade");
    }

}

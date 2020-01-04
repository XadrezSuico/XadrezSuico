<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enxadrista;
use App\Cidade;
use App\Clube;
use App\Sexo;
use App\Exports\EnxadristasCompletoFromView;
use Maatwebsite\Excel\Facades\Excel;

class EnxadristaController extends Controller
{
	public function __construct(){
		return $this->middleware("auth");
	}
    public function index(){
        // $enxadristas = Enxadrista::all();
        $enxadristas = Enxadrista::where([["id","=",1]])->get();
        return view('enxadrista.index',compact("enxadristas"));
    }
    public function new(){
        $cidades = Cidade::all();
        $clubes = Clube::all();
        $sexos = Sexo::all();
        return view('enxadrista.new',compact("cidades","clubes","sexos"));
    }
    public function new_post(Request $request){
        $enx = new Enxadrista;
        $enx->setBorn($request->input("born"));
        
        // Algoritmo para eliminar os problemas com espaçamentos duplos ou até triplos.
        $nome_corrigido = "";

        $part_names = explode(" ",mb_strtoupper($request->input("name")));
        foreach($part_names as $part_name){
            if($part_name != ' '){
                $trim = trim($part_name);
                if(strlen($trim) > 0){
                    $nome_corrigido .= $trim;
                    $nome_corrigido .= " ";
                }
            }
        }
        $nome_corrigido = trim($nome_corrigido);

        $validator = \Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|max:255',
            'born' => 'required|string',
            'cidade_id' => 'required|string',
            'sexos_id' => 'required|string',
            'celular' => 'required|string',
        ]);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator->errors());
        }

        $temEnxadrista = Enxadrista::where([
            ["name","=",$nome_corrigido],
            ["born","=",$enx->born]
        ])->get();
        if(count($temEnxadrista) > 0){
            return redirect()->back();
            exit();
        }else{
            $enxadrista = new Enxadrista;
            $enxadrista->name = $nome_corrigido;
            $enxadrista->setBorn($request->input("born"));
            $enxadrista->cidade_id = $request->input("cidade_id");
            $enxadrista->sexos_id = $request->input("sexos_id");
            $enxadrista->celular = $request->input("celular");
            $enxadrista->email = $request->input("email");
            if($request->has("clube_id")) if($request->input("clube_id")) $enxadrista->clube_id = $request->input("clube_id");
            if($request->has("cbx_id")) if($request->input("cbx_id")) $enxadrista->cbx_id = $request->input("cbx_id");
            if($request->has("fide_id")) if($request->input("fide_id")) $enxadrista->fide_id = $request->input("fide_id");
            if($request->has("lbx_id")) if($request->input("lbx_id")) $enxadrista->lbx_id = $request->input("lbx_id");
            $enxadrista->save();
            return redirect("/enxadrista/edit/".$enxadrista->id);
        }
    }
    public function edit($id){
        $enxadrista = Enxadrista::find($id);
        $cidades = Cidade::all();
        $clubes = Clube::all();
        $sexos = Sexo::all();
        return view('enxadrista.edit',compact("enxadrista","cidades","clubes","sexos"));
    }
    public function edit_post($id,Request $request){

        $validator = \Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|max:255',
            'born' => 'required|string',
            'cidade_id' => 'required|string',
            'sexos_id' => 'required|string',
            'celular' => 'required|string',
        ]);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator->errors());
        }

        
        // Algoritmo para eliminar os problemas com espaçamentos duplos ou até triplos.
        $nome_corrigido = "";

        $part_names = explode(" ",mb_strtoupper($request->input("name")));
        foreach($part_names as $part_name){
            if($part_name != ' '){
                $trim = trim($part_name);
                if(strlen($trim) > 0){
                    $nome_corrigido .= $trim;
                    $nome_corrigido .= " ";
                }
            }
        }
        $nome_corrigido = trim($nome_corrigido);

        
        $enxadrista = Enxadrista::find($id);
        $enxadrista->name = $nome_corrigido;
        $enxadrista->setBorn($request->input("born"));
        $enxadrista->cidade_id = $request->input("cidade_id");
        $enxadrista->sexos_id = $request->input("sexos_id");
        $enxadrista->celular = $request->input("celular");
        if($request->has("clube_id")){
            if($request->input("clube_id")){
                $enxadrista->clube_id = $request->input("clube_id");
            }else{
                $enxadrista->clube_id = NULL;
            }
        }else{
            $enxadrista->clube_id = NULL;
        }
        $enxadrista->email = $request->input("email");
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
        if($request->has("lbx_id")){
            if($request->input("lbx_id")){
                $enxadrista->lbx_id = $request->input("lbx_id");
            }else{
                $enxadrista->lbx_id = NULL;
            }
        }else{
            $enxadrista->lbx_id = NULL;
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

    
	
	public function downloadBaseCompleta(){
        if(
            !Auth::user()->hasPermissionGlobal()
        ){
            return redirect("/");
        }
		$enxadristasView = new EnxadristasCompletoFromView();
		return Excel::download($enxadristasView, 'xadrezSuico_exportacao_lista_enxadristas_'.date('d-m-Y--H-i-s').'.xlsx');
	}


    /*
     *
     * 
     * API
     * 
     * 
     */ 
    public function searchEnxadristasList($type = 0,Request $request){
        $permitido_edicao = false;
        if(
            Auth::user()->hasPermissionGlobal() ||
            Auth::user()->hasPermissionEventsByPerfil([4])
        ){
            $permitido_edicao = true;
        }


        $requisicao = $request->all();

        $enxadristaBorn = new Enxadrista();

        $recordsTotal = Enxadrista::count();
        $enxadristas = Enxadrista::where([["name","like","%".$requisicao["search"]["value"]."%"]]);
        $enxadristas->orWhere([["id","=",$requisicao["search"]["value"]]]);
        $enxadristas->orWhere(function($q1) use ($requisicao){
            $q1->whereHas("sexo",function($q2) use ($requisicao){
                $q2->where([["name","like","%".$requisicao["search"]["value"]."%"]]);
                $q2->orWhere([["abbr","like","%".$requisicao["search"]["value"]."%"]]);
            });
        });

        $enxadristaBorn->setBorn($requisicao["search"]["value"]);
        if($enxadristaBorn->getBorn()){
            $enxadristas->orWhere([["born","=",$enxadristaBorn->getBorn()]]);
        }

        $enxadristas->orWhere([["fide_id","=",$requisicao["search"]["value"]]]);
        $enxadristas->orWhere([["cbx_id","=",$requisicao["search"]["value"]]]);
        $enxadristas->orWhere([["lbx_id","=",$requisicao["search"]["value"]]]);
        $enxadristas->orWhere(function($q1) use ($requisicao){
            $q1->whereHas("cidade",function($q2) use ($requisicao){
                $q2->where([["name","like","%".$requisicao["search"]["value"]."%"]]);
            });
        });
        $enxadristas->orWhere(function($q1) use ($requisicao){
            $q1->whereHas("clube",function($q2) use ($requisicao){
                $q2->where([["name","like","%".$requisicao["search"]["value"]."%"]]);
            });
        });

        switch($requisicao["order"][0]["column"]){
        case 1:
            $enxadristas->orderBy("name",mb_strtoupper($requisicao["order"][0]["dir"]));
            break;
        case 2:
            $enxadristas->orderBy("born",mb_strtoupper($requisicao["order"][0]["dir"]));
            break;
        case 3:
            $enxadristas->orderBy("sexos_id",mb_strtoupper($requisicao["order"][0]["dir"]));
            break;
        case 4:
            $enxadristas->orderBy("fide_id",mb_strtoupper($requisicao["order"][0]["dir"]));
            $enxadristas->orderBy("cbx_id",mb_strtoupper($requisicao["order"][0]["dir"]));
            $enxadristas->orderBy("lbx_id",mb_strtoupper($requisicao["order"][0]["dir"]));
            break;
        case 5:
            $enxadristas->orderBy("cidade_id",mb_strtoupper($requisicao["order"][0]["dir"]));
            break;
        case 6:
            $enxadristas->orderBy("clube_id",mb_strtoupper($requisicao["order"][0]["dir"]));
            break;
        default:
            $enxadristas->orderBy("id",mb_strtoupper($requisicao["order"][0]["dir"]));
        }
        $total = count($enxadristas->get());
        $enxadristas->limit($requisicao["length"]);
        $enxadristas->skip($requisicao["start"]);

        $retorno = array("draw"=>$requisicao["draw"],"recordsTotal"=>$recordsTotal,"recordsFiltered"=>$total,"data"=>array(),"requisicao"=>$requisicao);
        foreach($enxadristas->get() as $enxadrista){
        $p = array();
        $p[0] = $enxadrista->id;
        $p[1] = $enxadrista->name;

        $p[2] = $enxadrista->getBorn();

        $p[3] = $enxadrista->sexo->name;

        $p[4] = "";
        if($enxadrista->cbx_id){
            $p[4] .= "CBX: ".$enxadrista->cbx_id."<br/>";
        }
        if($enxadrista->fide_id){
            $p[4] .= "FIDE: ".$enxadrista->fide_id."<br/>";
        }
        if($enxadrista->lbx_id){
            $p[4] .= "LBX: ".$enxadrista->lbx_id."<br/>";
        }

        $p[5] = "#".$enxadrista->cidade->id." - ".$enxadrista->cidade->name;

        if($enxadrista->clube){
            $p[6] = $enxadrista->clube->name;
        }else{
            $p[6] = "Não possui clube";
        }
        
        $p[7] = "";
        if($permitido_edicao) $p[7] .= '<a href="'.url("/enxadrista/edit/".$enxadrista->id).'" title="Editar Enxadrista: '.$enxadrista->id.' '.$enxadrista->name.'" class="btn btn-success btn-sm" data-toggle="tooltip" data-original-title="Editar Enxadrista"><i class="fa fa-edit"></i></a>';
        if($enxadrista->isDeletavel() && $permitido_edicao){
             $p[7] .= '<button type="button" class="btn btn-danger btn-sm" data-toggle="modal" title="Deletar Enxadrista: '.$enxadrista->id.' '.$enxadrista->name.'" data-target="#modalDelete_'.$enxadrista->id.'"><i class="fa fa-times"></i></button>
                    <!-- Modal Exclusão -->
                    <div class="modal fade modal-danger" id="modalDelete_'.$enxadrista->id.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Efetuar Exclusão - Enxadrista #'.$enxadrista->id.': '.$enxadrista->name.'</h4>
                            </div>
                            <div class="modal-body">
                            <h2>Você tem certeza que pretende fazer isso?</h2><br>
                            O enxadrista será deletado e não será possível recuperá-lo.
                            <h4>Você deseja ainda assim efetuar a exclusão?</h4>
                            </div>
                            <div class="modal-footer">
                            <button type="button" class="btn btn-success" data-dismiss="modal">Não quero mais</button>
                            <a class="btn btn-danger" href="'.url("/enxadrista/delete/".$enxadrista->id).'">Efetuar a exclusão</a>
                            </div>
                        </div>
                        </div>
                    </div>';
        }

        $retorno["data"][] = $p;
        }
        return response()->json($retorno);
    }
}

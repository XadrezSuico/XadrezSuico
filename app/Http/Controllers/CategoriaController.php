<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Evento;
use App\Categoria;
use App\Inscricao;

class CategoriaController extends Controller
{
    public function index(){
        $categorias = Categoria::all();
        return view('categoria.index',compact("categorias"));
    }
    public function new(){
        return view('categoria.new');
    }
    public function new_post(Request $request){
        $categoria = new Categoria;
        $categoria->name = $request->input("name");
        $categoria->idade_minima = $request->input("idade_minima");
        $categoria->idade_maxima = $request->input("idade_maxima");
        $categoria->code = $request->input("code");
        $categoria->cat_code = $request->input("cat_code");
        $categoria->save();
        return redirect("/categoria/edit/".$categoria->id);
    }
    public function edit($id){
        $categoria = Categoria::find($id);
        return view('categoria.edit',compact("categoria"));
    }
    public function edit_post($id,Request $request){
        $categoria = Categoria::find($id);
        $categoria->name = $request->input("name");
        $categoria->idade_minima = $request->input("idade_minima");
        $categoria->idade_maxima = $request->input("idade_maxima");
        $categoria->code = $request->input("code");
        $categoria->cat_code = $request->input("cat_code");
        $categoria->save();
        return redirect("/categoria/edit/".$categoria->id);
    }
    public function delete($id){
        $categoria = Categoria::find($id);
        
        if($categoria->isDeletavel()){
            $categoria->delete();
        }
        return redirect("/categoria");
    }


    public static function classificar($evento_id, $categoria_id){
        $evento = Evento::find($evento_id);
        $categoria = Categoria::find($categoria_id);
        $inscritos = array();
        $inscricoes = Inscricao::where([
                ["categoria_id","=",$categoria->id]
            ])
            ->whereHas("torneio",function($q1) use ($evento){
                $q1->where([
                    ["evento_id","=",$evento->id]
                ]);
            })
            ->orderBy("pontos","DESC")
        ->get();
        echo count($inscricoes);
        foreach($inscricoes as $inscricao){
            if($inscricao->pontos != NULL && $inscricao->confirmado){
                $inscritos[] = $inscricao;
            }
        }
        usort($inscritos, array("\App\Http\Controllers\CategoriaController","sort_classificacao_etapa"));
        $i = 1;
        foreach($inscritos as $inscricao){
            $inscricao->posicao = $i;
            // echo $i;
            $inscricao->save();
            $i++;
        }
    }

    public static function sort_classificacao_etapa($inscrito_a,$inscrito_b){
        if($inscrito_a->pontos > $inscrito_b->pontos){
            return -1;
        }elseif($inscrito_a->pontos < $inscrito_b->pontos){
            return 1;
        }else{
            if($inscrito_a->torneio->evento->criterios()->count() > 0){
                $criterios = $inscrito_a->torneio->evento->criterios()->orderBy("prioridade")->get();
            }else{
                $criterios = $inscrito_a->torneio->evento->grupo_evento->criterios()->orderBy("prioridade")->get();
            }
            foreach($criterios as $criterio){
                $desempate = $criterio->criterio->sort_desempate($inscrito_a, $inscrito_b);
                if($desempate != 0){
                    return $desempate;
                }
            }
            return strnatcmp($inscrito_a->enxadrista->getName(),$inscrito_b->enxadrista->getName());
        }
    }
}

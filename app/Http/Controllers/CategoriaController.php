<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Evento;
use App\Categoria;
use App\Inscricao;
use App\Sexo;
use App\CategoriaSexo;

class CategoriaController extends Controller
{
	public function __construct(){
		return $this->middleware("auth");
	}
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
        return redirect("/categoria/dashboard/".$categoria->id);
    }
    public function edit($id){
        $categoria = Categoria::find($id);
        $sexos = Sexo::all();
        return view('categoria.edit',compact("categoria","sexos"));
    }
    public function edit_post($id,Request $request){
        $categoria = Categoria::find($id);
        $categoria->name = $request->input("name");
        $categoria->idade_minima = $request->input("idade_minima");
        $categoria->idade_maxima = $request->input("idade_maxima");
        $categoria->code = $request->input("code");
        $categoria->cat_code = $request->input("cat_code");
        $categoria->save();
        return redirect("/categoria/dashboard/".$categoria->id);
    }
    public function delete($id){
        $categoria = Categoria::find($id);
        
        if($categoria->isDeletavel()){
            $categoria->delete();
        }
        return redirect("/categoria");
    }


    

    public function sexo_add($id,Request $request){
        $categoria_sexo = new CategoriaSexo;
        $categoria_sexo->categoria_id = $id;
        $categoria_sexo->sexos_id = $request->input("sexos_id");
        $categoria_sexo->save();
        return redirect("/categoria/dashboard/".$id);
    }
    public function sexo_remove($id,$categoria_sexo_id){
        $categoria_sexo = CategoriaSexo::find($categoria_sexo_id);
        $categoria_sexo->delete();
        return redirect("/categoria/dashboard/".$id);
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
            $criterios = $inscrito_a->torneio->getCriterios();
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

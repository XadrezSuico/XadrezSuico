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
        echo '<br/><br/> Categoria: '.$categoria->name;
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
        $j = 1;
        foreach($inscritos as $inscricao){
            $inscricao->posicao = $i;
            // echo $i;
            if(!$inscricao->desconsiderar_pontuacao_geral){
                $inscricao->posicao_geral = $j;
                $inscricao->pontos_geral = Pontuacao::getPontuacaoByEvento($evento->id,$j);
                $j++;
            }else{
                $inscricao->posicao_geral = NULL;
            }
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
            $criterios = $inscrito_a->torneio->getCriteriosTotal();
            echo "[".count($criterios)."]";
            foreach($criterios as $criterio){
                $desempate = $criterio->criterio->sort_desempate($inscrito_a, $inscrito_b);
                if($desempate != 0){
                    echo $criterio->criterio->name;
                    return $desempate;
                }
            }
            return strnatcmp($inscrito_a->enxadrista->getName(),$inscrito_b->enxadrista->getName());
        }
    }




    

    
    public static function classificar_geral($grupo_evento_id, $categoria_id){
		$retornos = array();
        $grupo_evento = GrupoEvento::find($grupo_evento_id);
        $categoria = Categoria::find($categoria_id);
		$retornos[] = date("d/m/Y H:i:s")." - Início do Processamento para a Categoria de #".$categoria->id." - '".$categoria->name."' do Grupo de Evento '".$grupo_evento->name."'";
		$retornos[] = "<hr/>";
        $inscritos = array();

        $retornos = array_merge($retornos, CategoriaController::somar_pontos_geral($grupo_evento,$categoria));
		$retornos[] = "<hr/>";
        $retornos = array_merge($retornos, CategoriaController::gerar_criterios_desempate($grupo_evento,$categoria));
		$retornos[] = "<hr/>";
        $retornos = array_merge($retornos, CategoriaController::classificar_enxadristas_geral($grupo_evento,$categoria));
		$retornos[] = "<hr/>";
		$retornos[] = date("d/m/Y H:i:s")." - Fim do Processamento para a Categoria de #".$categoria->id." - '".$categoria->name."' do Grupo de Evento '".$grupo_evento->name."'";
		$retornos[] = "<hr/>";
		$retornos[] = "<hr/>";
		$retornos[] = "<hr/>";
    
        return $retornos;
    }

    public static function somar_pontos_geral($grupo_evento, $categoria){
		$retornos = array();
		$retornos[] = date("d/m/Y H:i:s")." - Função de Soma de Pontos";
		$retornos[] = date("d/m/Y H:i:s")." - Zerando pontuações existentes";
        foreach(PontuacaoEnxadrista::where([
                    ["grupo_evento_id","=",$grupo_evento->id],
                    ["categoria_id","=",$categoria->id],
                ])->get() as $pontuacao){
            // $pontuacao->pontos = 0;
            // $pontuacao->inscricoes_calculadas = 0;
            // $pontuacao->save();
            $pontuacao->delete();
        }
		$retornos[] = date("d/m/Y H:i:s")." - Listando Eventos do Grupo de Evento";
		$retornos[] = "<hr/>";
        foreach($grupo_evento->eventos()->where([["classificavel","=",true]])->get() as $evento){
		    $retornos[] = date("d/m/Y H:i:s")." - Evento: ".$evento->name;
            $inscricoes = Inscricao::where([
                ["categoria_id","=",$categoria->id],
                ["pontos_geral","!=",NULL],
                ["pontos_geral",">",0],
            ])
            ->whereHas("torneio",function($q1) use ($evento){
                $q1->where([
                    ["evento_id","=",$evento->id]
                ]);
            })
            ->orderBy("pontos","DESC")
            ->get();
		    $retornos[] = date("d/m/Y H:i:s")." - Total de inscrições encontradas: ".count($inscricoes);
            foreach($inscricoes as $inscricao){
		        $retornos[] = date("d/m/Y H:i:s")." - Inscrição #: ".$inscricao->id." - Enxadrista: ".$inscricao->enxadrista->name;
                $pontos_geral = PontuacaoEnxadrista::where([
                    ["enxadrista_id","=",$inscricao->enxadrista->id],
                    ["grupo_evento_id","=",$inscricao->torneio->evento->grupo_evento->id],
                    ["categoria_id","=",$inscricao->categoria->id],
                ])->first();
                if(!$pontos_geral){
                    $pontos_geral = new PontuacaoEnxadrista;
                    $pontos_geral->enxadrista_id = $inscricao->enxadrista->id;
                    $pontos_geral->grupo_evento_id = $inscricao->torneio->evento->grupo_evento->id;
                    $pontos_geral->categoria_id = $inscricao->categoria->id;
                    $pontos_geral->pontos = 0;
                }
                
                if($grupo_evento->limite_calculo_geral){
		            $retornos[] = date("d/m/Y H:i:s")." - Limite do Grupo de Evento: ".$grupo_evento->limite_calculo_geral;
		            $retornos[] = date("d/m/Y H:i:s")." - Quantidade de inscrições calculadas já deste enxadrista: ".$pontos_geral->inscricoes_calculadas;
		            $retornos[] = date("d/m/Y H:i:s")." - Pontos desta inscrição: ".$inscricao->pontos_geral;
                    if($grupo_evento->limite_calculo_geral > $pontos_geral->inscricoes_calculadas){
                        $pontos_geral->pontos += $inscricao->pontos_geral;
                        $pontos_geral->inscricoes_calculadas++;
                    }
                }else{
                    $pontos_geral->pontos += $inscricao->pontos_geral;
                    $pontos_geral->inscricoes_calculadas++;
                }
                $pontos_geral->save();
        		$retornos[] = "<hr/>";
            }
        }
        $retornos[] = date("d/m/Y H:i:s")." - Finalizada a Função de Soma de Pontos";
        return $retornos;
    }

    public static function gerar_criterios_desempate($grupo_evento, $categoria){
        $retornos = array();
		$retornos[] = date("d/m/Y H:i:s")." - Função de geração de Critérios de Desempate";
        $criterios = $grupo_evento->getCriteriosDesempateGerais();
        $enxadristas = Enxadrista::getComInscricaoConfirmada($grupo_evento->id,$categoria->id);

        $gerador = new CriterioDesempateGeralController;
        foreach($enxadristas as $enxadrista){
            
            $retornos[] = date("d/m/Y H:i:s")." - Gerando critérios para o enxadrista #".$enxadrista->id." - ".$enxadrista->name;
            foreach($criterios as $criterio){
                $enxadrista_criterio = EnxadristaCriterioDesempateGeral::where([
                    ["enxadrista_id","=",$enxadrista->id],
                    ["grupo_evento_id","=",$grupo_evento->id],
                    ["categoria_id","=",$categoria->id],
                    ["criterio_desempate_id","=",$criterio->criterio->id],
                ])->first();
                if(!$enxadrista_criterio){
                    $enxadrista_criterio = new EnxadristaCriterioDesempateGeral;
                    $enxadrista_criterio->enxadrista_id = $enxadrista->id;
                    $enxadrista_criterio->grupo_evento_id = $grupo_evento->id;
                    $enxadrista_criterio->categoria_id = $categoria->id;
                    $enxadrista_criterio->criterio_desempate_id = $criterio->criterio->id;
                }
                $enxadrista_criterio->valor = $gerador->generate($grupo_evento,$enxadrista,$criterio->criterio);
                $enxadrista_criterio->save();
                $retornos[] = date("d/m/Y H:i:s")." - Critério: ".$criterio->criterio->name." - Valor: ".$enxadrista_criterio->valor;
            }
        }
		$retornos[] = date("d/m/Y H:i:s")." - Fim da Função de geração de Critérios de Desempate";
        return $retornos;
    }

    public static function classificar_enxadristas_geral($grupo_evento, $categoria){
        $retornos = array();
		$retornos[] = date("d/m/Y H:i:s")." - Função de classificação dos enxadristas";
        $criterios = $grupo_evento->getCriteriosDesempateGerais();
		$retornos[] = date("d/m/Y H:i:s")." - Listando as pontuações dos enxadristas que possuem alguma pontuação";
        $pontuacoes_enxadristas = PontuacaoEnxadrista::where([
            ["categoria_id","=",$categoria->id],
            ["grupo_evento_id","=",$grupo_evento->id],
        ])->get();

        $pontuacoes = array();
        foreach($pontuacoes_enxadristas as $pontuacao){
            if($pontuacao->pontos > 0){
                $pontuacoes[] = $pontuacao;
            }
        }
        usort($pontuacoes, array("\App\Http\Controllers\CategoriaController","sort_classificacao_geral"));
        $i = 1;
        foreach($pontuacoes as $pontuacao){
		    $retornos[] = date("d/m/Y H:i:s")." - Enxadrista: ".$pontuacao->enxadrista->name." - Posição: ".$i;
            $pontuacao->posicao = $i;
            $pontuacao->save();
            $i++;
        }
        $retornos[] = date("d/m/Y H:i:s")." - Fim da Função de classificação dos enxadristas";
        return $retornos;
    }

    public static function sort_classificacao_geral($pontuacao_a,$pontuacao_b){
        if($pontuacao_a->pontos > $pontuacao_b->pontos){
            return -1;
        }elseif($pontuacao_a->pontos < $pontuacao_b->pontos){
            return 1;
        }else{
            $criterios = $pontuacao_a->grupo_evento->getCriteriosGerais();
            foreach($criterios as $criterio){
                $desempate = $criterio->criterio->sort_desempate_geral($pontuacao_a, $pontuacao_b);
                if($desempate != 0){
                    // echo $criterio->criterio->name;
                    return $desempate;
                }
            }
            return strnatcmp($pontuacao_a->enxadrista->getName(),$pontuacao_b->enxadrista->getName());
        }
    }
}

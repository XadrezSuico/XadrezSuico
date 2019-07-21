<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Evento;
use App\Torneio;
use App\Inscricao;
use App\Enxadrista;
use App\InscricaoCriterioDesempate;
use App\MovimentacaoRating;
use App\Rating;
use App\Categoria;

class TorneioController extends Controller
{
	public function __construct(){
		return $this->middleware("auth");
	}
	
	public function index($id){
        $evento = Evento::find($id);
        $torneios = $evento->torneios->all();
		return view("evento.torneio.index",compact("evento","torneios"));
	}

	public function formResults($id,$torneio_id){
        $torneio = Torneio::find($torneio_id);
        $evento = $torneio->evento;
		return view("evento.torneio.resultados",compact("evento","torneio"));
	}

	public function sendResultsTxt($evento_id,$torneio_id,Request $request){
		return $this->setResults($request->input("results"),$torneio_id);
	}

	private function setResults($results,$torneio_id){
		$retornos = array();
		$torneio = Torneio::find($torneio_id);
		$retornos[] = date("d/m/Y H:i:s")." - Início do Processamento para o torneio de #".$torneio->id." - '".$torneio->name."' do Evento '".$torneio->evento->name."'";
		$retornos[] = "<hr/>";
		$lines = str_getcsv($results,"\n");
		$i = 0;
		$k = -1;
		$fields = array();
		foreach($lines as $line){
			$columns = str_getcsv($line,";");
			if($i == 0){
				$j = 0;
				$retornos[] = date("d/m/Y H:i:s")." - Capturando as informações de campos no cabeçalho:";
				foreach($columns as $column){
					if($k >= 0 && $k < $torneio->getCountCriteriosNaoManuais()){
						$retornos[] = date("d/m/Y H:i:s")." - ".($k+1)."º Critério de Desempate [".$j."] - Total: ".$torneio->getCountCriteriosNaoManuais();
						$fields ["C".($k+1)] = $j;
						$k++;
					}else
						switch($column){
							case "ID":
								$fields["ID"] = $j;
								$retornos[] = date("d/m/Y H:i:s")." - Coluna de Código do Enxadrista (ID) [".$j."]";
								break;
							case "Gr":
								$fields["Gr"] = $j;
								$retornos[] = date("d/m/Y H:i:s")." - Coluna de Grupo da Inscrição (Gr) [".$j."]";
								break;
							case "Pts":
								$fields["Pts"] = $j;
								$retornos[] = date("d/m/Y H:i:s")." - Coluna de Pontos do Enxadrista (Pts) [".$j."]";
								$k = 0;
								break;
							case "Val+/-":
								$fields["Val+/-"] = $j;
								$retornos[] = date("d/m/Y H:i:s")." - Coluna de Movimentação de Rating (Val+/-) [".$j."]";
								break;
						}
					$j++;
				}
				$retornos[] = "<hr/>";
				$retornos[] = date("d/m/Y H:i:s")." - Início do Processamento dos Resultados:";
			}else{
				$line = explode(";",$line);
				// print_r($line);echo "<br/>";
				if(isset($fields["ID"])){
					$inscricao = Inscricao::where([
						["enxadrista_id","=",$line[($fields["ID"])]],
						["torneio_id","=",$torneio->id],
					])
					->first();
					$enxadrista = Enxadrista::find($line[($fields["ID"])]);
					if($enxadrista){
						$retornos[] = date("d/m/Y H:i:s")." - Enxadrista de Código #".$enxadrista->id." - ".$enxadrista->name;
					}else{
						$retornos[] = date("d/m/Y H:i:s")." - Enxadrista com o Código #".$line[($fields["ID"])]." não encontrado.";
					}
					if(!$inscricao){
						$retornos[] = date("d/m/Y H:i:s")." - Não há inscrição deste enxadrista";
						if($enxadrista){
							$categoria = Categoria::where([["code","=",$line[($fields["Gr"])]]])->first();
							if($categoria){
								$retornos[] = date("d/m/Y H:i:s")." - Efetuando inscrição...";
								$inscricao = new Inscricao;
								$inscricao->enxadrista_id = $enxadrista->id;
								$inscricao->cidade_id = $enxadrista->cidade_id;
								$inscricao->clube_id = $enxadrista->clube_id;
								$inscricao->torneio_id = $torneio->id;
								$inscricao->categoria_id = $categoria->id;
								$inscricao->regulamento_aceito = true;
								$inscricao->confirmado = true;
								$retornos[] = date("d/m/Y H:i:s")." - Inscrição efetuada.";
							}else{
								$retornos[] = date("d/m/Y H:i:s")." - ERRO: Não há categoria cadastrada com o código de grupo '".$line[($fields["Gr"])]."'. A inscrição será ignorada.";
								$inscricao = NULL;
							}
						}
					}	
					if($enxadrista && $inscricao){
						$retornos[] = date("d/m/Y H:i:s")." - Há inscrição deste enxadrista.";
						$retornos[] = date("d/m/Y H:i:s")." - Pontos: ".$line[($fields["Pts"])];
						$exp_meio = explode("½",$line[($fields["Pts"])]);
						$exp_virgula = explode(",",$line[($fields["Pts"])]);

						$inscricao->confirmado = true;
						$inscricao->pontos = (count($exp_meio) > 1) ? $exp_meio[0].".5" : ((count($exp_virgula) > 1) ? $exp_virgula[0].".".$exp_virgula[1] : $exp_virgula[0]);
						$inscricao->save();


						$j = 1;
						$desempates = InscricaoCriterioDesempate::where([["inscricao_id","=",$inscricao->id]])->get();
						foreach($desempates as $desempate){
							$retornos[] = date("d/m/Y H:i:s")." - Apagando desempates antigos.";
							$desempate->delete();
						}
						
						foreach($torneio->getCriterios() as $criterio){
							if($criterio->softwares_id){
								// echo "Inserindo critério de desempate '".$criterio->criterio->name."' <br/>";
								$retornos[] = date("d/m/Y H:i:s")." - Inserindo critério de desempate '".$criterio->criterio->name."' - Valor: ".$line[($fields["C".$j])];
								$exp_meio = explode("½",$line[($fields["C".$j])]);
								$exp_virgula = explode(",",$line[($fields["C".$j])]);

								$desempate = new InscricaoCriterioDesempate;
								$desempate->inscricao_id = $inscricao->id;
								$desempate->criterio_desempate_id = $criterio->criterio->id;
								$desempate->valor = (count($exp_meio) > 1) ? $exp_meio[0].".5" : ((count($exp_virgula) > 1) ? $exp_virgula[0].".".$exp_virgula[1] : $exp_virgula[0]);
								// echo $desempate->valor."\n";
								$desempate->save();
								$retornos[] = date("d/m/Y H:i:s")." - Desempate inserido";
								$j++;
							}
						}
						if($torneio->evento->tipo_rating){
							$retornos[] = date("d/m/Y H:i:s")." - O evento calcula rating. Iniciando processamento do rating do enxadrista.";
							$temRating = $enxadrista->temRating($torneio->evento->id);
							if($temRating){
								$retornos[] = date("d/m/Y H:i:s")." - O enxadrista possui rating deste tipo. Rating #".$temRating["rating"]->id.".";
								$rating = $temRating["rating"];
								$movimentacao = MovimentacaoRating::where([
									["ratings_id","=",$rating->id],
									["torneio_id","=",$torneio->id],
								])->first();
								if($movimentacao){
									// echo "Apagando movimentação de rating deste torneio. <br/>";
									$retornos[] = date("d/m/Y H:i:s")." - Apagando movimentação de rating deste torneio.";
									$movimentacao->delete();
								}
								if($temRating["ok"] == 0){
									$retornos[] = date("d/m/Y H:i:s")." - O rating está como 0. Colocando rating como o inicial.";
									if($rating->movimentacoes()->count() == 0){
										$movimentacao = new MovimentacaoRating;
										$movimentacao->tipo_ratings_id = $rating->tipo_ratings_id;
										$movimentacao->ratings_id = $rating->id;
										$movimentacao->valor = $temRating["regra"]->inicial;
										$movimentacao->is_inicial = true;
										$movimentacao->save();
									}
								}
								
								$exp_meio = explode("½",$line[($fields["Val+/-"])]);
								$exp_virgula = explode(",",$line[($fields["Val+/-"])]);

								$retornos[] = date("d/m/Y H:i:s")." - Criando a movimentação do rating desta etapa. Modificação:".((count($exp_meio) > 1) ? $exp_meio[0].".5" : (count($exp_virgula) > 1) ? $exp_virgula[0].".".$exp_virgula[1] : $exp_virgula[0]).".";

								$movimentacao = new MovimentacaoRating;
								$movimentacao->tipo_ratings_id = $rating->tipo_ratings_id;
								$movimentacao->ratings_id = $rating->id;
								$movimentacao->torneio_id = $torneio->id;
								$movimentacao->inscricao_id = $inscricao->id;
								$movimentacao->valor = (count($exp_meio) > 1) ? $exp_meio[0].".5" : (count($exp_virgula) > 1) ? $exp_virgula[0].".".$exp_virgula[1] : $exp_virgula[0];
								$movimentacao->is_inicial = false;
								$movimentacao->save();
								$retornos[] = date("d/m/Y H:i:s")." - Movimentação salva. Calculando e atualizando rating do enxadrista.";
								$rating->calcular();
							}else{
								$retornos[] = date("d/m/Y H:i:s")." - O Enxadrista não possui rating deste tipo. Criando o rating.";
								$rating = new Rating;
								$rating->enxadrista_id = $enxadrista->id;
								$rating->tipo_ratings_id = $inscricao->torneio->evento->tipo_rating->tipo_ratings_id;
								$rating->valor = 0;
								$rating->save();

								$movimentacao = new MovimentacaoRating;
								$movimentacao->tipo_ratings_id = $rating->tipo_ratings_id;
								$movimentacao->ratings_id = $rating->id;
								$movimentacao->valor = $inscricao->torneio->evento->getRegraRating($enxadrista->id)->inicial;
								$movimentacao->is_inicial = true;
								$movimentacao->save();
								$retornos[] = date("d/m/Y H:i:s")." - Rating #".$rating->id.".";
										
								$exp_meio = explode("½",$line[($fields["Val+/-"])]);
								$exp_virgula = explode(",",$line[($fields["Val+/-"])]);

								$retornos[] = date("d/m/Y H:i:s")." - Criando a movimentação do rating desta etapa. Modificação:".((count($exp_meio) > 1) ? $exp_meio[0].".5" : (count($exp_virgula) > 1) ? $exp_virgula[0].".".$exp_virgula[1] : $exp_virgula[0]).".";
								$movimentacao = new MovimentacaoRating;
								$movimentacao->tipo_ratings_id = $rating->tipo_ratings_id;
								$movimentacao->ratings_id = $rating->id;
								$movimentacao->torneio_id = $torneio->id;
								$movimentacao->inscricao_id = $inscricao->id;
								$movimentacao->valor = (count($exp_meio) > 1) ? $exp_meio[0].".5" : (count($exp_virgula) > 1) ? $exp_virgula[0].".".$exp_virgula[1] : $exp_virgula[0];
								$movimentacao->is_inicial = false;
								$movimentacao->save();
								$rating->calcular();
							}	
						}
						$retornos[] = date("d/m/Y H:i:s")." - Fim do processamento do resultado da inscrição do enxadrista #".$enxadrista->id." - ".$enxadrista->name." .";
						// echo "Enxadrista: ".$enxadrista->name."<br/>";
					}else{
						// echo "DEU PROBLEMAAAAA AQUIIIII!";
						$retornos[] = date("d/m/Y H:i:s")." - Durante o processamento da inscrição com o ID #".$line[($fields["ID"])]." ocorreu um erro: não foi possível encontrar o enxadrista cadastrado.";
					}
				}else{
					// echo "DEU PROBLEMAAAAA AQUIIIII!";
					$retornos[] = date("d/m/Y H:i:s")." - Durante o processamento da inscrição: inscrição sem ID.";
				}
			}
			$retornos[] = date("d/m/Y H:i:s")." - <hr/>";
			$i++;
		}
		$retornos[] = date("d/m/Y H:i:s")." - Fim do Processamento";
		$evento = $torneio->evento;
		return view("evento.torneio.resultadosretorno",compact("retornos","torneio","evento"));
	}
}

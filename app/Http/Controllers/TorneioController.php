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
		$this->setResults($request->input("results"),$torneio_id);
	}

	private function setResults($results,$torneio_id){
		$torneio = Torneio::find($torneio_id);
		$lines = str_getcsv($results,"\n");
		$i = 0;
		$k = -1;
		$fields = array();
		foreach($lines as $line){
			$columns = str_getcsv($line,";");
			if($i == 0){
				$j = 0;
				foreach($columns as $column){
					if($k >= 0 && $k < $torneio->getCountCriterios()){
						$fields ["C".($k+1)] = $j;
						$k++;
					}else
						switch($column){
							case "ID":
								$fields["ID"] = $j;
								break;
							case "Gr":
								$fields["Gr"] = $j;
								break;
							case "Pts":
								$fields["Pts"] = $j;
								$k = 0;
								break;
							case "Val+/-":
								$fields["Val+/-"] = $j;
								break;
						}
					$j++;
				}
				print_r($fields);echo "<br/><br/><br/>";
			}else{
				$line = explode(";",$line);
				print_r($line);echo "<br/>";
				$inscricao = Inscricao::where([
					["enxadrista_id","=",$line[($fields["ID"])]],
					["torneio_id","=",$torneio->id],
				])
				->first();
				$enxadrista = Enxadrista::find($line[($fields["ID"])]);
				if(!$inscricao){
					echo "Não há inscrição deste enxadrista. <br/>";
					if($enxadrista){
						$inscricao = new Inscricao;
						$inscricao->enxadrista_id = $enxadrista->id;
						$inscricao->cidade_id = $enxadrista->cidade_id;
						$inscricao->clube_id = $enxadrista->clube_id;
						$inscricao->torneio_id = $torneio->id;
						$inscricao->regulamento_aceito = true;
						$inscricao->confirmado = true;
					}
				}	
				if($enxadrista && $inscricao){
					echo "Há inscrição deste enxadrista. <br/>";
					$exp_meio = explode("½",$line[($fields["Pts"])]);
					$exp_virgula = explode(",",$line[($fields["Pts"])]);

					$inscricao->pontos = (count($exp_meio) > 1) ? $exp_meio[0].".5" : ((count($exp_virgula) > 1) ? $exp_virgula[0].".".$exp_virgula[1] : $exp_virgula[0]);
					$inscricao->save();


					$j = 1;
					$desempates = InscricaoCriterioDesempate::where([["inscricao_id","=",$inscricao->id]])->get();
					foreach($desempates as $desempate){
						echo "Apagando desempate antigo. <br/>";
						$desempate->delete();
					}
					
					foreach($torneio->getCriterios() as $criterio){
						if($criterio->softwares_id){
							echo "Inserindo critério de desempate '".$criterio->criterio->name."' <br/>";
							$exp_meio = explode("½",$line[($fields["C".$j])]);
							$exp_virgula = explode(",",$line[($fields["C".$j])]);

							$desempate = new InscricaoCriterioDesempate;
							$desempate->inscricao_id = $inscricao->id;
							$desempate->criterio_desempate_id = $criterio->criterio->id;
							$desempate->valor = (count($exp_meio) > 1) ? $exp_meio[0].".5" : ((count($exp_virgula) > 1) ? $exp_virgula[0].".".$exp_virgula[1] : $exp_virgula[0]);
							$desempate->save();
							$j++;
						}
					}
					if($torneio->evento->tipo_rating){
						echo "O evento calcula rating. <br/>";
						$temRating = $enxadrista->temRating($torneio->evento->id);
						if($temRating){
							echo "O enxadrista possui rating deste tipo. Rating #".$temRating["rating"]->id." <br/>";
							$rating = $temRating["rating"];
							$movimentacao = MovimentacaoRating::where([
								["ratings_id","=",$rating->id],
								["torneio_id","=",$torneio->id],
							])->first();
							if($movimentacao){
								echo "Apagando movimentação de rating deste torneio. <br/>";
								$movimentacao->delete();
							}
							if($temRating["ok"] == 0){
								echo "O rating dele está como 0. Colocando rating como o inicial. <br/>";
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

							echo "Criando a movimentação do rating desta etapa. Modificação:".((count($exp_meio) > 1) ? $exp_meio[0].".5" : (count($exp_virgula) > 1) ? $exp_virgula[0].".".$exp_virgula[1] : $exp_virgula[0])." <br/>";

							$movimentacao = new MovimentacaoRating;
							$movimentacao->tipo_ratings_id = $rating->tipo_ratings_id;
							$movimentacao->ratings_id = $rating->id;
							$movimentacao->torneio_id = $torneio->id;
							$movimentacao->inscricao_id = $inscricao->id;
							$movimentacao->valor = (count($exp_meio) > 1) ? $exp_meio[0].".5" : (count($exp_virgula) > 1) ? $exp_virgula[0].".".$exp_virgula[1] : $exp_virgula[0];
							$movimentacao->is_inicial = false;
							$movimentacao->save();
							$rating->calcular();
						}else{
							echo "O Enxadrista não possui rating deste tipo. Criando o rating. <br/>";
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
							echo "Rating #".$rating->id." <br/>";
									
							$exp_meio = explode("½",$line[($fields["Val+/-"])]);
							$exp_virgula = explode(",",$line[($fields["Val+/-"])]);

							echo "Criando a movimentação do rating desta etapa. Modificação:".((count($exp_meio) > 1) ? $exp_meio[0].".5" : (count($exp_virgula) > 1) ? $exp_virgula[0].".".$exp_virgula[1] : $exp_virgula[0])." <br/>";
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
					echo "Enxadrista: ".$enxadrista->name."<br/>";
				}else{
					echo "DEU PROBLEMAAAAA AQUIIIII!";
				}
			}
			$i++;
		}
	}
}

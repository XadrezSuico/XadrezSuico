<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Helper\RatingEloHelper;

use App\Enxadrista;
use App\Evento;
use App\Torneio;
use App\Categoria;
use App\Inscricao;
use App\Rodada;
use App\Emparceiramento;
use App\InscricaoCriterioDesempate;
use App\MovimentacaoRating;


use Auth;


class Torneio_ImportacaoController extends Controller
{

    /*
     *
     * RESULTADOS
     *
     */
    public function formResults($id, $torneio_id)
    {
        $evento = Evento::find($id);
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/" . $evento->id);
        }

        $torneio = Torneio::find($torneio_id);
        $evento = $torneio->evento;
        return view("evento.torneio.resultados", compact("evento", "torneio"));
    }


    public function sendResultsTxt($evento_id, $torneio_id, Request $request)
    {
        $torneio = Torneio::find($torneio_id);
        if($torneio){
            switch($torneio->evento->exportacao_sm_modelo){
                case 0:
                case 2:
                    return $this->setResults_tipo_exportacao_0($request->input("results"), $evento_id, $torneio_id);
                    break;
                case 1:
                    return $this->setResults_tipo_exportacao_1($request->input("results"), $evento_id, $torneio_id);
            }
        }
        return false;
    }

    public function formResultsFile($id, $torneio_id)
    {
        $evento = Evento::find($id);
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/" . $evento->id);
        }

        $torneio = Torneio::find($torneio_id);
        $evento = $torneio->evento;
        return view("evento.torneio.resultados_send", compact("evento", "torneio"));
    }


    public function sendResultsFile($evento_id, $torneio_id, Request $request)
    {
        $torneio = Torneio::find($torneio_id);
        if($torneio){
            $file = file_get_contents($request->file('arquivo'));

            switch($torneio->evento->exportacao_sm_modelo){
                case 0:
                case 2:
                case 3:
                case 4:
                case 5:
                case 6:
                    return $this->setResults_tipo_exportacao_0($file, $evento_id, $torneio_id);
                    break;
                case 1:
                case 7:
                    return $this->setResults_tipo_exportacao_1($file, $evento_id, $torneio_id);
            }
        }
        return false;
    }

    private function setResults_tipo_exportacao_0($results, $evento_id, $torneio_id)
    {
        $evento = Evento::find($evento_id);
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/" . $evento->id);
        }

        $retornos = array();
        $torneio = Torneio::find($torneio_id);
        $retornos[] = date("d/m/Y H:i:s") . " - Início do Processamento para o torneio de #" . $torneio->id . " - '" . $torneio->name . "' do Evento '" . $torneio->evento->name . "'";
        $retornos[] = "<hr/>";
        $lines = str_getcsv($results, "\n");
        $i = 0;
        $k = -1;
        $total_des = 0;
        $des_ended = false;
        $fields = array();
        foreach ($lines as $line) {
            $columns = str_getcsv($line, ";");
            if ($i == 0) {
                $j = 0;
                $retornos[] = date("d/m/Y H:i:s") . " - Capturando as informações de campos no cabeçalho:";
                foreach ($columns as $column) {
                    if ($k >= 0 && $k < $torneio->getCountCriteriosNaoManuais() && !$des_ended) {
                        if ($column == "Clas") {
                            $retornos[] = date("d/m/Y H:i:s") . " - " . ($k + 1) . "º Critério de Desempate [" . $j . "] - ERRO! Acabou os critérios de desempate do Arquivo.";
                            $des_ended = true;
                            continue;
                        } else {
                            $retornos[] = date("d/m/Y H:i:s") . " - " . ($k + 1) . "º Critério de Desempate [" . $j . "] - Total: " . $torneio->getCountCriteriosNaoManuais();
                            $fields["Des" . ($k + 1)] = $j;
                            $total_des++;
                        }
                        $k++;
                    } else {
                        switch ($column) {
                            case "ID":
                                $fields["ID"] = $j;
                                $retornos[] = date("d/m/Y H:i:s") . " - Coluna de Código do Enxadrista (ID) [" . $j . "]";
                                break;
                            case "Cat":
                                $fields["Cat"] = $j;
                                $retornos[] = date("d/m/Y H:i:s") . " - Coluna de Categoria da Inscrição (Cat) [" . $j . "]";
                                break;
                            case "Gr":
                                $fields["Gr"] = $j;
                                $retornos[] = date("d/m/Y H:i:s") . " - Coluna de Grupo da Inscrição (Gr) [" . $j . "]";
                                break;
                            case "Pts":
                                $fields["Pts"] = $j;
                                $retornos[] = date("d/m/Y H:i:s") . " - Coluna de Pontos do Enxadrista (Pts) [" . $j . "]";
                                $k = 0;
                                break;
                            case "Val+/-":
                                $fields["Val+/-"] = $j;
                                $retornos[] = date("d/m/Y H:i:s") . " - Coluna de Movimentação de Rating (Val+/-) [" . $j . "]";
                                break;
                        }
                    }

                    $j++;
                }
                if($torneio->getCountCriteriosNaoManuais() == 0){
                    $retornos[] = date("d/m/Y H:i:s") . " - ALERTA! Grupo de Evento/Evento não possui critérios de desempate vinculados!!!";
                }
                $retornos[] = "<hr/>";
                $retornos[] = date("d/m/Y H:i:s") . " - Início do Processamento dos Resultados:";
            } else {
                $line = explode(";", $line);
                // print_r($line);echo "<br/>";
                if (isset($fields["ID"])) {
                    $inscricao = Inscricao::where([
                        ["enxadrista_id", "=", Enxadrista::getStaticId($line[($fields["ID"])])],
                        ["torneio_id", "=", $torneio->id],
                    ])
                        ->first();
                    $enxadrista = Enxadrista::find(Enxadrista::getStaticId($line[($fields["ID"])]));
                    if ($enxadrista) {
                        $retornos[] = date("d/m/Y H:i:s") . " - Enxadrista de Código #" . $enxadrista->id . " - " . $enxadrista->name;
                    } else {
                        $retornos[] = date("d/m/Y H:i:s") . " - Enxadrista com o Código #" . $line[($fields["ID"])] . " não encontrado.";
                    }

                    if (!$inscricao) {
                        $retornos[] = date("d/m/Y H:i:s") . " - Inscrição do Enxadrista com o Código #" . $line[($fields["ID"])] . " não encontrado neste torneio.";
                        if (
                            Inscricao::whereHas("torneio", function ($q1) use ($torneio) {
                                $q1->where([["evento_id", "=", $torneio->evento_id]]);
                            })
                            ->where([
                                ["enxadrista_id", "=", Enxadrista::getStaticId($line[($fields["ID"])])],
                            ])->count() > 0
                        ) {
                            $inscricao = Inscricao::whereHas("torneio", function ($q1) use ($torneio) {
                                $q1->where([["evento_id", "=", $torneio->evento_id]]);
                            })
                            ->where([
                                ["enxadrista_id", "=", Enxadrista::getStaticId($line[($fields["ID"])])],
                            ])->first();
                            $retornos[] = date("d/m/Y H:i:s") . " - Encontrada a Inscrição #{$inscricao->id}, porém, não pertence a este torneio (Categoria #{$inscricao->categoria->id} - {$inscricao->categoria->name}).";
                            $retornos[] = date("d/m/Y H:i:s") . " - Trocando categoria e torneio.";

                            $inscricao->torneio_id = $torneio->id;

                            if ($torneio->categorias()->whereHas("categoria", function ($q1) use ($line, $fields) {
                                $q1->where([["code", "=", $line[($fields["Gr"])]]]);
                            })->count() == 0) {
                                $retornos[] = date("d/m/Y H:i:s") . " - Categoria não encontrada no torneio - Ignorando.";
                                $i++;
                                continue;
                            }

                            $categoria = $torneio->categorias()->whereHas("categoria", function ($q1) use ($line, $fields) {
                                $q1->where([["code", "=", $line[($fields["Gr"])]]]);
                            })->first();

                            $inscricao->categoria_id = $categoria->id;
                            $inscricao->save();
                        }
                    }
                    if (!$inscricao) {
                        $retornos[] = date("d/m/Y H:i:s") . " - Não há inscrição deste enxadrista";
                        if ($enxadrista) {
                            if ($torneio->categorias()->whereHas("categoria", function ($q1) use ($line, $fields) {
                                $q1->where([["code", "=", $line[($fields["Gr"])]]]);
                            })->count() == 0) {
                                $retornos[] = date("d/m/Y H:i:s") . " - Categoria não encontrada no torneio - Ignorando.";
                                $i++;
                                continue;
                            }

                            $categoria = $torneio->categorias()->whereHas("categoria", function ($q1) use ($line, $fields) {
                                $q1->where([["code", "=", $line[($fields["Gr"])]]]);
                            })->first();

                            if ($categoria) {
                                $retornos[] = date("d/m/Y H:i:s") . " - Efetuando inscrição...";
                                $inscricao = new Inscricao;
                                $inscricao->enxadrista_id = $enxadrista->id;
                                $inscricao->cidade_id = $enxadrista->cidade_id;
                                $inscricao->clube_id = $enxadrista->clube_id;
                                $inscricao->torneio_id = $torneio->id;
                                $inscricao->categoria_id = $categoria->id;
                                $inscricao->regulamento_aceito = true;
                                $inscricao->confirmado = true;
                                $retornos[] = date("d/m/Y H:i:s") . " - Inscrição efetuada.";
                            } else {
                                $retornos[] = date("d/m/Y H:i:s") . " - ERRO: Não há categoria cadastrada com o código de grupo '" . $line[($fields["Gr"])] . "'. A inscrição será ignorada.";
                                $inscricao = null;
                            }
                        }
                    }
                    if ($enxadrista && $inscricao) {
                        $retornos[] = date("d/m/Y H:i:s") . " - Há inscrição deste enxadrista.";
                        $retornos[] = date("d/m/Y H:i:s") . " - Pontos: " . $line[($fields["Pts"])];
                        $exp_meio = explode("½", $line[($fields["Pts"])]);
                        $exp_virgula = explode(",", $line[($fields["Pts"])]);

                        $inscricao->confirmado = true;
                        $inscricao->pontos = (count($exp_meio) > 1) ? $exp_meio[0] . ".5" : ((count($exp_virgula) > 1) ? $exp_virgula[0] . "." . $exp_virgula[1] : $exp_virgula[0]);
                        $inscricao->save();

                        $j = 1;
                        $desempates = InscricaoCriterioDesempate::where([["inscricao_id", "=", $inscricao->id]])->get();
                        foreach ($desempates as $desempate) {
                            $retornos[] = date("d/m/Y H:i:s") . " - Apagando desempates antigos.";
                            $desempate->delete();
                        }

                        foreach ($torneio->getCriteriosNaoManuais() as $criterio) {
                            if ($criterio->softwares_id && $j <= $total_des) {
                                if(isset($line[($fields["Des" . $j])])){
                                    // echo "Inserindo critério de desempate '".$criterio->criterio->name."' <br/>";
                                    $retornos[] = date("d/m/Y H:i:s") . " - Inserindo critério de desempate '" . $criterio->criterio->name . "' - Valor: " . $line[($fields["Des" . $j])];
                                    $exp_meio = explode("½", $line[($fields["Des" . $j])]);
                                    $exp_virgula = explode(",", $line[($fields["Des" . $j])]);

                                    $desempate = new InscricaoCriterioDesempate;
                                    $desempate->inscricao_id = $inscricao->id;
                                    $desempate->criterio_desempate_id = $criterio->criterio->id;
                                    $desempate->valor = (count($exp_meio) > 1) ? $exp_meio[0] . ".5" : ((count($exp_virgula) > 1) ? $exp_virgula[0] . "." . $exp_virgula[1] : $exp_virgula[0]);
                                    // echo $desempate->valor."\n";
                                    $desempate->save();
                                    $retornos[] = date("d/m/Y H:i:s") . " - Desempate inserido";
                                    $j++;
                                }else{
                                    $retornos[] = date("d/m/Y H:i:s") . " - ERRO: critério de desempate '" . $criterio->criterio->name . "' - NÃO ENCONTRADO.";
                                }
                            }
                        }


                        if ($torneio->evento->tipo_rating) {
                            $retornos[] = date("d/m/Y H:i:s") . " - O evento calcula rating. Iniciando processamento do rating do enxadrista.";
                            $temRating = $enxadrista->temRating($torneio->evento->id);
                            if ($temRating) {
                                $retornos[] = date("d/m/Y H:i:s") . " - O enxadrista possui rating deste tipo. Rating #" . $temRating["rating"]->id . ".";
                                $rating = $temRating["rating"];
                                $movimentacao = MovimentacaoRating::where([
                                    ["ratings_id", "=", $rating->id],
                                    ["torneio_id", "=", $torneio->id],
                                ])->first();
                                if ($movimentacao) {
                                    // echo "Apagando movimentação de rating deste torneio. <br/>";
                                    $retornos[] = date("d/m/Y H:i:s") . " - Apagando movimentação de rating deste torneio.";
                                    $movimentacao->delete();
                                }
                                if ($temRating["ok"] == 0) {
                                    $retornos[] = date("d/m/Y H:i:s") . " - O rating está como 0. Colocando rating como o inicial.";
                                    if ($rating->movimentacoes()->count() == 0) {
                                        $movimentacao = new MovimentacaoRating;
                                        $movimentacao->tipo_ratings_id = $rating->tipo_ratings_id;
                                        $movimentacao->ratings_id = $rating->id;
                                        $movimentacao->valor = $temRating["regra"]->inicial;
                                        $movimentacao->is_inicial = true;
                                        $movimentacao->save();
                                    }
                                }

                                if (isset($fields["Val+/-"])) {
                                    if (!is_null($line[($fields["Val+/-"])])) {
                                        if ($line[($fields["Val+/-"])] != '') {
                                            $exp_meio = explode("½", $line[($fields["Val+/-"])]);
                                            $exp_virgula = explode(",", $line[($fields["Val+/-"])]);

                                            $retornos[] = date("d/m/Y H:i:s") . " - Criando a movimentação do rating desta etapa. Modificação: " . (count($exp_meio) > 1) ? $exp_meio[0] . ".5" : ((count($exp_virgula) > 1) ? $exp_virgula[0] . "." . $exp_virgula[1] : $exp_virgula[0]) . ".";

                                            $movimentacao = new MovimentacaoRating;
                                            $movimentacao->tipo_ratings_id = $rating->tipo_ratings_id;
                                            $movimentacao->ratings_id = $rating->id;
                                            $movimentacao->torneio_id = $torneio->id;
                                            $movimentacao->inscricao_id = $inscricao->id;
                                            $movimentacao->valor = (count($exp_meio) > 1) ? $exp_meio[0] . ".5" : ((count($exp_virgula) > 1) ? $exp_virgula[0] . "." . $exp_virgula[1] : $exp_virgula[0]);
                                            $movimentacao->is_inicial = false;
                                            $movimentacao->save();
                                            $retornos[] = date("d/m/Y H:i:s") . " - Movimentação salva. Calculando e atualizando rating do enxadrista.";
                                            $rating->calcular();
                                        } else {
                                            $retornos[] = date("d/m/Y H:i:s") . " - O rating veio nulo. Ignorando alteração.";
                                        }
                                    } else {
                                        $retornos[] = date("d/m/Y H:i:s") . " - O rating veio nulo. Ignorando alteração.";
                                    }
                                } else {
                                    $retornos[] = date("d/m/Y H:i:s") . " - O rating veio nulo. Ignorando alteração.";
                                }
                            } else {
                                if (isset($fields["Val+/-"])) {
                                    if (!is_null($line[($fields["Val+/-"])])) {
                                        if ($line[($fields["Val+/-"])] != '') {
                                            $retornos[] = date("d/m/Y H:i:s") . " - O Enxadrista não possui rating deste tipo. Criando o rating.";
                                            $rating = new Rating;
                                            $rating->enxadrista_id = $enxadrista->id;
                                            $rating->tipo_ratings_id = $inscricao->torneio->evento->tipo_rating->tipo_ratings_id;
                                            $rating->valor = 0;
                                            $rating->save();

                                            $rating_inicial = 1800;


                                            $fide = $inscricao->enxadrista->showRating(0, $evento->tipo_modalidade);
                                            $cbx = $inscricao->enxadrista->showRating(1, $evento->tipo_modalidade);
                                            $lbx = $inscricao->enxadrista->showRating(2, $evento->tipo_modalidade);

                                            $found = false;
                                            if($fide){
                                                if($fide > $evento->tipo_rating->tipo_rating->showRatingRegraIdade($inscricao->enxadrista->howOld(), $evento)){
                                                    $rating_inicial = $fide;
                                                    $found = true;
                                                    $retornos[] = date("d/m/Y H:i:s") . " - Encontrado rating FIDE - Definindo como Inicial: ".$fide.".";
                                                }
                                            }
                                            if($lbx && !$found){
                                                if($lbx > $evento->tipo_rating->tipo_rating->showRatingRegraIdade($inscricao->enxadrista->howOld(), $evento)){
                                                    $rating_inicial = $lbx;
                                                    $found = true;
                                                    $retornos[] = date("d/m/Y H:i:s") . " - Encontrado rating LBX - Definindo como Inicial: ".$lbx.".";
                                                }
                                            }
                                            if($cbx && !$found){
                                                if($cbx > $evento->tipo_rating->tipo_rating->showRatingRegraIdade($inscricao->enxadrista->howOld(), $evento)){
                                                    $rating_inicial = $cbx;
                                                    $found = true;
                                                    $retornos[] = date("d/m/Y H:i:s") . " - Encontrado rating CBX - Definindo como Inicial: ".$cbx.".";
                                                }
                                            }

                                            if(!$found){
                                                $rating_inicial = $evento->tipo_rating->tipo_rating->showRatingRegraIdade($inscricao->enxadrista->howOld(), $evento);
                                                $retornos[] = date("d/m/Y H:i:s") . " - Não foi encontrado outros Ratings; Definindo conforme regra de idade - Definindo como Inicial: ".$rating_inicial.".";
                                            }


                                            $movimentacao = new MovimentacaoRating;
                                            $movimentacao->tipo_ratings_id = $rating->tipo_ratings_id;
                                            $movimentacao->ratings_id = $rating->id;
                                            $movimentacao->valor = $rating_inicial;
                                            $movimentacao->is_inicial = true;
                                            $movimentacao->save();
                                            $retornos[] = date("d/m/Y H:i:s") . " - Rating #" . $rating->id . ".";

                                            $exp_meio = explode("½", $line[($fields["Val+/-"])]);
                                            $exp_virgula = explode(",", $line[($fields["Val+/-"])]);

                                            $retornos[] = date("d/m/Y H:i:s") . " - Criando a movimentação do rating desta etapa. Modificação:" . (count($exp_meio) > 1) ? $exp_meio[0] . ".5" : ((count($exp_virgula) > 1) ? $exp_virgula[0] . "." . $exp_virgula[1] : $exp_virgula[0]) . ".";
                                            $movimentacao = new MovimentacaoRating;
                                            $movimentacao->tipo_ratings_id = $rating->tipo_ratings_id;
                                            $movimentacao->ratings_id = $rating->id;
                                            $movimentacao->torneio_id = $torneio->id;
                                            $movimentacao->inscricao_id = $inscricao->id;
                                            $movimentacao->valor = (count($exp_meio) > 1) ? $exp_meio[0] . ".5" : ((count($exp_virgula) > 1) ? $exp_virgula[0] . "." . $exp_virgula[1] : $exp_virgula[0]);
                                            $movimentacao->is_inicial = false;
                                            $movimentacao->save();
                                            $rating->calcular();
                                        } else {
                                            $retornos[] = date("d/m/Y H:i:s") . " - O rating veio nulo. Ignorando alteração.";
                                        }
                                    } else {
                                        $retornos[] = date("d/m/Y H:i:s") . " - O rating veio nulo. Ignorando alteração.";
                                    }
                                } else {
                                    $retornos[] = date("d/m/Y H:i:s") . " - O rating veio nulo. Ignorando alteração.";
                                }
                            }
                        }


                        $retornos[] = date("d/m/Y H:i:s") . " - Fim do processamento do resultado da inscrição do enxadrista #" . $enxadrista->id . " - " . $enxadrista->name . " .";
                        // echo "Enxadrista: ".$enxadrista->name."<br/>";
                    } else {
                        // echo "DEU PROBLEMAAAAA AQUIIIII!";
                        $retornos[] = date("d/m/Y H:i:s") . " - Durante o processamento da inscrição com o ID #" . $line[($fields["ID"])] . " ocorreu um erro: não foi possível encontrar o enxadrista cadastrado.";
                    }
                } else {
                    // echo "DEU PROBLEMAAAAA AQUIIIII!";
                    $retornos[] = date("d/m/Y H:i:s") . " - Durante o processamento da inscrição: inscrição sem ID.";
                }
            }
            $retornos[] = date("d/m/Y H:i:s") . " - <hr/>";
            $i++;
        }
        $retornos[] = date("d/m/Y H:i:s") . " - Fim do Processamento";
        $evento = $torneio->evento;
        return view("evento.torneio.resultadosretorno", compact("retornos", "torneio", "evento"));
    }
    private function setResults_tipo_exportacao_1($results, $evento_id, $torneio_id)
    {
        $evento = Evento::find($evento_id);
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/" . $evento->id);
        }

        $retornos = array();
        $torneio = Torneio::find($torneio_id);
        $retornos[] = date("d/m/Y H:i:s") . " - Início do Processamento para o torneio de #" . $torneio->id . " - '" . $torneio->name . "' do Evento '" . $torneio->evento->name . "'";
        $retornos[] = "<hr/>";
        $lines = str_getcsv($results, "\n");
        $i = 0;
        $k = -1;
        $total_des = 0;
        $des_ended = false;
        $fields = array();
        foreach ($lines as $line) {
            $columns = str_getcsv($line, ";");
            if ($i == 0) {
                $j = 0;
                $retornos[] = date("d/m/Y H:i:s") . " - Capturando as informações de campos no cabeçalho:";
                foreach ($columns as $column) {
                    if ($k >= 0 && $k < $torneio->getCountCriteriosNaoManuais() && !$des_ended) {
                        if ($column == "Clas") {
                            $retornos[] = date("d/m/Y H:i:s") . " - " . ($k + 1) . "º Critério de Desempate [" . $j . "] - ERRO! Acabou os critérios de desempate do Arquivo.";
                            $des_ended = true;
                            continue;
                        } else {
                            $retornos[] = date("d/m/Y H:i:s") . " - " . ($k + 1) . "º Critério de Desempate [" . $j . "] - Total: " . $torneio->getCountCriteriosNaoManuais();
                            $fields["Des" . ($k + 1)] = $j;
                            $total_des++;
                        }
                        $k++;
                    } else {
                        switch ($column) {
                            case "ID":
                                $fields["ID"] = $j;
                                $retornos[] = date("d/m/Y H:i:s") . " - Coluna de Código do Enxadrista junto à CBX (ID) [" . $j . "]";
                                break;
                            case "id FIDE":
                                $fields["id FIDE"] = $j;
                                $retornos[] = date("d/m/Y H:i:s") . " - Coluna de Código do Enxadrista junto à FIDE (id FIDE) [" . $j . "]";
                                break;
                            case "Fonte":
                                $fields["Fonte"] = $j;
                                $retornos[] = date("d/m/Y H:i:s") . " - Coluna de Código do Enxadrista no Campo Fonte (Fonte) [" . $j . "]";
                                break;
                            case "Cat":
                                $fields["Cat"] = $j;
                                $retornos[] = date("d/m/Y H:i:s") . " - Coluna de Categoria da Inscrição (Cat) [" . $j . "]";
                                break;
                            case "Gr":
                                $fields["Gr"] = $j;
                                $retornos[] = date("d/m/Y H:i:s") . " - Coluna de Grupo da Inscrição (Gr) [" . $j . "]";
                                break;
                            case "Pts":
                                $fields["Pts"] = $j;
                                $retornos[] = date("d/m/Y H:i:s") . " - Coluna de Pontos do Enxadrista (Pts) [" . $j . "]";
                                $k = 0;
                                break;
                            case "Val+/-":
                                $fields["Val+/-"] = $j;
                                $retornos[] = date("d/m/Y H:i:s") . " - Coluna de Movimentação de Rating (Val+/-) [" . $j . "]";
                                break;
                        }
                    }

                    $j++;
                }
                $retornos[] = "<hr/>";
                $retornos[] = date("d/m/Y H:i:s") . " - Início do Processamento dos Resultados:";
            } else {
                $line = explode(";", $line);
                // print_r($line);echo "<br/>";
                if (isset($fields["Fonte"])) {
                    $inscricao = Inscricao::where([
                        ["enxadrista_id", "=", Enxadrista::getStaticId($line[($fields["Fonte"])])],
                        ["torneio_id", "=", $torneio->id],
                    ])
                        ->first();
                    $enxadrista = Enxadrista::find(Enxadrista::getStaticId($line[($fields["Fonte"])]));
                    if ($enxadrista) {
                        $retornos[] = date("d/m/Y H:i:s") . " - Enxadrista de Código #" . $enxadrista->id . " - " . $enxadrista->name;
                    } else {
                        $retornos[] = date("d/m/Y H:i:s") . " - Enxadrista com o Código #" . $line[($fields["ID"])] . " não encontrado.";
                    }
                    if (!$inscricao) {
                        $retornos[] = date("d/m/Y H:i:s") . " - Inscrição do Enxadrista com o Código #" . $line[($fields["ID"])] . " não encontrado neste torneio.";
                        if (Inscricao::whereHas("torneio", function ($q1) use ($torneio) {
                            $q1->where([["evento_id", "=", $torneio->evento_id]]);
                        })
                            ->where([
                                ["enxadrista_id", "=", Enxadrista::getStaticId($line[($fields["ID"])])],
                            ])->count() > 0
                        ) {
                            $inscricao = Inscricao::whereHas("torneio", function ($q1) use ($torneio) {
                                $q1->where([["evento_id", "=", $torneio->evento_id]]);
                            })
                                ->where([
                                    ["enxadrista_id", "=", Enxadrista::getStaticId($line[($fields["ID"])])],
                                ])->first();
                            $retornos[] = date("d/m/Y H:i:s") . " - Encontrada a Inscrição #{$inscricao->id}, porém, não pertence a este torneio (Categoria #{$inscricao->categoria->id} - {$inscricao->categoria->name}).";
                            $retornos[] = date("d/m/Y H:i:s") . " - Trocando categoria e torneio.";

                            $inscricao->torneio_id = $torneio->id;

                            if ($torneio->categorias()->whereHas("categoria", function ($q1) use ($line, $fields) {
                                $q1->where([["code", "=", $line[($fields["Gr"])]]]);
                            })->count() == 0) {
                                $retornos[] = date("d/m/Y H:i:s") . " - Categoria não encontrada no torneio - Ignorando.";
                                $i++;
                                continue;
                            }

                            $categoria = $torneio->categorias()->whereHas("categoria", function ($q1) use ($line, $fields) {
                                $q1->where([["code", "=", $line[($fields["Gr"])]]]);
                            })->first();

                            $inscricao->categoria_id = $categoria->id;
                            $inscricao->save();
                        }
                    }
                    if (!$inscricao) {
                        $retornos[] = date("d/m/Y H:i:s") . " - Não há inscrição deste enxadrista";
                        if ($enxadrista) {
                            if ($torneio->categorias()->whereHas("categoria", function ($q1) use ($line, $fields) {
                                $q1->where([["code", "=", $line[($fields["Gr"])]]]);
                            })->count() == 0) {
                                $retornos[] = date("d/m/Y H:i:s") . " - Categoria não encontrada no torneio - Ignorando.";
                                $i++;
                                continue;
                            }

                            $categoria = $torneio->categorias()->whereHas("categoria", function ($q1) use ($line, $fields) {
                                $q1->where([["code", "=", $line[($fields["Gr"])]]]);
                            })->first();

                            if ($categoria) {
                                $retornos[] = date("d/m/Y H:i:s") . " - Efetuando inscrição...";
                                $inscricao = new Inscricao;
                                $inscricao->enxadrista_id = $enxadrista->id;
                                $inscricao->cidade_id = $enxadrista->cidade_id;
                                $inscricao->clube_id = $enxadrista->clube_id;
                                $inscricao->torneio_id = $torneio->id;
                                $inscricao->categoria_id = $categoria->id;
                                $inscricao->regulamento_aceito = true;
                                $inscricao->confirmado = true;
                                $retornos[] = date("d/m/Y H:i:s") . " - Inscrição efetuada.";
                            } else {
                                $retornos[] = date("d/m/Y H:i:s") . " - ERRO: Não há categoria cadastrada com o código de grupo '" . $line[($fields["Gr"])] . "'. A inscrição será ignorada.";
                                $inscricao = null;
                            }
                        }
                    }
                    if ($enxadrista && $inscricao) {
                        $retornos[] = date("d/m/Y H:i:s") . " - Há inscrição deste enxadrista.";
                        $retornos[] = date("d/m/Y H:i:s") . " - Pontos: " . $line[($fields["Pts"])];
                        $exp_meio = explode("½", $line[($fields["Pts"])]);
                        $exp_virgula = explode(",", $line[($fields["Pts"])]);

                        $inscricao->confirmado = true;
                        $inscricao->pontos = (count($exp_meio) > 1) ? $exp_meio[0] . ".5" : ((count($exp_virgula) > 1) ? $exp_virgula[0] . "." . $exp_virgula[1] : $exp_virgula[0]);
                        $inscricao->save();

                        $j = 1;
                        $desempates = InscricaoCriterioDesempate::where([["inscricao_id", "=", $inscricao->id]])->get();
                        foreach ($desempates as $desempate) {
                            $retornos[] = date("d/m/Y H:i:s") . " - Apagando desempates antigos.";
                            $desempate->delete();
                        }

                        foreach ($torneio->getCriterios() as $criterio) {
                            if ($criterio->softwares_id && $j <= $total_des) {
                                if(isset($line[($fields["Des" . $j])])){
                                    // echo "Inserindo critério de desempate '".$criterio->criterio->name."' <br/>";
                                    $retornos[] = date("d/m/Y H:i:s") . " - Inserindo critério de desempate '" . $criterio->criterio->name . "' - Valor: " . $line[($fields["Des" . $j])];
                                    $exp_meio = explode("½", $line[($fields["Des" . $j])]);
                                    $exp_virgula = explode(",", $line[($fields["Des" . $j])]);

                                    $desempate = new InscricaoCriterioDesempate;
                                    $desempate->inscricao_id = $inscricao->id;
                                    $desempate->criterio_desempate_id = $criterio->criterio->id;
                                    $desempate->valor = (count($exp_meio) > 1) ? $exp_meio[0] . ".5" : ((count($exp_virgula) > 1) ? $exp_virgula[0] . "." . $exp_virgula[1] : $exp_virgula[0]);
                                    // echo $desempate->valor."\n";
                                    $desempate->save();
                                    $retornos[] = date("d/m/Y H:i:s") . " - Desempate inserido";
                                    $j++;

                                }else{
                                    $retornos[] = date("d/m/Y H:i:s") . " - ERRO: critério de desempate '" . $criterio->criterio->name . "' - NÃO ENCONTRADO.";
                                }
                            }
                        }

                        if($inscricao->enxadrista->cbx_id){
                            if($inscricao->enxadrista->cbx_id != trim($line[($fields["ID"])])){
                                $retornos[] = date("d/m/Y H:i:s") . " - O Cadastro do Enxadrista consta um ID da CBX diferente, portanto, será atualizado.";
                                $inscricao->enxadrista->cbx_id = trim($line[($fields["ID"])]);
                                $inscricao->enxadrista->save();
                            }else{
                                $retornos[] = date("d/m/Y H:i:s") . " - Não houve necessidade de atualizar o ID CBX do(a) enxadrista. - 1 - ID: ".$line[($fields["ID"])];
                            }
                        }else{
                            if(trim($line[($fields["ID"])]) > 0){
                                $retornos[] = date("d/m/Y H:i:s") . " - O Cadastro do Enxadrista não possui ID CBX, portanto, será adicionado o que consta no torneio.";
                                $inscricao->enxadrista->cbx_id = trim($line[($fields["ID"])]);
                                $inscricao->enxadrista->save();
                            }else{
                                $retornos[] = date("d/m/Y H:i:s") . " - Não houve necessidade de atualizar o ID CBX do(a) enxadrista. - 2 - ID: ".$line[($fields["ID"])];
                            }
                        }

                        if($inscricao->enxadrista->fide_id){
                            if($inscricao->enxadrista->fide_id != trim($line[($fields["id FIDE"])])){
                                $retornos[] = date("d/m/Y H:i:s") . " - O Cadastro do Enxadrista consta um ID da FIDE diferente, portanto, será atualizado.";
                                $inscricao->enxadrista->fide_id = trim($line[($fields["id FIDE"])]);
                                $inscricao->enxadrista->save();
                            }else{
                                $retornos[] = date("d/m/Y H:i:s") . " - Não houve necessidade de atualizar o ID FIDE do(a) enxadrista. - 1 - ID: ".$line[($fields["id FIDE"])];
                            }
                        }else{
                            if(trim($line[($fields["id FIDE"])]) > 0){
                                $retornos[] = date("d/m/Y H:i:s") . " - O Cadastro do Enxadrista não possui ID FIDE, portanto, será adicionado o que consta no torneio.";
                                $inscricao->enxadrista->fide_id = trim($line[($fields["id FIDE"])]);
                                $inscricao->enxadrista->save();
                            }else{
                                $retornos[] = date("d/m/Y H:i:s") . " - Não houve necessidade de atualizar o ID FIDE do(a) enxadrista. - 2 - ID: ".$line[($fields["id FIDE"])];
                            }
                        }

                        $retornos[] = date("d/m/Y H:i:s") . " - Fim do processamento do resultado da inscrição do enxadrista #" . $enxadrista->id . " - " . $enxadrista->name . " .";
                        // echo "Enxadrista: ".$enxadrista->name."<br/>";
                    } else {
                        // echo "DEU PROBLEMAAAAA AQUIIIII!";
                        $retornos[] = date("d/m/Y H:i:s") . " - Durante o processamento da inscrição com o ID #" . $line[($fields["ID"])] . " ocorreu um erro: não foi possível encontrar o enxadrista cadastrado.";
                    }
                } else {
                    // echo "DEU PROBLEMAAAAA AQUIIIII!";
                    $retornos[] = date("d/m/Y H:i:s") . " - Durante o processamento da inscrição: inscrição sem ID.";
                }
            }
            $retornos[] = date("d/m/Y H:i:s") . " - <hr/>";
            $i++;
        }
        $retornos[] = date("d/m/Y H:i:s") . " - Fim do Processamento";
        $evento = $torneio->evento;
        return view("evento.torneio.resultadosretorno", compact("retornos", "torneio", "evento"));
    }
    private function setResults_tipo_exportacao_0_rating($results, $evento_id, $torneio_id)
    {
        $evento = Evento::find($evento_id);
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/" . $evento->id);
        }

        $retornos = array();
        $torneio = Torneio::find($torneio_id);
        $retornos[] = date("d/m/Y H:i:s") . " - Início do Processamento para o torneio de #" . $torneio->id . " - '" . $torneio->name . "' do Evento '" . $torneio->evento->name . "'";
        $retornos[] = "<hr/>";
        $lines = str_getcsv($results, "\n");
        $i = 0;
        $k = -1;
        $fields = array();
        foreach ($lines as $line) {
            $columns = str_getcsv($line, ";");
            if ($i == 0) {
                $j = 0;
                $retornos[] = date("d/m/Y H:i:s") . " - Capturando as informações de campos no cabeçalho:";
                foreach ($columns as $column) {
                    if ($k >= 0 && $k < $torneio->getCountCriteriosNaoManuais()) {
                        $retornos[] = date("d/m/Y H:i:s") . " - " . ($k + 1) . "º Critério de Desempate [" . $j . "] - Total: " . $torneio->getCountCriteriosNaoManuais();
                        $fields["C" . ($k + 1)] = $j;
                        $k++;
                    } else {
                        switch ($column) {
                            case "ID":
                                $fields["ID"] = $j;
                                $retornos[] = date("d/m/Y H:i:s") . " - Coluna de Código do Enxadrista (ID) [" . $j . "]";
                                break;
                            case "Gr":
                                $fields["Gr"] = $j;
                                $retornos[] = date("d/m/Y H:i:s") . " - Coluna de Grupo da Inscrição (Gr) [" . $j . "]";
                                break;
                            case "Pts":
                                $fields["Pts"] = $j;
                                $retornos[] = date("d/m/Y H:i:s") . " - Coluna de Pontos do Enxadrista (Pts) [" . $j . "]";
                                $k = 0;
                                break;
                            case "Val+/-":
                                $fields["Val+/-"] = $j;
                                $retornos[] = date("d/m/Y H:i:s") . " - Coluna de Movimentação de Rating (Val+/-) [" . $j . "]";
                                break;
                        }
                    }

                    $j++;
                }
                $retornos[] = "<hr/>";
                $retornos[] = date("d/m/Y H:i:s") . " - Início do Processamento dos Resultados:";
            } else {
                $line = explode(";", $line);
                // print_r($line);echo "<br/>";
                if (isset($fields["ID"])) {
                    $inscricao = Inscricao::where([
                        ["enxadrista_id", "=", Enxadrista::getStaticId($line[($fields["ID"])])],
                        ["torneio_id", "=", $torneio->id],
                    ])
                    ->first();
                    $enxadrista = Enxadrista::find(Enxadrista::getStaticId($line[($fields["ID"])]));
                    if ($enxadrista) {
                        $retornos[] = date("d/m/Y H:i:s") . " - Enxadrista de Código #" . $enxadrista->id . " - " . $enxadrista->name;
                    }
                    if(!$inscricao) {
                        $retornos[] = date("d/m/Y H:i:s") . " - Inscrição do Enxadrista com o Código #" . $line[($fields["ID"])] . " não encontrado neste torneio.";
                        if(Inscricao::whereHas("torneio",function($q1) use ($torneio){
                            $q1->where([["evento_id","=",$torneio->evento_id]]);
                        })
                        ->where([
                            ["enxadrista_id", "=", Enxadrista::getStaticId($line[($fields["ID"])])],
                        ])->count() > 0){
                            $inscricao = Inscricao::whereHas("torneio", function ($q1) use ($torneio) {
                                $q1->where([["evento_id", "=", $torneio->evento_id]]);
                            })
                            ->where([
                                ["enxadrista_id", "=", Enxadrista::getStaticId($line[($fields["ID"])])],
                            ])->first();
                            $retornos[] = date("d/m/Y H:i:s") . " - Encontrada a Inscrição #{$inscricao->id}, porém, não pertence a este torneio (Categoria #{$inscricao->categoria->id} - {$inscricao->categoria->name}).";
                            $retornos[] = date("d/m/Y H:i:s") . " - Trocando categoria e torneio.";

                            $inscricao->torneio_id = $torneio->id;

                            if($torneio->categorias()->whereHas("categoria", function ($q1) use ($line, $fields) {
                                $q1->where([["code", "=", $line[($fields["Gr"])]]]);
                            })->count() == 0){
                                $retornos[] = date("d/m/Y H:i:s") . " - Categoria não encontrada no torneio - Ignorando.";
                                $i++;
                                continue;
                            }

                            $categoria = $torneio->categorias()->whereHas("categoria",function ($q1) use ($line, $fields){
                                $q1->where([["code", "=", $line[($fields["Gr"])]]]);
                            })->first();

                            $inscricao->categoria_id = $categoria->id;
                            $inscricao->save();
                        }
                    }
                    if (!$inscricao) {
                        $retornos[] = date("d/m/Y H:i:s") . " - Não há inscrição deste enxadrista";
                        if ($enxadrista) {
                            if ($torneio->categorias()->whereHas("categoria", function ($q1) use ($line, $fields) {
                                $q1->where([["code", "=", $line[($fields["Gr"])]]]);
                            })->count() == 0) {
                                $retornos[] = date("d/m/Y H:i:s") . " - Categoria não encontrada no torneio - Ignorando.";
                                $i++;
                                continue;
                            }

                            $categoria = $torneio->categorias()->whereHas("categoria", function ($q1) use ($line, $fields) {
                                $q1->where([["code", "=", $line[($fields["Gr"])]]]);
                            })->first();
                            if ($categoria) {
                                $retornos[] = date("d/m/Y H:i:s") . " - Efetuando inscrição...";
                                $inscricao = new Inscricao;
                                $inscricao->enxadrista_id = $enxadrista->id;
                                $inscricao->cidade_id = $enxadrista->cidade_id;
                                $inscricao->clube_id = $enxadrista->clube_id;
                                $inscricao->torneio_id = $torneio->id;
                                $inscricao->categoria_id = $categoria->id;
                                $inscricao->regulamento_aceito = true;
                                $inscricao->confirmado = true;
                                $retornos[] = date("d/m/Y H:i:s") . " - Inscrição efetuada.";
                            } else {
                                $retornos[] = date("d/m/Y H:i:s") . " - ERRO: Não há categoria cadastrada com o código de grupo '" . $line[($fields["Gr"])] . "'. A inscrição será ignorada.";
                                $inscricao = null;
                            }
                        }
                    }
                    if ($enxadrista && $inscricao) {
                        $retornos[] = date("d/m/Y H:i:s") . " - Há inscrição deste enxadrista.";
                        $retornos[] = date("d/m/Y H:i:s") . " - Pontos: " . $line[($fields["Pts"])];
                        $exp_meio = explode("½", $line[($fields["Pts"])]);
                        $exp_virgula = explode(",", $line[($fields["Pts"])]);

                        $inscricao->confirmado = true;
                        $inscricao->pontos = (count($exp_meio) > 1) ? $exp_meio[0] . ".5" : ((count($exp_virgula) > 1) ? $exp_virgula[0] . "." . $exp_virgula[1] : $exp_virgula[0]);
                        $inscricao->save();

                        $j = 1;
                        $desempates = InscricaoCriterioDesempate::where([["inscricao_id", "=", $inscricao->id]])->get();
                        foreach ($desempates as $desempate) {
                            $retornos[] = date("d/m/Y H:i:s") . " - Apagando desempates antigos.";
                            $desempate->delete();
                        }

                        foreach ($torneio->getCriterios() as $criterio) {
                            if ($criterio->softwares_id) {
                                // echo "Inserindo critério de desempate '".$criterio->criterio->name."' <br/>";
                                $retornos[] = date("d/m/Y H:i:s") . " - Inserindo critério de desempate '" . $criterio->criterio->name . "' - Valor: " . $line[($fields["C" . $j])];
                                $exp_meio = explode("½", $line[($fields["C" . $j])]);
                                $exp_virgula = explode(",", $line[($fields["C" . $j])]);

                                $desempate = new InscricaoCriterioDesempate;
                                $desempate->inscricao_id = $inscricao->id;
                                $desempate->criterio_desempate_id = $criterio->criterio->id;
                                $desempate->valor = (count($exp_meio) > 1) ? $exp_meio[0] . ".5" : ((count($exp_virgula) > 1) ? $exp_virgula[0] . "." . $exp_virgula[1] : $exp_virgula[0]);
                                // echo $desempate->valor."\n";
                                $desempate->save();
                                $retornos[] = date("d/m/Y H:i:s") . " - Desempate inserido";
                                $j++;
                            }
                        }
                        if ($torneio->evento->tipo_rating) {
                            $retornos[] = date("d/m/Y H:i:s") . " - O evento calcula rating. Iniciando processamento do rating do enxadrista.";
                            $temRating = $enxadrista->temRating($torneio->evento->id);
                            if ($temRating) {
                                $retornos[] = date("d/m/Y H:i:s") . " - O enxadrista possui rating deste tipo. Rating #" . $temRating["rating"]->id . ".";
                                $rating = $temRating["rating"];
                                $movimentacao = MovimentacaoRating::where([
                                    ["ratings_id", "=", $rating->id],
                                    ["torneio_id", "=", $torneio->id],
                                ])->first();
                                if ($movimentacao) {
                                    // echo "Apagando movimentação de rating deste torneio. <br/>";
                                    $retornos[] = date("d/m/Y H:i:s") . " - Apagando movimentação de rating deste torneio.";
                                    $movimentacao->delete();
                                }
                                if ($temRating["ok"] == 0) {
                                    $retornos[] = date("d/m/Y H:i:s") . " - O rating está como 0. Colocando rating como o inicial.";
                                    if ($rating->movimentacoes()->count() == 0) {
                                        $movimentacao = new MovimentacaoRating;
                                        $movimentacao->tipo_ratings_id = $rating->tipo_ratings_id;
                                        $movimentacao->ratings_id = $rating->id;
                                        $movimentacao->valor = $temRating["regra"]->inicial;
                                        $movimentacao->is_inicial = true;
                                        $movimentacao->save();
                                    }
                                }

                                if (isset($fields["Val+/-"])) {
                                    if (!is_null($line[($fields["Val+/-"])])) {
                                        if ($line[($fields["Val+/-"])] != '') {
                                            $exp_meio = explode("½", $line[($fields["Val+/-"])]);
                                            $exp_virgula = explode(",", $line[($fields["Val+/-"])]);

                                            $retornos[] = date("d/m/Y H:i:s") . " - Criando a movimentação do rating desta etapa. Modificação: " . (count($exp_meio) > 1) ? $exp_meio[0] . ".5" : ((count($exp_virgula) > 1) ? $exp_virgula[0] . "." . $exp_virgula[1] : $exp_virgula[0]) . ".";

                                            $movimentacao = new MovimentacaoRating;
                                            $movimentacao->tipo_ratings_id = $rating->tipo_ratings_id;
                                            $movimentacao->ratings_id = $rating->id;
                                            $movimentacao->torneio_id = $torneio->id;
                                            $movimentacao->inscricao_id = $inscricao->id;
                                            $movimentacao->valor = (count($exp_meio) > 1) ? $exp_meio[0] . ".5" : ((count($exp_virgula) > 1) ? $exp_virgula[0] . "." . $exp_virgula[1] : $exp_virgula[0]);
                                            $movimentacao->is_inicial = false;
                                            $movimentacao->save();
                                            $retornos[] = date("d/m/Y H:i:s") . " - Movimentação salva. Calculando e atualizando rating do enxadrista.";
                                            $rating->calcular();
                                        } else {
                                            $retornos[] = date("d/m/Y H:i:s") . " - O rating veio nulo. Ignorando alteração.";
                                        }
                                    } else {
                                        $retornos[] = date("d/m/Y H:i:s") . " - O rating veio nulo. Ignorando alteração.";
                                    }
                                } else {
                                    $retornos[] = date("d/m/Y H:i:s") . " - O rating veio nulo. Ignorando alteração.";
                                }
                            } else {
                                if (isset($fields["Val+/-"])) {
                                    if (!is_null($line[($fields["Val+/-"])])) {
                                        if ($line[($fields["Val+/-"])] != '') {
                                            $retornos[] = date("d/m/Y H:i:s") . " - O Enxadrista não possui rating deste tipo. Criando o rating.";
                                            $rating = new Rating;
                                            $rating->enxadrista_id = $enxadrista->id;
                                            $rating->tipo_ratings_id = $inscricao->torneio->evento->tipo_rating->tipo_ratings_id;
                                            $rating->valor = 0;
                                            $rating->save();

                                            $movimentacao = new MovimentacaoRating;
                                            $movimentacao->tipo_ratings_id = $rating->tipo_ratings_id;
                                            $movimentacao->ratings_id = $rating->id;
                                            $movimentacao->valor = $inscricao->torneio->evento->tipo_rating->tipo_rating->showRatingRegraIdade($inscricao->enxadrista->howOld(), $inscricao->torneio->evento);
                                            $movimentacao->is_inicial = true;
                                            $movimentacao->save();
                                            $retornos[] = date("d/m/Y H:i:s") . " - Rating #" . $rating->id . ".";

                                            $exp_meio = explode("½", $line[($fields["Val+/-"])]);
                                            $exp_virgula = explode(",", $line[($fields["Val+/-"])]);

                                            $retornos[] = date("d/m/Y H:i:s") . " - Criando a movimentação do rating desta etapa. Modificação:" . (count($exp_meio) > 1) ? $exp_meio[0] . ".5" : ((count($exp_virgula) > 1) ? $exp_virgula[0] . "." . $exp_virgula[1] : $exp_virgula[0]) . ".";
                                            $movimentacao = new MovimentacaoRating;
                                            $movimentacao->tipo_ratings_id = $rating->tipo_ratings_id;
                                            $movimentacao->ratings_id = $rating->id;
                                            $movimentacao->torneio_id = $torneio->id;
                                            $movimentacao->inscricao_id = $inscricao->id;
                                            $movimentacao->valor = (count($exp_meio) > 1) ? $exp_meio[0] . ".5" : ((count($exp_virgula) > 1) ? $exp_virgula[0] . "." . $exp_virgula[1] : $exp_virgula[0]);
                                            $movimentacao->is_inicial = false;
                                            $movimentacao->save();
                                            $rating->calcular();
                                        } else {
                                            $retornos[] = date("d/m/Y H:i:s") . " - O rating veio nulo. Ignorando alteração.";
                                        }
                                    } else {
                                        $retornos[] = date("d/m/Y H:i:s") . " - O rating veio nulo. Ignorando alteração.";
                                    }
                                } else {
                                    $retornos[] = date("d/m/Y H:i:s") . " - O rating veio nulo. Ignorando alteração.";
                                }
                            }
                        }
                        $retornos[] = date("d/m/Y H:i:s") . " - Fim do processamento do resultado da inscrição do enxadrista #" . $enxadrista->id . " - " . $enxadrista->name . " .";
                        // echo "Enxadrista: ".$enxadrista->name."<br/>";
                    } else {
                        // echo "DEU PROBLEMAAAAA AQUIIIII!";
                        $retornos[] = date("d/m/Y H:i:s") . " - Durante o processamento da inscrição com o ID #" . $line[($fields["ID"])] . " ocorreu um erro: não foi possível encontrar o enxadrista cadastrado.";
                    }
                } else {
                    // echo "DEU PROBLEMAAAAA AQUIIIII!";
                    $retornos[] = date("d/m/Y H:i:s") . " - Durante o processamento da inscrição: inscrição sem ID.";
                }
            }
            $retornos[] = date("d/m/Y H:i:s") . " - <hr/>";
            $i++;
        }
        $retornos[] = date("d/m/Y H:i:s") . " - Fim do Processamento";
        $evento = $torneio->evento;
        return view("evento.torneio.resultadosretorno", compact("retornos", "torneio", "evento"));
    }



    /*
     *
     * EMPARCEIRAMENTOS
     *
     */

    public function formPairingsFile($id, $torneio_id)
    {
        $evento = Evento::find($id);
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/" . $evento->id);
        }

        $torneio = Torneio::find($torneio_id);
        $evento = $torneio->evento;
        return view("evento.torneio.importacao.emparceiramentos", compact("evento", "torneio"));
    }


    public function sendPairingsFile($evento_id, $torneio_id, Request $request)
    {
        $torneio = Torneio::find($torneio_id);
        if($torneio){
            $file = file_get_contents($request->file('arquivo'));

            switch($torneio->software->name){
                case "Swiss-Manager":
                    return $this->importPairings_SwissManager($file, $evento_id, $torneio_id);
                    break;
            }
        }
        return false;
    }

    public function importPairings_SwissManager($emparceiramentos, $evento_id, $torneio_id){
        $evento = Evento::find($evento_id);
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/" . $evento->id);
        }

        $retornos = array();
        $torneio = Torneio::find($torneio_id);
        $retornos[] = date("d/m/Y H:i:s") . " - Início do Processamento para os emparceiramentos do torneio de #" . $torneio->id . " - '" . $torneio->name . "' do Evento '" . $torneio->evento->name . "'";
        $retornos[] = "<hr/>";
        $lines = str_getcsv($emparceiramentos, "\n");
        $i = 0;
        $k = -1;
        $fields = array();
        foreach ($lines as $line) {
            $columns = str_getcsv($line, ";");
            if ($i == 0) {
                $j = 0;
                $retornos[] = date("d/m/Y H:i:s") . " - Capturando as informações de campos no cabeçalho:";
                foreach ($columns as $column) {
                    switch ($column) {
                        case "Rodada":
                            $fields["Rodada"] = $j;
                            $retornos[] = date("d/m/Y H:i:s") . " - Coluna de Código de Rodada [" . $j . "]";
                            break;
                        case "Mesa":
                            $fields["Mesa"] = $j;
                            $retornos[] = date("d/m/Y H:i:s") . " - Coluna de Código de Mesa [" . $j . "]";
                            break;
                        case "ID-B":
                            $fields["ID-B"] = $j;
                            $retornos[] = date("d/m/Y H:i:s") . " - Coluna de Código do Enxadrista de Brancas [" . $j . "]";
                            break;
                        case "ID-N":
                            $fields["ID-N"] = $j;
                            $retornos[] = date("d/m/Y H:i:s") . " - Coluna de Código do Enxadrista de Negras [" . $j . "]";
                            break;
                        case "NoB":
                            $fields["NoB"] = $j;
                            $retornos[] = date("d/m/Y H:i:s") . " - Coluna do Número Inicial do Enxadrista de Brancas [" . $j . "]";
                            break;
                        case "NoN":
                            $fields["NoN"] = $j;
                            $retornos[] = date("d/m/Y H:i:s") . " - Coluna do Número Inicial do Enxadrista de Negras [" . $j . "]";
                            break;
                        case "ResB":
                            $fields["ResB"] = $j;
                            $retornos[] = date("d/m/Y H:i:s") . " - Coluna do Resultado de Brancas [" . $j . "]";
                            break;
                        case "ResM":
                            $fields["ResM"] = $j;
                            $retornos[] = date("d/m/Y H:i:s") . " - Coluna do Resultado das Negras [" . $j . "]";
                            break;
                        case "WO":
                            $fields["WO"] = $j;
                            $retornos[] = date("d/m/Y H:i:s") . " - Coluna de Houve W.O. [" . $j . "]";
                            break;
                    }

                    $j++;
                }
                $retornos[] = "<hr/>";
                $retornos[] = date("d/m/Y H:i:s") . " - Início do Processamento dos Resultados:";


                /*
                 *
                 * REMOVE TODOS OS EMPARCEIRAMENTOS DO TORNEIO
                 *
                 */
                $retornos[] = date("d/m/Y H:i:s") . " - Dado início da remoção de emparceiramentos.";
                foreach($torneio->rodadas->all() as $rodada){
                    foreach($rodada->emparceiramentos->all() as $emparceiramento){
                        $retornos[] = date("d/m/Y H:i:s") . " - Removido emparceiramento da rodada ".$rodada->numero." para a mesa ".$emparceiramento->numero.".";
                        $emparceiramento->delete();
                    }
                }
                $retornos[] = date("d/m/Y H:i:s") . " - Finalizado processo de remoção de emparceiramentos.";
            } else {
                $retornos[] = date("d/m/Y H:i:s") . " - Início de Processamento de Emparceiramento.";
                $line = explode(";", $line);

                $rodada_count = $torneio->rodadas()->where([["numero","=", $line[($fields["Rodada"])] ]])->count();
                if($rodada_count == 0){
                    $retornos[] = date("d/m/Y H:i:s") . " - Rodada de número ".$line[($fields["Rodada"])]." não encontrada. Rodada sendo criada.";
                    $rodada = new Rodada;
                    $rodada->torneio_id = $torneio->id;
                    $rodada->numero = $line[($fields["Rodada"])];
                    $rodada->save();
                    $retornos[] = date("d/m/Y H:i:s") . " - Rodada criada sob o ID ".$rodada->id.".";
                }else{
                    $rodada = $torneio->rodadas()->where([["numero","=", $line[($fields["Rodada"])] ]])->first();
                    $retornos[] = date("d/m/Y H:i:s") . " - Rodada encontrada sob o ID ".$rodada->id.".";
                }

                $retornos[] = date("d/m/Y H:i:s") . " - Iniciando o Processamento de Emparceiramento da Mesa ".$line[($fields["Mesa"])]." .";
                if(isset($line[($fields["ID-B"])]) && isset($line[($fields["ID-N"])])){
                    if($line[($fields["ID-B"])] > 0){
                        $emparceiramento = new Emparceiramento;
                        $emparceiramento->rodadas_id = $rodada->id;
                        $emparceiramento->numero = $line[($fields["Mesa"])];


                        $all_enxadristas_found = true;
                        // Busca do Enxadrista A
                        $enxadrista_a_count = Enxadrista::where([["id","=", Enxadrista::getStaticId($line[($fields["ID-B"])])]])->count();
                        if($enxadrista_a_count > 0){
                            $enxadrista_a = Enxadrista::find(Enxadrista::getStaticId($line[($fields["ID-B"])]));
                        }else{
                            $all_enxadristas_found = false;
                        }
                        if($line[($fields["ID-N"])] > 0){
                            // Busca do Enxadrista B
                            $enxadrista_b_count = Enxadrista::where([["id","=", Enxadrista::getStaticId($line[($fields["ID-N"])])]])->count();
                            if($enxadrista_b_count > 0){
                                $enxadrista_b = Enxadrista::find(Enxadrista::getStaticId($line[($fields["ID-N"])]));
                            }else{
                                $all_enxadristas_found = false;
                            }
                        }

                        if($all_enxadristas_found){

                            $all_enxadristas_are_inscritos = true;

                            if ($enxadrista_a->estaInscrito($torneio->evento->id)) {
                                $inscricao_a = $enxadrista_a->getInscricao($torneio->evento->id);
                            } else {
                                $all_enxadristas_are_inscritos = false;
                            }

                            if($line[($fields["ID-N"])] > 0){
                                if ($enxadrista_b->estaInscrito($torneio->evento->id)) {
                                    $inscricao_b = $enxadrista_b->getInscricao($torneio->evento->id);
                                } else {
                                    $all_enxadristas_are_inscritos = false;
                                }
                            }


                            if($all_enxadristas_are_inscritos){

                                $emparceiramento->inscricao_a = $inscricao_a->id;

                                if($line[($fields["ID-N"])] > 0){
                                    $emparceiramento->inscricao_b = $inscricao_b->id;
                                }

                                if($torneio->evento->tipo_rating){
                                    /*
                                    *
                                    * MÉTODO PARA TESTE DE RATING
                                    *
                                    */
                                    // $modifications = RatingEloHelper::generateElo(
                                    //     $enxadrista_a->ratingParaEvento($torneio->evento->tipo_rating->id),
                                    //     $enxadrista_b->ratingParaEvento($torneio->evento->tipo_rating->id),
                                    //     $enxadrista_a->KParaEvento($torneio->evento->tipo_rating->id),
                                    //     $enxadrista_b->KParaEvento($torneio->evento->tipo_rating->id)
                                    // );

                                    // $retornos[] = date("d/m/Y H:i:s") . " - Movimentações de Curioso: ".json_encode($modifications);

                                }

                                $emparceiramento->numero_a = $line[($fields["NoB"])];

                                if($line[($fields["ID-N"])] > 0){
                                    $emparceiramento->numero_b = $line[($fields["NoN"])];
                                }

                                $explode_res_a = explode(",",$line[($fields["ResB"])]);
                                if($line[($fields["ID-N"])] > 0){
                                    $explode_res_b = explode(",",$line[($fields["ResM"])]);
                                }

                                $emparceiramento->resultado_a = (count($explode_res_a) > 1) ? $explode_res_a[0].".".$explode_res_a[1] : $explode_res_a[0];

                                if($line[($fields["ID-N"])] > 0){
                                    $emparceiramento->resultado_b = (count($explode_res_b) > 1) ? $explode_res_b[0].".".$explode_res_b[1] : $explode_res_b[0];
                                }

                                if(isset($line[($fields["WO"])])){
                                    if($line[($fields["WO"])] != ""){
                                        $retornos[] = date("d/m/Y H:i:s") . " - Houve W.O no emparceiramento. Verificando resultados com 0 para armazenamento do resultado de W.O.";
                                        if($emparceiramento->resultado_a == 0){
                                            $retornos[] = date("d/m/Y H:i:s") . " - W.O para o Enxadrista A.";
                                            $emparceiramento->is_wo_a = true;
                                        }
                                        if($emparceiramento->resultado_b == 0){
                                            $retornos[] = date("d/m/Y H:i:s") . " - W.O para o Enxadrista B.";
                                            $emparceiramento->is_wo_b = true;
                                        }
                                    }
                                }

                                $emparceiramento->save();

                                $retornos[] = date("d/m/Y H:i:s") . " - Emparceiramento para a Rodada ".$rodada->numero." e Mesa ".$emparceiramento->numero." devidamente processado.";
                            } else {
                                // echo "DEU PROBLEMAAAAA AQUIIIII!";
                                $retornos[] = date("d/m/Y H:i:s") . " - Durante o processamento do emparceiramento: Enxadrista A e/ou Enxadrista B sem inscrição.";
                            }
                        } else {
                            // echo "DEU PROBLEMAAAAA AQUIIIII!";
                            $retornos[] = date("d/m/Y H:i:s") . " - Durante o processamento do emparceiramento: Enxadrista A e/ou Enxadrista B não foi encontrado.";
                        }
                    } else {
                        // echo "DEU PROBLEMAAAAA AQUIIIII!";
                        $retornos[] = date("d/m/Y H:i:s") . " - Durante o processamento do emparceiramento: Enxadrista A e/ou Enxadrista B sem ID.";
                    }
                } else {
                    // echo "DEU PROBLEMAAAAA AQUIIIII!";
                    $retornos[] = date("d/m/Y H:i:s") . " - Durante o processamento do emparceiramento: Enxadrista A e/ou Enxadrista B sem ID.";
                }
            }
            $retornos[] = date("d/m/Y H:i:s") . " - <hr/>";
            $i++;
        }
        $retornos[] = date("d/m/Y H:i:s") . " - Fim do Processamento";
        $evento = $torneio->evento;
        return view("evento.torneio.importacao.emparceiramentos_retorno", compact("retornos", "torneio", "evento"));
    }
}

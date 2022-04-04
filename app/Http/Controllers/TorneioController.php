<?php

namespace App\Http\Controllers;

use App\Categoria;
use App\CategoriaTorneio;
use App\CriterioDesempate;
use App\Enxadrista;
use App\Evento;
use App\Inscricao;
use App\InscricaoCriterioDesempate;
use App\MovimentacaoRating;
use App\Rating;
use App\TipoTorneio;
use App\Torneio;
use App\Rodada;
use App\Emparceiramento;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Enum\EmailType;

use App\Http\Controllers\LichessIntegrationController;
use App\Http\Controllers\CriterioDesempateController;

use Log;

class TorneioController extends Controller
{
    public function __construct()
    {
        return $this->middleware("auth");
    }

    public function index($id)
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

        $torneios = $evento->torneios->all();
        return view("evento.torneio.index", compact("evento", "torneios"));
    }

    function new ($id) {
        $evento = Evento::find($id);
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/" . $evento->id);
        }

        $tipos_torneio = TipoTorneio::all();
        return view('evento.torneio.new', compact("evento", "tipos_torneio"));
    }
    public function new_post($id, Request $request)
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

        $torneio = new Torneio;
        $torneio->name = $request->input("name");
        $torneio->tipo_torneio_id = $request->input("tipo_torneio_id");
        $torneio->evento_id = $evento->id;
        $torneio->save();
        return redirect("/evento/" . $evento->id . "/torneios/edit/" . $torneio->id);
    }
    public function edit($id, $torneio_id)
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
        $tipos_torneio = TipoTorneio::all();
        $categorias = Categoria::where([
            ["evento_id", "=", $evento->id],
        ])
            ->orWhere([
                ["grupo_evento_id", "=", $evento->grupo_evento->id],
            ])
            ->get();
        return view('evento.torneio.edit', compact("torneio", "tipos_torneio", "categorias"));
    }
    public function edit_post($id, $torneio_id, Request $request)
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
        $torneio->name = $request->input("name");
        $torneio->tipo_torneio_id = $request->input("tipo_torneio_id");

        $torneio->save();
        return redirect("/evento/" . $evento->id . "/torneios/edit/" . $torneio->id);
    }
    public function delete($id, $torneio_id)
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

        if ($torneio->isDeletavel()) {
            $torneio->delete();
        }
        return redirect("/evento/dashboard/" . $evento->id . "/?tab=torneio");
    }
    public function union($id, $torneio_id)
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
        $torneios = $torneio->evento->torneios()->where([["id", "!=", $torneio->id]])->get();
        return view('evento.torneio.union', compact("torneio", "torneios", "evento"));
    }
    public function union_post($id, $torneio_id, Request $request)
    {
        if (!$request->has("torneio_a_ser_unido")) {
            return redirect()->back();
        } elseif ($request->input("torneio_a_ser_unido") == "") {
            return redirect()->back();
        }

        $evento = Evento::find($id);
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/" . $id);
        }

        $torneio_base = Torneio::find($torneio_id);
        $torneio_a_ser_unido = Torneio::find($request->input("torneio_a_ser_unido"));

        if ($torneio_base && $torneio_a_ser_unido) {
            if ($torneio_base->evento->id == $torneio_a_ser_unido->evento->id) {
                foreach ($torneio_a_ser_unido->inscricoes->all() as $inscricao) {
                    $inscricao->torneio_id = $torneio_base->id;
                    $inscricao->save();
                }
                foreach ($torneio_a_ser_unido->categorias->all() as $categoria) {
                    $categoria_torneio = new CategoriaTorneio;
                    $categoria_torneio->torneio_id = $torneio_base->id;
                    $categoria_torneio->categoria_id = $categoria->categoria->id;
                    $categoria_torneio->save();

                    $categoria->delete();
                }

                $torneio_base->torneio_template_id = null;
                $torneio_base->save();

                $torneio_a_ser_unido->delete();

                return redirect("/evento/dashboard/" . $evento->id . "/?tab=torneio");
            }
        }

        return redirect()->back();
    }
    public function categoria_add($id, $torneio_id, Request $request)
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
        $categoria_torneio = new CategoriaTorneio;
        $categoria_torneio->torneio_id = $torneio_id;
        $categoria_torneio->categoria_id = $request->input("categoria_id");
        $categoria_torneio->save();

        $torneio->torneio_template_id = null;
        $torneio->save();

        return redirect("/evento/" . $evento->id . "/torneios/edit/" . $torneio->id);
    }
    public function categoria_remove($id, $torneio_id, $categoria_torneio_id)
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
        $categoria_torneio = CategoriaTorneio::find($categoria_torneio_id);
        $categoria_torneio->delete();

        $torneio->torneio_template_id = null;
        $torneio->save();

        return redirect("/evento/" . $evento->id . "/torneios/edit/" . $torneio->id);
    }

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
        $fields = array();
        foreach ($lines as $line) {
            $columns = str_getcsv($line, ";");
            if ($i == 0) {
                $j = 0;
                $retornos[] = date("d/m/Y H:i:s") . " - Capturando as informações de campos no cabeçalho:";
                foreach ($columns as $column) {
                    if ($k >= 0 && $k < $torneio->getCountCriteriosNaoManuais()) {
                        $retornos[] = date("d/m/Y H:i:s") . " - " . ($k + 1) . "º Critério de Desempate [" . $j . "] - Total: " . $torneio->getCountCriteriosNaoManuais();
                        $fields["Des" . ($k + 1)] = $j;
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
                $retornos[] = "<hr/>";
                $retornos[] = date("d/m/Y H:i:s") . " - Início do Processamento dos Resultados:";
            } else {
                $line = explode(";", $line);
                // print_r($line);echo "<br/>";
                if (isset($fields["ID"])) {
                    $inscricao = Inscricao::where([
                        ["enxadrista_id", "=", $line[($fields["ID"])]],
                        ["torneio_id", "=", $torneio->id],
                    ])
                        ->first();
                    $enxadrista = Enxadrista::find($line[($fields["ID"])]);
                    if ($enxadrista) {
                        $retornos[] = date("d/m/Y H:i:s") . " - Enxadrista de Código #" . $enxadrista->id . " - " . $enxadrista->name;
                    } else {
                        $retornos[] = date("d/m/Y H:i:s") . " - Enxadrista com o Código #" . $line[($fields["ID"])] . " não encontrado.";
                    }
                    if (!$inscricao) {
                        $retornos[] = date("d/m/Y H:i:s") . " - Não há inscrição deste enxadrista";
                        if ($enxadrista) {
                            $categoria = Categoria::where([["code", "=", $line[($fields["Gr"])]]])->whereHas("eventos", function ($q1) use ($torneio) {
                                $q1->where([["evento_id", "=", $torneio->evento->id]]);
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
        $fields = array();
        foreach ($lines as $line) {
            $columns = str_getcsv($line, ";");
            if ($i == 0) {
                $j = 0;
                $retornos[] = date("d/m/Y H:i:s") . " - Capturando as informações de campos no cabeçalho:";
                foreach ($columns as $column) {
                    if ($k >= 0 && $k < $torneio->getCountCriteriosNaoManuais()) {
                        $retornos[] = date("d/m/Y H:i:s") . " - " . ($k + 1) . "º Critério de Desempate [" . $j . "] - Total: " . $torneio->getCountCriteriosNaoManuais();
                        $fields["Des" . ($k + 1)] = $j;
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
                        ["enxadrista_id", "=", $line[($fields["Fonte"])]],
                        ["torneio_id", "=", $torneio->id],
                    ])
                        ->first();
                    $enxadrista = Enxadrista::find($line[($fields["Fonte"])]);
                    if ($enxadrista) {
                        $retornos[] = date("d/m/Y H:i:s") . " - Enxadrista de Código #" . $enxadrista->id . " - " . $enxadrista->name;
                    } else {
                        $retornos[] = date("d/m/Y H:i:s") . " - Enxadrista com o Código #" . $line[($fields["ID"])] . " não encontrado.";
                    }
                    if (!$inscricao) {
                        $retornos[] = date("d/m/Y H:i:s") . " - Não há inscrição deste enxadrista";
                        if ($enxadrista) {
                            $categoria = Categoria::where([["code", "=", $line[($fields["Gr"])]]])->whereHas("eventos", function ($q1) use ($torneio) {
                                $q1->where([["evento_id", "=", $torneio->evento->id]]);
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
                        ["enxadrista_id", "=", $line[($fields["ID"])]],
                        ["torneio_id", "=", $torneio->id],
                    ])
                        ->first();
                    $enxadrista = Enxadrista::find($line[($fields["ID"])]);
                    if ($enxadrista) {
                        $retornos[] = date("d/m/Y H:i:s") . " - Enxadrista de Código #" . $enxadrista->id . " - " . $enxadrista->name;
                    } else {
                        $retornos[] = date("d/m/Y H:i:s") . " - Enxadrista com o Código #" . $line[($fields["ID"])] . " não encontrado.";
                    }
                    if (!$inscricao) {
                        $retornos[] = date("d/m/Y H:i:s") . " - Não há inscrição deste enxadrista";
                        if ($enxadrista) {
                            $categoria = Categoria::where([["code", "=", $line[($fields["Gr"])]]])->whereHas("eventos", function ($q1) use ($torneio) {
                                $q1->where([["evento_id", "=", $torneio->evento->id]]);
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
     * LICHESS INTEGRATION
     *
     */
    public function check_players_in($id, $torneio_id)
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
        if($torneio->evento->is_lichess_integration){
            if($torneio->evento->lichess_tournament_id){
                $players_not_found = array();

                $lichess_integration_controller = new LichessIntegrationController;
                $retorno = $lichess_integration_controller->getSwissResults($torneio->evento->lichess_tournament_id);
                if($retorno["ok"] == 1){
                    $torneio->setAllInscricoesNotFound();
                    foreach(explode("\n",$retorno["data"]) as $lichess_player_raw){
                        $lichess_player = json_decode(trim($lichess_player_raw));
                        if($lichess_player){
                            $inscricao_count = $torneio->inscricoes()->whereHas("enxadrista",function($q1) use ($lichess_player){
                                $q1->where([
                                    ["lichess_username","=",mb_strtolower($lichess_player->username)]
                                ]);
                            })->count();
                            if($inscricao_count > 0){
                                $inscricao = $torneio->inscricoes()->whereHas("enxadrista",function($q1) use ($lichess_player){
                                    $q1->where([
                                        ["lichess_username","=",mb_strtolower($lichess_player->username)]
                                    ]);
                                })->first();

                                if(!$inscricao->is_lichess_found && (!$inscricao->is_last_lichess_found)){
                                    // EMAIL PARA O ENXADRISTA SOLICITANTE
                                    EmailController::schedule(
                                        $inscricao->enxadrista->email,
                                        $inscricao,
                                        EmailType::ConfirmacaoInscricaoLichess,
                                        $inscricao->enxadrista
                                    );
                                }

                                $inscricao->lichess_rating = $lichess_player->rating;
                                $inscricao->start_position = $lichess_player->rank;
                                $inscricao->is_lichess_found = true;
                                $inscricao->is_last_lichess_found = true;
                                $inscricao->save();
                            }else{
                                $inscricao->is_lichess_found = false;
                                $inscricao->save();
                                $players_not_found[] = $lichess_player->username;
                            }
                        }
                    }
                }
                $torneio->lichess_last_update = date("Y-m-d H:i:s");
                $torneio->save();

                activity()
                    ->performedOn($torneio)
                    ->causedBy(Auth::user())
                    ->log("Lichess.org - Não encontrados como inscritos: ".json_encode($players_not_found));


                $players_not_found = array();
                $retorno_team = $lichess_integration_controller->getTeamMembers($torneio->evento->lichess_team_id);
                if($retorno_team["ok"] == 1){
                    $token_user = $lichess_integration_controller->getUserData(env("LICHESS_TOKEN",""));
                    foreach(explode("\n",$retorno_team["data"]) as $lichess_player_raw){
                        $lichess_player = json_decode(trim($lichess_player_raw));
                        if($lichess_player){
                            $inscricao_count = $torneio->inscricoes()->whereHas("enxadrista",function($q1) use ($lichess_player){
                                $q1->where([
                                    ["lichess_username","=",mb_strtolower($lichess_player->username)]
                                ]);
                            })->count();
                            if($inscricao_count > 0){
                                $inscricao = $torneio->inscricoes()->whereHas("enxadrista",function($q1) use ($lichess_player){
                                    $q1->where([
                                        ["lichess_username","=",mb_strtolower($lichess_player->username)]
                                    ]);
                                })->first();


                                $inscricao->is_lichess_team_found = true;
                                $inscricao->save();
                            }else{
                                if($token_user["data"]->username != $lichess_player->username){
                                    $players_not_found[] = $lichess_player->username;
                                }
                            }
                        }
                    }
                }

                activity()
                    ->performedOn($torneio)
                    ->causedBy(Auth::user())
                    ->log("Lichess.org - Usuários Não Encontrados no Time: ".json_encode($players_not_found));

            }
        }

        return redirect("/evento/dashboard/" . $evento->id . "/?tab=torneio");
    }


    public function lichess_get_results($id, $torneio_id)
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
        if($torneio->evento->is_lichess_integration){
            if($torneio->evento->lichess_tournament_id){
                $criterio_lichess_tiebreak = CriterioDesempate::where([["is_lichess","=",true]])->first();

                $lichess_integration_controller = new LichessIntegrationController;
                $retorno = $lichess_integration_controller->getSwissResults($torneio->evento->lichess_tournament_id);
                if($retorno["ok"] == 1){
                $torneio->setAllInscricoesNotFound();
                    $k = 1;
                    foreach(explode("\n",$retorno["data"]) as $lichess_player_raw){
                        $lichess_player = json_decode(trim($lichess_player_raw));
                        if($lichess_player){
                            Log::debug("Import Username ".$k++.": ".$lichess_player->username);
                            $inscricao_count = $torneio->inscricoes()->whereHas("enxadrista",function($q1) use ($lichess_player){
                                $q1->where([
                                    ["lichess_username","=",trim(mb_strtolower($lichess_player->username))]
                                ]);
                            })->count();
                            if($inscricao_count > 0){
                                $inscricao = $torneio->inscricoes()->whereHas("enxadrista",function($q1) use ($lichess_player){
                                    $q1->where([
                                        ["lichess_username","=",trim(mb_strtolower($lichess_player->username))]
                                    ]);
                                })->first();

                                $inscricao->pontos = $lichess_player->points;

                                $inscricao->is_lichess_found = true;
                                $inscricao->confirmado = true;
                                $inscricao->save();

                                $inscricao_criterio = $inscricao->criterios_desempate()->where([
                                    ["criterio_desempate_id","=",$criterio_lichess_tiebreak->id]
                                ])->first();
                                if(!$inscricao_criterio){
                                    $inscricao_criterio = new InscricaoCriterioDesempate;
                                    $inscricao_criterio->inscricao_id = $inscricao->id;
                                    $inscricao_criterio->criterio_desempate_id = $criterio_lichess_tiebreak->id;
                                }
                                $inscricao_criterio->valor = $lichess_player->tieBreak;
                                $inscricao_criterio->save();

                            }
                        }
                    }
                }

                $torneio->lichess_last_update = date("Y-m-d H:i:s");
                $torneio->save();

                activity()
                    ->performedOn($torneio)
                    ->causedBy(Auth::user())
                    ->log("Resultados importados para o torneio.");

            }
        }

        return redirect("/evento/dashboard/" . $evento->id . "/?tab=torneio");
    }

    public function remove_lichess_players_not_found($id, $torneio_id)
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
        if($torneio->evento->is_lichess_integration){
            if($torneio->evento->lichess_tournament_id){
                $players_not_found = array();

                $lichess_integration_controller = new LichessIntegrationController;

                $token_user = $lichess_integration_controller->getUserData(env("LICHESS_TOKEN",""));

                if($token_user){
                    if($token_user["ok"]){
                        $retorno = $lichess_integration_controller->getTeamMembers($torneio->evento->lichess_team_id);
                        if($retorno["ok"] == 1){
                            foreach(explode("\n",$retorno["data"]) as $lichess_player_raw){
                                $lichess_player = json_decode(trim($lichess_player_raw));
                                if($lichess_player){
                                    $inscricao_count = $torneio->inscricoes()->whereHas("enxadrista",function($q1) use ($lichess_player){
                                        $q1->where([
                                            ["lichess_username","=",mb_strtolower($lichess_player->username)]
                                        ]);
                                    })->count();
                                    if($inscricao_count == 0){
                                        if($token_user["data"]->username != $lichess_player->username){
                                            $players_not_found[] = $lichess_player->username;
                                            $lichess_integration_controller->removeMemberFromTeam($torneio->evento->lichess_team_id, $lichess_player->username);
                                        }
                                    }
                                }
                            }
                        }
                        $torneio->save();

                        activity()
                            ->performedOn($torneio)
                            ->causedBy(Auth::user())
                            ->log("Lichess.org - Usuários Removidos do Time: ".json_encode($players_not_found));

                    }
                }
            }
        }

        return redirect("/evento/dashboard/" . $evento->id . "/?tab=torneio");
    }
    public function remove_lichess_players_not_found_on_team($id, $torneio_id)
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
        if($torneio->evento->is_lichess_integration){
            if($torneio->evento->lichess_tournament_id){
                $players_not_found = array();

                $lichess_integration_controller = new LichessIntegrationController;

                $token_user = $lichess_integration_controller->getUserData(env("LICHESS_TOKEN",""));

                if($token_user){
                    if($token_user["ok"]){
                        $retorno = $lichess_integration_controller->getTeamMembers($torneio->evento->lichess_team_id);
                        if($retorno["ok"] == 1){
                            foreach(explode("\n",$retorno["data"]) as $lichess_player_raw){
                                $lichess_player = json_decode(trim($lichess_player_raw));
                                if($lichess_player){
                                    $inscricao_count = $torneio->inscricoes()->whereHas("enxadrista",function($q1) use ($lichess_player){
                                        $q1->where([
                                            ["lichess_username","=",mb_strtolower($lichess_player->username)]
                                        ]);
                                    })->count();
                                    if($inscricao_count == 0){
                                        if($token_user["data"]->username != $lichess_player->username){
                                            $players_not_found[] = $lichess_player->username;
                                            $lichess_integration_controller->removeMemberFromTeam($torneio->evento->lichess_team_id, $lichess_player->username);
                                        }
                                    }
                                }
                            }
                        }
                        $torneio->save();

                        activity()
                            ->performedOn($torneio)
                            ->causedBy(Auth::user())
                            ->log("Lichess.org - Usuários Removidos do Time: ".json_encode($players_not_found));

                    }
                }
            }
        }

        return redirect("/evento/dashboard/" . $evento->id . "/?tab=torneio");
    }





    public static function generateRodadasDefault($torneio_id){
        $torneio = Torneio::find($torneio_id);
        if($torneio){
            if($torneio->tipo_torneio->id == 3){
                // GERANDO RODADAS PARA A ESTRUTURA DE CHAVE DE SEMI FINAL E FINAL (SEM DISPUTA DE 3o)
                /*
                 *       J1 --|                                               |-- J2
                 *            |----(VENCEDOR J1 X J4) X (VENCEDOR J2 X J3)----|
                 *       J4 --|                                               |-- J3
                 *
                 */

                $rodada_1 = new Rodada;
                $rodada_1->torneio_id = $torneio->id;
                $rodada_1->numero = 1;
                $rodada_1->save();

                $emparceiramento_1 = new Emparceiramento;
                $emparceiramento_1->rodadas_id = $rodada_1->id;
                $emparceiramento_1->numero_a = 1;
                $emparceiramento_1->numero_b = 4;
                $emparceiramento_1->save();

                $emparceiramento_2 = new Emparceiramento;
                $emparceiramento_2->rodadas_id = $rodada_1->id;
                $emparceiramento_2->numero_a = 2;
                $emparceiramento_2->numero_b = 3;
                $emparceiramento_2->save();

                $rodada_2 = new Rodada;
                $rodada_2->torneio_id = $torneio->id;
                $rodada_2->numero = 2;
                $rodada_2->save();

                $emparceiramento_3 = new Emparceiramento;
                $emparceiramento_3->rodadas_id = $rodada_2->id;
                $emparceiramento_3->numero_a = "J1xJ4";
                $emparceiramento_3->numero_b = "J2xJ3";
                $emparceiramento_3->save();
            }
        }
    }


    public static function gerarCriteriosDesempate($torneio_id){
        $torneio = Torneio::find($torneio_id);
        if($torneio){
            $criterio_desempate_controller = new CriterioDesempateController;
            $criterios_desempate = $torneio->evento->getCriterios();
            if($torneio->tipo_torneio->id == 3){
                foreach($torneio->inscricoes->all() as $inscricao){
                    foreach($inscricao->criterios_desempate->all() as $criterios_inscricao){
                        $criterios_inscricao->delete();
                    }
                }
                foreach($criterios_desempate as $criterio_desempate){
                    foreach($torneio->inscricoes->all() as $inscricao){
                        if(is_numeric($inscricao->pontos)){
                            $valor_criterio = $criterio_desempate_controller->generate($torneio->evento, $inscricao->enxadrista, $criterio_desempate->criterio);
                            if(!is_bool($valor_criterio)){
                                $criterio_desempate_inscricao = new InscricaoCriterioDesempate;
                                $criterio_desempate_inscricao->inscricao_id = $inscricao->id;
                                $criterio_desempate_inscricao->criterio_desempate_id = $criterio_desempate->criterio->id;
                                $criterio_desempate_inscricao->valor = (!$inscricao->desconsiderar_pontuacao_geral) ? $valor_criterio : 0;
                                $criterio_desempate_inscricao->save();
                            }
                        }
                    }
                }
            }
        }
    }

}

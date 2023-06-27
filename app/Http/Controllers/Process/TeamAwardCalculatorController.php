<?php

namespace App\Http\Controllers\Process;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Enum\ConfigType;

use App\EventTeamScore;
use App\EventTeamAwardScore;
use App\TiebreakTeamAwardValue;
use App\Inscricao;

use Log;

class TeamAwardCalculatorController extends Controller
{
    public static function sum_scores($evento = null, $grupoevento = null, $time_award)
    {
        $object = null;
        if($evento){
            $object = $evento;
        }elseif($grupoevento){
            $object = $grupoevento;
        }else{
            exit();
        }
        $retornos = array();
        Log::debug("Função de Soma de Pontos");
        Log::debug("Zerando pontuações existentes");
        foreach (EventTeamScore::where([
            ["event_team_awards_id", "=", $time_award->id],
        ])->get() as $score) {
            foreach($score->configs->all() as $config){
                $config->delete();
            }
            foreach($score->tiebreaks->all() as $tiebreak){
                $tiebreak->delete();
            }
            $score->delete();
        }
        Log::debug("Listando Eventos do Grupo de Evento");
        $categorias_id = $time_award->categories()->pluck("categories_id");
        Log::debug("Categorias: ".json_encode($categorias_id));

        $inscricoes_count = Inscricao::where([
            ["confirmado", "=", true],
            ["desconsiderar_pontuacao_geral", "=", false],
            ["posicao", ">", 0],
            ["posicao", "!=", NULL],
            ["clube_id", "!=", NULL],
        ])
        ->whereIn("categoria_id",$categorias_id)
        ->whereHas("torneio", function ($q1) use ($object) {
            if($object->isEvent()){
                $q1->where([
                    ["evento_id", "=", $object->id],
                ]);
            }else{
                $q1->whereHas("evento",function($q2) use ($object){
                    $q2->where([
                        ["grupo_evento_id","=",$object->id]
                    ]);
                });
            }
        })
        ->count();

        $inscricoes = Inscricao::where([
            ["confirmado", "=", true],
            ["desconsiderar_pontuacao_geral", "=", false],
            ["posicao", ">", 0],
            ["clube_id", "!=", NULL],
        ])
        ->whereIn("categoria_id",$categorias_id)
        ->orderBy("posicao", "ASC")
        ->whereHas("torneio", function ($q1) use ($object) {
            if($object->isEvent()){
                $q1->where([
                    ["evento_id", "=", $object->id],
                ]);
            }else{
                $q1->whereHas("evento",function($q2) use ($object){
                    $q2->where([
                        ["grupo_evento_id","=",$object->id]
                    ]);
                });
            }
        })
        ->get();

        $is_points = false;

        if($time_award->hasConfig("is_points")){
            if($time_award->getConfig("is_points",true)){
                $is_points = true;
            }
        }
        Log::debug("Total de inscrições encontradas: " . $inscricoes_count);
        foreach ($inscricoes as $inscricao) {
            if($time_award->hasPlace($inscricao->posicao) || $is_points){
                if($is_points){
                    $points = $time_award->getPlace($inscricao->posicao,true,true,$inscricao);
                }else{
                    $points = $time_award->getPlace($inscricao->posicao,true);
                }


                Log::debug("Inscrição #: " . $inscricao->id . " - Enxadrista: " . $inscricao->enxadrista->name);
                $pontos_time = $time_award->team_scores()->where([
                    ["clubs_id", "=", $inscricao->clube->id],
                ])->first();
                if (!$pontos_time) {
                    $pontos_time = new EventTeamScore;
                    $pontos_time->event_team_awards_id = $time_award->id;
                    $pontos_time->clubs_id = $inscricao->clube->id;
                    $pontos_time->place = -1;
                    $pontos_time->score = 0;
                    $pontos_time->save();
                }

                if ($time_award->hasConfig("limit_places")) {

                    if(!$pontos_time->hasConfig("registrations_processed_category_".$inscricao->categoria->id)){
                        $pontos_time->setConfig("registrations_processed_category_".$inscricao->categoria->id,ConfigType::Integer,0);
                    }

                    $quantidade = $pontos_time->getConfig("registrations_processed_category_".$inscricao->categoria->id,true);
                    if ($time_award->getConfig("limit_places",true) > $quantidade) {
                        $pontos_time->score += $points;

                        $quantidade++;
                        $pontos_time->setConfig("registrations_processed_category_".$inscricao->categoria->id,ConfigType::Integer,$quantidade);
                    }
                } else {
                    $pontos_time->score += $points;

                }
                $pontos_time->save();
                $retornos[] = "<hr/>";
            }
        }
        return $retornos;
    }

    public static function generate_tiebreaks($evento = null, $grupoevento = null, $team_award)
    {
        $object = null;
        if($evento){
            $object = $evento;
        }elseif($grupoevento){
            $object = $grupoevento;
        }else{
            exit();
        }
        $retornos = array();
        $retornos[] = date("d/m/Y H:i:s") . " - Função de geração de Critérios de Desempate";
        $tiebreaks = $team_award->tiebreaks()->orderBy("priority","ASC")->get();

        foreach($team_award->team_scores->all() as $score){
            if($score){
                $generator = new TeamAwardTiebreaksController;
                foreach ($score->tiebreaks->all() as $tiebreak) {
                    $tiebreak->delete();
                }
                foreach ($tiebreaks as $tiebreak_item) {
                    $tiebreak_score = new TiebreakTeamAwardValue;
                    $tiebreak_score->event_team_scores_id = $score->id;
                    $tiebreak_score->tiebreaks_id = $tiebreak_item->tiebreak->id;
                    $tiebreak_score->priority = $tiebreak_item->priority;
                    if($object->isEvent()){
                        $tiebreak_score->value = $generator->generate($object, null, $score, $tiebreak_item->tiebreak);
                    }else{
                        $tiebreak_score->value = $generator->generate(null, $object, $score, $tiebreak_item->tiebreak);
                    }
                    $tiebreak_score->save();
                }
            }
        }

        $retornos[] = date("d/m/Y H:i:s") . " - Fim da Função de geração de Critérios de Desempate";
        return $retornos;
    }

    public static function classificate_teams($evento = null, $grupoevento = null, $team_award)
    {
        $object = null;
        if($evento){
            $object = $evento;
        }elseif($grupoevento){
            $object = $grupoevento;
        }else{
            exit();
        }
        $retornos = array();
        $retornos[] = date("d/m/Y H:i:s") . " - Função de classificação dos enxadristas";
        $retornos[] = date("d/m/Y H:i:s") . " - Listando as pontuações dos enxadristas que possuem alguma pontuação";

        $scores = array();
        foreach ($team_award->team_scores->all() as $score) {
            if ($score->score > 0) {
                $scores[] = $score;
            }
        }
        usort($scores, array("\App\Http\Controllers\Process\TeamAwardCalculatorController", "sort"));
        $i = 1;
        foreach ($scores as $score) {
            $retornos[] = date("d/m/Y H:i:s") . " - Club: " . $score->club->name . " - Posição: " . $i;
            $score->place = $i;
            $score->save();
            $i++;
        }
        $retornos[] = date("d/m/Y H:i:s") . " - Fim da Função de classificação dos enxadristas";
        return $retornos;
    }



    public static function sort($score_a, $score_b)
    {
        if ($score_a->score > $score_b->score) {
            return -1;
        } elseif ($score_a->score < $score_b->score) {
            return 1;
        } else {
            if($score_a->tiebreaks()->orderBy("priority","ASC")->count() == $score_b->tiebreaks()->orderBy("priority","ASC")->count()){
                $tiebreaks_a = $score_a->tiebreaks()->orderBy("priority","ASC")->get()->toArray();
                $tiebreaks_b = $score_b->tiebreaks()->orderBy("priority","ASC")->get()->toArray();

                for($i = 0; $i < count($tiebreaks_a); $i++){
                    $tiebreak_a = $tiebreaks_a[$i];
                    $tiebreak_b = $tiebreaks_b[$i];
                    if ($tiebreak_a["value"] < $tiebreak_b["value"]) {
                        return 1;
                    } elseif ($tiebreak_a["value"] > $tiebreak_b["value"]) {
                        return -1;
                    }
                }
                return 0;
            }
            return strnatcmp($score_a->club->getName(), $score_b->club->getName());
        }
    }
}

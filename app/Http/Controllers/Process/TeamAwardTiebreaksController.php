<?php

namespace App\Http\Controllers\Process;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Inscricao;

class TeamAwardTiebreaksController extends Controller
{
    public function generate($evento = null, $grupo_evento = null, $team_score, $criterio_desempate)
    {
        switch ($criterio_desempate->internal_code) {
            case "TA1":
                return $this->generate_ta1($evento, $grupo_evento, $team_score);
                break;
            case "TA2":
                return $this->generate_ta2($evento, $grupo_evento, $team_score);
                break;
            case "TA3":
                return $this->generate_ta3($evento, $grupo_evento, $team_score);
                break;
        }
    }

    // CRITÉRIOS DE DESEMPATE

    // CÓDIGO: TA1
    // NOME DO CRITÉRIO: MAIOR NÚMERO DE PRIMEIROS LUGARES
    public function generate_ta1($evento = null, $grupo_evento = null, $team_score)
    {
        $value = 0;

        $categorias_id = $team_score->event_team_award->categories()->pluck("categories_id");
        return number_format(Inscricao::where([
            ["confirmado", "=", true],
            ["desconsiderar_pontuacao_geral", "=", false],
            ["posicao", "=", 1],
            ["clube_id", "=", $team_score->clubs_id],
        ])
        ->whereIn("categoria_id",$categorias_id)
        ->orderBy("posicao", "ASC")
        ->whereHas("torneio", function ($q1) use ($evento, $grupo_evento) {
            if($evento){
                $q1->where([
                    ["evento_id", "=", $evento->id],
                ]);
            }else{
                $q1->whereHas("evento",function($q2) use ($grupo_evento){
                    $q2->where([
                        ["grupo_evento_id","=",$grupo_evento->id]
                    ]);
                });
            }
        })
        ->count(), 2, '.', '');

        return number_format($value, 2, '.', '');
    }

    // CÓDIGO: TA2
    // NOME DO CRITÉRIO: MAIOR NÚMERO DE SEGUNDOS LUGARES
    public function generate_ta2($evento = null, $grupo_evento = null, $team_score)
    {
        $value = 0;
        $categorias_id = $team_score->event_team_award->categories()->pluck("categories_id");
        return number_format(Inscricao::where([
            ["confirmado", "=", true],
            ["desconsiderar_pontuacao_geral", "=", false],
            ["posicao", "=", 2],
            ["clube_id", "=", $team_score->clubs_id],
        ])
        ->whereIn("categoria_id",$categorias_id)
        ->orderBy("posicao", "ASC")
        ->whereHas("torneio", function ($q1) use ($evento, $grupo_evento) {
            if($evento){
                $q1->where([
                    ["evento_id", "=", $evento->id],
                ]);
            }else{
                $q1->whereHas("evento",function($q2) use ($grupo_evento){
                    $q2->where([
                        ["grupo_evento_id","=",$grupo_evento->id]
                    ]);
                });
            }
        })
        ->count(), 2, '.', '');

        return number_format($value, 2, '.', '');
    }

    // CÓDIGO: TA3
    // NOME DO CRITÉRIO: MAIOR NÚMERO DE TERCEIROS LUGARES
    public function generate_ta3($evento = null, $grupo_evento = null, $team_score)
    {
        $value = 0;

        $categorias_id = $team_score->event_team_award->categories()->pluck("categories_id");
        return number_format(Inscricao::where([
            ["confirmado", "=", true],
            ["desconsiderar_pontuacao_geral", "=", false],
            ["posicao", "=", 3],
            ["clube_id", "=", $team_score->clubs_id],
        ])
        ->whereIn("categoria_id",$categorias_id)
        ->orderBy("posicao", "ASC")
        ->whereHas("torneio", function ($q1) use ($evento, $grupo_evento) {
            if($evento){
                $q1->where([
                    ["evento_id", "=", $evento->id],
                ]);
            }else{
                $q1->whereHas("evento",function($q2) use ($grupo_evento){
                    $q2->where([
                        ["grupo_evento_id","=",$grupo_evento->id]
                    ]);
                });
            }
        })
        ->count(), 2, '.', '');

        return number_format($value, 2, '.', '');
    }
}

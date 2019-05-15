<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DateTime;

class CriterioDesempateGeralController extends Controller
{
    public function generate($grupo_evento, $enxadrista, $criterio_desempate){
        switch($criterio_desempate->internal_code){
            case "G1":
                return $this->generate_g1($grupo_evento, $enxadrista);
                break;
            case "G2":
                return $this->generate_g2($grupo_evento, $enxadrista);
                break;
            case "G3":
                return $this->generate_g3($grupo_evento, $enxadrista);
                break;
            case "G4":
                return $this->generate_g4($grupo_evento, $enxadrista);
                break;
            case "G5":
                return $this->generate_g5($grupo_evento, $enxadrista);
                break;
            case "G6":
                return $this->generate_g6($grupo_evento, $enxadrista);
                break;
            case "G7":
                return $this->generate_g7($grupo_evento, $enxadrista);
                break;
            case "G8":
                return $this->generate_g8($grupo_evento, $enxadrista);
                break;
            case "G9":
                return $this->generate_g9($grupo_evento, $enxadrista);
                break;
            case "G10":
                return $this->generate_g10($grupo_evento, $enxadrista);
                break;
        }
    }


    // CRITÉRIOS DE DESEMPATE


    // CÓDIGO: G1
    // NOME DO CRITÉRIO: MAIOR NÚMERO DE PRIMEIROS LUGARES
    public function generate_g1($grupo_evento, $enxadrista){
        $valor = 0;

        foreach($grupo_evento->eventos->all() as $evento){
            $inscricao = $evento->enxadristaInscrito($enxadrista->id);
            if($inscricao){
                if($inscricao->posicao == 1) $valor++;
            }
        }

        return $valor;
    }

    // CÓDIGO: G2
    // NOME DO CRITÉRIO: MAIOR NÚMERO DE SEGUNDOS LUGARES
    public function generate_g2($grupo_evento, $enxadrista){
        $valor = 0;

        foreach($grupo_evento->eventos->all() as $evento){
            $inscricao = $evento->enxadristaInscrito($enxadrista->id);
            if($inscricao){
                if($inscricao->posicao == 2) $valor++;
            }
        }

        return number_format($valor, 2, '.', '');
    }

    // CÓDIGO: G3
    // NOME DO CRITÉRIO: MAIOR NÚMERO DE TERCEIROS LUGARES
    public function generate_g3($grupo_evento, $enxadrista){
        $valor = 0;

        foreach($grupo_evento->eventos->all() as $evento){
            $inscricao = $evento->enxadristaInscrito($enxadrista->id);
            if($inscricao){
                if($inscricao->posicao == 3) $valor++;
            }
        }

        return number_format($valor, 2, '.', '');
    }

    // CÓDIGO: G4
    // NOME DO CRITÉRIO: MAIOR IDADE
    public function generate_g4($grupo_evento, $enxadrista){
        $datetime = new DateTime;
        $datetime_born = DateTime::createFromFormat('Y-m-d', $enxadrista->born);
        if($datetime_born){
            return number_format($datetime->diff($datetime_born)->days, 2, '.', '');
        }else
            return false;
    }

    // CÓDIGO: G5
    // NOME DO CRITÉRIO: PONTUAÇÃO TOTAL (SEM CORTES)
    public function generate_g5($grupo_evento, $enxadrista){
        $valor = 0;

        foreach($grupo_evento->eventos->all() as $evento){
            $inscricao = $evento->enxadristaInscrito($enxadrista->id);
            if($inscricao){
                $valor += $inscricao->pontos_geral;
            }
        }

        return number_format($valor, 2, '.', '');
    }
    
    // CÓDIGO: G6
    // NOME DO CRITÉRIO: PONTUAÇÃO TOTAL (COM CORTE DO PIOR RESULTADO)
    public function generate_g6($grupo_evento, $enxadrista){
        $valor = 0;
        $pontuacoes = array();

        foreach($grupo_evento->eventos->all() as $evento){
            $inscricao = $evento->enxadristaInscrito($enxadrista->id);
            if($inscricao){
                if($inscricao->pontos_geral){
                    if($inscricao->pontos_geral > 0) $pontuacoes[] = $inscricao->pontos_geral;
                }
            }
        }

        arsort($pontuacoes);
        $count = 1;
        $count_total = count($pontuacoes);

        foreach($pontuacoes as $pontuacao){
            if($count++ < $count_total){
                $valor += $pontuacao;
            }
        }

        return number_format($valor, 2, '.', '');
    }
    
    // CÓDIGO: G7
    // NOME DO CRITÉRIO: PONTUAÇÃO TOTAL (COM CORTE DO MELHOR E PIOR RESULTADO)
    public function generate_g7($grupo_evento, $enxadrista){
        $valor = 0;
        $pontuacoes = array();

        foreach($grupo_evento->eventos->all() as $evento){
            $inscricao = $evento->enxadristaInscrito($enxadrista->id);
            if($inscricao){
                if($inscricao->pontos_geral){
                    if($inscricao->pontos_geral > 0) $pontuacoes[] = $inscricao->pontos_geral;
                }
            }
        }

        arsort($pontuacoes);
        $count = 1;
        $count_total = count($pontuacoes);

        foreach($pontuacoes as $pontuacao){
            if($count < $count_total){
                if($count > 1) $valor += $pontuacao;
            }
            $count++;
        }

        return number_format($valor, 2, '.', '');
    }
    
    // CÓDIGO: G8
    // NOME DO CRITÉRIO: PONTUAÇÃO MÉDIA (CONSIDERANDO O NÚMERO TOTAL DE ETAPAS)
    public function generate_g8($grupo_evento, $enxadrista){
        $valor = 0;
        $pontuacoes = array();

        $pontuacao_total = $this->generate_g5($grupo_evento, $enxadrista);
        $total_etapas = $grupo_evento->etapas()->count();

        return number_format($pontuacao_total/$total_etapas, 2, '.', '');
    }
    
    // CÓDIGO: G9
    // NOME DO CRITÉRIO: PONTUAÇÃO MÉDIA (CONSIDERANDO O NÚMERO DE ETAPAS QUE PARTICIPOU)
    public function generate_g9($grupo_evento, $enxadrista){
        $valor = 0;
        $pontuacoes = array();

        $pontuacao_total = $this->generate_g5($grupo_evento, $enxadrista);
        $total_etapas_participadas = $grupo_evento->etapas()->whereHas("torneios",function($q1) use ($enxadrista){
            $q1->whereHas("inscricoes",function($q2) use ($enxadrista){
                $q2->where([
                    ["enxadrista_id","=",$enxadrista->id]
                ]);
            });
        })->count();

        return number_format($pontuacao_total/$total_etapas_participadas, 2, '.', '');
    }
    
    // CÓDIGO: G10
    // NOME DO CRITÉRIO: MANUAL
    public function generate_g10($grupo_evento, $enxadrista){
        return 0.00;
    }
}

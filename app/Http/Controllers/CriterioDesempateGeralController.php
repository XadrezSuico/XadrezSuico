<?php

namespace App\Http\Controllers;

use DateTime;

class CriterioDesempateGeralController extends Controller
{
    private function inscricaoMatchesCategoria($inscricao, $categoria)
    {
        if ($categoria === null) {
            return true;
        }
        return $inscricao->categoria_id == $categoria->id;
    }

    private function getPosicaoInscricaoDesempate($inscricao, $categoria)
    {
        if ($categoria === null) {
            return $inscricao->posicao_classificacao_geral;
        }
        return $inscricao->posicao;
    }

    private function getPontosInscricaoDesempate($inscricao, $categoria)
    {
        if ($categoria === null) {
            return $inscricao->pontos_classificacao_geral;
        }
        return $inscricao->pontos_geral;
    }

    private function getInscricoesEnxadristaEvento($evento, $enxadrista, $categoria)
    {
        $query = \App\Inscricao::where([
            ["enxadrista_id", "=", $enxadrista->id],
        ])->whereHas("torneio", function ($q1) use ($evento) {
            $q1->where("evento_id", "=", $evento->id);
        });

        if ($categoria !== null) {
            $query->where("categoria_id", "=", $categoria->id);
        }

        return $query->get();
    }

    public function generate($grupo_evento, $enxadrista, $criterio_desempate, $categoria = null)
    {
        switch ($criterio_desempate->internal_code) {
            case "G1":
                return $this->generate_g1($grupo_evento, $enxadrista, $categoria);
                break;
            case "G2":
                return $this->generate_g2($grupo_evento, $enxadrista, $categoria);
                break;
            case "G3":
                return $this->generate_g3($grupo_evento, $enxadrista, $categoria);
                break;
            case "G4":
                return $this->generate_g4($grupo_evento, $enxadrista);
                break;
            case "G5":
                return $this->generate_g5($grupo_evento, $enxadrista, $categoria);
                break;
            case "G6":
                return $this->generate_g6($grupo_evento, $enxadrista, $categoria);
                break;
            case "G7":
                return $this->generate_g7($grupo_evento, $enxadrista, $categoria);
                break;
            case "G8":
                return $this->generate_g8($grupo_evento, $enxadrista, $categoria);
                break;
            case "G9":
                return $this->generate_g9($grupo_evento, $enxadrista, $categoria);
                break;
            case "G10":
                return $this->generate_g10($grupo_evento, $enxadrista);
                break;
            case "G11":
                return $this->generate_g11($grupo_evento, $enxadrista, $categoria);
                break;
            case "G12":
                return $this->generate_g12($grupo_evento, $enxadrista, $categoria);
                break;
            case "G13":
                return $this->generate_g13($grupo_evento, $enxadrista, $categoria);
                break;
            case "G14":
                return $this->generate_g14($grupo_evento, $enxadrista, $categoria);
                break;
            case "G15":
                return $this->generate_g15($grupo_evento, $enxadrista, $categoria);
                break;
            case "G16":
                return $this->generate_g16($grupo_evento, $enxadrista, $categoria);
                break;
        }
    }

    // CRITÉRIOS DE DESEMPATE

    // CÓDIGO: G1
    // NOME DO CRITÉRIO: MAIOR NÚMERO DE PRIMEIROS LUGARES
    public function generate_g1($grupo_evento, $enxadrista, $categoria)
    {
        $valor = 0;

        foreach ($grupo_evento->eventos->all() as $evento) {
            foreach ($this->getInscricoesEnxadristaEvento($evento, $enxadrista, $categoria) as $inscricao) {
                if ($this->inscricaoMatchesCategoria($inscricao, $categoria)) {
                    if ($this->getPosicaoInscricaoDesempate($inscricao, $categoria) == 1) {
                        $valor++;
                    }
                }
            }
        }

        return $valor;
    }

    // CÓDIGO: G2
    // NOME DO CRITÉRIO: MAIOR NÚMERO DE SEGUNDOS LUGARES
    public function generate_g2($grupo_evento, $enxadrista, $categoria)
    {
        $valor = 0;

        foreach ($grupo_evento->eventos->all() as $evento) {
            foreach ($this->getInscricoesEnxadristaEvento($evento, $enxadrista, $categoria) as $inscricao) {
                if ($this->inscricaoMatchesCategoria($inscricao, $categoria)) {
                    if ($this->getPosicaoInscricaoDesempate($inscricao, $categoria) == 2) {
                        $valor++;
                    }
                }
            }
        }

        return number_format($valor, 2, '.', '');
    }

    // CÓDIGO: G3
    // NOME DO CRITÉRIO: MAIOR NÚMERO DE TERCEIROS LUGARES
    public function generate_g3($grupo_evento, $enxadrista, $categoria)
    {
        $valor = 0;

        foreach ($grupo_evento->eventos->all() as $evento) {
            foreach ($this->getInscricoesEnxadristaEvento($evento, $enxadrista, $categoria) as $inscricao) {
                if ($this->inscricaoMatchesCategoria($inscricao, $categoria)) {
                    if ($this->getPosicaoInscricaoDesempate($inscricao, $categoria) == 3) {
                        $valor++;
                    }
                }
            }
        }

        return number_format($valor, 2, '.', '');
    }

    // CÓDIGO: G4
    // NOME DO CRITÉRIO: MAIOR IDADE
    public function generate_g4($grupo_evento, $enxadrista)
    {
        $datetime = new DateTime;
        $datetime_born = DateTime::createFromFormat('Y-m-d', $enxadrista->born);
        if ($datetime_born) {
            return number_format($datetime->diff($datetime_born)->days, 2, '.', '');
        } else {
            return false;
        }

    }

    // CÓDIGO: G5
    // NOME DO CRITÉRIO: PONTUAÇÃO TOTAL (SEM CORTES)
    public function generate_g5($grupo_evento, $enxadrista, $categoria)
    {
        $valor = 0;

        foreach ($grupo_evento->eventos->all() as $evento) {
            foreach ($this->getInscricoesEnxadristaEvento($evento, $enxadrista, $categoria) as $inscricao) {
                if ($this->inscricaoMatchesCategoria($inscricao, $categoria)) {
                    if ($inscricao->isPresent()) {
                        $pontos = $this->getPontosInscricaoDesempate($inscricao, $categoria);
                        if ($pontos && $pontos > 0) {
                            $valor += $pontos;
                        }
                    }
                }
            }
        }

        return number_format($valor, 2, '.', '');
    }

    // CÓDIGO: G6
    // NOME DO CRITÉRIO: PONTUAÇÃO TOTAL (COM CORTE DO PIOR RESULTADO)
    public function generate_g6($grupo_evento, $enxadrista, $categoria)
    {
        $valor = 0;
        $pontuacoes = array();

        foreach ($grupo_evento->eventos->all() as $evento) {
            foreach ($this->getInscricoesEnxadristaEvento($evento, $enxadrista, $categoria) as $inscricao) {
                if ($this->inscricaoMatchesCategoria($inscricao, $categoria)) {
                    if ($inscricao->isPresent()) {
                        $pontos = $this->getPontosInscricaoDesempate($inscricao, $categoria);
                        if ($pontos && $pontos > 0) {
                            $pontuacoes[] = $pontos;
                        }
                    }
                }
            }
        }

        arsort($pontuacoes);
        $count = 1;
        $count_total = count($pontuacoes);

        foreach ($pontuacoes as $pontuacao) {
            if ($count++ < $count_total) {
                $valor += $pontuacao;
            }
        }

        return number_format($valor, 2, '.', '');
    }

    // CÓDIGO: G7
    // NOME DO CRITÉRIO: PONTUAÇÃO TOTAL (COM CORTE DO MELHOR E PIOR RESULTADO)
    public function generate_g7($grupo_evento, $enxadrista, $categoria)
    {
        $valor = 0;
        $pontuacoes = array();

        foreach ($grupo_evento->eventos->all() as $evento) {
            foreach ($this->getInscricoesEnxadristaEvento($evento, $enxadrista, $categoria) as $inscricao) {
                if ($this->inscricaoMatchesCategoria($inscricao, $categoria)) {
                    if ($inscricao->isPresent()) {
                        $pontos = $this->getPontosInscricaoDesempate($inscricao, $categoria);
                        if ($pontos && $pontos > 0) {
                            $pontuacoes[] = $pontos;
                        }
                    }
                }
            }
        }

        arsort($pontuacoes);
        $count = 1;
        $count_total = count($pontuacoes);

        foreach ($pontuacoes as $pontuacao) {
            if ($count < $count_total) {
                if ($count > 1) {
                    $valor += $pontuacao;
                }

            }
            $count++;
        }

        return number_format($valor, 2, '.', '');
    }

    // CÓDIGO: G8
    // NOME DO CRITÉRIO: PONTUAÇÃO MÉDIA (CONSIDERANDO O NÚMERO TOTAL DE ETAPAS)
    public function generate_g8($grupo_evento, $enxadrista, $categoria)
    {
        $valor = 0;
        $pontuacoes = array();

        $pontuacao_total = $this->generate_g5($grupo_evento, $enxadrista, $categoria);
        $total_etapas = $grupo_evento->eventos()->where([["classificavel","=",true]])->count();

        if ($total_etapas == 0) {
            return number_format(0, 2, '.', '');
        }

        return number_format($pontuacao_total / $total_etapas, 2, '.', '');
    }

    // CÓDIGO: G9
    // NOME DO CRITÉRIO: PONTUAÇÃO MÉDIA (CONSIDERANDO O NÚMERO DE ETAPAS QUE PARTICIPOU)
    public function generate_g9($grupo_evento, $enxadrista, $categoria)
    {
        $valor = 0;
        $pontuacoes = array();

        $pontuacao_total = $this->generate_g5($grupo_evento, $enxadrista, $categoria);
        $total_etapas_participadas = $grupo_evento->eventos()->whereHas("torneios", function ($q1) use ($enxadrista, $categoria) {
            $q1->whereHas("inscricoes", function ($q2) use ($enxadrista, $categoria) {
                $q2->where([
                    ["enxadrista_id", "=", $enxadrista->id],
                    ["confirmado", "=", true],
                    ["desconsiderar_pontuacao_geral", "=", false],
                ]);
                if ($categoria !== null) {
                    $q2->where("categoria_id", "=", $categoria->id);
                }
            });
        })->count();

        if ($total_etapas_participadas == 0) {
            return number_format(0, 2, '.', '');
        }

        return number_format($pontuacao_total / $total_etapas_participadas, 2, '.', '');
    }

    // CÓDIGO: G10
    // NOME DO CRITÉRIO: MANUAL
    public function generate_g10($grupo_evento, $enxadrista)
    {
        return 0.00;
    }

    public function getEtapasValidas($grupo_evento, $categoria)
    {
        if ($categoria === null) {
            return $grupo_evento->eventos()->where([["classificavel", "=", true]])->orderBy('data_inicio', 'asc')->get()->all();
        }

        $etapas = [];
        $eventos = $grupo_evento->eventos()->orderBy('data_inicio', 'asc')->get();
        foreach ($eventos as $evento) {
            $tem_categoria = false;
            
            // Verifica pela relação direta com a categoria
            foreach ($evento->categorias as $categoria_evento) {
                if ($categoria_evento->categoria_id == $categoria->id) {
                    $tem_categoria = true;
                    break;
                }
            }
            
            // Verifica pelos torneios (se a primeira checagem não encontrou)
            if (!$tem_categoria) {
                if ($evento->getTorneioByCategoria($categoria->id)) {
                    $tem_categoria = true;
                }
            }

            if ($tem_categoria) {
                $etapas[] = $evento;
            }
        }
        return $etapas;
    }

    public function generate_g_etapa_posicao($grupo_evento, $enxadrista, $categoria, $etapa_index)
    {
        $etapas = $this->getEtapasValidas($grupo_evento, $categoria);
        if (count($etapas) > $etapa_index) {
            $evento = $etapas[$etapa_index];
            foreach ($this->getInscricoesEnxadristaEvento($evento, $enxadrista, $categoria) as $inscricao) {
                if ($this->inscricaoMatchesCategoria($inscricao, $categoria)) {
                    $posicao = $this->getPosicaoInscricaoDesempate($inscricao, $categoria);
                    if ($inscricao->isPresent() && $posicao > 0) {
                        return $posicao;
                    }
                }
            }
        }
        return null;
    }

    // CÓDIGO: G11
    // NOME DO CRITÉRIO: MELHOR POSIÇÃO NA PRIMEIRA ETAPA
    public function generate_g11($grupo_evento, $enxadrista, $categoria)
    {
        return $this->generate_g_etapa_posicao($grupo_evento, $enxadrista, $categoria, 0);
    }

    // CÓDIGO: G12
    // NOME DO CRITÉRIO: MELHOR POSIÇÃO NA SEGUNDA ETAPA
    public function generate_g12($grupo_evento, $enxadrista, $categoria)
    {
        return $this->generate_g_etapa_posicao($grupo_evento, $enxadrista, $categoria, 1);
    }

    // CÓDIGO: G13
    // NOME DO CRITÉRIO: MELHOR POSIÇÃO NA TERCEIRA ETAPA
    public function generate_g13($grupo_evento, $enxadrista, $categoria)
    {
        return $this->generate_g_etapa_posicao($grupo_evento, $enxadrista, $categoria, 2);
    }

    // CÓDIGO: G14
    // NOME DO CRITÉRIO: MELHOR POSIÇÃO NA QUARTA ETAPA
    public function generate_g14($grupo_evento, $enxadrista, $categoria)
    {
        return $this->generate_g_etapa_posicao($grupo_evento, $enxadrista, $categoria, 3);
    }

    // CÓDIGO: G15
    // NOME DO CRITÉRIO: MELHOR POSIÇÃO NA QUINTA ETAPA
    public function generate_g15($grupo_evento, $enxadrista, $categoria)
    {
        return $this->generate_g_etapa_posicao($grupo_evento, $enxadrista, $categoria, 4);
    }

    // CÓDIGO: G16
    // NOME DO CRITÉRIO: QUANTIDADE DE ETAPAS PARTICIPANTES
    public function generate_g16($grupo_evento, $enxadrista, $categoria)
    {
        $valor = 0;
        $etapas = $this->getEtapasValidas($grupo_evento, $categoria);
        foreach ($etapas as $evento) {
            foreach ($this->getInscricoesEnxadristaEvento($evento, $enxadrista, $categoria) as $inscricao) {
                if ($this->inscricaoMatchesCategoria($inscricao, $categoria)) {
                    if ($inscricao->isPresent()) {
                        $valor++;
                    }
                }
            }
        }
        return $valor;
    }
}

<?php

namespace App\Http\Controllers;

use App\Categoria;
use App\CategoriaSexo;
use App\Enxadrista;
use App\EnxadristaCriterioDesempateGeral;
use App\Evento;
use App\GrupoEvento;
use App\Inscricao;
use App\Pontuacao;
use App\PontuacaoEnxadrista;
use App\Sexo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Log;

class CategoriaController extends Controller
{
    public function __construct()
    {
        return $this->middleware("auth");
    }

    public static function classificar($evento_id, $categoria_id)
    {
        $evento = Evento::find($evento_id);


        $categoria = Categoria::find($categoria_id);
        echo '<br/><br/> Categoria: ' . $categoria->name;
        $inscritos = array();
        $inscricoes_count = Inscricao::where([
            ["categoria_id", "=", $categoria->id],
        ])
            ->whereHas("torneio", function ($q1) use ($evento) {
                $q1->where([
                    ["evento_id", "=", $evento->id],
                ]);
            })
            ->orderBy("pontos", "DESC")
            ->count();

            $inscricoes = Inscricao::where([
            ["categoria_id", "=", $categoria->id],
        ])
            ->whereHas("torneio", function ($q1) use ($evento) {
                $q1->where([
                    ["evento_id", "=", $evento->id],
                ]);
            })
            ->orderBy("pontos", "DESC")
            ->get();
        echo $inscricoes_count;
        foreach ($inscricoes as $inscricao) {
            if ($inscricao->pontos != null && $inscricao->confirmado) {
                $inscritos[] = $inscricao;
            }
        }
        usort($inscritos, array("\App\Http\Controllers\CategoriaController", "sort_classificacao_etapa"));
        $i = 1;
        $j = 1;
        foreach ($inscritos as $inscricao) {
            Log::debug("Posição ".$i.": ".$inscricao->id);
            $inscricao->posicao = $i;
            // echo $i;
            if (!$inscricao->is_desclassificado) {
                if (!$inscricao->desconsiderar_pontuacao_geral) {
                    $inscricao->posicao_geral = $j;
                    if ($evento->grupo_evento->e_pontuacao_resultado_para_geral) {
                        $inscricao->pontos_geral = $inscricao->pontos;
                    } else {
                        $inscricao->pontos_geral = Pontuacao::getPontuacaoByEvento($evento->id, $j);
                    }
                    $j++;
                } else {
                    $inscricao->pontos_geral = null;
                    $inscricao->posicao_geral = null;
                }
            } else {
                $inscricao->pontos_geral = null;
                $inscricao->posicao = null;
                $inscricao->posicao_geral = null;
            }
            $inscricao->save();
            $i++;
        }
    }

    public static function sort_classificacao_etapa($inscrito_a, $inscrito_b)
    {
        if ($inscrito_a->pontos > $inscrito_b->pontos) {
            return -1;
        } elseif ($inscrito_a->pontos < $inscrito_b->pontos) {
            return 1;
        } elseif (!$inscrito_a->desconsiderar_pontuacao_geral && $inscrito_b->desconsiderar_pontuacao_geral) {
            return -1;
        } elseif ($inscrito_a->desconsiderar_pontuacao_geral && !$inscrito_b->desconsiderar_pontuacao_geral) {
            return 1;
        } else {
            $criterios = $inscrito_a->torneio->getCriteriosTotal();



            echo "[" . count((array) $criterios) . "]";
            foreach ($criterios as $criterio) {
                $desempate = $criterio->criterio->sort_desempate($inscrito_a, $inscrito_b);
                if ($desempate != 0) {
                    echo $criterio->criterio->name. " - Res(".$desempate.")---<br/>";
                    return $desempate;
                }
            }
            return strnatcmp($inscrito_a->enxadrista->getName(), $inscrito_b->enxadrista->getName());
        }
    }

    public static function classificar_geral($grupo_evento_id, $categoria_id)
    {
        $retornos = array();
        $grupo_evento = GrupoEvento::find($grupo_evento_id);
        $categoria = Categoria::find($categoria_id);
        $retornos[] = date("d/m/Y H:i:s") . " - Início do Processamento para a Categoria de #" . $categoria->id . " - '" . $categoria->name . "' do Grupo de Evento '" . $grupo_evento->name . "'";
        $retornos[] = "<hr/>";
        $inscritos = array();

        $retornos = array_merge($retornos, CategoriaController::somar_pontos_geral($grupo_evento, $categoria));
        $retornos[] = "<hr/>";
        $retornos = array_merge($retornos, CategoriaController::gerar_criterios_desempate($grupo_evento, $categoria));
        $retornos[] = "<hr/>";
        $retornos = array_merge($retornos, CategoriaController::classificar_enxadristas_geral($grupo_evento, $categoria));
        $retornos[] = "<hr/>";
        $retornos[] = date("d/m/Y H:i:s") . " - Fim do Processamento para a Categoria de #" . $categoria->id . " - '" . $categoria->name . "' do Grupo de Evento '" . $grupo_evento->name . "'";
        $retornos[] = "<hr/>";
        $retornos[] = "<hr/>";
        $retornos[] = "<hr/>";

        return $retornos;
    }

    public static function somar_pontos_geral($grupo_evento, $categoria)
    {
        $retornos = array();
        $retornos[] = date("d/m/Y H:i:s") . " - Função de Soma de Pontos";
        $retornos[] = date("d/m/Y H:i:s") . " - Zerando pontuações existentes";
        foreach (PontuacaoEnxadrista::where([
            ["grupo_evento_id", "=", $grupo_evento->id],
            ["categoria_id", "=", $categoria->id],
        ])->get() as $pontuacao) {
            // $pontuacao->pontos = 0;
            // $pontuacao->inscricoes_calculadas = 0;
            // $pontuacao->save();
            $pontuacao->delete();
        }
        $retornos[] = date("d/m/Y H:i:s") . " - Listando Eventos do Grupo de Evento";
        $retornos[] = "<hr/>";
        foreach ($grupo_evento->eventos()->where([["classificavel", "=", true]])->get() as $evento) {
            $retornos[] = date("d/m/Y H:i:s") . " - Evento: " . $evento->name;

            $inscricoes_count = Inscricao::where([
                ["categoria_id", "=", $categoria->id],
                ["pontos_geral", "!=", null],
                ["pontos_geral", ">", 0],
            ])
            ->whereHas("torneio", function ($q1) use ($evento) {
                $q1->where([
                    ["evento_id", "=", $evento->id],
                ]);
            })
            ->orderBy("pontos", "DESC")
            ->count();

            $inscricoes = Inscricao::where([
                ["categoria_id", "=", $categoria->id],
                ["pontos_geral", "!=", null],
                ["pontos_geral", ">", 0],
            ])
            ->whereHas("torneio", function ($q1) use ($evento) {
                $q1->where([
                    ["evento_id", "=", $evento->id],
                ]);
            })
            ->orderBy("pontos", "DESC")
            ->get();

            $retornos[] = date("d/m/Y H:i:s") . " - Total de inscrições encontradas: " . $inscricoes_count;
            foreach ($inscricoes as $inscricao) {
                $retornos[] = date("d/m/Y H:i:s") . " - Inscrição #: " . $inscricao->id . " - Enxadrista: " . $inscricao->enxadrista->name;
                $pontos_geral = PontuacaoEnxadrista::where([
                    ["enxadrista_id", "=", $inscricao->enxadrista->id],
                    ["grupo_evento_id", "=", $inscricao->torneio->evento->grupo_evento->id],
                    ["categoria_id", "=", $inscricao->categoria->id],
                ])->first();
                if (!$pontos_geral) {
                    $pontos_geral = new PontuacaoEnxadrista;
                    $pontos_geral->enxadrista_id = $inscricao->enxadrista->id;
                    $pontos_geral->grupo_evento_id = $inscricao->torneio->evento->grupo_evento->id;
                    $pontos_geral->categoria_id = $inscricao->categoria->id;
                    $pontos_geral->pontos = 0;
                }

                if ($grupo_evento->limite_calculo_geral) {
                    $retornos[] = date("d/m/Y H:i:s") . " - Limite do Grupo de Evento: " . $grupo_evento->limite_calculo_geral;
                    $retornos[] = date("d/m/Y H:i:s") . " - Quantidade de inscrições calculadas já deste enxadrista: " . $pontos_geral->inscricoes_calculadas;
                    $retornos[] = date("d/m/Y H:i:s") . " - Pontos desta inscrição: " . $inscricao->pontos_geral;
                    if ($grupo_evento->limite_calculo_geral > $pontos_geral->inscricoes_calculadas) {
                        $pontos_geral->pontos += $inscricao->pontos_geral;
                        $pontos_geral->inscricoes_calculadas++;
                    }
                } else {
                    $pontos_geral->pontos += $inscricao->pontos_geral;
                    $pontos_geral->inscricoes_calculadas++;
                }
                $pontos_geral->save();
                $retornos[] = "<hr/>";
            }
        }
        $retornos[] = date("d/m/Y H:i:s") . " - Finalizada a Função de Soma de Pontos";
        return $retornos;
    }

    public static function gerar_criterios_desempate($grupo_evento, $categoria)
    {
        $retornos = array();
        $retornos[] = date("d/m/Y H:i:s") . " - Função de geração de Critérios de Desempate";
        $criterios = $grupo_evento->getCriteriosDesempateGerais();
        $enxadristas = Enxadrista::getComInscricaoConfirmada($grupo_evento->id, $categoria->id);

        $gerador = new CriterioDesempateGeralController;
        foreach ($enxadristas as $enxadrista) {

            $retornos[] = date("d/m/Y H:i:s") . " - Gerando critérios para o enxadrista #" . $enxadrista->id . " - " . $enxadrista->name;
            foreach ($criterios as $criterio) {
                $enxadrista_criterio = EnxadristaCriterioDesempateGeral::where([
                    ["enxadrista_id", "=", $enxadrista->id],
                    ["grupo_evento_id", "=", $grupo_evento->id],
                    ["categoria_id", "=", $categoria->id],
                    ["criterio_desempate_id", "=", $criterio->criterio->id],
                ])->first();
                if (!$enxadrista_criterio) {
                    $enxadrista_criterio = new EnxadristaCriterioDesempateGeral;
                    $enxadrista_criterio->enxadrista_id = $enxadrista->id;
                    $enxadrista_criterio->grupo_evento_id = $grupo_evento->id;
                    $enxadrista_criterio->categoria_id = $categoria->id;
                    $enxadrista_criterio->criterio_desempate_id = $criterio->criterio->id;
                }
                $enxadrista_criterio->valor = $gerador->generate($grupo_evento, $enxadrista, $criterio->criterio);
                $enxadrista_criterio->save();
                $retornos[] = date("d/m/Y H:i:s") . " - Critério: " . $criterio->criterio->name . " - Valor: " . $enxadrista_criterio->valor;
            }
        }
        $retornos[] = date("d/m/Y H:i:s") . " - Fim da Função de geração de Critérios de Desempate";
        return $retornos;
    }

    public static function classificar_enxadristas_geral($grupo_evento, $categoria)
    {
        $retornos = array();
        $retornos[] = date("d/m/Y H:i:s") . " - Função de classificação dos enxadristas";
        $criterios = $grupo_evento->getCriteriosDesempateGerais();
        $retornos[] = date("d/m/Y H:i:s") . " - Listando as pontuações dos enxadristas que possuem alguma pontuação";
        $pontuacoes_enxadristas = PontuacaoEnxadrista::where([
            ["categoria_id", "=", $categoria->id],
            ["grupo_evento_id", "=", $grupo_evento->id],
        ])->get();

        $pontuacoes = array();
        foreach ($pontuacoes_enxadristas as $pontuacao) {
            if ($pontuacao->pontos > 0) {
                $pontuacoes[] = $pontuacao;
            }
        }
        usort($pontuacoes, array("\App\Http\Controllers\CategoriaController", "sort_classificacao_geral"));
        $i = 1;
        foreach ($pontuacoes as $pontuacao) {
            $retornos[] = date("d/m/Y H:i:s") . " - Enxadrista: " . $pontuacao->enxadrista->name . " - Posição: " . $i;
            $pontuacao->posicao = $i;
            $pontuacao->save();
            $i++;
        }
        $retornos[] = date("d/m/Y H:i:s") . " - Fim da Função de classificação dos enxadristas";
        return $retornos;
    }

    public static function sort_classificacao_geral($pontuacao_a, $pontuacao_b)
    {
        if ($pontuacao_a->pontos > $pontuacao_b->pontos) {
            return -1;
        } elseif ($pontuacao_a->pontos < $pontuacao_b->pontos) {
            return 1;
        } else {
            $criterios = $pontuacao_a->grupo_evento->getCriteriosGerais();
            foreach ($criterios as $criterio) {
                $desempate = $criterio->criterio->sort_desempate_geral($pontuacao_a, $pontuacao_b);
                if ($desempate != 0) {
                    // echo $criterio->criterio->name;
                    return $desempate;
                }
            }
            return strnatcmp($pontuacao_a->enxadrista->getName(), $pontuacao_b->enxadrista->getName());
        }
    }
}

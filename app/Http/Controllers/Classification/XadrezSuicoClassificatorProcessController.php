<?php

namespace App\Http\Controllers\Classification;

use App\Enum\ClassificationType;
use App\Enum\ClassificationTypeRule;
use App\Enum\ConfigType;
use App\Enum\EmailType;
use App\Evento;
use App\Http\Controllers\Controller;
use App\Http\Controllers\EmailController;
use App\Inscricao;
use App\MovimentacaoRating;
use App\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;

class XadrezSuicoClassificatorProcessController extends Controller
{
    public function __construct()
    {
        return $this->middleware("auth");
    }

    private $log = array();

    public function process($event_id, $event_classificates_id)
    {
        $user = Auth::user();
        $evento = Evento::find($event_id);

        if ($evento->event_classificates()->where([["id", "=", $event_classificates_id]])->count() == 0) {
            return redirect()->back();
        }
        $event_classificates = $evento->event_classificates()->where([["id", "=", $event_classificates_id]])->first();

        if (
            (
                !$user->hasPermissionGlobal() &&
                !$user->hasPermissionEventByPerfil($evento->id, [3, 4]) &&
                !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [6, 7])
            )
            ||
            !$user->hasPermissionEventByPerfil($event_classificates->event->id, [14, 15])
        ) {
            return redirect("/");
        }

        foreach (ClassificationTypeRule::getProcessOrder() as $type_to_process) {
            $this->log[] = "----- ----- ---- ----";
            $this->log[] = "----- ----- ---- ----";
            $this->log[] = "----- ----- ---- ----";
            $this->log[] = "----- ----- ---- ----";
            $this->log[] = "----- ----- ---- ----";
            $this->log[] = "----- ----- ---- ----";

            switch ($type_to_process) {
                case ClassificationTypeRule::POSITION:
                    $this->process_position($evento, $event_classificates);
                    break;
                case ClassificationTypeRule::POSITION_ABSOLUTE:
                    $this->process_position_absolute($evento, $event_classificates);
                    break;
                case ClassificationTypeRule::PRE_CLASSIFICATE:
                    $this->process_pre_classificate($evento, $event_classificates);
                    break;
                case ClassificationTypeRule::PLACE_BY_QUANTITY:
                    $this->process_place_by_quantity($evento, $event_classificates);
                    break;
            }
        }


        EmailController::schedule(
            $user->email,
            ["log" => $this->log, "classificator" => $event_classificates, "user" => $user],
            EmailType::XadrezSuicoClassificadorProcessamento
        );

        $messageBag = new MessageBag;
        $messageBag->add("type", "success");
        $messageBag->add("alerta", "O classificador foi executado com sucesso! Um e-mail será encaminhado com os detalhes.");

        return redirect("/evento/dashboard/{$evento->id}?tab=classificator")->withErrors($messageBag);
    }
    public function delete_classified($event_id, $event_classificates_id)
    {
        $user = Auth::user();
        $evento = Evento::find($event_id);

        if ($evento->event_classificates()->where([["id", "=", $event_classificates_id]])->count() == 0) {
            return redirect()->back();
        }
        $event_classificates = $evento->event_classificates()->where([["id", "=", $event_classificates_id]])->first();

        if (
            (
                !$user->hasPermissionGlobal() &&
                !$user->hasPermissionEventByPerfil($evento->id, [3, 4]) &&
                !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [6, 7])
            )
            ||
            !$user->hasPermissionEventByPerfil($event_classificates->event->id, [14, 15])
        ) {
            return redirect("/");
        }

        $count = 0;
        foreach(Inscricao::whereHas("configs",function($q1) use ($event_id,$event_classificates_id){
            $q1->where([
                ["key", "=", "event_classificator_id"],
                ["integer", "=", $event_id],
            ]);
        })->get() as $inscricao){
            if ($inscricao->isDeletavel()) {
                foreach ($inscricao->opcoes->all() as $campo) {
                    $campo->delete();
                }
                foreach ($inscricao->criterios_desempate->all() as $criterios) {
                    $criterios->delete();
                }
                foreach ($inscricao->configs->all() as $config) {
                    $config->delete();
                }
                $inscricao->delete();

                $count++;
            }
        }

        $messageBag = new MessageBag;
        $messageBag->add("type", "success");
        $messageBag->add("alerta", "Os classificados foram removidos com sucesso! Total: {$count}");

        return redirect("/evento/dashboard/{$evento->id}?tab=classificator")->withErrors($messageBag);
    }

    public function process_position($event, $xzsuic_classificator)
    {
        $type = ClassificationTypeRule::POSITION;
        $classificator_type = ClassificationTypeRule::get($type);
        $this->log[] = date("d/m/Y H:i:s") . " - Início do Processo - Tipo de Classificador: " . $classificator_type["name"];

        $type_count = $xzsuic_classificator->rules()->where([["type", "=", $type]])->count();

        if ($type_count == 0) {
            $this->log[] = date("d/m/Y H:i:s") . " - Não há regras para processar deste tipo.";
        } else {
            $this->log[] = date("d/m/Y H:i:s") . " - Há {$type_count} regra(s) para processar deste tipo.";
        }

        foreach ($xzsuic_classificator->rules()->where([["type", "=", $type]])->orderBy("id", "ASC")->get() as $rule) {
            $this->log[] = date("d/m/Y H:i:s") . " - Regra #{$rule->id} - Posição Inicial: {$rule->value}";

            foreach ($xzsuic_classificator->event_classificator->categorias()->whereHas("categoria", function ($q1) use ($xzsuic_classificator) {
                $q1->whereHas("event_classificates", function ($q2) use ($xzsuic_classificator) {
                    $q2->whereHas("category", function ($q3) use ($xzsuic_classificator) {
                        $q3->whereHas("eventos", function ($q4) use ($xzsuic_classificator) {
                            $q4->where([["evento_id", "=", $xzsuic_classificator->event->id]]);
                        });
                    });
                });
            })->get() as $category_event_classificator) {
                $category_classificator = $category_event_classificator->categoria;
                $category = $category_classificator->event_classificates()->whereHas("category", function ($q2) use ($xzsuic_classificator) {
                    $q2->whereHas("eventos", function ($q3) use ($xzsuic_classificator) {
                        $q3->where([["evento_id", "=", $xzsuic_classificator->event->id]]);
                    });
                })->first()->category;

                $this->log[] = date("d/m/Y H:i:s") . " - INÍCIO PROCESSAMENTO CATEGORIA.";
                $this->log[] = date("d/m/Y H:i:s") . " - Categoria Classificadora: #{$category_classificator->id} - {$category_classificator->name}.";
                $this->log[] = date("d/m/Y H:i:s") . " - Categoria Destino: #{$category->id} - {$category->name}.";

                $classification_found = false;

                $tournament_classificators_before_this = array();

                foreach ($xzsuic_classificator->event->event_classificators()->whereHas("event_classificator", function ($q1) use ($xzsuic_classificator) {
                    $q1->where([["data_inicio", "<=", $xzsuic_classificator->event_classificator->data_inicio]]);
                })->get() as $event_classificator) {
                    $tournament_classificators_before_this[] = $event_classificator->event_classificator->id;
                }
                $this->log[] = date("d/m/Y H:i:s") . " - Torneios anteriores a este: " . implode(",", $tournament_classificators_before_this);

                foreach ($category_classificator->inscricoes()->whereHas("torneio", function ($q1) use ($xzsuic_classificator) {
                    $q1->where([["evento_id", "=", $xzsuic_classificator->event_classificator->id]]);
                })
                    ->where([
                        ["confirmado", "=", true],
                        ["desconsiderar_pontuacao_geral", "=", false],
                        ["posicao", ">=", $rule->value],
                    ])
                    ->whereNotNull("posicao")
                    ->orderBy("posicao", "ASC")
                    ->get() as $classificacao) {
                    if (!$classification_found) {
                        $this->log[] = date("d/m/Y H:i:s") . " - Posição: #{$classificacao->posicao} - Enxadrista #{$classificacao->enxadrista->id} - {$classificacao->enxadrista->name}.";
                        if (
                            Inscricao::whereHas("torneio", function ($q1) use ($xzsuic_classificator) {
                                $q1->whereHas("evento", function ($q2) use ($xzsuic_classificator) {
                                    $q2->where([["id", "=", $xzsuic_classificator->event->id]]);
                                });
                            })
                            ->where(function($q0) use ($xzsuic_classificator, $tournament_classificators_before_this){
                                $q0->whereHas("configs", function ($q1) use ($xzsuic_classificator, $tournament_classificators_before_this) {
                                    $q1->where([
                                        ["key", "=", "event_classificator_id"],
                                    ]);
                                    $q1->whereIn("integer", $tournament_classificators_before_this);
                                })
                                ->orWhereHas("configs", function ($q1) use ($xzsuic_classificator, $tournament_classificators_before_this) {
                                    $q1->where([
                                        ["key", "=", "classificated_manually"],
                                        ["boolean", "=", true],
                                    ]);
                                });
                            })
                            ->where([
                                ["enxadrista_id", "=", $classificacao->enxadrista_id]
                            ])
                            ->count() == 0
                        ) {
                            if (
                                Inscricao::whereHas("torneio", function ($q1) use ($xzsuic_classificator,$rule) {
                                    $q1->where([["evento_id", "=", $xzsuic_classificator->event->id]]);
                                })
                                ->whereHas("configs", function ($q1) use ($xzsuic_classificator) {
                                    $q1->where([
                                        ["key", "=", "event_classificator_id"],
                                        ["integer", "=", $xzsuic_classificator->event_classificator->id]
                                    ]);
                                })
                                ->whereHas("configs", function ($q1) use ($xzsuic_classificator,$rule) {
                                    $q1->where([
                                        ["key", "=", "event_classificator_rule_id"],
                                        ["integer", "=", $rule->id],
                                    ]);
                                })
                                ->where([
                                    ["enxadrista_id", "=", $classificacao->enxadrista_id]
                                ])
                                ->count() > 0
                            ) {
                                $this->log[] = date("d/m/Y H:i:s") . " - Já foi classificado por este mesmo evento.";
                                $classification_found = true;
                            } else {
                                $this->log[] = date("d/m/Y H:i:s") . " - Ainda não foi classificado.";

                                if (
                                    Inscricao::whereHas("torneio", function ($q1) use ($xzsuic_classificator) {
                                        $q1->whereHas("evento", function ($q2) use ($xzsuic_classificator) {
                                            $q2->where([["id", "=", $xzsuic_classificator->event->id]]);
                                        });
                                    })
                                    ->whereDoesntHave("configs", function ($q1) use ($xzsuic_classificator, $tournament_classificators_before_this) {
                                        $q1->where([
                                            ["key", "=", "event_classificator_id"],
                                        ]);
                                    })
                                    ->whereDoesntHave("configs", function ($q1) use ($xzsuic_classificator, $tournament_classificators_before_this) {
                                        $q1->where([
                                            ["key", "=", "classificated_manually"],
                                            ["boolean", "=", true],
                                        ]);
                                    })
                                    ->where([
                                        ["enxadrista_id", "=", $classificacao->enxadrista_id]
                                    ])
                                    ->count() == 0
                                ) {

                                    if ($category->idade_minima) {
                                        if ($classificacao->enxadrista->howOldForEvento($xzsuic_classificator->event->id) < $category->idade_minima) {
                                            $this->log[] = date("d/m/Y H:i:s") . " - Categoria inválida - Obtendo nova categoria.";
                                            $category = $this->getNewCategory($xzsuic_classificator->event, $classificacao->enxadrista);
                                            if ($category) {
                                                $this->log[] = date("d/m/Y H:i:s") . " - Nova Categoria Encontrada - #{$categoria->id} - {$categoria->name}.";
                                            } else {
                                                $this->log[] = date("d/m/Y H:i:s") . " - Não há categoria para classificar este enxadrista - Ignorando.";
                                                continue;
                                            }
                                        }
                                    }
                                    if ($category->idade_maxima) {
                                        if ($classificacao->enxadrista->howOldForEvento($xzsuic_classificator->event->id) > $category->idade_maxima) {
                                            $this->log[] = date("d/m/Y H:i:s") . " - Categoria inválida - Obtendo nova categoria.";
                                            $category = $this->getNewCategory($xzsuic_classificator->event, $classificacao->enxadrista);
                                            if ($category) {
                                                $this->log[] = date("d/m/Y H:i:s") . " - Nova Categoria Encontrada - #{$categoria->id} - {$categoria->name}.";
                                            } else {
                                                $this->log[] = date("d/m/Y H:i:s") . " - Não há categoria para classificar este enxadrista - Ignorando.";
                                                continue;
                                            }
                                        }
                                    }

                                    if ($xzsuic_classificator->event->torneios()->whereHas("categorias", function ($q1) use ($category) {
                                        $q1->where([["categoria_id", "=", $category->id]]);
                                    })->count() > 0) {
                                        $torneio = $xzsuic_classificator->event->torneios()->whereHas("categorias", function ($q1) use ($category) {
                                            $q1->where([["categoria_id", "=", $category->id]]);
                                        })->first();

                                        $inscricao_nova = new Inscricao;
                                        $inscricao_nova->enxadrista_id = $classificacao->enxadrista_id;
                                        $inscricao_nova->categoria_id = $category->id;
                                        $inscricao_nova->cidade_id = $classificacao->cidade_id;
                                        $inscricao_nova->clube_id = $classificacao->clube_id;
                                        $inscricao_nova->torneio_id = $torneio->id;
                                        $inscricao_nova->regulamento_aceito = $classificacao->regulamento_aceito;
                                        $inscricao_nova->confirmado = false;
                                        $inscricao_nova->xadrezsuico_aceito = $classificacao->xadrezsuico_aceito;
                                        $inscricao_nova->is_aceito_imagem = $classificacao->xadrezsuico_aceito;
                                        $inscricao_nova->inscricao_from = $classificacao->id;
                                        $inscricao_nova->save();

                                        $inscricao_nova->setConfig("event_classificator_id", ConfigType::Integer, $xzsuic_classificator->event_classificator->id);
                                        $inscricao_nova->setConfig("event_classificator_rule_id", ConfigType::Integer, $rule->id);

                                        if ($xzsuic_classificator->event->tipo_rating) {
                                            $rating_count = $inscricao_nova->enxadrista->ratings()->where([
                                                ["tipo_ratings_id", "=", $xzsuic_classificator->event->tipo_rating->id]
                                            ])->count();
                                            if ($rating_count == 0) {
                                                $rating_inicial = $inscricao_nova->enxadrista->ratingParaEvento($xzsuic_classificator->event->id);

                                                $rating = new Rating;
                                                $rating->tipo_ratings_id = $xzsuic_classificator->event->tipo_rating->tipo_rating->id;
                                                $rating->enxadrista_id = $inscricao_nova->enxadrista->id;
                                                $rating->valor = $rating_inicial;
                                                $rating->save();

                                                $movimentacao = new MovimentacaoRating;
                                                $movimentacao->tipo_ratings_id = $rating->tipo_ratings_id;
                                                $movimentacao->ratings_id = $rating->id;
                                                $movimentacao->valor = $rating_inicial;
                                                $movimentacao->is_inicial = true;
                                                $movimentacao->save();
                                            }
                                        }

                                        if ($inscricao_nova->enxadrista->email) {
                                            if ($xzsuic_classificator->event->isPaid() && $inscricao_nova->getPaymentInfo("link")) {
                                                EmailController::schedule(
                                                    $inscricao_nova->enxadrista->email,
                                                    $inscricao_nova,
                                                    EmailType::InscricaoRecebidaPagamentoPendente,
                                                    $inscricao_nova->enxadrista
                                                );
                                            } else {
                                                EmailController::schedule(
                                                    $inscricao_nova->enxadrista->email,
                                                    $inscricao_nova,
                                                    EmailType::ConfirmacaoInscricao,
                                                    $inscricao_nova->enxadrista
                                                );
                                            }
                                        }

                                        $classification_found = true;
                                    } else {
                                        $this->log[] = date("d/m/Y H:i:s") . " - ERRO!!! Categoria não possui torneio!";
                                    }
                                } else {
                                    $this->log[] = date("d/m/Y H:i:s") . " - O mesmo já encontra inscrito pro evento - Apenas homologar classificação.";
                                    $inscricao_on_event = Inscricao::whereHas("torneio", function ($q1) use ($xzsuic_classificator) {
                                        $q1->whereHas("evento", function ($q2) use ($xzsuic_classificator) {
                                            $q2->where([["id", "=", $xzsuic_classificator->event->id]]);
                                        });
                                    })
                                        ->whereDoesntHave("configs", function ($q1) use ($xzsuic_classificator, $tournament_classificators_before_this) {
                                            $q1->where([
                                                ["key", "=", "event_classificator_id"],
                                            ]);
                                        })
                                        ->whereDoesntHave("configs", function ($q1) use ($xzsuic_classificator, $tournament_classificators_before_this) {
                                            $q1->where([
                                                ["key", "=", "classificated_manually"],
                                                ["boolean", "=", true],
                                            ]);
                                        })->first();

                                    $inscricao_on_event->setConfig("event_classificator_id", ConfigType::Integer, $xzsuic_classificator->event_classificator->id);
                                    $inscricao_on_event->setConfig("event_classificator_rule_id", ConfigType::Integer, $rule->id);
                                    $inscricao_on_event->inscricao_from = $classificacao->id;
                                    $inscricao_on_event->save();

                                    $classification_found = true;
                                }
                            }
                        } else {
                            $this->log[] = date("d/m/Y H:i:s") . " - Já foi classificado por outro evento ou manualmente.";
                        }
                    }
                }
                $this->log[] = date("d/m/Y H:i:s") . " - FIM PROCESSAMENTO CATEGORIA.";
            }
        }

        $this->log[] = date("d/m/Y H:i:s") . " - Fim do Processo - Tipo de Classificador: " . $classificator_type["name"];
    }
    public function process_position_absolute($event, $xzsuic_classificator)
    {
        $type = ClassificationTypeRule::POSITION_ABSOLUTE;
        $classificator_type = ClassificationTypeRule::get($type);
        $this->log[] = date("d/m/Y H:i:s") . " - Início do Processo - Tipo de Classificador: " . $classificator_type["name"];

        $type_count = $xzsuic_classificator->rules()->where([["type", "=", $type]])->count();

        if ($type_count == 0) {
            $this->log[] = date("d/m/Y H:i:s") . " - Não há regras para processar deste tipo.";
        } else {
            $this->log[] = date("d/m/Y H:i:s") . " - Há {$type_count} regra(s) para processar deste tipo.";
        }

        foreach ($xzsuic_classificator->rules()->where([["type", "=", $type]])->orderBy("id", "ASC")->get() as $rule) {
            $this->log[] = date("d/m/Y H:i:s") . " - Regra #{$rule->id} - Posição ABSOLUTA: {$rule->value}";

            foreach ($xzsuic_classificator->event_classificator->categorias()->whereHas("categoria", function ($q1) use ($xzsuic_classificator) {
                $q1->whereHas("event_classificates", function ($q2) use ($xzsuic_classificator) {
                    $q2->whereHas("category", function ($q3) use ($xzsuic_classificator) {
                        $q3->whereHas("eventos", function ($q4) use ($xzsuic_classificator) {
                            $q4->where([["evento_id", "=", $xzsuic_classificator->event->id]]);
                        });
                    });
                });
            })->get() as $category_event_classificator) {
                $category_classificator = $category_event_classificator->categoria;
                $category = $category_classificator->event_classificates()->whereHas("category", function ($q2) use ($xzsuic_classificator) {
                    $q2->whereHas("eventos", function ($q3) use ($xzsuic_classificator) {
                        $q3->where([["evento_id", "=", $xzsuic_classificator->event->id]]);
                    });
                })->first()->category;

                $this->log[] = date("d/m/Y H:i:s") . " - INÍCIO PROCESSAMENTO CATEGORIA.";
                $this->log[] = date("d/m/Y H:i:s") . " - Categoria Classificadora: #{$category_classificator->id} - {$category_classificator->name}.";
                $this->log[] = date("d/m/Y H:i:s") . " - Categoria Destino: #{$category->id} - {$category->name}.";

                $classification_found = false;

                $tournament_classificators_before_this = array();

                foreach ($xzsuic_classificator->event->event_classificators()->whereHas("event_classificator", function ($q1) use ($xzsuic_classificator) {
                    $q1->where([["data_inicio", "<=", $xzsuic_classificator->event_classificator->data_inicio]]);
                })->get() as $event_classificator) {
                    $tournament_classificators_before_this[] = $event_classificator->event_classificator->id;
                }
                $this->log[] = date("d/m/Y H:i:s") . " - Torneios anteriores a este: " . implode(",", $tournament_classificators_before_this);

                foreach ($category_classificator->inscricoes()->whereHas("torneio", function ($q1) use ($xzsuic_classificator) {
                    $q1->where([["evento_id", "=", $xzsuic_classificator->event_classificator->id]]);
                })
                    ->where([
                        ["confirmado", "=", true],
                        ["desconsiderar_pontuacao_geral", "=", false],
                        ["posicao", "=", $rule->value],
                    ])
                    ->whereNotNull("posicao")
                    ->orderBy("posicao", "ASC")
                    ->get() as $classificacao) {
                    if (!$classification_found) {
                        $this->log[] = date("d/m/Y H:i:s") . " - Posição: #{$classificacao->posicao} - Enxadrista #{$classificacao->enxadrista->id} - {$classificacao->enxadrista->name}.";
                        if (
                            Inscricao::whereHas("torneio", function ($q1) use ($xzsuic_classificator) {
                                $q1->whereHas("evento", function ($q2) use ($xzsuic_classificator) {
                                    $q2->where([["id", "=", $xzsuic_classificator->event->id]]);
                                });
                            })
                            ->where(function ($q0) use ($xzsuic_classificator, $tournament_classificators_before_this) {
                                $q0->whereHas("configs", function ($q1) use ($xzsuic_classificator, $tournament_classificators_before_this) {
                                    $q1->where([
                                        ["key", "=", "event_classificator_id"],
                                    ]);
                                    $q1->whereIn("integer", $tournament_classificators_before_this);
                                })
                                ->orWhereHas("configs", function ($q1) use ($xzsuic_classificator, $tournament_classificators_before_this) {
                                    $q1->where([
                                        ["key", "=", "classificated_manually"],
                                        ["boolean", "=", true],
                                    ]);
                                });
                            })
                            ->where([
                                ["enxadrista_id", "=", $classificacao->enxadrista_id]
                            ])
                            ->count() == 0
                        ) {
                            if (
                                Inscricao::whereHas("torneio", function ($q1) use ($xzsuic_classificator) {
                                    $q1->whereHas("evento", function ($q2) use ($xzsuic_classificator) {
                                        $q2->where([["id", "=", $xzsuic_classificator->event->id]]);
                                    });
                                })
                                ->whereHas("configs", function ($q1) use ($xzsuic_classificator, $tournament_classificators_before_this) {
                                    $q1->where([
                                        ["key", "=", "event_classificator_id"],
                                    ]);
                                    $q1->where([["integer", "=", $xzsuic_classificator->event_classificator->id]]);
                                })
                                ->whereHas("configs", function ($q1) use ($xzsuic_classificator, $tournament_classificators_before_this, $rule) {
                                    $q1->where([
                                        ["key", "=", "event_classificator_rule_id"],
                                    ]);
                                    $q1->where([["integer","=",$rule->id]]);
                                })
                                ->where([
                                    ["enxadrista_id", "=", $classificacao->enxadrista_id]
                                ])
                                ->count() > 0
                            ) {
                                $this->log[] = date("d/m/Y H:i:s") . " - Já foi classificado por este mesmo evento.";
                                $classification_found = true;
                            } else {
                                $this->log[] = date("d/m/Y H:i:s") . " - Ainda não foi classificado.";

                                if (
                                    Inscricao::whereHas("torneio", function ($q1) use ($xzsuic_classificator) {
                                        $q1->whereHas("evento", function ($q2) use ($xzsuic_classificator) {
                                            $q2->where([["id", "=", $xzsuic_classificator->event->id]]);
                                        });
                                    })
                                    ->whereDoesntHave("configs", function ($q1) use ($xzsuic_classificator, $tournament_classificators_before_this) {
                                        $q1->where([
                                            ["key", "=", "event_classificator_id"],
                                        ]);
                                    })
                                    ->whereDoesntHave("configs", function ($q1) use ($xzsuic_classificator, $tournament_classificators_before_this) {
                                        $q1->where([
                                            ["key", "=", "classificated_manually"],
                                            ["boolean", "=", true],
                                        ]);
                                    })
                                    ->where([
                                        ["enxadrista_id", "=", $classificacao->enxadrista_id]
                                    ])
                                    ->count() == 0
                                ) {

                                    if ($category->idade_minima) {
                                        if ($classificacao->enxadrista->howOldForEvento($xzsuic_classificator->event->id) < $category->idade_minima) {
                                            $this->log[] = date("d/m/Y H:i:s") . " - Categoria inválida - Obtendo nova categoria.";
                                            $category = $this->getNewCategory($xzsuic_classificator->event, $classificacao->enxadrista);
                                            if ($category) {
                                                $this->log[] = date("d/m/Y H:i:s") . " - Nova Categoria Encontrada - #{$categoria->id} - {$categoria->name}.";
                                            } else {
                                                $this->log[] = date("d/m/Y H:i:s") . " - Não há categoria para classificar este enxadrista - Ignorando.";
                                                continue;
                                            }
                                        }
                                    }
                                    if ($category->idade_maxima) {
                                        if ($classificacao->enxadrista->howOldForEvento($xzsuic_classificator->event->id) > $category->idade_maxima) {
                                            $this->log[] = date("d/m/Y H:i:s") . " - Categoria inválida - Obtendo nova categoria.";
                                            $category = $this->getNewCategory($xzsuic_classificator->event, $classificacao->enxadrista);
                                            if ($category) {
                                                $this->log[] = date("d/m/Y H:i:s") . " - Nova Categoria Encontrada - #{$categoria->id} - {$categoria->name}.";
                                            } else {
                                                $this->log[] = date("d/m/Y H:i:s") . " - Não há categoria para classificar este enxadrista - Ignorando.";
                                                continue;
                                            }
                                        }
                                    }

                                    if ($xzsuic_classificator->event->torneios()->whereHas("categorias", function ($q1) use ($category) {
                                        $q1->where([["categoria_id", "=", $category->id]]);
                                    })->count() > 0) {
                                        $torneio = $xzsuic_classificator->event->torneios()->whereHas("categorias", function ($q1) use ($category) {
                                            $q1->where([["categoria_id", "=", $category->id]]);
                                        })->first();

                                        $inscricao_nova = new Inscricao;
                                        $inscricao_nova->enxadrista_id = $classificacao->enxadrista_id;
                                        $inscricao_nova->categoria_id = $category->id;
                                        $inscricao_nova->cidade_id = $classificacao->cidade_id;
                                        $inscricao_nova->clube_id = $classificacao->clube_id;
                                        $inscricao_nova->torneio_id = $torneio->id;
                                        $inscricao_nova->regulamento_aceito = $classificacao->regulamento_aceito;
                                        $inscricao_nova->confirmado = false;
                                        $inscricao_nova->xadrezsuico_aceito = $classificacao->xadrezsuico_aceito;
                                        $inscricao_nova->is_aceito_imagem = $classificacao->xadrezsuico_aceito;
                                        $inscricao_nova->inscricao_from = $classificacao->id;
                                        $inscricao_nova->save();

                                        $inscricao_nova->setConfig("event_classificator_id", ConfigType::Integer, $xzsuic_classificator->event_classificator->id);
                                        $inscricao_nova->setConfig("event_classificator_rule_id", ConfigType::Integer, $rule->id);

                                        if ($xzsuic_classificator->event->tipo_rating) {
                                            $rating_count = $inscricao_nova->enxadrista->ratings()->where([
                                                ["tipo_ratings_id", "=", $xzsuic_classificator->event->tipo_rating->id]
                                            ])->count();
                                            if ($rating_count == 0) {
                                                $rating_inicial = $inscricao_nova->enxadrista->ratingParaEvento($xzsuic_classificator->event->id);

                                                $rating = new Rating;
                                                $rating->tipo_ratings_id = $xzsuic_classificator->event->tipo_rating->tipo_rating->id;
                                                $rating->enxadrista_id = $inscricao_nova->enxadrista->id;
                                                $rating->valor = $rating_inicial;
                                                $rating->save();

                                                $movimentacao = new MovimentacaoRating;
                                                $movimentacao->tipo_ratings_id = $rating->tipo_ratings_id;
                                                $movimentacao->ratings_id = $rating->id;
                                                $movimentacao->valor = $rating_inicial;
                                                $movimentacao->is_inicial = true;
                                                $movimentacao->save();
                                            }
                                        }

                                        if ($inscricao_nova->enxadrista->email) {
                                            if ($xzsuic_classificator->event->isPaid() && $inscricao_nova->getPaymentInfo("link")) {
                                                EmailController::schedule(
                                                    $inscricao_nova->enxadrista->email,
                                                    $inscricao_nova,
                                                    EmailType::InscricaoRecebidaPagamentoPendente,
                                                    $inscricao_nova->enxadrista
                                                );
                                            } else {
                                                EmailController::schedule(
                                                    $inscricao_nova->enxadrista->email,
                                                    $inscricao_nova,
                                                    EmailType::ConfirmacaoInscricao,
                                                    $inscricao_nova->enxadrista
                                                );
                                            }
                                        }

                                        $classification_found = true;
                                    } else {
                                        $this->log[] = date("d/m/Y H:i:s") . " - ERRO!!! Categoria não possui torneio!";
                                    }
                                } else {
                                    $this->log[] = date("d/m/Y H:i:s") . " - O mesmo já encontra inscrito pro evento - Apenas homologar classificação.";
                                    $inscricao_on_event = Inscricao::whereHas("torneio", function ($q1) use ($xzsuic_classificator) {
                                        $q1->whereHas("evento", function ($q2) use ($xzsuic_classificator) {
                                            $q2->where([["id", "=", $xzsuic_classificator->event->id]]);
                                        });
                                    })
                                        ->whereDoesntHave("configs", function ($q1) use ($xzsuic_classificator, $tournament_classificators_before_this) {
                                            $q1->where([
                                                ["key", "=", "event_classificator_id"],
                                            ]);
                                        })
                                        ->whereDoesntHave("configs", function ($q1) use ($xzsuic_classificator, $tournament_classificators_before_this) {
                                            $q1->where([
                                                ["key", "=", "classificated_manually"],
                                                ["boolean", "=", true],
                                            ]);
                                        })->first();

                                    $inscricao_on_event->setConfig("event_classificator_id", ConfigType::Integer, $xzsuic_classificator->event_classificator->id);
                                    $inscricao_on_event->setConfig("event_classificator_rule_id", ConfigType::Integer, $rule->id);
                                    $inscricao_on_event->inscricao_from = $classificacao->id;
                                    $inscricao_on_event->save();

                                    $classification_found = true;
                                }
                            }
                        } else {
                            $this->log[] = date("d/m/Y H:i:s") . " - Já foi classificado por outro evento ou manualmente.";
                        }
                    }
                }
                $this->log[] = date("d/m/Y H:i:s") . " - FIM PROCESSAMENTO CATEGORIA.";
            }
        }

        $this->log[] = date("d/m/Y H:i:s") . " - Fim do Processo - Tipo de Classificador: " . $classificator_type["name"];
    }

    public function process_pre_classificate($event, $xzsuic_classificator)
    {
        $type = ClassificationTypeRule::PRE_CLASSIFICATE;
        $classificator_type = ClassificationTypeRule::get($type);
        $this->log[] = date("d/m/Y H:i:s") . " - Início do Processo - Tipo de Classificador: " . $classificator_type["name"];

        $type_count = $xzsuic_classificator->rules()->where([["type", "=", $type]])->count();

        if ($type_count == 0) {
            $this->log[] = date("d/m/Y H:i:s") . " - Não há regras para processar deste tipo.";
        } else {
            $this->log[] = date("d/m/Y H:i:s") . " - Há {$type_count} regra(s) para processar deste tipo.";
        }

        foreach ($xzsuic_classificator->rules()->where([["type", "=", $type]])->orderBy("id", "ASC")->get() as $rule) {
            $this->log[] = date("d/m/Y H:i:s") . " - Regra #{$rule->id} - Evento Pré-Classificador: #{$rule->event->id} - {$rule->event->name}";

            foreach ($xzsuic_classificator->event_classificator->categorias()->whereHas("categoria", function ($q1) use ($xzsuic_classificator) {
                $q1->whereHas("event_classificates", function ($q2) use ($xzsuic_classificator) {
                    $q2->whereHas("category", function ($q3) use ($xzsuic_classificator) {
                        $q3->whereHas("eventos", function ($q4) use ($xzsuic_classificator) {
                            $q4->where([["evento_id", "=", $xzsuic_classificator->event->id]]);
                        });
                    });
                });
            })->get() as $category_event_classificator) {
                $category_classificator = $category_event_classificator->categoria;
                $category = $category_classificator->event_classificates()->whereHas("category", function ($q2) use ($xzsuic_classificator) {
                    $q2->whereHas("eventos", function ($q3) use ($xzsuic_classificator) {
                        $q3->where([["evento_id", "=", $xzsuic_classificator->event->id]]);
                    });
                })->first()->category;

                $this->log[] = date("d/m/Y H:i:s") . " - INÍCIO PROCESSAMENTO CATEGORIA.";
                $this->log[] = date("d/m/Y H:i:s") . " - Categoria Classificadora: #{$category_classificator->id} - {$category_classificator->name}.";
                $this->log[] = date("d/m/Y H:i:s") . " - Categoria Destino: #{$category->id} - {$category->name}.";

                $registrations_valid_total = $category_classificator->inscricoes()->whereHas("torneio", function ($q1) use ($xzsuic_classificator) {
                    $q1->where([["evento_id", "=", $xzsuic_classificator->event_classificator->id]]);
                })
                    ->where([
                        ["confirmado", "=", true],
                        ["desconsiderar_pontuacao_geral", "=", false],
                        ["posicao", "=", $rule->value],
                    ])
                    ->whereNotNull("posicao")
                    ->orderBy("posicao", "ASC")
                    ->count();

                $tournament_classificators_before_this = array();

                foreach ($xzsuic_classificator->event->event_classificators()->whereHas("event_classificator", function ($q1) use ($xzsuic_classificator) {
                    $q1->where([["data_inicio", "<=", $xzsuic_classificator->event_classificator->data_inicio]]);
                })->get() as $event_classificator) {
                    $tournament_classificators_before_this[] = $event_classificator->event_classificator->id;
                }
                $this->log[] = date("d/m/Y H:i:s") . " - Torneios anteriores a este: " . implode(",", $tournament_classificators_before_this);

                foreach (
                    $category_classificator->inscricoes()->whereHas("torneio", function ($q1) use ($xzsuic_classificator) {
                        $q1->where([["evento_id", "=", $xzsuic_classificator->event_classificator->id]]);
                    })
                    ->where([
                        ["confirmado", "=", true],
                        ["desconsiderar_pontuacao_geral", "=", false],
                    ])
                    ->whereNotNull("posicao")
                    ->orderBy("posicao", "ASC")
                    ->get() as $classificacao
                ) {
                        $this->log[] = date("d/m/Y H:i:s") . " - Posição: #{$classificacao->posicao} - Enxadrista #{$classificacao->enxadrista->id} - {$classificacao->enxadrista->name}.";
                        if($classificacao->enxadrista->estaInscrito($rule->event->id)){
                            $this->log[] = date("d/m/Y H:i:s") . " - ENXADRISTA INSCRITO - Possui Direto a Vaga.";

                            if (
                                Inscricao::whereHas("torneio", function ($q1) use ($xzsuic_classificator) {
                                    $q1->whereHas("evento", function ($q2) use ($xzsuic_classificator) {
                                        $q2->where([["id", "=", $xzsuic_classificator->event->id]]);
                                    });
                                })
                                ->where(function ($q0) use ($xzsuic_classificator, $tournament_classificators_before_this) {
                                    $q0->whereHas("configs", function ($q1) use ($xzsuic_classificator, $tournament_classificators_before_this) {
                                        $q1->where([
                                            ["key", "=", "event_classificator_id"],
                                        ]);
                                        $q1->whereIn("integer", $tournament_classificators_before_this);
                                    })
                                    ->orWhereHas("configs", function ($q1) use ($xzsuic_classificator, $tournament_classificators_before_this) {
                                        $q1->where([
                                            ["key", "=", "classificated_manually"],
                                            ["boolean", "=", true],
                                        ]);
                                    });
                                })
                                ->where([
                                    ["enxadrista_id", "=", $classificacao->enxadrista_id]
                                ])
                                ->count() == 0
                            ) {
                                if (
                                    Inscricao::whereHas("torneio", function ($q1) use ($xzsuic_classificator) {
                                        $q1->whereHas("evento", function ($q2) use ($xzsuic_classificator) {
                                            $q2->where([["id", "=", $xzsuic_classificator->event->id]]);
                                        });
                                    })
                                    ->whereHas("configs", function ($q1) use ($xzsuic_classificator, $tournament_classificators_before_this) {
                                        $q1->where([
                                            ["key", "=", "event_classificator_id"],
                                        ]);
                                        $q1->where([["integer","=", $xzsuic_classificator->event_classificator->id]]);
                                    })
                                    ->whereHas("configs", function ($q1) use ($xzsuic_classificator, $tournament_classificators_before_this,$rule) {
                                        $q1->where([
                                            ["key", "=", "event_classificator_rule_id"],
                                        ]);
                                        $q1->where([["integer","=",$rule->id]]);
                                    })
                                    ->where([
                                        ["enxadrista_id", "=", $classificacao->enxadrista_id]
                                    ])
                                    ->count() > 0
                                ) {
                                    $this->log[] = date("d/m/Y H:i:s") . " - Já foi classificado por este mesmo evento.";
                                    $classification_found = true;
                                } else {
                                    $this->log[] = date("d/m/Y H:i:s") . " - Ainda não foi classificado.";

                                    if (
                                        Inscricao::whereHas("torneio", function ($q1) use ($xzsuic_classificator) {
                                            $q1->whereHas("evento", function ($q2) use ($xzsuic_classificator) {
                                                $q2->where([["id", "=", $xzsuic_classificator->event->id]]);
                                            });
                                        })
                                        ->whereDoesntHave("configs", function ($q1) use ($xzsuic_classificator, $tournament_classificators_before_this) {
                                            $q1->where([
                                                ["key", "=", "event_classificator_id"],
                                            ]);
                                        })
                                        ->whereDoesntHave("configs", function ($q1) use ($xzsuic_classificator, $tournament_classificators_before_this) {
                                            $q1->where([
                                                ["key", "=", "classificated_manually"],
                                                ["boolean", "=", true],
                                            ]);
                                        })
                                        ->where([
                                            ["enxadrista_id", "=", $classificacao->enxadrista_id]
                                        ])
                                        ->count() == 0
                                    ) {

                                        if ($category->idade_minima) {
                                            if ($classificacao->enxadrista->howOldForEvento($xzsuic_classificator->event->id) < $category->idade_minima) {
                                                $this->log[] = date("d/m/Y H:i:s") . " - Categoria inválida - Obtendo nova categoria.";
                                                $category = $this->getNewCategory($xzsuic_classificator->event, $classificacao->enxadrista);
                                                if ($category) {
                                                    $this->log[] = date("d/m/Y H:i:s") . " - Nova Categoria Encontrada - #{$categoria->id} - {$categoria->name}.";
                                                } else {
                                                    $this->log[] = date("d/m/Y H:i:s") . " - Não há categoria para classificar este enxadrista - Ignorando.";
                                                    continue;
                                            }
                                            }
                                        }
                                        if ($category->idade_maxima) {
                                            if ($classificacao->enxadrista->howOldForEvento($xzsuic_classificator->event->id) > $category->idade_maxima) {
                                                $this->log[] = date("d/m/Y H:i:s") . " - Categoria inválida - Obtendo nova categoria.";
                                                $category = $this->getNewCategory($xzsuic_classificator->event, $classificacao->enxadrista);
                                                if ($category) {
                                                    $this->log[] = date("d/m/Y H:i:s") . " - Nova Categoria Encontrada - #{$categoria->id} - {$categoria->name}.";
                                                } else {
                                                    $this->log[] = date("d/m/Y H:i:s") . " - Não há categoria para classificar este enxadrista - Ignorando.";
                                                    continue;
                                                }
                                            }
                                        }
                                        if ($xzsuic_classificator->event->torneios()->whereHas("categorias", function ($q1) use ($category) {
                                            $q1->where([["categoria_id", "=", $category->id]]);
                                        })->count() > 0) {
                                            $torneio = $xzsuic_classificator->event->torneios()->whereHas("categorias", function ($q1) use ($category) {
                                                $q1->where([["categoria_id", "=", $category->id]]);
                                            })->first();

                                            $inscricao_nova = new Inscricao;
                                            $inscricao_nova->enxadrista_id = $classificacao->enxadrista_id;
                                            $inscricao_nova->categoria_id = $category->id;
                                            $inscricao_nova->cidade_id = $classificacao->cidade_id;
                                            $inscricao_nova->clube_id = $classificacao->clube_id;
                                            $inscricao_nova->torneio_id = $torneio->id;
                                            $inscricao_nova->regulamento_aceito = $classificacao->regulamento_aceito;
                                            $inscricao_nova->confirmado = false;
                                            $inscricao_nova->xadrezsuico_aceito = $classificacao->xadrezsuico_aceito;
                                            $inscricao_nova->is_aceito_imagem = $classificacao->xadrezsuico_aceito;
                                            $inscricao_nova->inscricao_from = $classificacao->id;
                                            $inscricao_nova->save();

                                            $inscricao_nova->setConfig("event_classificator_id", ConfigType::Integer, $xzsuic_classificator->event_classificator->id);
                                            $inscricao_nova->setConfig("event_classificator_rule_id", ConfigType::Integer, $rule->id);

                                            if ($xzsuic_classificator->event->tipo_rating) {
                                                $rating_count = $inscricao_nova->enxadrista->ratings()->where([
                                                    ["tipo_ratings_id", "=", $xzsuic_classificator->event->tipo_rating->id]
                                                ])->count();
                                                if ($rating_count == 0) {
                                                    $rating_inicial = $inscricao_nova->enxadrista->ratingParaEvento($xzsuic_classificator->event->id);

                                                    $rating = new Rating;
                                                    $rating->tipo_ratings_id = $xzsuic_classificator->event->tipo_rating->tipo_rating->id;
                                                    $rating->enxadrista_id = $inscricao_nova->enxadrista->id;
                                                    $rating->valor = $rating_inicial;
                                                    $rating->save();

                                                    $movimentacao = new MovimentacaoRating;
                                                    $movimentacao->tipo_ratings_id = $rating->tipo_ratings_id;
                                                    $movimentacao->ratings_id = $rating->id;
                                                    $movimentacao->valor = $rating_inicial;
                                                    $movimentacao->is_inicial = true;
                                                    $movimentacao->save();
                                                }
                                            }

                                            if ($inscricao_nova->enxadrista->email) {
                                                if ($xzsuic_classificator->event->isPaid() && $inscricao_nova->getPaymentInfo("link")) {
                                                    EmailController::schedule(
                                                        $inscricao_nova->enxadrista->email,
                                                        $inscricao_nova,
                                                        EmailType::InscricaoRecebidaPagamentoPendente,
                                                        $inscricao_nova->enxadrista
                                                    );
                                                } else {
                                                    EmailController::schedule(
                                                        $inscricao_nova->enxadrista->email,
                                                        $inscricao_nova,
                                                        EmailType::ConfirmacaoInscricao,
                                                        $inscricao_nova->enxadrista
                                                    );
                                                }
                                            }
                                        } else {
                                            $this->log[] = date("d/m/Y H:i:s") . " - ERRO!!! Categoria não possui torneio!";
                                        }
                                    } else {
                                        $this->log[] = date("d/m/Y H:i:s") . " - O mesmo já encontra inscrito pro evento - Apenas homologar classificação.";
                                        $inscricao_on_event = Inscricao::whereHas("torneio", function ($q1) use ($xzsuic_classificator) {
                                            $q1->whereHas("evento", function ($q2) use ($xzsuic_classificator) {
                                                $q2->where([["id", "=", $xzsuic_classificator->event->id]]);
                                            });
                                        })
                                            ->whereDoesntHave("configs", function ($q1) use ($xzsuic_classificator, $tournament_classificators_before_this) {
                                                $q1->where([
                                                    ["key", "=", "event_classificator_id"],
                                                ]);
                                            })
                                            ->whereDoesntHave("configs", function ($q1) use ($xzsuic_classificator, $tournament_classificators_before_this) {
                                                $q1->where([
                                                    ["key", "=", "classificated_manually"],
                                                    ["boolean", "=", true],
                                                ]);
                                            })->first();

                                        $inscricao_on_event->setConfig("event_classificator_id", ConfigType::Integer, $xzsuic_classificator->event_classificator->id);
                                        $inscricao_on_event->setConfig("event_classificator_rule_id", ConfigType::Integer, $rule->id);
                                        $inscricao_on_event->inscricao_from = $classificacao->id;
                                        $inscricao_on_event->save();
                                    }
                                }
                            } else {
                                $this->log[] = date("d/m/Y H:i:s") . " - Já foi classificado por outro evento ou manualmente.";
                            }
                        }else{
                            $this->log[] = date("d/m/Y H:i:s") . " - ENXADRISTA NÃO INSCRITO - NÃO possui Direto a Vaga.";

                        }

                }
                $this->log[] = date("d/m/Y H:i:s") . " - FIM PROCESSAMENTO CATEGORIA.";
            }
        }

        $this->log[] = date("d/m/Y H:i:s") . " - Fim do Processo - Tipo de Classificador: " . $classificator_type["name"];
    }

    public function process_place_by_quantity($event, $xzsuic_classificator)
    {
        $type = ClassificationTypeRule::PLACE_BY_QUANTITY;
        $classificator_type = ClassificationTypeRule::get($type);
        $this->log[] = date("d/m/Y H:i:s") . " - Início do Processo - Tipo de Classificador: " . $classificator_type["name"];

        $type_count = $xzsuic_classificator->rules()->where([["type", "=", $type]])->count();

        if ($type_count == 0) {
            $this->log[] = date("d/m/Y H:i:s") . " - Não há regras para processar deste tipo.";
        } else {
            $this->log[] = date("d/m/Y H:i:s") . " - Há {$type_count} regra(s) para processar deste tipo.";
        }

        foreach ($xzsuic_classificator->rules()->where([["type", "=", $type]])->orderBy("id", "ASC")->get() as $rule) {
            if($rule->is_absolute){
                $this->log[] = date("d/m/Y H:i:s") . " - Regra #{$rule->id} - Uma vaga a cada: {$rule->value}";
            }else{
                $this->log[] = date("d/m/Y H:i:s") . " - Regra #{$rule->id} - Uma vaga a cada: {$rule->value} ou fração";
            }


            foreach ($xzsuic_classificator->event_classificator->categorias()->whereHas("categoria", function ($q1) use ($xzsuic_classificator) {
                $q1->whereHas("event_classificates", function ($q2) use ($xzsuic_classificator) {
                    $q2->whereHas("category", function ($q3) use ($xzsuic_classificator) {
                        $q3->whereHas("eventos", function ($q4) use ($xzsuic_classificator) {
                            $q4->where([["evento_id", "=", $xzsuic_classificator->event->id]]);
                        });
                    });
                });
            })->get() as $category_event_classificator) {
                $category_classificator = $category_event_classificator->categoria;
                $category = $category_classificator->event_classificates()->whereHas("category", function ($q2) use ($xzsuic_classificator) {
                    $q2->whereHas("eventos", function ($q3) use ($xzsuic_classificator) {
                        $q3->where([["evento_id", "=", $xzsuic_classificator->event->id]]);
                    });
                })->first()->category;

                $this->log[] = date("d/m/Y H:i:s") . " - INÍCIO PROCESSAMENTO CATEGORIA.";
                $this->log[] = date("d/m/Y H:i:s") . " - Categoria Classificadora: #{$category_classificator->id} - {$category_classificator->name}.";
                $this->log[] = date("d/m/Y H:i:s") . " - Categoria Destino: #{$category->id} - {$category->name}.";

                $registrations_valid_total = $category_classificator->inscricoes()->whereHas("torneio", function ($q1) use ($xzsuic_classificator) {
                    $q1->where([["evento_id", "=", $xzsuic_classificator->event_classificator->id]]);
                })
                ->where([
                    ["confirmado", "=", true],
                    ["desconsiderar_pontuacao_geral", "=", false],
                    ["posicao", "=", $rule->value],
                ])
                ->whereNotNull("posicao")
                ->orderBy("posicao", "ASC")
                ->count();

                $total_places = $registrations_valid_total / $rule->value;
                $total_places = intval($total_places);

                // Se absoluto, não permite usar fração.
                if(!$rule->is_absolute){
                    if($total_places < $registrations_valid_total / $rule->value){
                        $total_places++;
                    }
                }
                $this->log[] = date("d/m/Y H:i:s") . " - Total de Inscritos Válidos: {$registrations_valid_total}.";
                $this->log[] = date("d/m/Y H:i:s") . " - Total de Vagas: {$total_places}.";

                $total_registrations = 0;

                $tournament_classificators_before_this = array();

                foreach ($xzsuic_classificator->event->event_classificators()->whereHas("event_classificator", function ($q1) use ($xzsuic_classificator) {
                    $q1->where([["data_inicio", "<=", $xzsuic_classificator->event_classificator->data_inicio]]);
                })->get() as $event_classificator) {
                    $tournament_classificators_before_this[] = $event_classificator->event_classificator->id;
                }
                $this->log[] = date("d/m/Y H:i:s") . " - Torneios anteriores a este: ".implode(",", $tournament_classificators_before_this);

                foreach ($category_classificator->inscricoes()->whereHas("torneio", function ($q1) use ($xzsuic_classificator) {
                    $q1->where([["evento_id", "=", $xzsuic_classificator->event_classificator->id]]);
                })
                    ->where([
                        ["confirmado", "=", true],
                        ["desconsiderar_pontuacao_geral", "=", false],
                    ])
                    ->whereNotNull("posicao")
                    ->orderBy("posicao", "ASC")
                    ->get() as $classificacao) {
                    if ($total_registrations < $total_places) {
                        $this->log[] = date("d/m/Y H:i:s") . " - Posição: #{$classificacao->posicao} - Enxadrista #{$classificacao->enxadrista->id} - {$classificacao->enxadrista->name}.";
                        if (
                            Inscricao::whereHas("torneio", function ($q1) use ($xzsuic_classificator) {
                                $q1->whereHas("evento", function ($q2) use ($xzsuic_classificator) {
                                    $q2->where([["id", "=", $xzsuic_classificator->event->id]]);
                                });
                            })
                            ->where(function ($q0) use ($xzsuic_classificator, $tournament_classificators_before_this) {
                                $q0->whereHas("configs", function ($q1) use ($xzsuic_classificator, $tournament_classificators_before_this) {
                                    $q1->where([
                                        ["key", "=", "event_classificator_id"],
                                    ]);
                                    $q1->whereIn("integer", $tournament_classificators_before_this);
                                })
                                ->orWhereHas("configs", function ($q1) use ($xzsuic_classificator, $tournament_classificators_before_this) {
                                    $q1->where([
                                        ["key", "=", "classificated_manually"],
                                        ["boolean", "=", true],
                                    ]);
                                });
                            })
                            ->where([
                                ["enxadrista_id", "=", $classificacao->enxadrista_id]
                            ])
                            ->count() == 0
                        ) {
                            if (
                                Inscricao::whereHas("torneio", function ($q1) use ($xzsuic_classificator) {
                                    $q1->whereHas("evento", function ($q2) use ($xzsuic_classificator) {
                                        $q2->where([["id", "=", $xzsuic_classificator->event->id]]);
                                    });
                                })
                                ->whereHas("configs", function ($q1) use ($xzsuic_classificator, $tournament_classificators_before_this) {
                                    $q1->where([
                                        ["key", "=", "event_classificator_id"],
                                    ]);
                                    $q1->where([["integer", "=", $xzsuic_classificator->event_classificator->id]]);
                                })
                                ->whereHas("configs", function ($q1) use ($xzsuic_classificator, $tournament_classificators_before_this,$rule) {
                                    $q1->where([
                                        ["key", "=", "event_classificator_rule_id"],
                                    ]);
                                    $q1->where([["integer","=",$rule->id]]);
                                })
                                ->where([
                                    ["enxadrista_id", "=", $classificacao->enxadrista_id]
                                ])
                                ->count() > 0
                            ) {
                                $this->log[] = date("d/m/Y H:i:s") . " - Já foi classificado por este mesmo evento.";
                                $total_registrations++;
                            } else {
                                $this->log[] = date("d/m/Y H:i:s") . " - Ainda não foi classificado.";

                                if (
                                    Inscricao::whereHas("torneio", function ($q1) use ($xzsuic_classificator) {
                                        $q1->whereHas("evento", function ($q2) use ($xzsuic_classificator) {
                                            $q2->where([["id", "=", $xzsuic_classificator->event->id]]);
                                        });
                                    })
                                    ->whereDoesntHave("configs", function ($q1) use ($xzsuic_classificator, $tournament_classificators_before_this) {
                                        $q1->where([
                                            ["key", "=", "event_classificator_id"],
                                        ]);
                                    })
                                    ->whereDoesntHave("configs", function ($q1) use ($xzsuic_classificator, $tournament_classificators_before_this) {
                                        $q1->where([
                                            ["key", "=", "classificated_manually"],
                                            ["boolean", "=", true],
                                        ]);
                                    })
                                    ->where([
                                        ["enxadrista_id", "=", $classificacao->enxadrista_id]
                                    ])
                                    ->count() == 0
                                ) {

                                    if($category->idade_minima){
                                        if($classificacao->enxadrista->howOldForEvento($xzsuic_classificator->event->id) < $category->idade_minima){
                                            $this->log[] = date("d/m/Y H:i:s") . " - Categoria inválida - Obtendo nova categoria.";
                                            $category = $this->getNewCategory($xzsuic_classificator->event, $classificacao->enxadrista);
                                            if($category){
                                                $this->log[] = date("d/m/Y H:i:s") . " - Nova Categoria Encontrada - #{$categoria->id} - {$categoria->name}.";
                                            }else{
                                                $this->log[] = date("d/m/Y H:i:s") . " - Não há categoria para classificar este enxadrista - Ignorando.";
                                                continue;
                                            }
                                        }
                                    }
                                    if ($category->idade_maxima) {
                                        if ($classificacao->enxadrista->howOldForEvento($xzsuic_classificator->event->id) > $category->idade_maxima) {
                                            $this->log[] = date("d/m/Y H:i:s") . " - Categoria inválida - Obtendo nova categoria.";
                                            $category = $this->getNewCategory($xzsuic_classificator->event, $classificacao->enxadrista);
                                            if ($category) {
                                                $this->log[] = date("d/m/Y H:i:s") . " - Nova Categoria Encontrada - #{$categoria->id} - {$categoria->name}.";
                                            }else{
                                                $this->log[] = date("d/m/Y H:i:s") . " - Não há categoria para classificar este enxadrista - Ignorando.";
                                                continue;
                                            }
                                        }
                                    }

                                    if ($xzsuic_classificator->event->torneios()->whereHas("categorias", function ($q1) use ($category) {
                                        $q1->where([["categoria_id", "=", $category->id]]);
                                    })->count() > 0) {
                                        $torneio = $xzsuic_classificator->event->torneios()->whereHas("categorias", function ($q1) use ($category) {
                                            $q1->where([["categoria_id", "=", $category->id]]);
                                        })->first();

                                        $inscricao_nova = new Inscricao;
                                        $inscricao_nova->enxadrista_id = $classificacao->enxadrista_id;
                                        $inscricao_nova->categoria_id = $category->id;
                                        $inscricao_nova->cidade_id = $classificacao->cidade_id;
                                        $inscricao_nova->clube_id = $classificacao->clube_id;
                                        $inscricao_nova->torneio_id = $torneio->id;
                                        $inscricao_nova->regulamento_aceito = $classificacao->regulamento_aceito;
                                        $inscricao_nova->confirmado = false;
                                        $inscricao_nova->xadrezsuico_aceito = $classificacao->xadrezsuico_aceito;
                                        $inscricao_nova->is_aceito_imagem = $classificacao->xadrezsuico_aceito;
                                        $inscricao_nova->inscricao_from = $classificacao->id;
                                        $inscricao_nova->save();

                                        $inscricao_nova->setConfig("event_classificator_id", ConfigType::Integer, $xzsuic_classificator->event_classificator->id);
                                        $inscricao_nova->setConfig("event_classificator_rule_id", ConfigType::Integer, $rule->id);

                                        if ($xzsuic_classificator->event->tipo_rating) {
                                            $rating_count = $inscricao_nova->enxadrista->ratings()->where([
                                                ["tipo_ratings_id", "=", $xzsuic_classificator->event->tipo_rating->id]
                                            ])->count();
                                            if ($rating_count == 0) {
                                                $rating_inicial = $inscricao_nova->enxadrista->ratingParaEvento($xzsuic_classificator->event->id);

                                                $rating = new Rating;
                                                $rating->tipo_ratings_id = $xzsuic_classificator->event->tipo_rating->tipo_rating->id;
                                                $rating->enxadrista_id = $inscricao_nova->enxadrista->id;
                                                $rating->valor = $rating_inicial;
                                                $rating->save();

                                                $movimentacao = new MovimentacaoRating;
                                                $movimentacao->tipo_ratings_id = $rating->tipo_ratings_id;
                                                $movimentacao->ratings_id = $rating->id;
                                                $movimentacao->valor = $rating_inicial;
                                                $movimentacao->is_inicial = true;
                                                $movimentacao->save();
                                            }
                                        }

                                        if ($inscricao_nova->enxadrista->email) {
                                            if ($xzsuic_classificator->event->isPaid() && $inscricao_nova->getPaymentInfo("link")) {
                                                EmailController::schedule(
                                                    $inscricao_nova->enxadrista->email,
                                                    $inscricao_nova,
                                                    EmailType::InscricaoRecebidaPagamentoPendente,
                                                    $inscricao_nova->enxadrista
                                                );
                                            } else {
                                                EmailController::schedule(
                                                    $inscricao_nova->enxadrista->email,
                                                    $inscricao_nova,
                                                    EmailType::ConfirmacaoInscricao,
                                                    $inscricao_nova->enxadrista
                                                );
                                            }
                                        }

                                        $total_registrations++;
                                    } else {
                                        $this->log[] = date("d/m/Y H:i:s") . " - ERRO!!! Categoria não possui torneio!";
                                    }
                                } else {
                                    $this->log[] = date("d/m/Y H:i:s") . " - O mesmo já encontra inscrito pro evento - Apenas homologar classificação.";
                                    $inscricao_on_event = Inscricao::whereHas("torneio", function ($q1) use ($xzsuic_classificator) {
                                        $q1->whereHas("evento", function ($q2) use ($xzsuic_classificator) {
                                            $q2->where([["id", "=", $xzsuic_classificator->event->id]]);
                                        });
                                    })
                                        ->whereDoesntHave("configs", function ($q1) use ($xzsuic_classificator, $tournament_classificators_before_this) {
                                            $q1->where([
                                                ["key", "=", "event_classificator_id"],
                                            ]);
                                        })
                                        ->whereDoesntHave("configs", function ($q1) use ($xzsuic_classificator, $tournament_classificators_before_this) {
                                            $q1->where([
                                                ["key", "=", "classificated_manually"],
                                                ["boolean", "=", true],
                                            ]);
                                        })->first();

                                    $inscricao_on_event->setConfig("event_classificator_id", ConfigType::Integer, $xzsuic_classificator->event_classificator->id);
                                    $inscricao_on_event->setConfig("event_classificator_rule_id", ConfigType::Integer, $rule->id);
                                    $inscricao_on_event->inscricao_from = $classificacao->id;
                                    $inscricao_on_event->save();

                                    $total_registrations++;
                                }
                            }
                        } else {
                            $this->log[] = date("d/m/Y H:i:s") . " - Já foi classificado por outro evento ou manualmente.";
                        }
                    }
                }
                $this->log[] = date("d/m/Y H:i:s") . " - FIM PROCESSAMENTO CATEGORIA.";
            }
        }

        $this->log[] = date("d/m/Y H:i:s") . " - Fim do Processo - Tipo de Classificador: " . $classificator_type["name"];
    }


    public function getNewCategory($event, $enxadrista){
        if($event->hasCategoryForEnxadrista($enxadrista)){
            return $event->getCategoryForEnxadrista($enxadrista);
        }

        return null;
    }
}

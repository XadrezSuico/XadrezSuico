<?php

namespace App\Http\Controllers\Classification;

use App\Classification\EventClassificateRule;
use App\Enum\ClassificationType;
use App\Enum\ClassificationTypeRule;
use App\Enum\ClassificationTypeRuleConfig;
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
use Illuminate\Support\Facades\Log;
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
                    $this->process_position_rules($evento, $event_classificates);
                    break;
                case ClassificationTypeRule::POSITION_ABSOLUTE:
                    $this->process_position_absolute_rules($evento, $event_classificates);
                    break;
                case ClassificationTypeRule::PRE_CLASSIFICATE:
                    $this->process_pre_classificate_rules($evento, $event_classificates);
                    break;
                case ClassificationTypeRule::PLACE_BY_QUANTITY:
                    $this->process_place_by_quantity_rules($evento, $event_classificates);
                    break;
                case ClassificationTypeRule::CLASSIFICATE_BY_START_POSITION:
                    $this->process_classificate_by_start_position_rules($evento, $event_classificates);
                    break;
            }
        }
        $this->log[] = "----- ----- ---- ----";
        $this->log[] = "----- ----- ---- ----";
        $this->log[] = "----- ----- ---- ----";
        $this->log[] = "----- ----- ---- ----";
        $this->log[] = "----- ----- ---- ----";
        $this->log[] = "----- ----- ---- ----";
        $this->process_default_confirmed_rules($evento, $event_classificates);
        $this->log[] = "----- ----- ---- ----";
        $this->log[] = "----- ----- ---- ----";
        $this->log[] = "----- ----- ---- ----";
        $this->log[] = "----- ----- ---- ----";
        $this->log[] = "----- ----- ---- ----";
        $this->log[] = "----- ----- ---- ----";
        $this->process_default_rules($evento, $event_classificates);
        $this->log[] = "----- ----- ---- ----";
        $this->log[] = "----- ----- ---- ----";
        $this->log[] = "----- ----- ---- ----";
        $this->log[] = "----- ----- ---- ----";
        $this->log[] = "----- ----- ---- ----";
        $this->log[] = "----- ----- ---- ----";
        $this->process_default_not_classificated_rules($evento, $event_classificates);


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
        foreach(
            Inscricao::whereHas("configs",function($q1) use ($event_id){
                $q1->where([
                    ["key", "=", "event_classificator_id"],
                    ["integer", "=", $event_id],
                ]);
            })
            ->whereHas("torneio", function ($q1) use ($event_classificates) {
                $q1->where([["evento_id", "=", $event_classificates->event->id]]);
            })
            ->get() as $inscricao
        ){
            if ($inscricao->isDeletavel(true)) {
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

    public function process_position_rules($event, $xzsuic_classificator)
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
            $this->process_position($event, $xzsuic_classificator, $rule);
        }

        $this->log[] = date("d/m/Y H:i:s") . " - Fim do Processo - Tipo de Classificador: " . $classificator_type["name"];
    }
    public function process_position($event, $xzsuic_classificator, $rule, $is_default = false, $is_default_not_classificated =false, $is_default_confirmed = false)
    {
        $type = ClassificationTypeRule::POSITION;
        $classificator_type = ClassificationTypeRule::get($type);
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

                $tournament_classificators_before_this = $this->getTournamentsBeforeThis($xzsuic_classificator);
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
                                Inscricao::whereHas("torneio", function ($q1) use ($xzsuic_classificator, $rule) {
                                    $q1->where([["evento_id", "=", $xzsuic_classificator->event->id]]);
                                })
                                ->whereHas("configs", function ($q1) use ($xzsuic_classificator) {
                                    $q1->where([
                                        ["key", "=", "event_classificator_id"],
                                        ["integer", "=", $xzsuic_classificator->event_classificator->id]
                                    ]);
                                })
                                ->whereHas("configs", function ($q1) use ($xzsuic_classificator, $rule) {
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
                                $this->log[] = date("d/m/Y H:i:s") . " - Já foi classificado por este mesmo evento e regra.";
                                $classification_found = true;
                            } elseif (
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
                                    $q1->where([["integer", "!=", $rule->id], ["integer", "!=", null]]);
                                })
                                ->where([
                                    ["enxadrista_id", "=", $classificacao->enxadrista_id]
                                ])
                                ->count() > 0
                            ) {
                                $this->log[] = date("d/m/Y H:i:s") . " - Já foi classificado por este mesmo evento mas não pela mesma regra.";
                                $this->log[] = date("d/m/Y H:i:s") . " - Avaliar a preferência da regra.";

                                $inscricao_to_check = Inscricao::whereHas("torneio", function ($q1) use ($xzsuic_classificator) {
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
                                        $q1->where([["integer", "!=", $rule->id], ["integer", "!=", null]]);
                                    })
                                    ->where([
                                        ["enxadrista_id", "=", $classificacao->enxadrista_id]
                                    ])
                                    ->first();

                                $rule_id = $inscricao_to_check->getConfig("event_classificator_rule_id");
                                $rule_to_check = EventClassificateRule::where([["id", "=", $rule_id]])->first();
                                if ($rule_to_check) {
                                    // Minor number - More Priority
                                    $rule_priority = ClassificationTypeRule::getOrderPriority($rule->type);
                                    $rule_to_check_priority = ClassificationTypeRule::getOrderPriority($rule_to_check->type);

                                    if ($rule_priority < $rule_to_check_priority) {
                                        $this->log[] = date("d/m/Y H:i:s") . " - REGRA PRIORITÁRIA - Trocando regra.";

                                        $inscricao_to_check->setConfig("event_classificator_id", ConfigType::Integer, $xzsuic_classificator->event_classificator->id);
                                        $inscricao_to_check->setConfig("event_classificator_rule_id", ConfigType::Integer, $rule->id);
                                        $inscricao_to_check->inscricao_from = $classificacao->id;
                                        $inscricao_to_check->save();

                                        $classification_found = true;
                                    } elseif ($rule_priority == $rule_to_check_priority) {
                                        $this->log[] = date("d/m/Y H:i:s") . " - REGRA IGUAL - Avaliando se a regra atual foi inserida antes da outra.";
                                        if ($rule->id > $rule_to_check->id) {
                                            $this->log[] = date("d/m/Y H:i:s") . " - REGRA IGUAL - REGRA INSERIDA ANTES - Trocando regra.";
                                            $inscricao_to_check->setConfig("event_classificator_id", ConfigType::Integer, $xzsuic_classificator->event_classificator->id);
                                            $inscricao_to_check->setConfig("event_classificator_rule_id", ConfigType::Integer, $rule->id);
                                            $inscricao_to_check->inscricao_from = $classificacao->id;
                                            $inscricao_to_check->save();

                                            $classification_found = true;
                                        } else {
                                            $this->log[] = date("d/m/Y H:i:s") . " - REGRA IGUAL - REGRA INSERIDA DEPOIS - Mantendo regra.";
                                        }
                                    } else {
                                        $this->log[] = date("d/m/Y H:i:s") . " - REGRA COM MENOR PRIORIDADE - Mantendo regra.";
                                    }
                                }
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
                                    $category = $this->getCategory($xzsuic_classificator->event, $classificacao->enxadrista, $category);
                                    if (!$category) {
                                        $this->log[] = date("d/m/Y H:i:s") . " - Não há categoria para classificar este enxadrista - Ignorando.";
                                        continue;
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

                                        if ($inscricao_nova->save()) {
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

                                            if ($inscricao_nova->enxadrista->email && !$xzsuic_classificator->event->hasConfig("classificator_not_send_email")) {
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
                                            $this->log[] = date("d/m/Y H:i:s") . " - ERRO!!! Enxadrista JÁ inscrito!";
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
                                        })
                                        ->where([
                                            ["enxadrista_id", "=", $classificacao->enxadrista_id]
                                        ])
                                        ->first();

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


    public function process_position_absolute_rules($event, $xzsuic_classificator)
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
            $this->process_position_absolute($event, $xzsuic_classificator, $rule);
        }

        $this->log[] = date("d/m/Y H:i:s") . " - Fim do Processo - Tipo de Classificador: " . $classificator_type["name"];
    }
    public function process_position_absolute($event, $xzsuic_classificator,$rule, $is_default = false, $is_default_not_classificated =false, $is_default_confirmed = false)
    {
        $type = ClassificationTypeRule::POSITION_ABSOLUTE;
        $classificator_type = ClassificationTypeRule::get($type);
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

                $tournament_classificators_before_this = $this->getTournamentsBeforeThis($xzsuic_classificator);
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
                                    $q1->where([["integer", "=", $rule->id]]);
                                })
                                ->where([
                                    ["enxadrista_id", "=", $classificacao->enxadrista_id]
                                ])
                                ->count() > 0
                            ) {
                                $this->log[] = date("d/m/Y H:i:s") . " - Já foi classificado por este mesmo evento.";
                                $classification_found = true;
                            } elseif (
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
                                    $q1->where([["integer", "!=", $rule->id], ["integer", "!=", null]]);
                                })
                                ->where([
                                    ["enxadrista_id", "=", $classificacao->enxadrista_id]
                                ])
                                ->count() > 0
                            ) {
                                $this->log[] = date("d/m/Y H:i:s") . " - Já foi classificado por este mesmo evento mas não pela mesma regra.";
                                $this->log[] = date("d/m/Y H:i:s") . " - Avaliar a preferência da regra.";

                                $inscricao_to_check = Inscricao::whereHas("torneio", function ($q1) use ($xzsuic_classificator) {
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
                                        $q1->where([["integer", "!=", $rule->id], ["integer", "!=", null]]);
                                    })
                                    ->where([
                                        ["enxadrista_id", "=", $classificacao->enxadrista_id]
                                    ])
                                    ->first();

                                $rule_id = $inscricao_to_check->getConfig("event_classificator_rule_id");
                                $rule_to_check = EventClassificateRule::where([["id", "=", $rule_id]])->first();
                                if ($rule_to_check) {
                                    // Minor number - More Priority
                                    $rule_priority = ClassificationTypeRule::getOrderPriority($rule->type);
                                    $rule_to_check_priority = ClassificationTypeRule::getOrderPriority($rule_to_check->type);

                                    if ($rule_priority < $rule_to_check_priority) {
                                        $this->log[] = date("d/m/Y H:i:s") . " - REGRA PRIORITÁRIA - Trocando regra.";

                                        $inscricao_to_check->setConfig("event_classificator_id", ConfigType::Integer, $xzsuic_classificator->event_classificator->id);
                                        $inscricao_to_check->setConfig("event_classificator_rule_id", ConfigType::Integer, $rule->id);
                                        $inscricao_to_check->inscricao_from = $classificacao->id;
                                        $inscricao_to_check->save();

                                        $classification_found = true;
                                    } elseif ($rule_priority == $rule_to_check_priority) {
                                        $this->log[] = date("d/m/Y H:i:s") . " - REGRA IGUAL - Avaliando se a regra atual foi inserida antes da outra.";
                                        if ($rule->id > $rule_to_check->id) {
                                            $this->log[] = date("d/m/Y H:i:s") . " - REGRA IGUAL - REGRA INSERIDA ANTES - Trocando regra.";
                                            $inscricao_to_check->setConfig("event_classificator_id", ConfigType::Integer, $xzsuic_classificator->event_classificator->id);
                                            $inscricao_to_check->setConfig("event_classificator_rule_id", ConfigType::Integer, $rule->id);
                                            $inscricao_to_check->inscricao_from = $classificacao->id;
                                            $inscricao_to_check->save();

                                            $classification_found = true;
                                        } else {
                                            $this->log[] = date("d/m/Y H:i:s") . " - REGRA IGUAL - REGRA INSERIDA DEPOIS - Mantendo regra.";
                                        }
                                    } else {
                                        $this->log[] = date("d/m/Y H:i:s") . " - REGRA COM MENOR PRIORIDADE - Mantendo regra.";
                                    }
                                }
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
                                    $category = $this->getCategory($xzsuic_classificator->event, $classificacao->enxadrista, $category);
                                    if (!$category) {
                                        $this->log[] = date("d/m/Y H:i:s") . " - Não há categoria para classificar este enxadrista - Ignorando.";
                                        continue;
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
                                        if ($inscricao_nova->save()) {

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

                                            if ($inscricao_nova->enxadrista->email && !$xzsuic_classificator->event->hasConfig("classificator_not_send_email")) {
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
                                            $this->log[] = date("d/m/Y H:i:s") . " - ERRO!!! Enxadrista JÁ inscrito!";
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
                                        })
                                        ->where([
                                            ["enxadrista_id", "=", $classificacao->enxadrista_id]
                                        ])
                                        ->first();

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



    public function process_pre_classificate_rules($event, $xzsuic_classificator)
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
            $this->process_pre_classificate($event, $xzsuic_classificator, $rule);
        }

        $this->log[] = date("d/m/Y H:i:s") . " - Fim do Processo - Tipo de Classificador: " . $classificator_type["name"];
    }
    public function process_pre_classificate($event, $xzsuic_classificator,$rule, $is_default = false, $is_default_not_classificated =false, $is_default_confirmed = false)
    {
        $type = ClassificationTypeRule::PRE_CLASSIFICATE;
        $classificator_type = ClassificationTypeRule::get($type);
            $this->log[] = date("d/m/Y H:i:s") . " - Regra #{$rule->id} - Evento Pré-Classificador: #{$rule->event->id} - {$rule->event->name}";

            if($rule->is_absolute){
                // Se absoluta, só vale para as categorias que classificam
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
                            ["desconsiderar_pontuacao_geral", "=", false]
                        ])
                        ->whereNotNull("posicao")
                        ->orderBy("posicao", "ASC")
                        ->count();

                    $tournament_classificators_before_this = $this->getTournamentsBeforeThis($xzsuic_classificator);
                    $this->log[] = date("d/m/Y H:i:s") . " - Torneios anteriores a este: " . implode(",", $tournament_classificators_before_this);
                    $is_found = false;
                    foreach (
                        $category_classificator->inscricoes()->whereHas("torneio", function ($q1) use ($xzsuic_classificator) {
                            $q1->where([["evento_id", "=", $xzsuic_classificator->event_classificator->id]]);
                        })
                        ->where([
                            ["confirmado", "=", true],
                            ["desconsiderar_pontuacao_geral", "=", false]
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
                                    } elseif (
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
                                            $q1->where([["integer", "!=", $rule->id], ["integer", "!=", null]]);
                                        })
                                        ->where([
                                            ["enxadrista_id", "=", $classificacao->enxadrista_id]
                                        ])
                                        ->count() > 0
                                    ) {
                                        $this->log[] = date("d/m/Y H:i:s") . " - Já foi classificado por este mesmo evento mas não pela mesma regra.";
                                        $this->log[] = date("d/m/Y H:i:s") . " - Avaliar a preferência da regra.";

                                        $inscricao_to_check = Inscricao::whereHas("torneio", function ($q1) use ($xzsuic_classificator) {
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
                                            $q1->where([["integer", "!=", $rule->id], ["integer", "!=", null]]);
                                        })
                                        ->where([
                                            ["enxadrista_id", "=", $classificacao->enxadrista_id]
                                        ])
                                        ->first();

                                        $rule_id = $inscricao_to_check->getConfig("event_classificator_rule_id");
                                        $rule_to_check = EventClassificateRule::where([["id", "=", $rule_id]])->first();
                                        if ($rule_to_check) {
                                            // Minor number - More Priority
                                            $rule_priority = ClassificationTypeRule::getOrderPriority($rule->type);
                                            $rule_to_check_priority = ClassificationTypeRule::getOrderPriority($rule_to_check->type);

                                            if ($rule_priority < $rule_to_check_priority) {
                                                $this->log[] = date("d/m/Y H:i:s") . " - REGRA PRIORITÁRIA - Trocando regra.";

                                                $inscricao_to_check->setConfig("event_classificator_id", ConfigType::Integer, $xzsuic_classificator->event_classificator->id);
                                                $inscricao_to_check->setConfig("event_classificator_rule_id", ConfigType::Integer, $rule->id);
                                                $inscricao_to_check->inscricao_from = $classificacao->id;
                                                $inscricao_to_check->save();

                                                $classification_found = true;
                                            } elseif ($rule_priority == $rule_to_check_priority) {
                                                $this->log[] = date("d/m/Y H:i:s") . " - REGRA IGUAL - Avaliando se a regra atual foi inserida antes da outra.";
                                                if ($rule->id > $rule_to_check->id) {
                                                    $this->log[] = date("d/m/Y H:i:s") . " - REGRA IGUAL - REGRA INSERIDA ANTES - Trocando regra.";
                                                    $inscricao_to_check->setConfig("event_classificator_id", ConfigType::Integer, $xzsuic_classificator->event_classificator->id);
                                                    $inscricao_to_check->setConfig("event_classificator_rule_id", ConfigType::Integer, $rule->id);
                                                    $inscricao_to_check->inscricao_from = $classificacao->id;
                                                    $inscricao_to_check->save();

                                                    $classification_found = true;
                                                } else {
                                                    $this->log[] = date("d/m/Y H:i:s") . " - REGRA IGUAL - REGRA INSERIDA DEPOIS - Mantendo regra.";
                                                }
                                            } else {
                                                $this->log[] = date("d/m/Y H:i:s") . " - REGRA COM MENOR PRIORIDADE - Mantendo regra.";
                                            }
                                        }
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
                                            $category = $this->getCategory($xzsuic_classificator->event, $classificacao->enxadrista, $category);
                                            if (!$category) {
                                                $this->log[] = date("d/m/Y H:i:s") . " - Não há categoria para classificar este enxadrista - Ignorando.";
                                                continue;
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
                                                if($inscricao_nova->save()){

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

                                                    if ($inscricao_nova->enxadrista->email && !$xzsuic_classificator->event->hasConfig("classificator_not_send_email")) {
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
                                                }else{
                                                    $this->log[] = date("d/m/Y H:i:s") . " - ERRO!!! Enxadrista JÁ inscrito!";
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
                                            })
                                            ->where([
                                                ["enxadrista_id", "=", $classificacao->enxadrista_id]
                                            ])
                                            ->first();

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
            }else{
                foreach($xzsuic_classificator->event_classificator->categorias->all() as $categoria){
                    $category_classificator = $categoria->categoria;
                    $this->log[] = date("d/m/Y H:i:s") . " - INÍCIO PROCESSAMENTO CATEGORIA.";
                    $this->log[] = date("d/m/Y H:i:s") . " - Categoria do Evento: #{$category_classificator->id} - {$category_classificator->name}.";

                    $registrations_valid_total = $category_classificator->inscricoes()->whereHas("torneio", function ($q1) use ($xzsuic_classificator) {
                        $q1->where([["evento_id", "=", $xzsuic_classificator->event_classificator->id]]);
                    })
                    ->where([
                        ["confirmado", "=", true],
                        ["desconsiderar_pontuacao_geral", "=", false]
                    ])
                    ->whereNotNull("posicao")
                    ->orderBy("posicao", "ASC")
                    ->count();

                    $tournament_classificators_before_this = $this->getTournamentsBeforeThis($xzsuic_classificator);
                    $this->log[] = date("d/m/Y H:i:s") . " - Torneios anteriores a este: " . implode(",", $tournament_classificators_before_this);
                    $is_found = false;
                    foreach (
                        $category_classificator->inscricoes()->whereHas("torneio", function ($q1) use ($xzsuic_classificator) {
                        $q1->where([["evento_id", "=", $xzsuic_classificator->event_classificator->id]]);
                        })
                        ->where([
                            ["confirmado", "=", true],
                            ["desconsiderar_pontuacao_geral", "=", false]
                        ])
                        ->whereNotNull("posicao")
                        ->orderBy("posicao", "ASC")
                        ->get() as $classificacao
                    ) {
                        $this->log[] = date("d/m/Y H:i:s") . " - Posição: #{$classificacao->posicao} - Enxadrista #{$classificacao->enxadrista->id} - {$classificacao->enxadrista->name}.";
                        if ($classificacao->enxadrista->estaInscrito($rule->event->id)) {
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
                                        $q1->where([["integer", "=", $xzsuic_classificator->event_classificator->id]]);
                                    })
                                    ->whereHas("configs", function ($q1) use ($xzsuic_classificator, $tournament_classificators_before_this, $rule) {
                                        $q1->where([
                                            ["key", "=", "event_classificator_rule_id"],
                                        ]);
                                        $q1->where([["integer", "=", $rule->id]]);
                                    })
                                    ->where([
                                        ["enxadrista_id", "=", $classificacao->enxadrista_id]
                                    ])
                                    ->count() > 0
                                ) {
                                    $this->log[] = date("d/m/Y H:i:s") . " - Já foi classificado por este mesmo evento e regra.";
                                    $classification_found = true;
                                } elseif (
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
                                        $q1->where([["integer", "!=", $rule->id], ["integer", "!=", null]]);
                                    })
                                    ->where([
                                        ["enxadrista_id", "=", $classificacao->enxadrista_id]
                                    ])
                                    ->count() > 0
                                ) {
                                    $this->log[] = date("d/m/Y H:i:s") . " - Já foi classificado por este mesmo evento mas não pela mesma regra.";
                                    $this->log[] = date("d/m/Y H:i:s") . " - Avaliar a preferência da regra.";

                                    $inscricao_to_check = Inscricao::whereHas("torneio", function ($q1) use ($xzsuic_classificator) {
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
                                        $q1->where([["integer", "!=", $rule->id], ["integer", "!=", null]]);
                                    })
                                    ->where([
                                        ["enxadrista_id", "=", $classificacao->enxadrista_id]
                                    ])
                                    ->first();

                                    $rule_id = $inscricao_to_check->getConfig("event_classificator_rule_id");
                                    $rule_to_check = EventClassificateRule::where([["id", "=", $rule_id]])->first();
                                    if($rule_to_check){
                                        // Minor number - More Priority
                                        $rule_priority = ClassificationTypeRule::getOrderPriority($rule->type);
                                        $rule_to_check_priority = ClassificationTypeRule::getOrderPriority($rule_to_check->type);

                                        if($rule_priority < $rule_to_check_priority){
                                            $this->log[] = date("d/m/Y H:i:s") . " - REGRA PRIORITÁRIA - Trocando regra.";

                                            $inscricao_to_check->setConfig("event_classificator_id", ConfigType::Integer, $xzsuic_classificator->event_classificator->id);
                                            $inscricao_to_check->setConfig("event_classificator_rule_id", ConfigType::Integer, $rule->id);
                                            $inscricao_to_check->inscricao_from = $classificacao->id;
                                            $inscricao_to_check->save();

                                            $classification_found = true;
                                        } elseif ($rule_priority == $rule_to_check_priority) {
                                            $this->log[] = date("d/m/Y H:i:s") . " - REGRA IGUAL - Avaliando se a regra atual foi inserida antes da outra.";
                                            if($rule->id > $rule_to_check->id){
                                            $this->log[] = date("d/m/Y H:i:s") . " - REGRA IGUAL - REGRA INSERIDA ANTES - Trocando regra.";
                                                $inscricao_to_check->setConfig("event_classificator_id", ConfigType::Integer, $xzsuic_classificator->event_classificator->id);
                                                $inscricao_to_check->setConfig("event_classificator_rule_id", ConfigType::Integer, $rule->id);
                                                $inscricao_to_check->inscricao_from = $classificacao->id;
                                                $inscricao_to_check->save();

                                                $classification_found = true;
                                            }else{
                                                $this->log[] = date("d/m/Y H:i:s") . " - REGRA IGUAL - REGRA INSERIDA DEPOIS - Mantendo regra.";
                                            }
                                        }else{
                                            $this->log[] = date("d/m/Y H:i:s") . " - REGRA COM MENOR PRIORIDADE - Mantendo regra.";
                                        }
                                    }
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
                                        $category = $this->getCategory($xzsuic_classificator->event, $classificacao->enxadrista);
                                        if (!$category) {
                                            $this->log[] = date("d/m/Y H:i:s") . " - Não há categoria para classificar este enxadrista - Ignorando.";
                                            continue;
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
                                            if ($inscricao_nova->save()) {

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

                                                if ($inscricao_nova->enxadrista->email && !$xzsuic_classificator->event->hasConfig("classificator_not_send_email")) {
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
                                                $this->log[] = date("d/m/Y H:i:s") . " - ERRO!!! Enxadrista JÁ inscrito!";
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
                                            })
                                            ->where([
                                                ["enxadrista_id", "=", $classificacao->enxadrista_id]
                                            ])
                                            ->first();

                                        $inscricao_on_event->setConfig("event_classificator_id", ConfigType::Integer, $xzsuic_classificator->event_classificator->id);
                                        $inscricao_on_event->setConfig("event_classificator_rule_id", ConfigType::Integer, $rule->id);
                                        $inscricao_on_event->inscricao_from = $classificacao->id;
                                        $inscricao_on_event->save();
                                    }
                                }
                            } else {
                                $this->log[] = date("d/m/Y H:i:s") . " - Já foi classificado por outro evento ou manualmente.";
                            }
                        } else {
                            $this->log[] = date("d/m/Y H:i:s") . " - ENXADRISTA NÃO INSCRITO - NÃO possui Direto a Vaga.";
                        }
                    }
                    $this->log[] = date("d/m/Y H:i:s") . " - FIM PROCESSAMENTO CATEGORIA.";
                }
            }
    }

    public function process_place_by_quantity_rules($event, $xzsuic_classificator)
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
            $this->process_place_by_quantity($event, $xzsuic_classificator, $rule);
        }

        $this->log[] = date("d/m/Y H:i:s") . " - Fim do Processo - Tipo de Classificador: " . $classificator_type["name"];
    }
    public function process_place_by_quantity($event, $xzsuic_classificator,$rule, $is_default = false, $is_default_not_classificated =false, $is_default_confirmed = false)
    {
        $type = ClassificationTypeRule::PLACE_BY_QUANTITY;
        $classificator_type = ClassificationTypeRule::get($type);

            if ($rule->is_absolute) {
                $this->log[] = date("d/m/Y H:i:s") . " - Regra #{$rule->id} - Uma vaga a cada: {$rule->value}";
            } else {
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
                    ])
                    ->whereNotNull("posicao")
                    ->orderBy("posicao", "ASC")
                    ->count();

                $total_places = $registrations_valid_total / $rule->value;
                $total_places = intval($total_places);

                // Se absoluto, não permite usar fração.
                if (!$rule->is_absolute) {
                    if ($total_places < $registrations_valid_total / $rule->value) {
                        $total_places++;
                    }
                }
                $this->log[] = date("d/m/Y H:i:s") . " - Total de Inscritos Válidos: {$registrations_valid_total}.";
                $this->log[] = date("d/m/Y H:i:s") . " - Total de Vagas: {$total_places}.";

                $total_registrations = 0;

                $tournament_classificators_before_this = $this->getTournamentsBeforeThis($xzsuic_classificator);
                $this->log[] = date("d/m/Y H:i:s") . " - Torneios anteriores a este: " . implode(",", $tournament_classificators_before_this);

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
                            ->where(function ($q0) use ($xzsuic_classificator, $tournament_classificators_before_this, $rule) {
                                $q0->whereHas("configs", function ($q1) use ($xzsuic_classificator, $tournament_classificators_before_this) {
                                    $q1->where([
                                        ["key", "=", "event_classificator_id"],
                                    ]);
                                    $q1->whereIn("integer", $tournament_classificators_before_this);
                                })
                                    ->whereHas("configs", function ($q1) use ($xzsuic_classificator, $tournament_classificators_before_this, $rule) {
                                        $q1->where([
                                            ["key", "=", "event_classificator_rule_id"],
                                        ]);
                                        $q1->where([["integer", "=", $rule->id]]);
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
                                    $q1->where([["integer", "=", $rule->id]]);
                                })
                                ->where([
                                    ["enxadrista_id", "=", $classificacao->enxadrista_id]
                                ])
                                ->count() > 0
                            ) {
                                $this->log[] = date("d/m/Y H:i:s") . " - Já foi classificado por este mesmo evento.";
                                $total_registrations++;
                            } elseif (
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
                                    $q1->where([["integer", "!=", $rule->id], ["integer", "!=", null]]);
                                })
                                ->where([
                                    ["enxadrista_id", "=", $classificacao->enxadrista_id]
                                ])
                                ->count() > 0
                            ) {
                                $this->log[] = date("d/m/Y H:i:s") . " - Já foi classificado por este mesmo evento mas não pela mesma regra.";
                                $this->log[] = date("d/m/Y H:i:s") . " - Avaliar a preferência da regra.";

                                $inscricao_to_check = Inscricao::whereHas("torneio", function ($q1) use ($xzsuic_classificator) {
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
                                        $q1->where([["integer", "!=", $rule->id], ["integer", "!=", null]]);
                                    })
                                    ->where([
                                        ["enxadrista_id", "=", $classificacao->enxadrista_id]
                                    ])
                                    ->first();

                                $rule_id = $inscricao_to_check->getConfig("event_classificator_rule_id");
                                $rule_to_check = EventClassificateRule::where([["id", "=", $rule_id]])->first();
                                if ($rule_to_check) {
                                    // Minor number - More Priority
                                    $rule_priority = ClassificationTypeRule::getOrderPriority($rule->type);
                                    $rule_to_check_priority = ClassificationTypeRule::getOrderPriority($rule_to_check->type);

                                    if ($rule_priority < $rule_to_check_priority) {
                                        $this->log[] = date("d/m/Y H:i:s") . " - REGRA PRIORITÁRIA - Trocando regra.";

                                        $inscricao_to_check->setConfig("event_classificator_id", ConfigType::Integer, $xzsuic_classificator->event_classificator->id);
                                        $inscricao_to_check->setConfig("event_classificator_rule_id", ConfigType::Integer, $rule->id);
                                        $inscricao_to_check->inscricao_from = $classificacao->id;
                                        $inscricao_to_check->save();

                                        $total_registrations++;
                                    } elseif ($rule_priority == $rule_to_check_priority) {
                                        $this->log[] = date("d/m/Y H:i:s") . " - REGRA IGUAL - Avaliando se a regra atual foi inserida antes da outra.";
                                        if ($rule->id > $rule_to_check->id) {
                                            $this->log[] = date("d/m/Y H:i:s") . " - REGRA IGUAL - REGRA INSERIDA ANTES - Trocando regra.";
                                            $inscricao_to_check->setConfig("event_classificator_id", ConfigType::Integer, $xzsuic_classificator->event_classificator->id);
                                            $inscricao_to_check->setConfig("event_classificator_rule_id", ConfigType::Integer, $rule->id);
                                            $inscricao_to_check->inscricao_from = $classificacao->id;
                                            $inscricao_to_check->save();

                                            $total_registrations++;
                                        } else {
                                            $this->log[] = date("d/m/Y H:i:s") . " - REGRA IGUAL - REGRA INSERIDA DEPOIS - Mantendo regra.";
                                        }
                                    } else {
                                        $this->log[] = date("d/m/Y H:i:s") . " - REGRA COM MENOR PRIORIDADE - Mantendo regra.";
                                    }
                                }
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
                                    $category = $this->getCategory($xzsuic_classificator->event, $classificacao->enxadrista, $category);
                                    if (!$category) {
                                        $this->log[] = date("d/m/Y H:i:s") . " - Não há categoria para classificar este enxadrista - Ignorando.";
                                        continue;
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
                                        if ($inscricao_nova->save()) {

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

                                            if ($inscricao_nova->enxadrista->email && !$xzsuic_classificator->event->hasConfig("classificator_not_send_email")) {
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
                                            $this->log[] = date("d/m/Y H:i:s") . " - ERRO!!! Enxadrista JÁ inscrito!";
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
                                        })

                                        ->where([
                                            ["enxadrista_id", "=", $classificacao->enxadrista_id]
                                        ])
                                        ->first();

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


    public function process_classificate_by_start_position_rules($event, $xzsuic_classificator)
    {
        $type = ClassificationTypeRule::CLASSIFICATE_BY_START_POSITION;
        $classificator_type = ClassificationTypeRule::get($type);
        $this->log[] = date("d/m/Y H:i:s") . " - Início do Processo - Tipo de Classificador: " . $classificator_type["name"];

        $type_count = $xzsuic_classificator->rules()->where([["type", "=", $type]])->count();

        if ($type_count == 0) {
            $this->log[] = date("d/m/Y H:i:s") . " - Não há regras para processar deste tipo.";
        } else {
            $this->log[] = date("d/m/Y H:i:s") . " - Há {$type_count} regra(s) para processar deste tipo.";
        }

        foreach ($xzsuic_classificator->rules()->where([["type", "=", $type]])->orderBy("id", "ASC")->get() as $rule) {
            $this->process_classificate_by_start_position($event, $xzsuic_classificator, $rule);
        }

        $this->log[] = date("d/m/Y H:i:s") . " - Fim do Processo - Tipo de Classificador: " . $classificator_type["name"];
    }
    public function process_classificate_by_start_position($event, $xzsuic_classificator,$rule, $is_default = false, $is_default_not_classificated =false, $is_default_confirmed = false)
    {
        $type = ClassificationTypeRule::CLASSIFICATE_BY_START_POSITION;
        $classificator_type = ClassificationTypeRule::get($type);

            $this->log[] = date("d/m/Y H:i:s") . " - Regra #{$rule->id} - Posição na Classificação Inicial: {$rule->value}";


            if ($rule->value == 0 && !$is_default && !$is_default_not_classificated && !$is_default_confirmed) {
                $this->log[] = date("d/m/Y H:i:s") . " - Regra #{$rule->id} - Valor 0 - Ignorando regra (apenas é atendida se Padrão, Padrão para Confirmados ou Padrão para Não Classificados).";

                return;
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

                $check_rules_result = $this->checkRules($category_classificator, $rule);

                if (!$check_rules_result["result"]) {
                    $this->log[] = date("d/m/Y H:i:s") . " - Regra #{$rule->id} - Regras adicionais não atendidas. Mensagem: {$check_rules_result["message"]}";
                    $this->log[] = date("d/m/Y H:i:s") . " - Regra #{$rule->id} - Regra ignorada.";

                    continue;
                } else {
                    $this->log[] = date("d/m/Y H:i:s") . " - Regra #{$rule->id} - Regras adicionais ATENDIDAS. (Total Inscritos na categoria: {$check_rules_result["total_registrations"]} - Evento {$rule->event_id})";
                }

                $category = $category_classificator->event_classificates()->whereHas("category", function ($q2) use ($xzsuic_classificator) {
                    $q2->whereHas("eventos", function ($q3) use ($xzsuic_classificator) {
                        $q3->where([["evento_id", "=", $xzsuic_classificator->event->id]]);
                    });
                })->first()->category;

                $this->log[] = date("d/m/Y H:i:s") . " - INÍCIO PROCESSAMENTO CATEGORIA.";
                $this->log[] = date("d/m/Y H:i:s") . " - Categoria Classificadora: #{$category_classificator->id} - {$category_classificator->name}.";
                $this->log[] = date("d/m/Y H:i:s") . " - Categoria Destino: #{$category->id} - {$category->name}.";

                $classification_found = false;

                $tournament_classificators_before_this = $this->getTournamentsBeforeThis($xzsuic_classificator);
                $this->log[] = date("d/m/Y H:i:s") . " - Torneios anteriores a este: " . implode(",", $tournament_classificators_before_this);

                $i = 1;
                foreach ($category_classificator->getStartingRank($xzsuic_classificator->event_classificator->id) as $item) {
                    Log::debug(json_encode($item));
                    $classificacao = $item["registration"];
                    if ($rule->value != $item["position"] && !$is_default && !$is_default_not_classificated && !$is_default_confirmed) {
                        $this->log[] = date("d/m/Y H:i:s") . " - Posição: #{$item["position"]}/{$rule->value} - Não atendido para esta regra - Enxadrista #{$classificacao->enxadrista->id} - {$classificacao->enxadrista->name}.";
                        continue;
                    } elseif($is_default_confirmed && $item["registration"]->confirmado == 0) {
                        $this->log[] = date("d/m/Y H:i:s") . " - Posição: #{$item["position"]}/{$rule->value} - Não atendido para esta regra (PADRÃO PARA CONFIRMADOS - NÃO CONFIRMADO) - Enxadrista #{$classificacao->enxadrista->id} - {$classificacao->enxadrista->name}.";
                        continue;
                    } else {
                        $this->log[] = date("d/m/Y H:i:s") . " - Posição: #{$item["position"]}/{$rule->value} - ATENDE A REGRA - Enxadrista #{$classificacao->enxadrista->id} - {$classificacao->enxadrista->name}.";
                        if($is_default_confirmed){
                            $this->log[] = date("d/m/Y H:i:s") . " - Confirmado: {$item["registration"]->confirmado}.";
                        }
                    }
                    if (!$classification_found || $is_default || !$is_default_not_classificated || ($is_default_confirmed && $classificacao->confirmado)) {
                        $this->log[] = date("d/m/Y H:i:s") . " - Posição: #{$item["position"]} - Enxadrista #{$classificacao->enxadrista->id} - {$classificacao->enxadrista->name}.";
                        if($is_default_not_classificated){
                            if(!$this->checkIfNotClassificatedByAllEventRules($event, $classificacao)){
                                $this->log[] = date("d/m/Y H:i:s") . " - Posição: #{$item["position"]}/{$rule->value} - JÁ CLASSIFICADO EM OUTRA REGRA - Ignorando - Enxadrista #{$classificacao->enxadrista->id} - {$classificacao->enxadrista->name}.";

                                continue;
                            }
                        }
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
                                    $q1->where([["integer", "=", $rule->id]]);
                                })
                                ->where([
                                    ["enxadrista_id", "=", $classificacao->enxadrista_id]
                                ])
                                ->count() > 0
                            ) {
                                $this->log[] = date("d/m/Y H:i:s") . " - Já foi classificado por este mesmo evento.";
                                $classification_found = true;
                            } elseif (
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
                                    $q1->where([["integer", "!=", $rule->id], ["integer", "!=", null]]);
                                })
                                ->where([
                                    ["enxadrista_id", "=", $classificacao->enxadrista_id]
                                ])
                                ->count() > 0
                            ) {
                                $this->log[] = date("d/m/Y H:i:s") . " - Já foi classificado por este mesmo evento mas não pela mesma regra.";
                                if ($is_default_not_classificated) {
                                    $this->log[] = date("d/m/Y H:i:s") . " - PADRÃO PARA NÃO CLASSIFICADOS - Ignorando inscrição.";
                                    continue;
                                }
                                if ($is_default) {
                                    $this->log[] = date("d/m/Y H:i:s") . " - PADRÃO - Ignorando inscrição.";
                                    continue;
                                }
                                if ($is_default_confirmed) {
                                    $this->log[] = date("d/m/Y H:i:s") . " - PADRÃO PARA CONFIRMADOS - Ignorando inscrição.";
                                    continue;
                                }
                                $this->log[] = date("d/m/Y H:i:s") . " - Avaliar a preferência da regra.";

                                $inscricao_to_check = Inscricao::whereHas("torneio", function ($q1) use ($xzsuic_classificator) {
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
                                        $q1->where([["integer", "!=", $rule->id], ["integer", "!=", null]]);
                                    })
                                    ->where([
                                        ["enxadrista_id", "=", $classificacao->enxadrista_id]
                                    ])
                                    ->first();

                                $rule_id = $inscricao_to_check->getConfig("event_classificator_rule_id");
                                $rule_to_check = EventClassificateRule::where([["id", "=", $rule_id]])->first();
                                if ($rule_to_check) {
                                    // Minor number - More Priority
                                    $rule_priority = ClassificationTypeRule::getOrderPriority($rule->type);
                                    $rule_to_check_priority = ClassificationTypeRule::getOrderPriority($rule_to_check->type);

                                    if ($rule_priority < $rule_to_check_priority) {
                                        $this->log[] = date("d/m/Y H:i:s") . " - REGRA PRIORITÁRIA - Trocando regra.";

                                        $inscricao_to_check->setConfig("event_classificator_id", ConfigType::Integer, $xzsuic_classificator->event_classificator->id);
                                        $inscricao_to_check->setConfig("event_classificator_rule_id", ConfigType::Integer, $rule->id);
                                        $inscricao_to_check->inscricao_from = $classificacao->id;
                                        $inscricao_to_check->save();

                                        $classification_found = true;
                                    } elseif ($rule_priority == $rule_to_check_priority) {
                                        $this->log[] = date("d/m/Y H:i:s") . " - REGRA IGUAL - Avaliando se a regra atual foi inserida antes da outra.";
                                        if ($rule->id > $rule_to_check->id) {
                                            $this->log[] = date("d/m/Y H:i:s") . " - REGRA IGUAL - REGRA INSERIDA ANTES - Trocando regra.";
                                            $inscricao_to_check->setConfig("event_classificator_id", ConfigType::Integer, $xzsuic_classificator->event_classificator->id);
                                            $inscricao_to_check->setConfig("event_classificator_rule_id", ConfigType::Integer, $rule->id);
                                            $inscricao_to_check->inscricao_from = $classificacao->id;
                                            $inscricao_to_check->save();

                                            $classification_found = true;
                                        } else {
                                            $this->log[] = date("d/m/Y H:i:s") . " - REGRA IGUAL - REGRA INSERIDA DEPOIS - Mantendo regra.";
                                        }
                                    } else {
                                        $this->log[] = date("d/m/Y H:i:s") . " - REGRA COM MENOR PRIORIDADE - Mantendo regra.";
                                    }
                                }
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
                                    $category = $this->getCategory($xzsuic_classificator->event, $classificacao->enxadrista, $category);
                                    if (!$category) {
                                        $this->log[] = date("d/m/Y H:i:s") . " - Não há categoria para classificar este enxadrista - Ignorando.";
                                        continue;
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
                                        if ($inscricao_nova->save()) {

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

                                            if ($inscricao_nova->enxadrista->email && !$xzsuic_classificator->event->hasConfig("classificator_not_send_email")) {
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
                                            $this->log[] = date("d/m/Y H:i:s") . " - ERRO!!! Enxadrista JÁ inscrito!";
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
                                        })
                                        ->where([
                                            ["enxadrista_id", "=", $classificacao->enxadrista_id]
                                        ])
                                        ->first();

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


        $this->log[] = date("d/m/Y H:i:s") . " - Fim do Processo - Tipo de Classificador: " . $classificator_type["name"];
    }



    public function process_default_rules($event, $xzsuic_classificator)
    {
        $this->log[] = date("d/m/Y H:i:s") . " - Início do Processo - Regras PADRÃO";
        foreach ($xzsuic_classificator->rules->all() as $rule) {
            if ($rule->hasConfig(ClassificationTypeRuleConfig::DEFAULT)) {
                switch ($rule->type) {
                    case ClassificationTypeRule::POSITION:
                        $this->process_position($event, $xzsuic_classificator, $rule, true);
                        break;
                    case ClassificationTypeRule::POSITION_ABSOLUTE:
                        $this->process_position_absolute($event, $xzsuic_classificator, $rule, true);
                        break;
                    case ClassificationTypeRule::PRE_CLASSIFICATE:
                        $this->process_pre_classificate($event, $xzsuic_classificator, $rule, true);
                        break;
                    case ClassificationTypeRule::PLACE_BY_QUANTITY:
                        $this->process_place_by_quantity($event, $xzsuic_classificator, $rule, true);
                        break;
                    case ClassificationTypeRule::CLASSIFICATE_BY_START_POSITION:
                        $this->process_classificate_by_start_position($event, $xzsuic_classificator, $rule, true);
                        break;
                }
            }
        }
        $this->log[] = date("d/m/Y H:i:s") . " - Fim do Processo - Regras PADRÃO";
    }
    public function process_default_confirmed_rules($event, $xzsuic_classificator)
    {
        $this->log[] = date("d/m/Y H:i:s") . " - Início do Processo - Regras PADRÃO PARA CONFIRMADOS";
        foreach ($xzsuic_classificator->rules->all() as $rule) {
            if ($rule->hasConfig(ClassificationTypeRuleConfig::DEFAULT_CONFIRMED)) {
                switch ($rule->type) {
                    case ClassificationTypeRule::POSITION:
                        $this->process_position($event, $xzsuic_classificator, $rule, false, false, true);
                        break;
                    case ClassificationTypeRule::POSITION_ABSOLUTE:
                        $this->process_position_absolute($event, $xzsuic_classificator, $rule, false, false, true);
                        break;
                    case ClassificationTypeRule::PRE_CLASSIFICATE:
                        $this->process_pre_classificate($event, $xzsuic_classificator, $rule, false, false, true);
                        break;
                    case ClassificationTypeRule::PLACE_BY_QUANTITY:
                        $this->process_place_by_quantity($event, $xzsuic_classificator, $rule, false, false, true);
                        break;
                    case ClassificationTypeRule::CLASSIFICATE_BY_START_POSITION:
                        $this->process_classificate_by_start_position($event, $xzsuic_classificator, $rule, false, false,true);
                        break;
                }
            }
        }
        $this->log[] = date("d/m/Y H:i:s") . " - Fim do Processo - Regras PADRÃO PARA CONFIRMADOS";
    }
    public function process_default_not_classificated_rules($event, $xzsuic_classificator)
    {
        $this->log[] = date("d/m/Y H:i:s") . " - Início do Processo - Regras PADRÃO PARA NÃO CLASSIFICADOS";
        foreach ($xzsuic_classificator->rules->all() as $rule) {
            if ($rule->hasConfig(ClassificationTypeRuleConfig::DEFAULT_NOT_CLASSIFICATED)) {

                switch ($rule->type) {
                    case ClassificationTypeRule::POSITION:
                        $this->process_position($event, $xzsuic_classificator, $rule, true, true);
                        break;
                    case ClassificationTypeRule::POSITION_ABSOLUTE:
                        $this->process_position_absolute($event, $xzsuic_classificator, $rule, true, true);
                        break;
                    case ClassificationTypeRule::PRE_CLASSIFICATE:
                        $this->process_pre_classificate($event, $xzsuic_classificator, $rule, true, true);
                        break;
                    case ClassificationTypeRule::PLACE_BY_QUANTITY:
                        $this->process_place_by_quantity($event, $xzsuic_classificator, $rule, true, true);
                        break;
                    case ClassificationTypeRule::CLASSIFICATE_BY_START_POSITION:
                        $this->process_classificate_by_start_position($event, $xzsuic_classificator, $rule, true, true);
                        break;
                }
            }
        }
        $this->log[] = date("d/m/Y H:i:s") . " - Fim do Processo - Regras PADRÃO PARA NÃO CLASSIFICADOS";
    }


    public function getCategory($event, $enxadrista, $category = null){
        if($category){
            if ($category->idade_minima) {
                if ($enxadrista->howOldForEvento($event->id) < $category->idade_minima) {
                    $this->log[] = date("d/m/Y H:i:s") . " - Categoria inválida - Obtendo nova categoria.";
                    $category = $this->getNewCategory($event, $enxadrista);
                    if ($category) {
                        $this->log[] = date("d/m/Y H:i:s") . " - Nova Categoria Encontrada - #{$category->id} - {$category->name}.";
                    } else {
                        $this->log[] = date("d/m/Y H:i:s") . " - Não há categoria para classificar este enxadrista - Ignorando.";
                        return false;
                    }
                }
            }
            if ($category->idade_maxima) {
                if ($enxadrista->howOldForEvento($event->id) > $category->idade_maxima) {
                    $this->log[] = date("d/m/Y H:i:s") . " - Categoria inválida - Obtendo nova categoria.";
                    $category = $this->getNewCategory($event, $enxadrista);
                    if ($category) {
                        $this->log[] = date("d/m/Y H:i:s") . " - Nova Categoria Encontrada - #{$category->id} - {$category->name}.";
                    } else {
                        $this->log[] = date("d/m/Y H:i:s") . " - Não há categoria para classificar este enxadrista - Ignorando.";
                        return false;
                    }
                }
            }
        }else{
            $this->log[] = date("d/m/Y H:i:s") . " - Não possui categoria - Obtendo categoria.";
            $category = $this->getNewCategory($event, $enxadrista);
            if ($category) {
                $this->log[] = date("d/m/Y H:i:s") . " - Nova Categoria Encontrada - #{$category->id} - {$category->name}.";
            } else {
                $this->log[] = date("d/m/Y H:i:s") . " - Não há categoria para classificar este enxadrista - Ignorando.";
                return false;
            }
        }

        return $category;
    }

    public function getNewCategory(Evento $event, $enxadrista){
        $this->log[] = date("d/m/Y H:i:s") . " - getNewCategory.";
        if ($event->hasCategoryForEnxadrista($enxadrista)) {
            $this->log[] = date("d/m/Y H:i:s") . " - getNewCategory - Found.";
            // $this->log[] = date("d/m/Y H:i:s") . " - getNewCategory - ".json_encode($event->getCategoryForEnxadrista($enxadrista)->categoria);
            return $event->getCategoryForEnxadrista($enxadrista)->categoria;
        }
        $this->log[] = date("d/m/Y H:i:s") . " - getNewCategory - Not found - event {$event->id} - ".$enxadrista->howOldForEvento($event->getYear())." yo. - event year:".$event->getYear()." - count: ".count((array) $event->getCategoryForEnxadrista($enxadrista)).".";

        return null;
    }

    public function getTournamentsBeforeThis($xzsuic_classificator){
        $tournament_classificators_before_this = array();

        foreach ($xzsuic_classificator->event->event_classificators()->whereHas("event_classificator", function ($q1) use ($xzsuic_classificator) {
            $q1->where([
                ["id", "!=", $xzsuic_classificator->event_classificator->id],
                ["data_inicio", "<=", $xzsuic_classificator->event_classificator->data_inicio]
            ]);
        })->get() as $event_classificator) {
            $tournament_classificators_before_this[] = $event_classificator->event_classificator->id;
        }

        return $tournament_classificators_before_this;
    }

    public function checkRules($category_classificator, $rule){
        $result = array("result" => true);
        $total_registrations = count($category_classificator->getStartingRank($rule->event_classificate->event_classificator->id));
        $result["total_registrations"] = $total_registrations;
        foreach(ClassificationTypeRuleConfig::list() as $key => $type_rule){
            if($rule->hasConfig($key)){
                $rule_rule = $rule->getConfig($key, true);

                switch($key) {
                    case ClassificationTypeRuleConfig::REGISTRATIONS_MAX:
                        if ($total_registrations > $rule_rule) {
                            $result["result"] = false;
                            $result["message"] = "Regra de máximo de inscritos ultrapassada. Máximo da Regra: {$rule_rule} - Total de Inscrições: {$total_registrations}";

                            return $result;
                        }
                        break;
                    case ClassificationTypeRuleConfig::REGISTRATIONS_MIN:
                        if ($total_registrations < $rule_rule) {
                            $result["result"] = false;
                            $result["message"] = "Regra de mínimo de inscritos não atingida. Mínimo da Regra: {$rule_rule} - Total de Inscrições: {$total_registrations}";

                            return $result;
                        }
                        break;
                }
            }
        }

        return $result;
    }

    public function checkIfNotClassificatedByAllEventRules($event, $inscricao){
        $this->log[] = date("d/m/Y H:i:s") . " - VALIDANDO SE O ENXADRISTA NÃO SE CLASSIFICOU AINDA.";

        foreach($event->event_classificates->all() as $event_classificator){
            foreach($event_classificator->rules->all() as $rule){
                foreach($inscricao->enxadrista->inscricoes()->whereHas("configs", function ($q1){
                    $q1->where([
                        ["key","=", "event_classificator_rule_id"]
                    ]);
                })->get() as $registration){
                        $this->log[] = date("d/m/Y H:i:s") . " - Inscricão {$registration->id} - ". $registration->getConfig("event_classificator_rule_id",true);
                    if ($registration->getConfig("event_classificator_rule_id", true) == $rule->id){
                        $this->log[] = date("d/m/Y H:i:s") . " - JÁ CLASSIFICADO - Regra #{$rule->id}.";
                        return false;
                    }
                }
            }
        }
        $this->log[] = date("d/m/Y H:i:s") . " - NÃO CLASSIFICADO AINDA.";

        return true;
    }
}

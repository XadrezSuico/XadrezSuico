<?php

namespace App\Http\Controllers\API\Event;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Evento;

class RegisterController extends Controller
{
    public function register($uuid,Request $request)
    {
        if($uuid){
            if(Evento::where([["uuid","=",$uuid]])->count() == 0){
                return response()->json(["ok"=>0,"error"=>1,"message"=>"Evento não encontrado.","httpcode"=>404],404);
            }
            $evento = Evento::where([["uuid","=",$uuid]])->first();

            if (
                !$request->has("accepts.regulation")
            ) {
                return response()->json(["ok" => 0, "error" => 1, "message" => "Você deve aceitar o regulamento do evento!", "result" => false]);
            } elseif (
                !$request->has("accepts.policy")
            ) {
                return response()->json(["ok" => 0, "error" => 1, "message" => "Você deve aceitar o termo de uso e política de privacidade da plataforma XadrezSuíço!", "result" => false]);
            } elseif (
                !$request->has("accepts.image")
            ) {
                return response()->json(["ok" => 0, "error" => 1, "message" => "Os direitos de imagem devem ser fornecidos para inscrição neste evento.", "result" => false]);
            } elseif (
                !$request->has("accepts.category")
            ) {
                return response()->json(["ok" => 0, "error" => 1, "message" => "Você deve verificar a categoria e marcar o campo 'Categoria conferida'.", "result" => false]);
            } elseif (
                !$request->input("accepts.regulation")
            ) {
                return response()->json(["ok" => 0, "error" => 1, "message" => "Você deve aceitar o regulamento do evento!", "result" => false]);
            } elseif (
                !$request->input("accepts.policy")
            ) {
                return response()->json(["ok" => 0, "error" => 1, "message" => "Você deve aceitar o termo de uso e política de privacidade da plataforma XadrezSuíço!", "result" => false]);
            } elseif (
                !$request->input("accepts.image")
            ) {
                return response()->json(["ok" => 0, "error" => 1, "message" => "Os direitos de imagem devem ser fornecidos para inscrição neste evento.", "result" => false]);
            } elseif (
                !$request->input("accepts.category")
            ) {
                return response()->json(["ok" => 0, "error" => 1, "message" => "Você deve verificar a categoria e marcar o campo 'Categoria conferida'.", "result" => false]);
            } elseif (
                !$request->has("player_id") ||
                !$request->has("category_id") ||
                !$request->has("city_id") ||
                !$request->has("event_id")
            ) {
                return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "result" => false]);
            } elseif (
                $request->input("player_id") == null || $request->input("player_id") == "" ||
                $request->input("category_id") == null || $request->input("category_id") == "" ||
                $request->input("city_id") == null || $request->input("city_id") == "" ||
                $request->input("event_id") == null || $request->input("event_id") == ""
            ) {
                return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "result" => false]);
            }

            $inscricao = new Inscricao;
            $torneio = null;

            if ($evento->inscricoes_encerradas(true)) {
                // if($user){
                //     if (
                //         !$user->hasPermissionGlobal() &&
                //         !$user->hasPermissionEventByPerfil($evento->id, [3, 4, 5]) &&
                //         !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [6,7])
                //     ) {
                //         return response()->json(["ok" => 0, "error" => 1, "message" => env("MENSAGEM_FIM_INSCRICOES", "O prazo de inscrições antecipadas se esgotou ou então o limite de inscritos se completou. As inscrições poderão ser feitas no local do evento conforme regulamento.")]);
                //     }
                // }else{
                    return response()->json(["ok" => 0, "error" => 1, "message" => env("MENSAGEM_FIM_INSCRICOES", "O prazo de inscrições antecipadas se esgotou ou então o limite de inscritos se completou. As inscrições poderão ser feitas no local do evento conforme regulamento.")]);
                // }
            }

            // if ($evento->e_inscricao_apenas_com_link) {
            //     if (!$evento->inscricaoLiberada($request->input("token"))) {
            //         return response()->json(["ok" => 0, "error" => 1, "message" => "A inscrição para este evento deve ser feita com o link de inscrições enviado (Inscrições Privadas)."]);
            //     }
            // }
            // foreach ($evento->campos_obrigatorios() as $campo) {
            //     if (
            //         !$request->has("campo_personalizado_" . $campo->id)
            //     ) {
            //         return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!<br/>Erro: Campo Personalizado 1.<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "registred" => 0]);
            //     } elseif (
            //         $request->input("campo_personalizado_" . $campo->id) == null ||
            //         $request->input("campo_personalizado_" . $campo->id) == "" ||
            //         $request->input("campo_personalizado_" . $campo->id) == "null"
            //     ) {
            //         return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!<br/>Erro: Campo Personalizado 2.<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "registred" => 0]);
            //     }
            // }

            foreach ($evento->torneios->all() as $Torneio) {
                foreach ($Torneio->categorias->all() as $categoria) {
                    if ($categoria->categoria_id == $request->input("categoria_id")) {
                        $torneio = $Torneio;
                    }
                }
            }
            if (!$torneio) {
                return response()->json(["ok" => 0, "error" => 1, "message" => "Ocorreu um erro inesperado de pesquisa de Torneio. Por favor, tente novamente mais tarde."]);
            }
            $temInscricao_count = $evento->torneios()->whereHas("inscricoes", function ($q) use ($request) {
                $q->where([["enxadrista_id", "=", $request->input("enxadrista_id")]]);
            })->count();
            $temInscricao = $evento->torneios()->whereHas("inscricoes", function ($q) use ($request) {
                $q->where([["enxadrista_id", "=", $request->input("enxadrista_id")]]);
            })->first();
            if ($temInscricao_count > 0) {
                $inscricao = Inscricao::where([["enxadrista_id", "=", $request->input("enxadrista_id")], ["torneio_id", "=", $temInscricao->id]])->first();
                return response()->json(["ok" => 0, "error" => 1, "message" => "Você já possui inscrição para este evento!<br/> Categoria: " . $inscricao->categoria->name . "<br/> Caso queira efetuar alguma alteração, favor enviar via email para " . env("EMAIL_ALTERACAO", "circuitoxadrezcascavel@gmail.com") . "."]);
            }

            $enxadrista = Enxadrista::find($request->input("enxadrista_id"));
            $categoria = Categoria::find($request->input("categoria_id"));
            if ($categoria) {
                if ($categoria->idade_minima) {
                    if (!($categoria->idade_minima <= $enxadrista->howOldForEvento($evento->getYear()))) {
                        return response()->json(["ok" => 0, "error" => 1, "message" => "Você não está apto a participar da categoria que você enviou! Motivo: Não possui idade mínima."]);
                    }
                }
                if ($categoria->idade_maxima) {
                    if (!($categoria->idade_maxima >= $enxadrista->howOldForEvento($evento->getYear()))) {
                        return response()->json(["ok" => 0, "error" => 1, "message" => "Você não está apto a participar da categoria que você enviou! Motivo: Idade ultrapassa a máxima."]);
                    }
                }
            }

            $inscricao->torneio_id = $torneio->id;
            $inscricao->enxadrista_id = $enxadrista->id;
            $inscricao->categoria_id = $categoria->id;
            $inscricao->cidade_id = $request->input("cidade_id");
            if ($request->has("clube_id")) {
                if ($request->input("clube_id") > 0) {
                    $inscricao->clube_id = $request->input("clube_id");
                }
            }
            $inscricao->regulamento_aceito = true;
            $inscricao->xadrezsuico_aceito = true;
            $inscricao->is_aceito_imagem = 1;

            if($user){
                if (
                    $user->hasPermissionGlobal() ||
                    $user->hasPermissionEventByPerfil($evento->id, [4, 5]) ||
                    $user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [6,7])
                ) {
                    if($request->has("inscricao_confirmada")){
                        $inscricao->confirmado = true;
                    }
                    if($request->has("atualizar_cadastro")){
                        $enxadrista->clube_id = $inscricao->clube_id;
                        $enxadrista->cidade_id = $inscricao->cidade_id;
                        $enxadrista->save();
                    }
                }
            }
            $inscricao->save();

            foreach ($evento->campos() as $campo) {
                if ($request->has("campo_personalizado_" . $campo->id)) {
                    if ($request->input("campo_personalizado_" . $campo->id) != "") {
                        if ($request->input("campo_personalizado_" . $campo->id) != NULL) {
                            if ($request->input("campo_personalizado_" . $campo->id) != "null") {
                                $opcao_inscricao = new CampoPersonalizadoOpcaoInscricao;
                                $opcao_inscricao->inscricao_id = $inscricao->id;
                                $opcao_inscricao->opcaos_id = $request->input("campo_personalizado_" . $campo->id);
                                $opcao_inscricao->campo_personalizados_id = $campo->id;
                                $opcao_inscricao->save();
                            }
                        }
                    }
                }
            }

            if($evento->tipo_rating){
                $rating_count = $inscricao->enxadrista->ratings()->where([
                    ["tipo_ratings_id","=",$evento->tipo_rating->id]
                ])->count();
                if($rating_count == 0){
                    $rating_inicial = $enxadrista->ratingParaEvento($evento->id);

                    $rating = new Rating;
                    $rating->tipo_ratings_id = $evento->tipo_rating->tipo_rating->id;
                    $rating->enxadrista_id = $enxadrista->id;
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

            if ($enxadrista->email) {
                // EMAIL PARA O ENXADRISTA SOLICITANTE
                // if($evento->is_lichess_integration){
                //     $text = "Olá " . $enxadrista->name . "!<br/>";
                //     $text .= "Parabéns! você iniciou a inscrição no Evento '" . $evento->name . "'.<br/>";
                //     $text .= $evento->orientacao_pos_inscricao . "<hr/>";
                //     $text .= "Informações:<br/>";
                //     $text .= "ID da Inscrição: " . $inscricao->id . "<br/>";
                //     $text .= "ID do Cadastro de Enxadrista: " . $inscricao->enxadrista->id . "<br/>";
                //     $text .= "Cidade: " . $inscricao->cidade->name . "<br/>";
                //     $text .= "Clube: " . (($inscricao->clube) ? $inscricao->clube->name : "Sem Clube") . "<br/>";
                //     $text .= "Categoria: " . $inscricao->categoria->name . "<hr/>";
                // }else{
                //     $text = "Olá " . $enxadrista->name . "!<br/>";
                //     $text .= "Você está recebendo este email para confirmar a inscrição no Evento '" . $evento->name . "'.<br/>";
                //     $text .= "Informações:<br/>";
                //     $text .= "ID da Inscrição: " . $inscricao->id . "<br/>";
                //     $text .= "ID do Cadastro de Enxadrista: " . $inscricao->enxadrista->id . "<br/>";
                //     $text .= "Cidade: " . $inscricao->cidade->name . "<br/>";
                //     $text .= "Clube: " . (($inscricao->clube) ? $inscricao->clube->name : "Sem Clube") . "<br/>";
                //     $text .= "Categoria: " . $inscricao->categoria->name . "<hr/>";
                //     if($evento->orientacao_pos_inscricao != NULL){
                //         $text .= "<strong>Orientações Pós-Inscrição:</strong><br/>";
                //         $text .= $evento->orientacao_pos_inscricao . "<hr/>";
                //     }
                // }
                // EmailController::scheduleEmail(
                //     $enxadrista->email,
                //     $evento->name . " - Inscrição Recebida - Enxadrista: " . $enxadrista->name,
                //     $text,
                //     $enxadrista
                // );
                EmailController::schedule(
                    $enxadrista->email,
                    $inscricao,
                    EmailType::ConfirmacaoInscricao,
                    $enxadrista
                );
            }

            if ($inscricao->id > 0) {
                if($inscricao->torneio->evento->is_lichess_integration){
                return response()->json(["ok" => 1, "error" => 0, "is_lichess_integration" => 1, "lichess_process_link" => $inscricao->getLichessProcessLink()]);
                }
                return response()->json(["ok" => 1, "error" => 0, "is_lichess_integration" => 0]);
            } else {
                return response()->json(["ok" => 0, "error" => 1, "message" => "Um erro inesperado aconteceu. Por favor, tente novamente mais tarde."]);
            }
        }
        return response()->json(["ok"=>0,"error"=>1,"message"=>"Evento não encontrado","httpcode"=>404],404);
    }
}

<?php

namespace App\Http\Controllers\API\Event;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\EmailController;

use App\Enum\EmailType;

use App\Evento;
use App\Inscricao;
use App\Enxadrista;
use App\Categoria;
use App\CampoPersonalizadoOpcaoInscricao;

use App\Rating;
use App\MovimentacaoRating;

use Log;

class RegisterController extends Controller
{
    public function register($uuid,Request $request)
    {
        Log::debug(json_encode($request->all()));
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
                !$request->has("city_id")
            ) {
                return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "result" => false]);
            } elseif (
                $request->input("player_id") == null || $request->input("player_id") == "" ||
                $request->input("category_id") == null || $request->input("category_id") == "" ||
                $request->input("city_id") == null || $request->input("city_id") == ""
            ) {
                return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "result" => false]);
            }

            $inscricao = new Inscricao;

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

                    if($evento->is_inscricoes_bloqueadas){
                        $message = "Inscrições Encerradas - O evento não permite inscrições no momento.";
                    }elseif($evento->inscricoes_encerradas(false,true)){
                        $message = "Inscrições Encerradas - Prazo para Inscrição Encerrado.";
                    }elseif($evento->estaLotado()){
                        $message = "Inscrições Encerradas - O evento chegou ao limite de inscritos.";
                    }else{
                        $message = "Inscrições Encerradas.";
                    }
                    return response()->json(["ok" => 0, "error" => 1, "message" => $message]);
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
            if ($evento->torneios()->whereHas("categorias",function($q1) use ($request){
                $q1->where([["categoria_id","=",$request->input("category_id")]]);
            })->count() == 0) {
                return response()->json(["ok" => 0, "error" => 1, "message" => "Ocorreu um erro inesperado de pesquisa de Torneio. Por favor, tente novamente mais tarde."]);
            }

            $torneio = $evento->torneios()->whereHas("categorias",function($q1) use ($request){
                $q1->where([["categoria_id","=",$request->input("category_id")]]);
            })->first();

            if ($evento->torneios()->whereHas("inscricoes", function ($q) use ($request) {
                    $q->where([["enxadrista_id", "=", $request->input("player_id")]]);
                })->count() > 0) {
                $temInscricao = $evento->torneios()->whereHas("inscricoes", function ($q) use ($request) {
                    $q->where([["enxadrista_id", "=", $request->input("player_id")]]);
                })->first();

                $inscricao = Inscricao::where([["enxadrista_id", "=", $request->input("player_id")], ["torneio_id", "=", $temInscricao->id]])->first();
                return response()->json(["ok" => 0, "error" => 1, "message" => "Você já possui inscrição para este evento!<br/> Categoria: " . $inscricao->categoria->name . "<br/> Caso queira efetuar alguma alteração, favor enviar via email para " . env("EMAIL_ALTERACAO", "circuitoxadrezcascavel@gmail.com") . "."]);
            }

            $enxadrista = Enxadrista::find($request->input("player_id"));
            $categoria = Categoria::find($request->input("category_id"));
            if ($categoria) {
                if ($categoria->idade_minima) {
                    if (!($categoria->idade_minima <= $enxadrista->howOldForEvento($evento->getYear()))) {
                        return response()->json(["ok" => 0, "error" => 1, "message" => "O(a) enxadrista não está apto(a) a inscrever nesta categoria! Motivo: Não possui idade mínima."]);
                    }
                }
                if ($categoria->idade_maxima) {
                    if (!($categoria->idade_maxima >= $enxadrista->howOldForEvento($evento->getYear()))) {
                        return response()->json(["ok" => 0, "error" => 1, "message" => "O(a) enxadrista não está apto(a) a inscrever nesta categoria! Motivo: Idade ultrapassa a máxima."]);
                    }
                }
            }

            $inscricao->torneio_id = $torneio->id;
            $inscricao->enxadrista_id = $enxadrista->id;
            $inscricao->categoria_id = $categoria->id;
            $inscricao->cidade_id = $request->input("city_id");
            if ($request->has("club_id")) {
                if ($request->input("club_id") > 0) {
                    $inscricao->clube_id = $request->input("club_id");
                }
            }
            $inscricao->regulamento_aceito = true;
            $inscricao->xadrezsuico_aceito = true;
            $inscricao->is_aceito_imagem = 1;

            // if($user){
            //     if (
            //         $user->hasPermissionGlobal() ||
            //         $user->hasPermissionEventByPerfil($evento->id, [4, 5]) ||
            //         $user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [6,7])
            //     ) {
            //         if($request->has("inscricao_confirmada")){
            //             $inscricao->confirmado = true;
            //         }
            //         if($request->has("atualizar_cadastro")){
            //             $enxadrista->clube_id = $inscricao->clube_id;
            //             $enxadrista->cidade_id = $inscricao->cidade_id;
            //             $enxadrista->save();
            //         }
            //     }
            // }
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
                if($evento->isPaid() && $inscricao->getPaymentInfo("link")){
                    EmailController::schedule(
                        $enxadrista->email,
                        $inscricao,
                        EmailType::InscricaoRecebidaPagamentoPendente,
                        $enxadrista
                    );
                }else{
                    EmailController::schedule(
                        $enxadrista->email,
                        $inscricao,
                        EmailType::ConfirmacaoInscricao,
                        $enxadrista
                    );
                }
            }
            $response_adicional_fields = array();
            $response_adicional_fields["response"] = false;
            if(Inscricao::find($inscricao->id)->payment_info){
                if($evento->isPaid()){
                    if($inscricao->getPaymentInfo("link")){
                        $response_adicional_fields["link"] = $inscricao->getPaymentInfo("link");
                        $response_adicional_fields["response"] = true;
                    }
                }
            }

            if ($inscricao->id > 0) {
                if($inscricao->torneio->evento->is_lichess_integration){
                return response()->json(array_merge($response_adicional_fields,["ok" => 1, "error" => 0, "is_lichess_integration" => 1, "lichess_process_link" => $inscricao->getLichessProcessLink()]));
                }
                return response()->json(array_merge($response_adicional_fields,["ok" => 1, "error" => 0, "is_lichess_integration" => 0]));
            } else {
                return response()->json(["ok" => 0, "error" => 1, "message" => "Um erro inesperado aconteceu. Por favor, tente novamente mais tarde."]);
            }
        }
        return response()->json(["ok"=>0,"error"=>1,"message"=>"Evento não encontrado","httpcode"=>404],404);
    }

    public function list($uuid){
        if($uuid){
            if(Evento::where([["uuid","=",$uuid]])->count() == 0){
                return response()->json(["ok"=>0,"error"=>1,"message"=>"Evento não encontrado.","httpcode"=>404],404);
            }
            $evento = Evento::where([["uuid","=",$uuid]])->first();

            if(!$evento->e_permite_visualizar_lista_inscritos_publica){
                return response()->json(["ok"=>0,"error"=>1,"message"=>"O evento não permite exibir a lista de inscritos.","httpcode"=>401],401);
            }

            $list = array();
            foreach($evento->getInscricoes() as $inscricao){
                $item = array();

                $item["uuid"] = $inscricao->uuid;
                $item["player"] = [
                    "id" => $inscricao->enxadrista->id,
                    "name" => $inscricao->enxadrista->getNomePublico(),
                    "birthday" => $inscricao->enxadrista->getNascimentoPublico(),
                    "city_name" => $inscricao->enxadrista->cidade->getName()
                ];
                $item["category"] = [
                    "id" => $inscricao->categoria->id,
                    "name" => $inscricao->categoria->name,
                ];
                $item["city_name"] = $inscricao->cidade->getName();
                $item["city"] = $inscricao->cidade->toAPIObject(true);
                $item["club"] = null;
                $item["club_name"] = null;

                if($inscricao->clube){
                    $item["club_name"] = $inscricao->clube->getFullName();
                    $item["club"] = $inscricao->clube->toAPIObject(true);
                }

                if($evento->is_lichess_integration){
                    $item["lichess_info"] = [
                        "username" => ($inscricao->enxadrista->lichess_username) ? $inscricao->enxadrista->lichess_username : null,
                        "is_subscribed" => $inscricao->is_lichess_found,
                        "rating" => ($inscricao->lichess_rating) ? $inscricao->lichess_rating : 0,
                        "start_no" => ($inscricao->start_position) ? $inscricao->start_position : 0,
                    ];
                }elseif($evento->is_lichess){
                    $item["lichess_info"] = [
                        "username" => ($inscricao->enxadrista->lichess_username) ? $inscricao->enxadrista->lichess_username : null
                    ];
                }
                if($evento->is_chess_com){
                    $item["chess_com_info"] = [
                        "username" => ($inscricao->hasConfig("chesscom_username")) ? $inscricao->getConfig("chesscom_username",true) : $insccricao->enxadrista->chess_com_username." (*)",
                    ];
                }
                if($evento->tipo_rating){
                    $item["rating"] = $inscricao->enxadrista->ratingParaEvento($evento->id,true);
                }
                if($evento->usa_fide){
                    $item["fide_info"] = [
                        "id" => $inscricao->enxadrista->fide_id,
                        "rating" => $inscricao->enxadrista->showRating(0, $evento->tipo_modalidade, $evento->getConfig("fide_sequence")),
                    ];
                }
                if($evento->usa_cbx){
                    $item["cbx_info"] = [
                        "id" => $inscricao->enxadrista->cbx_id,
                        "rating" => $inscricao->enxadrista->showRating(1, $evento->tipo_modalidade),
                    ];
                }
                if($evento->usa_lbx){
                    $item["lbx_info"] = [
                        "id" => $inscricao->enxadrista->lbx_id,
                        "rating" => $inscricao->enxadrista->showRating(2, $evento->tipo_modalidade),
                    ];
                }

                if($evento->isPaid()){
                    $item["payment_info"] = [
                        "is_free" => $inscricao->isFree(),
                        "is_paid" => $inscricao->paid
                    ];
                    if(!$inscricao->paid){
                        if($inscricao->getPaymentInfo("link")){
                            $item["payment_info"]["link"] = $inscricao->getPaymentInfo("link");
                        }
                    }
                }else{
                    $item["payment_info"] = [
                        "is_free" => true,
                        "is_paid" => false
                    ];
                }

                $item["custom_fields"] = array();
                foreach($evento->getPublicCustomFields() as $custom_field){
                    if($inscricao->hasOpcao($custom_field->id)){
                        $item["custom_fields"][] = $inscricao->getOpcao($custom_field->id)->toAPIObject();
                    }else{
                        $item["custom_fields"][] = array_merge(["value"=>"-"],$custom_field->toAPIObject());
                    }
                }

                $list[] = $item;
            }
            return response()->json(["ok"=>1,"error"=>0,"registrations"=>$list]);
        }
        return response()->json(["ok"=>0,"error"=>1,"message"=>"Evento não encontrado.","httpcode"=>404],404);
    }
}

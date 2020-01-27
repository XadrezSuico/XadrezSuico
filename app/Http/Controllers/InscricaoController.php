<?php

namespace App\Http\Controllers;

use App\CampoPersonalizadoOpcaoInscricao;
use App\Categoria;
use App\Pais;
use App\Estado;
use App\Cidade;
use App\Clube;
use App\Email;
use App\Enxadrista;
use App\Evento;
use App\Inscricao;
use App\Sexo;
use App\Documento;
use App\TipoDocumento;
use App\TipoDocumentoPais;
use App\Http\Util\Util;
use Illuminate\Http\Request;

class InscricaoController extends Controller
{
    public function inscricao($id, Request $request)
    {
        $evento = Evento::find($id);
        $sexos = Sexo::all();
        $token = "";
        if ($evento) {
            if ($evento->e_inscricao_apenas_com_link) {
                if ($evento->inscricaoLiberada($request->input("token"))) {
                    if ($evento->inscricoes_encerradas()) {
                        return view("inscricao.encerradas", compact("evento"));
                    } else {
                        $token = $request->input("token");
                        return view("inscricao.inscricao_nova", compact("evento", "sexos", "token"));
                    }
                } else {
                    return view("inscricao.naopermitida");
                }
            } else {
                if ($evento->inscricoes_encerradas()) {
                    return view("inscricao.encerradas", compact("evento"));
                } else {
                    return view("inscricao.inscricao", compact("evento", "sexos"));
                }
            }
        } else {
            return view("inscricao.naoha");
        }
    }

    public function visualizar_inscricoes($id)
    {
        $evento = Evento::find($id);
        if ($evento) {
            if ($evento->e_permite_visualizar_lista_inscritos_publica) {
                return view("inscricao.inscricoes", compact("evento"));
            }
        }
        return redirect("/inscricao/" . $id);
    }

    public function adicionarNovaInscricao(Request $request)
    {
        if (
            !$request->has("regulamento_aceito")
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Você deve aceitar o regulamento do evento!", "registred" => 0]);
        } elseif (
            !$request->has("enxadrista_id") ||
            !$request->has("categoria_id") ||
            !$request->has("cidade_id") ||
            !$request->has("evento_id")
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "registred" => 0]);
        } elseif (
            $request->input("enxadrista_id") == null || $request->input("enxadrista_id") == "" ||
            $request->input("categoria_id") == null || $request->input("categoria_id") == "" ||
            $request->input("cidade_id") == null || $request->input("cidade_id") == "" ||
            $request->input("evento_id") == null || $request->input("evento_id") == ""
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "registred" => 0]);
        }

        $inscricao = new Inscricao;
        $torneio = null;
        $evento = Evento::find($request->input("evento_id"));

        if ($evento->inscricoes_encerradas(true)) {
            return response()->json(["ok" => 0, "error" => 1, "message" => env("MENSAGEM_FIM_INSCRICOES", "O prazo de inscrições antecipadas se esgotou ou então o limite de inscritos se completou. As inscrições poderão ser feitas no local do evento conforme regulamento.")]);
        }

        if ($evento->e_inscricao_apenas_com_link) {
            if (!$evento->inscricaoLiberada($request->input("token"))) {
                return response()->json(["ok" => 0, "error" => 1, "message" => "A inscrição para este evento deve ser feita com o link de inscrições enviado (Inscrições Privadas)."]);
            }
        }
        foreach ($evento->campos_obrigatorios() as $campo) {
            if (
                !$request->has("campo_personalizado_" . $campo->id)
            ) {
                return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "registred" => 0]);
            } elseif (
                $request->input("campo_personalizado_" . $campo->id) == null || $request->input("campo_personalizado_" . $campo->id) == ""
            ) {
                return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "registred" => 0]);
            }
        }

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
        $temInscricao = $evento->torneios()->whereHas("inscricoes", function ($q) use ($request) {
            $q->where([["enxadrista_id", "=", $request->input("enxadrista_id")]]);
        })->first();
        if (count($temInscricao) > 0) {
            $inscricao = Inscricao::where([["enxadrista_id", "=", $request->input("enxadrista_id")], ["torneio_id", "=", $temInscricao->id]])->first();
            return response()->json(["ok" => 0, "error" => 1, "message" => "Você já possui inscrição para este evento!<br/> Categoria: " . $inscricao->categoria->name . "<br/> Caso queira efetuar alguma alteração, favor enviar via email para " . env("EMAIL_ALTERACAO", "circuitoxadrezcascavel@gmail.com") . "."]);
        }

        $enxadrista = Enxadrista::find($request->input("enxadrista_id"));
        $categoria = Categoria::find($request->input("categoria_id"));
        if ($categoria) {
            if ($categoria->idade_minima) {
                if (!($categoria->idade_minima <= $enxadrista->howOld())) {
                    return response()->json(["ok" => 0, "error" => 1, "message" => "Você não está apto a participar da categoria que você enviou! Motivo: Não possui idade mínima."]);
                }
            }
            if ($categoria->idade_maxima) {
                if (!($categoria->idade_maxima >= $enxadrista->howOld())) {
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
        $inscricao->save();

        foreach ($evento->campos() as $campo) {
            if ($request->has("campo_personalizado_" . $campo->id)) {
                if ($request->input("campo_personalizado_" . $campo->id) != "") {
                    $opcao_inscricao = new CampoPersonalizadoOpcaoInscricao;
                    $opcao_inscricao->inscricao_id = $inscricao->id;
                    $opcao_inscricao->opcaos_id = $request->input("campo_personalizado_" . $campo->id);
                    $opcao_inscricao->campo_personalizados_id = $campo->id;
                    $opcao_inscricao->save();
                }
            }
        }

        if ($enxadrista->email) {
            // EMAIL PARA O ENXADRISTA SOLICITANTE
            $text = "Olá " . $enxadrista->name . "!<br/>";
            $text .= "Você está recebendo este email para confirmar a inscrição no Evento '" . $evento->name . "'.<br/>";
            $text .= "Informações:<br/>";
            $text .= "Cidade: " . $inscricao->cidade->name . "<br/>";
            $text .= "Clube: " . (($inscricao->clube) ? $inscricao->clube->name : "Sem Clube") . "<br/>";
            $text .= "Categoria: " . $inscricao->categoria->name . "<br/>";
            EmailController::scheduleEmail(
                $enxadrista->email,
                $evento->name . " - Inscrição Recebida - Enxadrista: " . $enxadrista->name,
                $text,
                $enxadrista
            );
        }

        if ($inscricao->id > 0) {
            return response()->json(["ok" => 1, "error" => 0]);
        } else {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um erro inesperado aconteceu. Por favor, tente novamente mais tarde."]);
        }
    }

    public function adicionarNovoEnxadrista(Request $request)
    {
        if (
            !$request->has("name") ||
            !$request->has("born") ||
            !$request->has("sexos_id") ||
            !$request->has("email") ||
            !$request->has("celular") ||
            !$request->has("cidade_id")
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "registred" => 0]);
        } elseif (
            $request->input("name") == null || $request->input("name") == "" ||
            $request->input("born") == null || $request->input("born") == "" ||
            $request->input("sexos_id") == null || $request->input("sexos_id") == "" ||
            $request->input("email") == null || $request->input("email") == "" ||
            $request->input("celular") == null || $request->input("celular") == "" ||
            $request->input("cidade_id") == null || $request->input("cidade_id") == ""
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "registred" => 0]);
        }

        $validator = \Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "O e-mail é inválido. Por favor, verifique e tente novamente.", "registred" => 0]);
        }

        // Algoritmo para eliminar os problemas com espaçamentos duplos ou até triplos.
        $nome_corrigido = "";

        $part_names = explode(" ", mb_strtoupper($request->input("name")));
        foreach ($part_names as $part_name) {
            if ($part_name != ' ') {
                $trim = trim($part_name);
                if (strlen($trim) > 0) {
                    $nome_corrigido .= $trim;
                    $nome_corrigido .= " ";
                }
            }
        }
        $nome_corrigido = trim($nome_corrigido);

        $enxadrista = new Enxadrista;
        if (!$enxadrista->setBorn($request->input("born"))) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "A data de nascimento é inválida.", "registred" => 0]);
        }

        $temEnxadrista = Enxadrista::where([["name", "=", $nome_corrigido], ["born", "=", $enxadrista->born]])->first();
        if (count($temEnxadrista) > 0) {
            if ($temEnxadrista->clube) {
                return response()->json([
                    "ok" => 0,
                    "error" => 1,
                    "message" => "Você já possui cadastro! Você será direcionado(a) à próxima etapa da inscrição!",
                    "registred" => 1,
                    "enxadrista_id" => $temEnxadrista->id,
                    "enxadrista_name" => $temEnxadrista->name . " | " . $temEnxadrista->getBorn(),
                    "cidade" => [
                        "id" => $temEnxadrista->cidade->id,
                        "name" => $temEnxadrista->cidade->name,
                    ],
                    "clube" => [
                        "id" => $temEnxadrista->clube->id,
                        "name" => $temEnxadrista->clube->name,
                    ],
                ]);
            } else {
                return response()->json([
                    "ok" => 0,
                    "error" => 1,
                    "message" => "Você já possui cadastro! Você será direcionado(a) à próxima etapa da inscrição!",
                    "registred" => 1,
                    "enxadrista_id" => $temEnxadrista->id,
                    "enxadrista_name" => $temEnxadrista->name . " | " . $temEnxadrista->getBorn(),
                    "cidade" => [
                        "id" => $temEnxadrista->cidade->id,
                        "name" => $temEnxadrista->cidade->name,
                    ],
                    "clube" => ["id" => 0],
                ]);
            }
        }

        $enxadrista->name = $nome_corrigido;
        $enxadrista->sexos_id = $request->input("sexos_id");
        $enxadrista->email = $request->input("email");
        $enxadrista->celular = $request->input("celular");
        if ($request->has("cbx_id")) {
            if ($request->input("cbx_id") > 0) {
                $enxadrista->cbx_id = $request->input("cbx_id");
            }
        }
        if ($request->has("fide_id")) {
            if ($request->input("fide_id") > 0) {
                $enxadrista->fide_id = $request->input("fide_id");
            }
        }
        if ($request->has("lbx_id")) {
            if ($request->input("lbx_id") > 0) {
                $enxadrista->lbx_id = $request->input("lbx_id");
            }
        }
        $enxadrista->cidade_id = $request->input("cidade_id");
        if ($request->has("clube_id")) {
            if ($request->input("clube_id") > 0) {
                $enxadrista->clube_id = $request->input("clube_id");
            }
        }
        $enxadrista->save();
        if ($enxadrista->id > 0) {
            if ($enxadrista->clube) {
                return response()->json(["ok" => 1, "error" => 0, "enxadrista_id" => $enxadrista->id, "cidade" => ["id" => $enxadrista->cidade->id, "name" => $enxadrista->cidade->name], "clube" => ["id" => $enxadrista->clube->id, "name" => $enxadrista->clube->name]]);
            } else {
                return response()->json(["ok" => 1, "error" => 0, "enxadrista_id" => $enxadrista->id, "cidade" => ["id" => $enxadrista->cidade->id, "name" => $enxadrista->cidade->name], "clube" => ["id" => 0]]);
            }
        } else {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um erro inesperado aconteceu. Por favor, tente novamente mais tarde.", "registred" => 0]);
        }
    }

    public function adicionarNovaCidade(Request $request)
    {
        if (
            !$request->has("name")
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "O campo obrigatório não está preenchido. Por favor, verifique e envie novamente!", "registred" => 0]);
        } elseif (
            $request->input("name") == null || $request->input("name") == ""
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "O campo obrigatório não está preenchido. Por favor, verifique e envie novamente!", "registred" => 0]);
        }
        $cidade = new Cidade;

        $temCidade = Cidade::where([["name", "=", mb_strtoupper($request->input("name"))]])->first();
        if (count($temCidade) > 0) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Esta cidade já está cadastrada! Selecionamos ela para você.", "registred" => 1, "cidade" => ["id" => $temCidade->id, "name" => $temCidade->name]]);
        }

        $cidade->name = mb_strtoupper($request->input("name"));
        $cidade->save();
        if ($cidade->id > 0) {
            return response()->json(["ok" => 1, "error" => 0, "cidade" => ["id" => $cidade->id, "name" => $cidade->name]]);
        } else {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um erro inesperado aconteceu. Por favor, tente novamente mais tarde.", "registred" => 0]);
        }
    }

    public function adicionarNovoClube(Request $request)
    {
        if (
            !$request->has("name") || !$request->has("cidade_id")
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Os campos obrigatórios não estão preenchidos. Por favor, verifique e envie novamente!", "registred" => 0]);
        } elseif (
            $request->input("name") == null || $request->input("name") == "" ||
            $request->input("cidade_id") == null || $request->input("cidade_id") == ""
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Os campos obrigatórios não estão preenchidos. Por favor, verifique e envie novamente!", "registred" => 0]);
        }
        $clube = new Clube;

        $temClube = Clube::where([["name", "=", mb_strtoupper($request->input("name"))], ["cidade_id", "=", $request->input("cidade_id")]])->first();
        if (count($temClube) > 0) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Este clube já está cadastrado! Selecionamos ele para você.", "registred" => 1, "clube" => ["id" => $temClube->id, "name" => $temClube->name]]);
        }

        $clube->name = mb_strtoupper($request->input("name"));
        $clube->cidade_id = mb_strtoupper($request->input("cidade_id"));
        $clube->save();
        if ($clube->id > 0) {
            return response()->json(["ok" => 1, "error" => 0, "clube" => ["id" => $clube->id, "name" => $clube->name]]);
        } else {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um erro inesperado aconteceu. Por favor, tente novamente mais tarde.", "registred" => 0]);
        }
    }

    public function buscaEnxadrista(Request $request)
    {
        $evento = Evento::find($request->input("evento_id"));
        $enxadristas = Enxadrista::where([
            ["name", "like", "%" . $request->input("q") . "%"],
        ])->orderBy("name", "ASC")->limit(30)->get();
        $results = array();
        foreach ($enxadristas as $enxadrista) {
            $rating = $enxadrista->ratingParaEvento($evento->id);
            if ($rating) {
                if ($enxadrista->estaInscrito($request->input("evento_id"))) {
                    $results[] = array("id" => $enxadrista->id, "text" => $enxadrista->name . " | " . $enxadrista->getBorn() . " | Rating: " . $rating . " - Já Está Inscrito neste Evento");
                } else {
                    $results[] = array("id" => $enxadrista->id, "text" => $enxadrista->name . " | " . $enxadrista->getBorn() . " | Rating: " . $rating);
                }

            } else {
                if ($enxadrista->estaInscrito($request->input("evento_id"))) {
                    $results[] = array("id" => $enxadrista->id, "text" => $enxadrista->name . " | " . $enxadrista->getBorn() . " - Já Está Inscrito neste Evento");
                } else {
                    $results[] = array("id" => $enxadrista->id, "text" => $enxadrista->name . " | " . $enxadrista->getBorn());
                }

            }
        }
        return response()->json(["results" => $results, "pagination" => true]);
    }

    public function getCidadeClube($id, $enxadrista_id)
    {
        $enxadrista = Enxadrista::find($enxadrista_id);
        if ($enxadrista) {
            if ($enxadrista->clube) {
                return response()->json(["ok" => 1, "error" => 0, "cidade" => ["id" => $enxadrista->cidade->id, "name" => $enxadrista->cidade->name], "clube" => ["id" => $enxadrista->clube->id, "name" => $enxadrista->clube->name]]);
            } else {
                return response()->json(["ok" => 1, "error" => 0, "cidade" => ["id" => $enxadrista->cidade->id, "name" => $enxadrista->cidade->name], "clube" => ["id" => 0]]);
            }
        } else {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Não há enxadrista com esse código!"]);
        }
    }

    public function buscaCategoria(Request $request)
    {
        $evento = Evento::find($request->input("evento_id"));
        $enxadrista = Enxadrista::find($request->input("enxadrista_id"));
        $categorias = $evento->categorias()->whereHas("categoria", function ($q1) use ($request, $enxadrista) {
            $q1->where([
                ["name", "like", "%" . $request->input("q") . "%"],
            ])
                ->where(function ($q2) use ($enxadrista) {
                    $q2->where(function ($q3) use ($enxadrista) {
                        $q3->where([["idade_minima", "<=", $enxadrista->howOld()]]);
                        $q3->where([["idade_maxima", ">=", $enxadrista->howOld()]]);
                    })
                        ->orWhere(function ($q3) use ($enxadrista) {
                            $q3->where([["idade_minima", "<=", $enxadrista->howOld()]]);
                            $q3->where([["idade_maxima", "=", null]]);
                        })
                        ->orWhere(function ($q3) use ($enxadrista) {
                            $q3->where([["idade_minima", "=", null]]);
                            $q3->where([["idade_maxima", ">=", $enxadrista->howOld()]]);
                        })
                        ->orWhere(function ($q3) {
                            $q3->where([["idade_minima", "=", null]]);
                            $q3->where([["idade_maxima", "=", null]]);
                        });
                })
                ->where(function ($q2) use ($enxadrista) {
                    $q2->where(function ($q3) use ($enxadrista) {
                        if ($enxadrista->sexos_id) {
                            $q3->where(function ($q4) use ($enxadrista) {
                                $q4->whereHas("sexos", function ($q5) use ($enxadrista) {
                                    $q5->where([["sexos_id", "=", $enxadrista->sexos_id]]);
                                });
                            });
                            $q3->orWhere(function ($q4) {
                                $q4->doesntHave("sexos");
                            });
                        } else {
                            $q3->whereDoesntHave("sexos");
                        }
                    });
                });
        })
            ->get();
        // echo ($categorias); exit();
        $results = array();
        foreach ($categorias as $categoria) {
            $results[] = array("id" => $categoria->categoria->id, "text" => $categoria->categoria->name);
        }
        return response()->json(["results" => $results, "pagination" => true]);
    }

    public function buscaCidade(Request $request)
    {
        $cidades = Cidade::where([
            ["name", "like", "%" . $request->input("q") . "%"],
        ])->get();
        $results = array();
        foreach ($cidades as $cidade) {
            $results[] = array("id" => $cidade->id, "text" => $cidade->name);
        }
        return response()->json(["results" => $results, "pagination" => true]);
    }

    public function buscaClube(Request $request)
    {
        $clubes = Clube::where([
            ["name", "like", "%" . $request->input("q") . "%"],
        ])->orWhere(function ($q) use ($request) {
            $q->whereHas("cidade", function ($Q) use ($request) {
                $Q->where([
                    ["name", "like", "%" . $request->input("q") . "%"],
                ]);
            });
        })->get();
        $results = array(array("id" => -1, "text" => "Sem Clube"));
        foreach ($clubes as $clube) {
            $results[] = array("id" => $clube->id, "text" => $clube->cidade->name . " - " . $clube->name);
        }
        return response()->json(["results" => $results, "pagination" => true]);
    }








    /*
     *
     * 
     * NOVAS FUNÇÕES PARA NOVA TELA DE INSCRIÇÃO
     * 
     * 
     */ 
    

    public function telav2_buscaEnxadrista($id,Request $request)
    {
        $evento = Evento::find($id);
        $enxadristas = Enxadrista::where([
            ["id", "like", "%" . $request->input("q") . "%"],
        ])
        ->orWhere([
            ["name", "like", "%" . $request->input("q") . "%"],
        ])
        ->orWhere(function($q1) use ($request){
            $q1->whereHas("documentos",function($q2) use ($request){
                $q2->where([
                    ["numero","=",$request->input("q")]
                ]);
            });
        })->orderBy("name", "ASC")->limit(30)->get();
        $results = array();
        foreach ($enxadristas as $enxadrista) {
            $rating = $enxadrista->ratingParaEvento($evento->id);
            $item = array();
            $item["id"] = $enxadrista->id;
            $item["name"] = "<strong>#".$enxadrista->id." - ".$enxadrista->name . "</strong>";
            $item["text"] = $enxadrista->getBorn() . " | ".$enxadrista->cidade->name;
            $item["permitida_inscricao"] = true;
            if($enxadrista->clube){
                $item["text"] .= " | Clube: ".$enxadrista->clube->name;
            }
            if ($rating) {
                $item["text"] .= " | Rating: ".$rating;
            }
            
            if ($enxadrista->estaInscrito($evento->id)) {
                $item["text"] .= " - Já Está Inscrito neste Evento";
                $item["permitida_inscricao"] = false;
            }
            $results[] = $item;
        }

        $total = Enxadrista::where([
            ["id", "like", "%" . $request->input("q") . "%"],
        ])
        ->orWhere([
            ["name", "like", "%" . $request->input("q") . "%"],
        ])
        ->orderBy("name", "ASC")
        ->count();

        return response()->json(["results" => $results, "hasMore" => ($total > 30) ? true : false]);
    }

    public function telav2_buscaEstado($evento_id,$pais_id)
    {
        $estados = Estado::where([
            ["pais_id", "=", $pais_id],
        ])->get();
        $results = array();
        foreach ($estados as $estado) {
            $results[] = array("id" => $estado->id, "text" => $estado->nome);
        }
        return response()->json(["results" => $results, "pagination" => true]);
    }

    public function telav2_buscaCidade($evento_id,$estados_id)
    {
        $cidades = Cidade::where([
            ["estados_id", "=", $estados_id],
        ])->get();
        $results = array();
        foreach ($cidades as $cidade) {
            $results[] = array("id" => $cidade->id, "text" => $cidade->name);
        }
        return response()->json(["results" => $results, "pagination" => true]);
    }

    public function telav2_buscaClube(Request $request)
    {
        $clubes = Clube::where([
            ["name", "like", "%" . $request->input("q") . "%"],
        ])->orWhere(function ($q) use ($request) {
            $q->whereHas("cidade", function ($Q) use ($request) {
                $Q->where([
                    ["name", "like", "%" . $request->input("q") . "%"],
                ]);
            });
        })->get();
        $results = array(array("id" => -1, "text" => "Sem Clube"));
        foreach ($clubes as $clube) {
            $results[] = array("id" => $clube->id, "text" => $clube->cidade->name . " - " . $clube->name);
        }
        return response()->json(["results" => $results, "pagination" => true]);
    }

    public function telav2_buscarDadosEnxadrista($id,$enxadrista_id)
    {
        $evento = Evento::find($id);
        $enxadrista = Enxadrista::find($enxadrista_id);
        if($enxadrista){
            $retorno = array();
            $retorno["id"] = $enxadrista->id;
            $retorno["name"] = $enxadrista->name;
            $retorno["born"] = $enxadrista->getBorn();
            $retorno["cidade"] = array("id"=>$enxadrista->cidade->id,"name"=>$enxadrista->cidade->name);
            $retorno["cidade"]["estado"] = array("id"=>$enxadrista->cidade->estado->id,"name"=>$enxadrista->cidade->estado->nome);
            $retorno["cidade"]["estado"]["pais"] = array("id"=>$enxadrista->cidade->estado->pais->id,"name"=>$enxadrista->cidade->estado->pais->nome);
            $retorno["clube"] = ($enxadrista->clube) ? array("id"=>$enxadrista->clube->id,"name"=>$enxadrista->clube->name) : array("id" => 0);
            $retorno["categorias"] = array();
            foreach($this->categoriasEnxadrista($evento,$enxadrista) as $categoria){
                $retorno["categorias"][] = array("id"=>$categoria->categoria->id,"name"=>$categoria->categoria->name);
            }
            return response()->json(["ok" => 1, "error"=>0, "data" => $retorno]);
        }else{
            return response()->json(["ok" => 0, "error"=>1, "message" => "O enxadrista não foi encontrado."]);
        }
    }



    public function telav2_adicionarNovaInscricao(Request $request)
    {
        if (
            !$request->has("regulamento_aceito")
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Você deve aceitar o regulamento do evento!", "registred" => 0]);
        } elseif (
            !$request->has("xadrezsuico_aceito")
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Você deve aceitar o termo de uso da plataforma XadrezSuíço!", "registred" => 0]);
        } elseif (
            !$request->has("enxadrista_id") ||
            !$request->has("categoria_id") ||
            !$request->has("cidade_id") ||
            !$request->has("evento_id")
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "registred" => 0]);
        } elseif (
            $request->input("enxadrista_id") == null || $request->input("enxadrista_id") == "" ||
            $request->input("categoria_id") == null || $request->input("categoria_id") == "" ||
            $request->input("cidade_id") == null || $request->input("cidade_id") == "" ||
            $request->input("evento_id") == null || $request->input("evento_id") == ""
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "registred" => 0]);
        }

        $inscricao = new Inscricao;
        $torneio = null;
        $evento = Evento::find($request->input("evento_id"));

        if ($evento->inscricoes_encerradas(true)) {
            return response()->json(["ok" => 0, "error" => 1, "message" => env("MENSAGEM_FIM_INSCRICOES", "O prazo de inscrições antecipadas se esgotou ou então o limite de inscritos se completou. As inscrições poderão ser feitas no local do evento conforme regulamento.")]);
        }

        if ($evento->e_inscricao_apenas_com_link) {
            if (!$evento->inscricaoLiberada($request->input("token"))) {
                return response()->json(["ok" => 0, "error" => 1, "message" => "A inscrição para este evento deve ser feita com o link de inscrições enviado (Inscrições Privadas)."]);
            }
        }
        foreach ($evento->campos_obrigatorios() as $campo) {
            if (
                !$request->has("campo_personalizado_" . $campo->id)
            ) {
                return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "registred" => 0]);
            } elseif (
                $request->input("campo_personalizado_" . $campo->id) == null || $request->input("campo_personalizado_" . $campo->id) == ""
            ) {
                return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "registred" => 0]);
            }
        }

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
        $temInscricao = $evento->torneios()->whereHas("inscricoes", function ($q) use ($request) {
            $q->where([["enxadrista_id", "=", $request->input("enxadrista_id")]]);
        })->first();
        if (count($temInscricao) > 0) {
            $inscricao = Inscricao::where([["enxadrista_id", "=", $request->input("enxadrista_id")], ["torneio_id", "=", $temInscricao->id]])->first();
            return response()->json(["ok" => 0, "error" => 1, "message" => "Você já possui inscrição para este evento!<br/> Categoria: " . $inscricao->categoria->name . "<br/> Caso queira efetuar alguma alteração, favor enviar via email para " . env("EMAIL_ALTERACAO", "circuitoxadrezcascavel@gmail.com") . "."]);
        }

        $enxadrista = Enxadrista::find($request->input("enxadrista_id"));
        $categoria = Categoria::find($request->input("categoria_id"));
        if ($categoria) {
            if ($categoria->idade_minima) {
                if (!($categoria->idade_minima <= $enxadrista->howOld())) {
                    return response()->json(["ok" => 0, "error" => 1, "message" => "Você não está apto a participar da categoria que você enviou! Motivo: Não possui idade mínima."]);
                }
            }
            if ($categoria->idade_maxima) {
                if (!($categoria->idade_maxima >= $enxadrista->howOld())) {
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
        $inscricao->save();

        foreach ($evento->campos() as $campo) {
            if ($request->has("campo_personalizado_" . $campo->id)) {
                if ($request->input("campo_personalizado_" . $campo->id) != "") {
                    $opcao_inscricao = new CampoPersonalizadoOpcaoInscricao;
                    $opcao_inscricao->inscricao_id = $inscricao->id;
                    $opcao_inscricao->opcaos_id = $request->input("campo_personalizado_" . $campo->id);
                    $opcao_inscricao->campo_personalizados_id = $campo->id;
                    $opcao_inscricao->save();
                }
            }
        }

        if ($enxadrista->email) {
            // EMAIL PARA O ENXADRISTA SOLICITANTE
            $text = "Olá " . $enxadrista->name . "!<br/>";
            $text .= "Você está recebendo este email para confirmar a inscrição no Evento '" . $evento->name . "'.<br/>";
            $text .= "Informações:<br/>";
            $text .= "Cidade: " . $inscricao->cidade->name . "<br/>";
            $text .= "Clube: " . (($inscricao->clube) ? $inscricao->clube->name : "Sem Clube") . "<br/>";
            $text .= "Categoria: " . $inscricao->categoria->name . "<br/>";
            EmailController::scheduleEmail(
                $enxadrista->email,
                $evento->name . " - Inscrição Recebida - Enxadrista: " . $enxadrista->name,
                $text,
                $enxadrista
            );
        }

        if ($inscricao->id > 0) {
            return response()->json(["ok" => 1, "error" => 0]);
        } else {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um erro inesperado aconteceu. Por favor, tente novamente mais tarde."]);
        }
    }

    
    public function telav2_adicionarNovoEnxadrista(Request $request)
    {
        if (
            !$request->has("name") ||
            !$request->has("born") ||
            !$request->has("sexos_id") ||
            !$request->has("email") ||
            !$request->has("pais_nascimento_id") ||
            !$request->has("pais_celular_id") ||
            !$request->has("celular") ||
            !$request->has("cidade_id")
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "registred" => 0, "ask" => 0]);
        } elseif (
            $request->input("name") == null || $request->input("name") == "" ||
            $request->input("born") == null || $request->input("born") == "" ||
            $request->input("sexos_id") == null || $request->input("sexos_id") == "" ||
            $request->input("email") == null || $request->input("email") == "" ||
            $request->input("pais_nascimento_id") == null || $request->input("pais_nascimento_id") == "" ||
            $request->input("pais_celular_id") == null || $request->input("pais_celular_id") == "" ||
            $request->input("celular") == null || $request->input("celular") == "" ||
            $request->input("cidade_id") == null || $request->input("cidade_id") == ""
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "registred" => 0, "ask" => 0]);
        }

        $validator = \Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "O e-mail é inválido. Por favor, verifique e tente novamente.", "registred" => 0, "ask" => 0]);
        }

        // Algoritmo para eliminar os problemas com espaçamentos duplos ou até triplos.
        $nome_corrigido = "";

        $part_names = explode(" ", mb_strtoupper($request->input("name")));
        foreach ($part_names as $part_name) {
            if ($part_name != ' ') {
                $trim = trim($part_name);
                if (strlen($trim) > 0) {
                    $nome_corrigido .= $trim;
                    $nome_corrigido .= " ";
                }
            }
        }


        $documentos = array();

        foreach(TipoDocumentoPais::where([["pais_id","=",$request->input("pais_nascimento_id")]])->get() as $tipo_documento_pais){
            if($tipo_documento_pais->e_requerido){
                if(!$request->has("tipo_documento_".$tipo_documento_pais->tipo_documento->id)){
                    return response()->json(["ok"=>0,"error"=>1,"Há um documento que é requerido que não foi informado.", "registred" => 0, "ask" => 0]);
                }
                if(
                    $request->input("tipo_documento_".$tipo_documento_pais->tipo_documento->id) == "" || 
                    $request->input("tipo_documento_".$tipo_documento_pais->tipo_documento->id) == NULL
                ){
                    return response()->json(["ok"=>0,"error"=>1,"Há um documento que é requerido que não foi informado.", "registred" => 0, "ask" => 0]);
                }
            }
            if($request->has("tipo_documento_".$tipo_documento_pais->tipo_documento->id)){
                if(
                    $request->input("tipo_documento_".$tipo_documento_pais->tipo_documento->id) != "" && 
                    $request->input("tipo_documento_".$tipo_documento_pais->tipo_documento->id) != NULL
                ){
                    
                    $temEnxadrista = Enxadrista::whereHas("documentos",function($q1) use($request, $tipo_documento_pais){
                        if($tipo_documento_pais->tipo_documento->id == 1){
                            $q1->where([
                                ["tipo_documentos_id","=",$tipo_documento_pais->tipo_documento->id],
                                ["numero","=",Util::numeros($request->input("tipo_documento_".$tipo_documento_pais->tipo_documento->id))],
                            ]);
                        }else{
                            $q1->where([
                                ["tipo_documentos_id","=",$tipo_documento_pais->tipo_documento->id],
                                ["numero","=",$request->input("tipo_documento_".$tipo_documento_pais->tipo_documento->id)],
                            ]);
                        }
                    })->first();
                    if(count($temEnxadrista) > 0){
                        return response()->json([
                            "ok"=>0,
                            "error"=>1,
                            "message" => "Já há um cadastro de Enxadrista com o Documento informado. Deseja utilizar ele?",
                            "registred" => 0,
                            "ask" => 1,
                            "enxadrista_id" => $temEnxadrista->id,
                            "enxadrista_name" => $temEnxadrista->name,
                            "enxadrista_born" => $temEnxadrista->getBorn(),
                            "enxadrista_city" => $temEnxadrista->cidade->name,
                        ]);
                    }

                    $documento = new Documento;
                    $documento->tipo_documentos_id = $tipo_documento_pais->tipo_documento->id;
                    if($tipo_documento_pais->tipo_documento->id == 1){
                        $documento->numero = Util::numeros($request->input("tipo_documento_".$tipo_documento_pais->tipo_documento->id));
                    }else{
                        $documento->numero = $request->input("tipo_documento_".$tipo_documento_pais->tipo_documento->id);
                    }

                    $documentos[] = $documento;
                }
            }
        }

        if(count($documentos) == 0){
            return response()->json(["ok"=>0,"error"=>1,"É obrigatório a inserção de ao menos UM DOCUMENTO.", "registred" => 0, "ask" => 0]);
        }

        $nome_corrigido = trim($nome_corrigido);

        $enxadrista = new Enxadrista;
        if (!$enxadrista->setBorn($request->input("born"))) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "A data de nascimento é inválida.", "registred" => 0, "ask" => 0]);
        }
        if($enxadrista->howOld() >= 130){
            return response()->json(["ok" => 0, "error" => 1, "message" => "A data de nascimento parece inválida. Por favor, verifique e tente novamente.", "registred" => 0, "ask" => 0]);
        }

        $temEnxadrista = Enxadrista::where([["name", "=", $nome_corrigido], ["born", "=", $enxadrista->born]])->first();
        if (count($temEnxadrista) > 0) {
            if ($temEnxadrista->clube) {
                return response()->json([
                    "ok" => 0,
                    "error" => 1,
                    "message" => "Você já possui cadastro! Você será direcionado(a) à próxima etapa da inscrição!",
                    "registred" => 1,
                    "ask" => 0,
                    "enxadrista_id" => $temEnxadrista->id,
                ]);
            } else {
                return response()->json([
                    "ok" => 0,
                    "error" => 1,
                    "message" => "Você já possui cadastro! Você será direcionado(a) à próxima etapa da inscrição!",
                    "registred" => 1,
                    "ask" => 0,
                    "enxadrista_id" => $temEnxadrista->id,
                ]);
            }
        }

        $enxadrista->name = $nome_corrigido;
        $enxadrista->splitName();
        $enxadrista->sexos_id = $request->input("sexos_id");
        $enxadrista->email = $request->input("email");
        $enxadrista->pais_id = $request->input("pais_nascimento_id");
        $enxadrista->pais_celular_id = $request->input("pais_celular_id");
        $enxadrista->celular = $request->input("celular");
        if ($request->has("cbx_id")) {
            if ($request->input("cbx_id") > 0) {
                $enxadrista->cbx_id = $request->input("cbx_id");
            }
        }
        if ($request->has("fide_id")) {
            if ($request->input("fide_id") > 0) {
                $enxadrista->fide_id = $request->input("fide_id");
            }
        }
        if ($request->has("lbx_id")) {
            if ($request->input("lbx_id") > 0) {
                $enxadrista->lbx_id = $request->input("lbx_id");
            }
        }
        $enxadrista->cidade_id = $request->input("cidade_id");
        if ($request->has("clube_id")) {
            if ($request->input("clube_id") > 0) {
                $enxadrista->clube_id = $request->input("clube_id");
            }
        }
        $enxadrista->save();

        foreach($documentos as $documento){
            $documento->enxadrista_id = $enxadrista->id;
            $documento->save();
        }

        if ($enxadrista->id > 0) {
            if ($enxadrista->clube) {
                return response()->json(["ok" => 1, "error" => 0, "enxadrista_id" => $enxadrista->id, "ask" => 0]);
            } else {
                return response()->json(["ok" => 1, "error" => 0, "enxadrista_id" => $enxadrista->id, "ask" => 0]);
            }
        } else {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um erro inesperado aconteceu. Por favor, tente novamente mais tarde.", "registred" => 0, "ask" => 0]);
        }
    }


    /*
     *
     * 
     * TELA V2: FUNÇÕES AUXILIARES
     * 
     * 
     */ 
    public function categoriasEnxadrista($evento, $enxadrista)
    {
        $categorias = $evento->categorias()->whereHas("categoria", function ($q1) use ($enxadrista) {
            $q1->where(function ($q2) use ($enxadrista) {
                    $q2->where(function ($q3) use ($enxadrista) {
                        $q3->where([["idade_minima", "<=", $enxadrista->howOld()]]);
                        $q3->where([["idade_maxima", ">=", $enxadrista->howOld()]]);
                    })
                        ->orWhere(function ($q3) use ($enxadrista) {
                            $q3->where([["idade_minima", "<=", $enxadrista->howOld()]]);
                            $q3->where([["idade_maxima", "=", null]]);
                        })
                        ->orWhere(function ($q3) use ($enxadrista) {
                            $q3->where([["idade_minima", "=", null]]);
                            $q3->where([["idade_maxima", ">=", $enxadrista->howOld()]]);
                        })
                        ->orWhere(function ($q3) {
                            $q3->where([["idade_minima", "=", null]]);
                            $q3->where([["idade_maxima", "=", null]]);
                        });
                })
                ->where(function ($q2) use ($enxadrista) {
                    $q2->where(function ($q3) use ($enxadrista) {
                        if ($enxadrista->sexos_id) {
                            $q3->where(function ($q4) use ($enxadrista) {
                                $q4->whereHas("sexos", function ($q5) use ($enxadrista) {
                                    $q5->where([["sexos_id", "=", $enxadrista->sexos_id]]);
                                });
                            });
                            $q3->orWhere(function ($q4) {
                                $q4->doesntHave("sexos");
                            });
                        } else {
                            $q3->whereDoesntHave("sexos");
                        }
                    });
                });
        })
            ->get();
        // echo ($categorias); exit();
        $results = array();
        foreach ($categorias as $categoria) {
            $results[] = $categoria;
        }
        return $results;
    }
}

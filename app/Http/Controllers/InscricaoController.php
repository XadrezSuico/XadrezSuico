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
use App\Rating;
use App\MovimentacaoRating;
use Illuminate\Support\Facades\Auth;
use App\Http\Util\Util;
use Illuminate\Http\Request;

use App\Enum\EmailType;

use Log;

class InscricaoController extends Controller
{
    public function inscricao($id, Request $request)
    {
        $evento = Evento::find($id);
        $sexos = Sexo::all();
        $token = "";
        $user = Auth::user();
        $permite_confirmacao = false;
        if ($evento) {

            if($user){
                if (
                    $user->hasPermissionGlobal() ||
                    $user->hasPermissionEventByPerfil($evento->id, [4, 5]) ||
                    $user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [6,7])
                ) {
                    $permite_confirmacao = true;
                }
            }


            if ($evento->e_inscricao_apenas_com_link) {
                if ($evento->inscricaoLiberada($request->input("token"))) {
                    if ($evento->inscricoes_encerradas()) {
                        if(!$user){
                            return view("inscricao.encerradas", compact("evento"));
                        }
                        if (
                            !$user->hasPermissionGlobal() &&
                            !$user->hasPermissionEventByPerfil($evento->id, [3, 4, 5]) &&
                            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [6,7])
                        ) {
                            return view("inscricao.encerradas", compact("evento"));
                        }
                    }
                    $token = $request->input("token");

                    $go_to_inscricao = false;
                    if($request->has("inscrever")){
                        $go_to_inscricao = true;
                    }

                    return view("inscricao.inscricao_nova", compact("evento", "sexos", "token","user","permite_confirmacao","go_to_inscricao"));
                } else {
                    return view("inscricao.naopermitida");
                }
            } else {
                if ($evento->inscricoes_encerradas()) {
                    if(!$user){
                        return view("inscricao.encerradas", compact("evento"));
                    }
                    if (
                        !$user->hasPermissionGlobal() &&
                        !$user->hasPermissionEventByPerfil($evento->id, [3, 4, 5]) &&
                        !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [6,7])
                    ) {
                        return view("inscricao.encerradas", compact("evento"));
                    }
                }

                $go_to_inscricao = false;
                if($request->has("inscrever")){
                    $go_to_inscricao = true;
                }

                return view("inscricao.inscricao_nova", compact("evento", "sexos","user","permite_confirmacao","go_to_inscricao"));
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


    public function visualizar_premiados($id)
    {
        $evento = Evento::find($id);
        if ($evento) {
            if ($evento->e_permite_visualizar_lista_inscritos_publica) {
                return view("inscricao.premiados", compact("evento"));
            }
        }
        return redirect("/inscricao/" . $id);
    }

    public function editar_inscricao($uuid)
    {
        $inscricao_count = Inscricao::where([["uuid","=",$uuid]])->count();
        if($inscricao_count > 0){
            $inscricao = Inscricao::where([["uuid","=",$uuid]])->first();
            if(!$inscricao->torneio->evento->permite_edicao_inscricao){
                $evento = $inscricao->torneio->evento;
                return view("inscricao.encerradas", compact("evento"));
            }
            if(!$inscricao->torneio->evento->inscricoes_encerradas(false,true)){
                $evento = $inscricao->torneio->evento;
                $categorias = $this->categoriasEnxadrista($evento, $inscricao->enxadrista);
                return view("inscricao.editar_inscricao", compact("evento", "inscricao", "categorias"));
            }else{
                $evento = $inscricao->torneio->evento;
                return view("inscricao.encerradas", compact("evento"));
            }
        }else{
            return view("inscricao.naoha");
        }
    }

    public function editar_inscricao_post($uuid,Request $request){
        $inscricao_count = Inscricao::where([["uuid","=",$uuid]])->count();
        if($inscricao_count > 0){
            $inscricao = Inscricao::where([["uuid","=",$uuid]])->first();


            if($inscricao->torneio->evento->inscricoes_encerradas(false,true)){
                return response()->json(["ok" => 0, "error" => 1, "message" => "O período para edição de inscrição se esgotou."]);
            }
            if(!$inscricao->torneio->evento->permite_edicao_inscricao){
                return response()->json(["ok" => 0, "error" => 1, "message" => "O evento não permite edição de inscrição."]);
            }

            if($request->has("categoria_id")){
                if($request->input("categoria_id") > 0){
                    $categoria = $request->input("categoria_id");
                    $is_found = false;

                    foreach($this->categoriasEnxadrista($inscricao->torneio->evento,$inscricao->enxadrista) as $categoria_possivel){
                        if($categoria_possivel->categoria->id == $categoria){
                            $is_found = true;
                        }
                    }
                    if(!$is_found){
                        return response()->json(["ok" => 0, "error" => 1, "message" => "O enxadrista não pode jogar nesta categoria."]);
                    }

                    if ($inscricao->categoria_id != $request->input("categoria_id")) {
                        $inscricao->categoria_id = $request->input("categoria_id");

                        $torneio = null;

                        foreach ($inscricao->torneio->evento->torneios->all() as $Torneio) {
                            foreach ($Torneio->categorias->all() as $categoria) {
                                if ($categoria->categoria_id == $request->input("categoria_id")) {
                                    $torneio = $Torneio;
                                }
                            }
                        }
                        $inscricao->torneio_id = $torneio->id;

                    }

                }
            }
            if($request->has("cidade_id")){
                if($request->input("cidade_id") > 0){
                    $inscricao->cidade_id = $request->input("cidade_id");
                }
            }
            if($request->has("clube_id")){
                if($request->input("clube_id") > 0){
                    $inscricao->clube_id = $request->input("clube_id");
                }
            }

            $inscricao->save();

            foreach ($inscricao->torneio->evento->campos() as $campo) {
                if ($request->has("campo_personalizado_" . $campo->id)) {
                    if ($request->input("campo_personalizado_" . $campo->id) != "") {
                        if ($request->input("campo_personalizado_" . $campo->id) != null) {
                            if ($request->input("campo_personalizado_" . $campo->id) != "null") {
                                if(CampoPersonalizadoOpcaoInscricao::where([
                                    ["inscricao_id","=",$inscricao->id],
                                    ["campo_personalizados_id","=", $campo->id]
                                    ])->count() > 0){
                                     $opcao_inscricao = CampoPersonalizadoOpcaoInscricao::where([
                                        ["inscricao_id","=",$inscricao->id],
                                        ["campo_personalizados_id","=", $campo->id]
                                        ])->first();
                                }else{
                                    $opcao_inscricao = new CampoPersonalizadoOpcaoInscricao;
                                    $opcao_inscricao->inscricao_id = $inscricao->id;
                                    $opcao_inscricao->campo_personalizados_id = $campo->id;
                                }
                                $opcao_inscricao->opcaos_id = $request->input("campo_personalizado_" . $campo->id);
                                $opcao_inscricao->save();
                            }
                        }
                    }
                }
            }


            return response()->json(["ok"=>1,"error"=>0]);
        }else{
            return response()->json(["ok"=>0,"error"=>1,"message"=>"Inscrição não encontrada."]);
        }
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
        $user = Auth::user();
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
            $item["text"] = $enxadrista->getNascimentoPublico() . " | ".$enxadrista->cidade->name;
            $item["permitida_inscricao"] = true;
            if($enxadrista->clube){
                $item["text"] .= " | Clube: ".$enxadrista->clube->name;
            }
            if ($rating) {
                $item["text"] .= " | Rating: ".$rating;
            }

            if($user){

                if (
                    $user->hasPermissionGlobal() ||
                    $user->hasPermissionEventByPerfil($evento->id, [3, 4, 5]) ||
                    $user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [6,7])
                ) {
                    // Sem Inscrição
                    $item["status"] = 1;
                    if ($enxadrista->estaInscrito($evento->id)) {
                        // Inscrito - Não Confirmado
                        $item["status"] = 2;
                        $inscricao = Inscricao::where([
                            ["enxadrista_id","=",$enxadrista->id],
                        ])
                        ->whereHas("torneio",function($q1) use ($evento){
                            $q1->where([
                                ["evento_id","=",$evento->id]
                            ]);
                        })
                        ->first();
                        $item["inscricao_id"] = $inscricao->id;
                        if($inscricao->confirmado){
                            // Inscrito - Confirmado
                            $item["status"] = 3;
                        }
                    }
                    if ($enxadrista->estaInscrito($evento->id)) {
                        $item["name"] .= "(Já está Inscrito)";
                        $item["permitida_inscricao"] = false;
                    }
                }else{
                    if ($enxadrista->estaInscrito($evento->id)) {
                        $item["text"] .= " - Já Está Inscrito neste Evento";
                        $item["permitida_inscricao"] = false;
                    }
                    // Deslogado ou Sem permissão
                    $item["status"] = 0;
                }
            }else{
                if ($enxadrista->estaInscrito($evento->id)) {
                    $item["name"] .= "(Já está Inscrito)";
                    $item["text"] .= " - Já Está Inscrito neste Evento";
                    $item["permitida_inscricao"] = false;
                }
                // Deslogado ou Sem permissão
                $item["status"] = 0;
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
            $results[] = array("id" => $clube->id, "text" => $clube->cidade->estado->pais->nome . "-" . $clube->cidade->name . "/" . $clube->cidade->estado->nome . " | ".$clube->id." -  " . $clube->name);
        }
        return response()->json(["results" => $results, "pagination" => true]);
    }

    public function telav2_conferenciaDados($id,$enxadrista_id)
    {
        $evento = Evento::find($id);
        $enxadrista = Enxadrista::find($enxadrista_id);
        if($enxadrista){
            if(
                !(
                    $enxadrista->name != NULL &&
                    $enxadrista->born != NULL &&
                    $enxadrista->pais_id != NULL &&
                    $enxadrista->cidade_id != NULL &&
                    $enxadrista->email != NULL &&
                    $enxadrista->sexos_id != NULL &&
                    $enxadrista->pais_celular_id != NULL &&
                    $enxadrista->celular != NULL &&
                    $enxadrista->documentos()->count() > 0
                )
                ||
                $enxadrista->howOldForEvento($evento->getYear()) >= 130
                ||
                ($evento->calcula_cbx && (!$enxadrista->cbx_id || $enxadrista->cbx_id == 0))
                ||
                ($evento->calcula_fide &&
                    (
                        (
                            $enxadrista->pais_id == 33 && (!$enxadrista->cbx_id || $enxadrista->cbx_id == 0)
                        )||(
                            $enxadrista->pais_id != 33 && (!$enxadrista->fide_id || $enxadrista->fide_id == 0)
                        )
                    )
                )
                ||
                ($evento->is_lichess && ($enxadrista->lichess_username == NULL || $enxadrista->lichess_username == ""))
                ||
                ($evento->is_chess_com && ($enxadrista->chess_com_username == NULL || $enxadrista->chess_com_username == ""))
                ||
                $enxadrista->last_cadastral_update == NULL
                ||
                $enxadrista->last_cadastral_update <= "01-01-".date("Y")." 00:00:00"
            ){
                $what = array();
                if($enxadrista->name == NULL) $what[] = "name";
                if($enxadrista->born == NULL) $what[] = "born";
                if($enxadrista->pais_id == NULL) $what[] = "pais_id";
                if($enxadrista->cidade_id == NULL) $what[] = "cidade_id";
                if($enxadrista->email == NULL) $what[] = "email";
                if($enxadrista->sexos_id == NULL) $what[] = "sexos_id";
                if($enxadrista->pais_celular_id == NULL) $what[] = "pais_celular_id";
                if($enxadrista->celular == NULL) $what[] = "celular";
                if($enxadrista->documentos()->count() == 0) $what[] = "documentos";
                if($enxadrista->howOldForEvento($evento->getYear()) >= 130) $what[] = "born";

                if(($evento->calcula_cbx && (!$enxadrista->cbx_id || $enxadrista->cbx_id == 0))) $what[] = "cbx_id";
                if(($evento->calcula_fide &&
                    (
                        (
                            $enxadrista->pais_id == 33 && (!$enxadrista->cbx_id || $enxadrista->cbx_id == 0)
                        )||(
                            $enxadrista->pais_id != 33 && (!$enxadrista->fide_id || $enxadrista->fide_id == 0)
                        )
                    )
                )) $what[] = "fide_id";

                if(($evento->is_lichess && ($enxadrista->lichess_username == NULL || $enxadrista->lichess_username == ""))) $what[] = "lichess_id";

                if(($evento->is_chess_com && ($enxadrista->chess_com_username == NULL || $enxadrista->chess_com_username == ""))) $what[] = "chess_com_id";

                if($enxadrista->last_cadastral_update == NULL) $what[] = "last_cadastral_update_null";

                if($enxadrista->last_cadastral_update <= "01-01-".date("Y")." 00:00:00") $what[] = "last_cadastral_update_when";


                $fields = array();
                // 1/5
                $fields["id"] = $enxadrista->id;
                $fields["name"] = $enxadrista->name;
                if($enxadrista->howOldForEvento($evento->getYear()) < 130) $fields["born"] = $enxadrista->getBorn();
                $fields["sexos_id"] = $enxadrista->sexos_id;
                $fields["pais_nascimento_id"] = $enxadrista->pais_id;
                // 2/5 - NADA
                // 3/5 - NADA
                // 4/5
                $fields["cbx_id"] = $enxadrista->cbx_id;
                $fields["fide_id"] = $enxadrista->fide_id;
                $fields["lbx_id"] = $enxadrista->lbx_id;

                $fields["chess_com_username"] = $enxadrista->chess_com_username;
                $fields["lichess_username"] = mb_strtolower($enxadrista->lichess_username);
                // 5/5
                if($enxadrista->cidade){
                    if($enxadrista->cidade->estado){
                        if($enxadrista->cidade->estado->pais){
                            $fields["cidade_id"] = $enxadrista->cidade_id;
                            $fields["estados_id"] = $enxadrista->cidade->estado->id;
                            $fields["pais_id"] = $enxadrista->cidade->estado->pais->id;
                        }
                    }
                }
                $fields["clube_id"] = $enxadrista->clube_id;
                $fields["clube"]["id"] = "";
                $fields["clube"]["name"] = "";
                if($enxadrista->clube){
                    $fields["clube"]["id"] = $enxadrista->clube->id;
                    $fields["clube"]["name"] = $enxadrista->clube->cidade->name."/".$enxadrista->clube->name;
                }


                return response()->json(["ok" => 0, "error" => 1, "message" => "Antes de efetuar a inscrição, é necessária fazer uma atualização cadastral, por favor, preencha os dados cadastrais obrigatórios para continuar.", "necessita_atualizacao" => 1, "fields"=>$fields, "what"=>$what]);
            }
            return response()->json(["ok" => 1, "error" => 0]);
        }else{
            return response()->json(["ok" => 0, "error" => 1, "message" => "O enxadrista não foi encontrado.", "necessita_atualizacao" => 0]);
        }
    }

    public function telav2_buscarDadosEnxadrista($id,$enxadrista_id)
    {
        $evento = Evento::find($id);
        $enxadrista = Enxadrista::find($enxadrista_id);
        if($enxadrista){
            $retorno = array();
            $retorno["id"] = $enxadrista->id;
            $retorno["name"] = $enxadrista->name;
            $retorno["cbx_id"] = $enxadrista->cbx_id;
            $retorno["fide_id"] = $enxadrista->fide_id;
            $retorno["lbx_id"] = $enxadrista->lbx_id;
            $retorno["chess_com_username"] = $enxadrista->chess_com_username;
            $retorno["lichess_username"] = mb_strtolower($enxadrista->lichess_username);
            $retorno["born"] = $enxadrista->getBorn();
            $retorno["cidade"] = array("id"=>$enxadrista->cidade->id,"name"=>$enxadrista->cidade->name);
            $retorno["cidade"]["estado"] = array("id"=>$enxadrista->cidade->estado->id,"name"=>$enxadrista->cidade->estado->nome);
            $retorno["cidade"]["estado"]["pais"] = array("id"=>$enxadrista->cidade->estado->pais->id,"name"=>$enxadrista->cidade->estado->pais->nome);
            $retorno["clube"] = ($enxadrista->clube) ? array("id"=>$enxadrista->clube->id,"name"=>$enxadrista->clube->name) : array("id" => 0);
            $retorno["categorias"] = array();
            $categorias = $this->categoriasEnxadrista($evento,$enxadrista);
            if(count($categorias) == 0){
                return response()->json(["ok" => 0, "error"=>1, "message" => "Não há categorias que você pode se inscrever neste evento."]);
            }
            foreach($this->categoriasEnxadrista($evento,$enxadrista) as $categoria){
                $retorno["categorias"][] = array("id"=>$categoria->categoria->id,"name"=>$categoria->categoria->name);
            }
            return response()->json(["ok" => 1, "error"=>0, "data" => $retorno]);
        }else{
            return response()->json(["ok" => 0, "error"=>1, "message" => "O enxadrista não foi encontrado."]);
        }
    }



    public function telav2_adicionarNovaInscricao($evento_id,Request $request)
    {
        $user = Auth::user();

        $evento = Evento::find($evento_id);

        if (
            !$request->has("regulamento_aceito")
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Você deve aceitar o regulamento do evento!", "registred" => 0]);
        } elseif (
            !$request->has("xadrezsuico_aceito")
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Você deve aceitar o termo de uso e política de privacidade da plataforma XadrezSuíço!", "registred" => 0]);
        } elseif (
            !$request->has("imagem_aceito")
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Os direitos de imagem devem ser fornecidos para inscrição neste evento.", "registred" => 0]);
        } elseif (
            !$request->has("categoria_conferida")
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Você deve verificar a categoria e marcar o campo 'Categoria conferida'.", "registred" => 0]);
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
            if($user){
                if (
                    !$user->hasPermissionGlobal() &&
                    !$user->hasPermissionEventByPerfil($evento->id, [3, 4, 5]) &&
                    !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [6,7])
                ) {
                    return response()->json(["ok" => 0, "error" => 1, "message" => env("MENSAGEM_FIM_INSCRICOES", "O prazo de inscrições antecipadas se esgotou ou então o limite de inscritos se completou. As inscrições poderão ser feitas no local do evento conforme regulamento.")]);
                }
            }else{
                return response()->json(["ok" => 0, "error" => 1, "message" => env("MENSAGEM_FIM_INSCRICOES", "O prazo de inscrições antecipadas se esgotou ou então o limite de inscritos se completou. As inscrições poderão ser feitas no local do evento conforme regulamento.")]);
            }
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
                return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!<br/>Erro: Campo Personalizado 1.<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "registred" => 0]);
            } elseif (
                $request->input("campo_personalizado_" . $campo->id) == null ||
                $request->input("campo_personalizado_" . $campo->id) == "" ||
                $request->input("campo_personalizado_" . $campo->id) == "null"
            ) {
                return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!<br/>Erro: Campo Personalizado 2.<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "registred" => 0]);
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


    public function telav2_adicionarNovoEnxadrista($evento_id,Request $request)
    {
        $user = Auth::user();
        $evento = Evento::find($evento_id);

        if (
            !$request->has("xadrezsuico_aceito")
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Você deve aceitar o termo de uso e política de privacidade da plataforma XadrezSuíço!"]);
        }

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

        if((($evento->calcula_cbx || $evento->cbx_required) && (!$request->has("cbx_id") || $request->input("cbx_id") == 0))){
            return response()->json(["ok" => 0, "error" => 1, "message" => "Para este evento, é obrigatório que informe o ID CBX (ID de Cadastro junto à Confederação Brasileira de Xadrez), e portanto, é necessário que seja informado para poder efetuar a inscrição. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "registred" => 0, "ask" => 0]);
        }
        if((($evento->calcula_fide || $evento->fide_required) &&
            (
                (
                    $request->input("pais_nascimento_id") == 33 && (!$request->has("cbx_id") || $request->input("cbx_id") == 0)
                )||(
                    $request->input("pais_nascimento_id") != 33 && (!$request->has("fide_id") || $request->input("fide_id") == 0)
                )
            )
        )){
            if($request->input("pais_nascimento_id") == 33){
                return response()->json(["ok" => 0, "error" => 1, "message" => "Para este evento, é obrigatório que jogadores brasileiros informem o ID CBX (ID de Cadastro junto à Confederação Brasileira de Xadrez), e portanto, é necessário que seja informado para poder efetuar a inscrição. Este ID é obrigatório e DEVE SER VÁLIDO, sob pena de remoção da inscrição. Maiores informações podem ser vistas no Passo 4/5 - Cadastros nas Entidades. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "registred" => 0, "ask" => 0]);
            }else{
                return response()->json(["ok" => 0, "error" => 1, "message" => "Para este evento, é obrigatório que jogadores estrangeiros informem o ID FIDE (ID de Cadastro junto à Federação Internacional de Xadrez), e portanto, é necessário que seja informado para poder efetuar a inscrição. Este ID é obrigatório e DEVE SER VÁLIDO, sob pena de remoção da inscrição. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "registred" => 0, "ask" => 0]);

            }
        }
        if ((($evento->is_lichess) && (!$request->has("lichess_username") || $request->input("lichess_username") == ""))) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Para este evento, é obrigatório que possua cadastro e informe o nome de usuário do enxadrista na plataforma Lichess.org e portanto, é necessário que seja informado para poder efetuar a inscrição. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "registred" => 0, "ask" => 0]);
        }
        if ((($evento->is_chess_com) && (!$request->has("chess_com_username") || $request->input("chess_com_username") == ""))) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Para este evento, é obrigatório que possua cadastro e informe o nome de usuário do enxadrista na plataforma Chess.com e portanto, é necessário que seja informado para poder efetuar a inscrição. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "registred" => 0, "ask" => 0]);
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
                    return response()->json(["ok"=>0,"error"=>1,"message"=>"Há um documento que é requerido que não foi informado.", "registred" => 0, "ask" => 0]);
                }
                if(
                    $request->input("tipo_documento_".$tipo_documento_pais->tipo_documento->id) == "" ||
                    $request->input("tipo_documento_".$tipo_documento_pais->tipo_documento->id) == NULL
                ){
                    return response()->json(["ok"=>0,"error"=>1,"message"=>"Há um documento que é requerido que não foi informado.", "registred" => 0, "ask" => 0]);
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
                        $array = [
                            "ok"=>0,
                            "error"=>1,
                            "message" => "Já há um cadastro de Enxadrista com o Documento informado. Deseja utilizar ele?",
                            "registred" => 0,
                            "ask" => 1,
                            "enxadrista_id" => $temEnxadrista->id,
                            "enxadrista_name" => $temEnxadrista->name,
                            "enxadrista_born" => $temEnxadrista->getBorn(),
                            "enxadrista_city" => $temEnxadrista->cidade->name,
                        ];

                        if ($temEnxadrista->estaInscrito($request->input("evento_id"))) {
                            $array["esta_inscrito"] = true;
                        }else{
                            $array["esta_inscrito"] = false;
                        }
                        return response()->json($array);
                    }

                    $validacao = $this->documento_validaDocumento($request->input("tipo_documento_".$tipo_documento_pais->tipo_documento->id),$tipo_documento_pais->tipo_documento->id);
                    if($validacao["ok"] == 0){
                        return response()->json(["ok"=>0,"error"=>1,"message"=>$validacao["message"], "registred" => 0, "ask" => 0]);
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
            return response()->json(["ok"=>0,"error"=>1, "message" => "É obrigatório a inserção de ao menos UM DOCUMENTO.", "registred" => 0, "ask" => 0]);
        }

        $nome_corrigido = trim($nome_corrigido);

        $enxadrista = new Enxadrista;
        if (!$enxadrista->setBorn($request->input("born"))) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "A data de nascimento é inválida.", "registred" => 0, "ask" => 0]);
        }
        if($enxadrista->howOldForEvento($evento->getYear()) > 130 || $enxadrista->howOldForEvento($evento->getYear()) < 0){
            return response()->json(["ok" => 0, "error" => 1, "message" => "A data de nascimento parece inválida. Por favor, verifique e tente novamente.", "registred" => 0, "ask" => 0]);
        }

        $temEnxadrista = Enxadrista::where([["name", "=", $nome_corrigido], ["born", "=", $enxadrista->born]])->first();
        if ($temEnxadrista) {
            if ($temEnxadrista->id) {

                if ($temEnxadrista->estaInscrito($evento->id)) {
                        return response()->json([
                            "ok" => 0,
                            "error" => 1,
                            "message" => "Você já possui cadastro! Porém, já está inscrito neste evento. Caso queira efetuar alguma alteração, entre em contato com a equipe do evento ou envie uma mensagem de email para o endereço de email para " . env("EMAIL_ALTERACAO", "circuitoxadrezcascavel@gmail.com") . ".",
                            "registred" => 0,
                            "ask" => 0,
                            "enxadrista_id" => $temEnxadrista->id,
                        ]);
                }else{
                    if ($temEnxadrista->clube) {
                        return response()->json([
                            "ok" => 0,
                            "error" => 1,
                            "message" => "Você já possui cadastro! Você será direcionado(a) à próxima etapa da inscrição!",
                            "registred" => 1,
                            "ask" => 0,
                            "esta_inscrito" => false,
                            "enxadrista_id" => $temEnxadrista->id,
                        ]);
                    } else {
                        return response()->json([
                            "ok" => 0,
                            "error" => 1,
                            "message" => "Você já possui cadastro! Você será direcionado(a) à próxima etapa da inscrição!",
                            "registred" => 1,
                            "ask" => 0,
                            "esta_inscrito" => 0,
                            "enxadrista_id" => $temEnxadrista->id,
                        ]);
                    }
                }
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

                $enxadrista = CBXRatingController::getRating($enxadrista, false, true, false);
                if (!$enxadrista->encontrado_cbx) {
                    return response()->json(["ok" => 0, "error" => 1, "message" => "O ID CBX informado não existe. Por favor, verifique esta informação e tente novamente. Lembrando que esta informação DEVE SER válida e deve corresponder ao cadastro deste enxadrista!", "registred" => 0, "ask" => 0]);
                }
            }
        }
        if ($request->has("fide_id")) {
            if ($request->input("fide_id") > 0) {
                $enxadrista->fide_id = $request->input("fide_id");

                $enxadrista = FIDERatingController::getRating($enxadrista, false, true, false);
                if (!$enxadrista->encontrado_fide) {
                    return response()->json(["ok" => 0, "error" => 1, "message" => "O ID FIDE informado não existe. Por favor, verifique esta informação e tente novamente. Lembrando que esta informação DEVE SER válida e deve corresponder ao cadastro deste enxadrista!", "registred" => 0, "ask" => 0]);
                }
            }
        }
        if ($request->has("lbx_id")) {
            if ($request->input("lbx_id") > 0) {
                $enxadrista->lbx_id = $request->input("lbx_id");

                $enxadrista = LBXRatingController::getRating($enxadrista, false, true, false);
                if(!$enxadrista->encontrado_lbx){
                    return response()->json(["ok" => 0, "error" => 1, "message" => "O ID LBX informado não existe. Por favor, verifique esta informação e tente novamente. Lembrando que esta informação DEVE SER válida e deve corresponder ao seu cadastro!", "registred" => 0, "ask" => 0]);
                }
            }
        }
        if ($request->has("chess_com_username")) {
            if ($request->input("chess_com_username") != "") {
                if ($this->checkChessComUser($request->input("chess_com_username"))) {
                    $enxadrista->chess_com_username = mb_strtolower($request->input("chess_com_username"));
                }else{
                    return response()->json(["ok" => 0, "error" => 1, "message" => "O usuário do Chess.com não existe. Por favor, verifique esta informação e tente novamente. Lembrando que esta informação DEVE SER válida e deve corresponder ao seu cadastro!", "registred" => 0, "ask" => 0]);
                }
            }
        }
        if ($request->has("lichess_username")) {
            if ($request->input("lichess_username") != "") {
                if ($this->checkLichessUser($request->input("lichess_username"))) {
                    $enxadrista->lichess_username = mb_strtolower($request->input("lichess_username"));
                }else{
                    return response()->json(["ok" => 0, "error" => 1, "message" => "O usuário do Lichess.org não existe. Por favor, verifique esta informação e tente novamente. Lembrando que esta informação DEVE SER válida e deve corresponder ao seu cadastro!", "registred" => 0, "ask" => 0]);
                }
            }
        }
        $enxadrista->cidade_id = $request->input("cidade_id");
        if ($request->has("clube_id")) {
            if ($request->input("clube_id") > 0) {
                $enxadrista->clube_id = $request->input("clube_id");
            }
        }
        $enxadrista->last_cadastral_update = date("Y-m-d H:i:s");
        $enxadrista->save();


        if ($enxadrista->encontrado_cbx) {
            CBXRatingController::getRating($enxadrista, false, false);
        }
        if ($enxadrista->encontrado_fide) {
            FIDERatingController::getRating($enxadrista, false, false);
        }
        if ($enxadrista->encontrado_lbx) {
            LBXRatingController::getRating($enxadrista, false, false);
        }

        foreach($documentos as $documento){
            $documento->enxadrista_id = $enxadrista->id;
            $documento->save();
        }


        if ($enxadrista->email) {
            // EMAIL PARA O ENXADRISTA CADASTRADO
            // $text = "Olá " . $enxadrista->name . "!<br/>";
            // $text .= "Esta é uma confirmação do seu cadastro no Sistema XadrezSuíço implementado pela ".env("IMPLEMENTADO_POR")."<br/>";
            // $text .= "O seu ID de Cadastro é <strong><u>".$enxadrista->id."</u></strong> e você poderá utilizar ele para encontrar seu cadastro para inscrição no Sistema XadrezSuíço implementado pela ".env("IMPLEMENTADO_POR")." e também para poder efetuar a sua confirmação nos eventos que foi utilizado esta implementação do sistema.<br/>";
            // $text .= "Recomendamos que você mantenha salvo este ID/Código de Cadastro para poder agilizar o processo de confirmação ou inscrição.<br/>";
            // $text .= "Além disso, você receberá neste e-mail as confirmações de inscrições efetuadas nesta implementação do Sistema XadrezSuíço.<br/>";
            // $text .= "Atenciosamente.";
            // EmailController::scheduleEmail(
            //     $enxadrista->email,
            //     "Sistema XadrezSuíço (".env("IMPLEMENTADO_POR").") - Cadastro de Enxadrista Realizado - Enxadrista: " . $enxadrista->name,
            //     $text,
            //     $enxadrista
            // );
            EmailController::schedule(
                $enxadrista->email,
                $enxadrista,
                EmailType::CadastroEnxadrista,
                $enxadrista
            );
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

    public function telav2_atualizarEnxadrista($evento_id, $enxadrista_id, Request $request)
    {

        $evento = Evento::find($evento_id);
        $enxadrista = Enxadrista::find($enxadrista_id);
        if(!$enxadrista){
            return response()->json(["ok" => 0, "error" => 1, "message" => "O cadastro do enxadrista não foi encontrado.", "registred" => 0, "ask" => 0]);
        }
        if(
            (
                $enxadrista->name != NULL &&
                $enxadrista->born != NULL &&
                $enxadrista->pais_id != NULL &&
                $enxadrista->cidade_id != NULL &&
                $enxadrista->email != NULL &&
                $enxadrista->sexos_id != NULL &&
                $enxadrista->pais_celular_id != NULL &&
                $enxadrista->celular != NULL &&
                $enxadrista->documentos()->count() > 0
            )
            &&
            $enxadrista->howOldForEvento($evento->getYear()) < 130
            &&
            (
                !(($evento->calcula_cbx || $evento->cbx_required) && (!$enxadrista->cbx_id || $enxadrista->cbx_id == 0))
                ||
                !(
                    ($evento->calcula_fide || $evento->fide_required) &&
                    (
                        (
                            $enxadrista->pais_id == 33 && (!$enxadrista->cbx_id || $enxadrista->cbx_id == 0)
                        ) || (
                            $enxadrista->pais_id != 33 && (!$enxadrista->fide_id || $enxadrista->fide_id == 0)
                        )
                    )
                )
            )
            &&
            ($evento->is_lichess && $enxadrista->lichess_username != NULL && $enxadrista->lichess_username != "")
            &&
            ($evento->is_chess_com && $enxadrista->chess_com_username != NULL && $enxadrista->chess_com_username != "")
            &&
            $enxadrista->last_cadastral_update >= "01-01-".date("Y")." 00:00:00"
        ){
            return response()->json(["ok" => 0, "error" => 1, "message" => "O enxadrista não necessita de atualização de cadastro.", "registred" => 0, "ask" => 0]);
        }

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

        if ((($evento->calcula_cbx || $evento->cbx_required) && (!$request->input("cbx_id") || $request->input("cbx_id") == 0))) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Para este evento, é obrigatório que informe o ID CBX (ID de Cadastro junto à Confederação Brasileira de Xadrez), e portanto, é necessário que seja informado para poder efetuar a inscrição. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "registred" => 0, "ask" => 0]);
        }
        if ((($evento->calcula_fide || $evento->fide_required) &&
            (
                (
                    $request->input("pais_nascimento_id") == 33 && (!$request->input("cbx_id") || $request->input("cbx_id") == 0)
                ) || (
                    $request->input("pais_nascimento_id") != 33 && (!$request->input("fide_id") || $request->input("fide_id") == 0)
                )
            )
        )) {
            if ($request->input("pais_id") == 33) {
                return response()->json(["ok" => 0, "error" => 1, "message" => "Para este evento, é obrigatório que jogadores brasileiros informem o ID CBX (ID de Cadastro junto à Confederação Brasileira de Xadrez), e portanto, é necessário que seja informado para poder efetuar a inscrição. Este ID é obrigatório e DEVE SER VÁLIDO, sob pena de remoção da inscrição. Maiores informações podem ser vistas no Passo 4/5 - Cadastros nas Entidades. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "registred" => 0, "ask" => 0]);
            } else {
                return response()->json(["ok" => 0, "error" => 1, "message" => "Para este evento, é obrigatório que jogadores estrangeiros informem o ID FIDE (ID de Cadastro junto à Federação Internacional de Xadrez), e portanto, é necessário que seja informado para poder efetuar a inscrição. Este ID é obrigatório e DEVE SER VÁLIDO, sob pena de remoção da inscrição. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "registred" => 0, "ask" => 0]);

            }
        }
        if ((($evento->is_lichess) && (!$request->has("lichess_username") || $request->input("lichess_username") == ""))) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Para este evento, é obrigatório que possua cadastro e informe o nome de usuário do enxadrista na plataforma Lichess.org e portanto, é necessário que seja informado para poder efetuar a inscrição. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "registred" => 0, "ask" => 0]);
        }
        if ((($evento->is_chess_com) && (!$request->has("chess_com_username") || $request->input("chess_com_username") == ""))) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Para este evento, é obrigatório que possua cadastro e informe o nome de usuário do enxadrista na plataforma Chess.com e portanto, é necessário que seja informado para poder efetuar a inscrição. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "registred" => 0, "ask" => 0]);
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
                    return response()->json(["ok"=>0,"error"=>1,"message"=>"Há um documento que é requerido que não foi informado.", "registred" => 0, "ask" => 0]);
                }
                if(
                    $request->input("tipo_documento_".$tipo_documento_pais->tipo_documento->id) == "" ||
                    $request->input("tipo_documento_".$tipo_documento_pais->tipo_documento->id) == NULL
                ){
                    return response()->json(["ok"=>0,"error"=>1,"message"=>"Há um documento que é requerido que não foi informado.", "registred" => 0, "ask" => 0]);
                }
            }
            if($request->has("tipo_documento_".$tipo_documento_pais->tipo_documento->id)){
                if(
                    $request->input("tipo_documento_".$tipo_documento_pais->tipo_documento->id) != "" &&
                    $request->input("tipo_documento_".$tipo_documento_pais->tipo_documento->id) != NULL
                ){
                    $documento_valor = trim($request->input("tipo_documento_".$tipo_documento_pais->tipo_documento->id));

                    $temEnxadrista_count = Enxadrista::whereHas("documentos",function($q1) use($request, $tipo_documento_pais,$documento_valor){
                        if($tipo_documento_pais->tipo_documento->id == 1){
                            $q1->where([
                                ["tipo_documentos_id","=",$tipo_documento_pais->tipo_documento->id],
                                ["numero","=",Util::numeros($documento_valor)],
                            ]);
                        }else{
                            $q1->where([
                                ["tipo_documentos_id","=",$tipo_documento_pais->tipo_documento->id],
                                ["numero","=",$documento_valor],
                            ]);
                        }
                    })
                    ->where([
                        ["id","!=",$enxadrista->id]
                    ])
                    ->count();
                    if($temEnxadrista_count > 0){
                        $temEnxadrista = Enxadrista::whereHas("documentos",function($q1) use($request, $tipo_documento_pais,$documento_valor){
                            if($tipo_documento_pais->tipo_documento->id == 1){
                                $q1->where([
                                    ["tipo_documentos_id","=",$tipo_documento_pais->tipo_documento->id],
                                    ["numero","=",Util::numeros($documento_valor)],
                                ]);
                            }else{
                                $q1->where([
                                    ["tipo_documentos_id","=",$tipo_documento_pais->tipo_documento->id],
                                    ["numero","=",$documento_valor],
                                ]);
                            }
                        })
                        ->where([
                            ["id","!=",$enxadrista->id]
                        ])
                        ->first();
                        $array = [
                            "ok"=>0,
                            "error"=>1,
                            "message" => "Já há um cadastro de Enxadrista com o Documento informado. Deseja utilizar ele?",
                            "registred" => 0,
                            "ask" => 1,
                            "enxadrista_id" => $temEnxadrista->id,
                            "enxadrista_name" => $temEnxadrista->name,
                            "enxadrista_born" => $temEnxadrista->getBorn(),
                            "enxadrista_city" => $temEnxadrista->cidade->name,
                        ];

                        if ($temEnxadrista->estaInscrito($request->input("evento_id"))) {
                            $array["esta_inscrito"] = true;
                        }else{
                            $array["esta_inscrito"] = false;
                        }
                        return response()->json($array);
                    }


                    $validacao = $this->documento_validaDocumento($documento_valor,$tipo_documento_pais->tipo_documento->id);
                    if($validacao["ok"] == 0){
                        return response()->json(["ok"=>0,"error"=>1,"message"=>$validacao["message"], "registred" => 0, "ask" => 0]);
                    }

                    $documento = new Documento;
                    $documento->tipo_documentos_id = $tipo_documento_pais->tipo_documento->id;
                    if($tipo_documento_pais->tipo_documento->id == 1){
                        $documento->numero = Util::numeros($documento_valor);
                    }else{
                        $documento->numero = $documento_valor;
                    }

                    $documentos[] = $documento;
                }
            }
        }

        if(count($documentos) == 0){
            return response()->json(["ok"=>0,"error"=>1, "message" => "É obrigatório a inserção de ao menos UM DOCUMENTO.", "registred" => 0, "ask" => 0]);
        }

        $nome_corrigido = trim($nome_corrigido);
        if(!$enxadrista->born){
            if (!$enxadrista->setBorn($request->input("born"))) {
                return response()->json(["ok" => 0, "error" => 1, "message" => "A data de nascimento é inválida.", "registred" => 0, "ask" => 0]);
            }
            if($enxadrista->howOldForEvento($evento->getYear()) >= 130){
                return response()->json(["ok" => 0, "error" => 1, "message" => "A data de nascimento parece inválida. Por favor, verifique e tente novamente.", "registred" => 0, "ask" => 0]);
            }
        }
        if(
            !$enxadrista->name || !$enxadrista->born
        ){
            $temEnxadrista = Enxadrista::where([["name", "=", $nome_corrigido], ["born", "=", $enxadrista->born],["id","!=",$enxadrista->id]])->first();
            if (count($temEnxadrista) > 0) {

                if ($temEnxadrista->estaInscrito($evento->id)) {
                        return response()->json([
                            "ok" => 0,
                            "error" => 1,
                            "message" => "Você já possui cadastro! Porém, já está inscrito neste evento. Caso queira efetuar alguma alteração, entre em contato com a equipe do evento ou envie uma mensagem de email para o endereço de email para " . env("EMAIL_ALTERACAO", "circuitoxadrezcascavel@gmail.com") . ".",
                            "registred" => 0,
                            "ask" => 0,
                            "enxadrista_id" => $temEnxadrista->id,
                        ]);
                }else{
                    if ($temEnxadrista->clube) {
                        return response()->json([
                            "ok" => 0,
                            "error" => 1,
                            "message" => "Você já possui cadastro! Você será direcionado(a) à próxima etapa da inscrição!",
                            "registred" => 1,
                            "ask" => 0,
                            "esta_inscrito" => false,
                            "enxadrista_id" => $temEnxadrista->id,
                        ]);
                    } else {
                        return response()->json([
                            "ok" => 0,
                            "error" => 1,
                            "message" => "Você já possui cadastro! Você será direcionado(a) à próxima etapa da inscrição!",
                            "registred" => 1,
                            "ask" => 0,
                            "esta_inscrito" => 0,
                            "enxadrista_id" => $temEnxadrista->id,
                        ]);
                    }
                }
            }
        }

        if(!$enxadrista->name){
            $enxadrista->name = $nome_corrigido;
            $enxadrista->splitName();
        }
        if(!$enxadrista->sexos_id) $enxadrista->sexos_id = $request->input("sexos_id");
        if(!$enxadrista->pais_id) $enxadrista->pais_id = $request->input("pais_nascimento_id");
        $enxadrista->email = $request->input("email");
        $enxadrista->pais_celular_id = $request->input("pais_celular_id");
        $enxadrista->celular = $request->input("celular");
        if ($request->has("cbx_id")) {
            if ($request->input("cbx_id") > 0) {
                $enxadrista->cbx_id = $request->input("cbx_id");

                $enxadrista = CBXRatingController::getRating($enxadrista, false, true);
                if (!$enxadrista->encontrado_cbx) {
                    return response()->json(["ok" => 0, "error" => 1, "message" => "O ID CBX informado não existe. Por favor, verifique esta informação e tente novamente. Lembrando que esta informação DEVE SER válida e deve corresponder ao cadastro deste enxadrista!", "registred" => 0, "ask" => 0]);
                }
            }else{
                $enxadrista->cbx_id = NULL;
            }
        }else{
            $enxadrista->cbx_id = NULL;
        }
        if ($request->has("fide_id")) {
            if ($request->input("fide_id") > 0) {
                $enxadrista->fide_id = $request->input("fide_id");

                $enxadrista = FIDERatingController::getRating($enxadrista,false,true);
                if(!$enxadrista->encontrado_fide){
                    return response()->json(["ok" => 0, "error" => 1, "message" => "O ID FIDE informado não existe. Por favor, verifique esta informação e tente novamente. Lembrando que esta informação DEVE SER válida e deve corresponder ao seu cadastro!", "registred" => 0, "ask" => 0]);
                }
            }else{
                $enxadrista->fide_id = NULL;
            }
        }else{
            $enxadrista->fide_id = NULL;
        }
        if ($request->has("lbx_id")) {
            if ($request->input("lbx_id") > 0) {
                $enxadrista->lbx_id = $request->input("lbx_id");

                $enxadrista = LBXRatingController::getRating($enxadrista,false,true);
                if(!$enxadrista->encontrado_lbx){
                    return response()->json(["ok" => 0, "error" => 1, "message" => "O ID LBX informado não existe. Por favor, verifique esta informação e tente novamente. Lembrando que esta informação DEVE SER válida e deve corresponder ao seu cadastro!", "registred" => 0, "ask" => 0]);
                }
            }else{
                $enxadrista->lbx_id = NULL;
            }
        }else{
            $enxadrista->lbx_id = NULL;
        }
        if ($request->has("chess_com_username")) {
            if ($request->input("chess_com_username") != "") {
                if ($this->checkChessComUser($request->input("chess_com_username"))) {
                    $enxadrista->chess_com_username = mb_strtolower($request->input("chess_com_username"));
                }else{
                    return response()->json(["ok" => 0, "error" => 1, "message" => "O usuário do Chess.com não existe. Por favor, verifique esta informação e tente novamente. Lembrando que esta informação DEVE SER válida e deve corresponder ao seu cadastro!", "registred" => 0, "ask" => 0]);
                }
            }
        }
        if ($request->has("lichess_username")) {
            if ($request->input("lichess_username") != "") {
                if ($this->checkLichessUser($request->input("lichess_username"))) {
                    $enxadrista->lichess_username = mb_strtolower($request->input("lichess_username"));
                }else{
                    return response()->json(["ok" => 0, "error" => 1, "message" => "O usuário do Lichess.org não existe. Por favor, verifique esta informação e tente novamente. Lembrando que esta informação DEVE SER válida e deve corresponder ao seu cadastro!", "registred" => 0, "ask" => 0]);
                }
            }
        }
        $enxadrista->cidade_id = $request->input("cidade_id");
        if ($request->has("clube_id")) {
            if ($request->input("clube_id") > 0) {
                $enxadrista->clube_id = $request->input("clube_id");
            }else{
                $enxadrista->clube_id = NULL;
            }
        }else{
            $enxadrista->clube_id = NULL;
        }
        $enxadrista->last_cadastral_update = date("Y-m-d H:i:s");
        $enxadrista->save();

        foreach($enxadrista->documentos->all() as $documento){
            $documento->delete();
        }

        foreach($documentos as $documento){
            $documento->enxadrista_id = $enxadrista->id;
            $documento->save();
        }

        if ($enxadrista->id > 0) {
            return response()->json(["ok" => 1, "error" => 0, "enxadrista_id" => $enxadrista->id, "ask" => 0]);
        } else {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um erro inesperado aconteceu. Por favor, tente novamente mais tarde.", "registred" => 0, "ask" => 0]);
        }
    }





    public function telav2_getInscricaoDados($id, $inscricao_id)
    {
        $user = Auth::user();
        $evento = Evento::find($id);
        if (!$user) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Você não possui permissão para fazer isto.", "registred" => 0]);
        }
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4,5]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Você não possui permissão para fazer isto.", "registred" => 0]);
        }

        $inscricao = Inscricao::find($inscricao_id);
        if ($inscricao) {

            $retorno = array();
            $retorno["id"] = $inscricao->enxadrista->id;
            $retorno["name"] = $inscricao->enxadrista->name;
            $retorno["cbx_id"] = $inscricao->enxadrista->cbx_id;
            $retorno["fide_id"] = $inscricao->enxadrista->fide_id;
            $retorno["lbx_id"] = $inscricao->enxadrista->lbx_id;
            $retorno["chess_com_username"] = $inscricao->enxadrista->chess_com_username;
            $retorno["lichess_username"] = $inscricao->enxadrista->lichess_username;
            $retorno["born"] = $inscricao->enxadrista->getBorn();
            $retorno["cidade"] = array("id"=>$inscricao->cidade->id,"name"=>$inscricao->cidade->name);
            $retorno["cidade"]["estado"] = array("id"=>$inscricao->cidade->estado->id,"name"=>$inscricao->cidade->estado->nome);
            $retorno["cidade"]["estado"]["pais"] = array("id"=>$inscricao->cidade->estado->pais->id,"name"=>$inscricao->cidade->estado->pais->nome);
            $retorno["clube"] = ($inscricao->clube) ? array("id"=>$inscricao->clube->id,"name"=>$inscricao->clube->name) : array("id" => 0);
            $retorno["categoria"] = array("id"=>$inscricao->categoria->id,"name"=>$inscricao->categoria->name);
            $retorno["categorias"] = array();
            $categorias = $this->categoriasEnxadrista($evento,$inscricao->enxadrista);
            if(count($categorias) == 0){
                return response()->json(["ok" => 0, "error"=>1, "message" => "Não há categorias que você pode se inscrever neste evento."]);
            }
            foreach($categorias as $categoria){
                $retorno["categorias"][] = array("id"=>$categoria->categoria->id,"name"=>$categoria->categoria->name);
            }
            return response()->json(["ok" => 1, "error"=>0, "data" => $retorno]);


            // if ($inscricao->clube) {
            //     return response()->json(["ok" => 1, "error" => 0, "enxadrista_id" => $inscricao->enxadrista->id, "cidade_id" => $inscricao->cidade->id, "categoria_id" => $inscricao->categoria->id, "clube_id" => $inscricao->clube->id]);
            // } else {
            //     return response()->json(["ok" => 1, "error" => 0, "enxadrista_id" => $inscricao->enxadrista->id, "cidade_id" => $inscricao->cidade->id, "categoria_id" => $inscricao->categoria->id, "clube_id" => 0]);
            // }
        } else {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Não há enxadrista com esse código!"]);
        }
    }


    public function telav2_confirmarInscricao($id,Request $request)
    {
        $user = Auth::user();
        $evento = Evento::find($id);
        if($user){
            if (
                !$user->hasPermissionGlobal() &&
                !$user->hasPermissionEventByPerfil($evento->id, [4,5]) &&
                !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
            ) {
                return response()->json(["ok" => 0, "error" => 1, "message" => "Você não possui permissão para fazer isto.", "registred" => 0]);
            }

            if (
                !$request->has("inscricao_id") || !$request->has("categoria_id") || !$request->has("cidade_id") || !$request->has("evento_id")
            ) {
                return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!", "registred" => 0]);
            } elseif (
                $request->input("inscricao_id") == null || $request->input("inscricao_id") == "" ||
                $request->input("categoria_id") == null || $request->input("categoria_id") == "" ||
                $request->input("cidade_id") == null || $request->input("cidade_id") == "" ||
                $request->input("evento_id") == null || $request->input("evento_id") == ""
            ) {
                return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!"]);
            } elseif (
                !$request->has("categoria_conferida")
            ) {
                return response()->json(["ok" => 0, "error" => 1, "message" => "Você deve verificar a categoria e marcar o campo 'Categoria conferida'.", "registred" => 0]);
            }

            $inscricao = Inscricao::find($request->input("inscricao_id"));
            if (!$inscricao) {
                return response()->json(["ok" => 0, "error" => 1, "message" => "Não existe um inscrição com o código informado!"]);
            }
            if ($inscricao->confirmado) {
                return response()->json(["ok" => 0, "error" => 1, "message" => "A inscrição já está confirmada!"]);
            }
            $enxadrista = $inscricao->enxadrista;
            $torneio = null;
            if ($request->input("categoria_id") != $inscricao->categoria_id) {
                $evento = Evento::find($request->input("evento_id"));

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
                $inscricao->categoria_id = $categoria->id;
                $inscricao->torneio_id = $torneio->id;
            }

            if ($inscricao->cidade_id != $request->input("cidade_id")) {
                $inscricao->cidade_id = $request->input("cidade_id");
            }

            if ($inscricao->clube_id != $request->input("clube_id")) {
                if ($request->has("clube_id")) {
                    if ($request->input("clube_id") > 0) {
                        $inscricao->clube_id = $request->input("clube_id");
                    }
                }
            }

            $inscricao->confirmado = true;
            $inscricao->regulamento_aceito = true;
            $inscricao->save();

            if ($request->has("atualizar_cadastro")) {
                $enxadrista = Enxadrista::find($inscricao->enxadrista_id);
                $enxadrista->cidade_id = $inscricao->cidade_id;
                if ($request->has("clube_id")) {
                    if ($request->input("clube_id") > 0) {
                        $enxadrista->clube_id = $request->input("clube_id");
                    } else {
                        $enxadrista->clube_id = null;
                    }
                } else {
                    $enxadrista->clube_id = null;
                }
                $enxadrista->save();

                if ($inscricao->id > 0) {
                    if ($inscricao->confirmado) {
                        return response()->json(["ok" => 1, "error" => 0, "updated" => 1, "confirmed" => 1]);
                    } else {
                        return response()->json(["ok" => 1, "error" => 0, "updated" => 1, "confirmed" => 0]);
                    }
                } else {
                    return response()->json(["ok" => 0, "error" => 1, "message" => "Um erro inesperado aconteceu. Por favor, tente novamente mais tarde.", "updated" => 1]);
                }
            } else {
                if ($inscricao->id > 0) {
                    if ($inscricao->confirmado) {
                        return response()->json(["ok" => 1, "error" => 0, "updated" => 0, "confirmed" => 1]);
                    } else {
                        return response()->json(["ok" => 1, "error" => 0, "updated" => 0, "confirmed" => 0]);
                    }
                } else {
                    return response()->json(["ok" => 0, "error" => 1, "message" => "Um erro inesperado aconteceu. Por favor, tente novamente mais tarde.", "updated" => 0]);
                }
            }
        } else {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Você não possui permissão para fazer isto.", "registred" => 0]);
        }
    }

    public function telav2_desconfirmarInscricao($id,$inscricao_id)
    {
        $user = Auth::user();
        $evento = Evento::find($id);
        if($user){
            if (
                !$user->hasPermissionGlobal() &&
                !$user->hasPermissionEventByPerfil($evento->id, [4,5]) &&
                !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
            ) {
                return response()->json(["ok" => 0, "error" => 1, "message" => "Você não possui permissão para fazer isto.", "registred" => 0]);
            }

            $inscricao = Inscricao::find($inscricao_id);
            if($inscricao){
                $inscricao->confirmado = false;
                $inscricao->save();

                return response()->json(["ok" => 1, "error" => 0]);
            }
            return response()->json(["ok" => 0, "error" => 1, "message" => "Você não possui permissão para fazer isto.", "registred" => 0]);
        } else {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Você não possui permissão para fazer isto.", "registred" => 0]);
        }
    }



    public function telav2_adicionarNovoEstado(Request $request)
    {
        if (
            !$request->has("name") || !$request->has("pais_id")
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!", "registred" => 0]);
        } elseif (
            $request->input("name") == null || $request->input("name") == "" ||
            $request->input("pais_id") == null || $request->input("pais_id") == ""
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!", "registred" => 0]);
        }
        $pais = Pais::find($request->input("pais_id"));
        if(!$pais){
            return response()->json(["ok" => 0, "error" => 1, "message" => "O estado não foi encontrado. Por favor, verifique e tente novamente!", "registred" => 0]);
        }
        if($pais->id == 33){
            return response()->json(["ok" => 0, "error" => 1, "message" => "Este país não permite o cadastro de cidade.", "registred" => 0]);
        }

        $estado = new Estado;

        $temEstado = Estado::where([["nome", "=", mb_strtoupper($request->input("name"))],["pais_id","=",$request->input("pais_id")]])->first();
        if (count($temEstado) > 0) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Este estado já está cadastrado! Selecionamos ele para você.", "registred" => 1, "estados_id" => $temEstado->id, "pais_id" => $temEstado->pais->id]);
        }

        $estado->nome = mb_strtoupper($request->input("name"));
        $estado->pais_id = mb_strtoupper($request->input("pais_id"));
        $estado->save();
        if ($estado->id > 0) {
            return response()->json(["ok" => 1, "error" => 0, "estados_id" => $estado->id, "pais_id" => $estado->pais->id]);
        } else {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um erro inesperado aconteceu. Por favor, tente novamente mais tarde.", "registred" => 0]);
        }
    }

    public function telav2_adicionarNovaCidade(Request $request)
    {
        if (
            !$request->has("name") || !$request->has("estados_id")
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!", "registred" => 0]);
        } elseif (
            $request->input("name") == null || $request->input("name") == "" ||
            $request->input("estados_id") == null || $request->input("estados_id") == ""
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!", "registred" => 0]);
        }
        $estado = Estado::find($request->input("estados_id"));
        if(!$estado){
            return response()->json(["ok" => 0, "error" => 1, "message" => "O estado não foi encontrado. Por favor, verifique e tente novamente!", "registred" => 0]);
        }
        if($estado->pais_id == 33){
            return response()->json(["ok" => 0, "error" => 1, "message" => "Este país não permite o cadastro de cidade.", "registred" => 0]);
        }

        $cidade = new Cidade;

        $temCidade = Cidade::where([["name", "=", mb_strtoupper($request->input("name"))],["estados_id","=",$request->input("estados_id")]])->first();
        if (count($temCidade) > 0) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Esta cidade já está cadastrada! Selecionamos ela para você.", "registred" => 1, "cidade_id" => $temCidade->id, "estados_id" => $temCidade->estado->id, "pais_id" => $temCidade->estado->pais->id]);
        }

        $cidade->name = mb_strtoupper($request->input("name"));
        $cidade->estados_id = mb_strtoupper($request->input("estados_id"));
        $cidade->save();
        if ($cidade->id > 0) {
            return response()->json(["ok" => 1, "error" => 0, "cidade_id" => $cidade->id, "estados_id" => $cidade->estado->id, "pais_id" => $cidade->estado->pais->id]);
        } else {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um erro inesperado aconteceu. Por favor, tente novamente mais tarde.", "registred" => 0]);
        }
    }

    public function telav2_adicionarNovoClube(Request $request)
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
            return response()->json(["ok" => 0, "error" => 1, "message" => "Este clube já está cadastrado! Selecionamos ele para você.", "registred" => 1, "clube_id" => $temClube->id, "clube_nome" => $temClube->name, "cidade_nome" => $temClube->cidade->estado->pais->nome."-".$temClube->cidade->name."/".$temClube->cidade->estado->nome]);
        }

        $clube->name = mb_strtoupper($request->input("name"));
        $clube->cidade_id = mb_strtoupper($request->input("cidade_id"));
        $clube->save();
        if ($clube->id > 0) {
            return response()->json(["ok" => 1, "error" => 0, "clube_id" => $clube->id, "clube_nome" => $clube->name, "cidade_nome" => $clube->cidade->estado->pais->nome."-".$clube->cidade->name."/".$clube->cidade->estado->nome]);
        } else {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um erro inesperado aconteceu. Por favor, tente novamente mais tarde.", "registred" => 0]);
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
        $categorias = $evento->categorias()->whereHas("categoria", function ($q1) use ($enxadrista, $evento) {
            $q1->where(function ($q2) use ($enxadrista, $evento) {
                    $q2->where(function ($q3) use ($enxadrista, $evento) {
                        $q3->where([["idade_minima", "<=", $enxadrista->howOldForEvento($evento->getYear())]]);
                        $q3->where([["idade_maxima", ">=", $enxadrista->howOldForEvento($evento->getYear())]]);
                    })
                        ->orWhere(function ($q3) use ($enxadrista, $evento) {
                            $q3->where([["idade_minima", "<=", $enxadrista->howOldForEvento($evento->getYear())]]);
                            $q3->where([["idade_maxima", "=", null]]);
                        })
                        ->orWhere(function ($q3) use ($enxadrista, $evento) {
                            $q3->where([["idade_minima", "=", null]]);
                            $q3->where([["idade_maxima", ">=", $enxadrista->howOldForEvento($evento->getYear())]]);
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


    public function checkChessComUser($username){
        $response = \Httpful\Request::get('https://api.chess.com/pub/player/'.mb_strtolower($username))
            ->send();
            Log::debug("ChessCom User: ".mb_strtolower($username));
            Log::debug("ChessCom Uri: ".'https://api.chess.com/pub/player/'.mb_strtolower($username));
            Log::debug("ChessCom code: ".$response->code);
        if($response->code == 200){
            return true;
        }
        return false;
    }
    public function checkChessComUser_api(Request $request){
        if($request->has("username")){
            if($request->input("username") != ""){
                if($this->checkChessComUser($request->input("username"))){
                    return response()->json(["ok" => 1, "error" => 0]);
                }else{
                    return response()->json(["ok" => 0, "error" => 1, "message" => "Usuário não encontrado."]);
                }
            }
        }
        return response()->json(["ok"=>0,"error"=>1]);
    }


    public function checkLichessUser($username){
        $response = \Httpful\Request::get('https://lichess.org/api/user/'.$username)
            ->expectsJson()
            ->send();
        if($response->code == 200){
            return true;
        }
        return false;
    }
    public function checkLichessUser_api(Request $request){
        if($request->has("username")){
            if($request->input("username") != ""){
                if($this->checkLichessUser($request->input("username"))){
                    return response()->json(["ok" => 1, "error" => 0]);
                }else{
                    return response()->json(["ok" => 0, "error" => 1, "message" => "Usuário não encontrado."]);
                }
            }
        }
        return response()->json(["ok"=>0,"error"=>1]);
    }





    public function documento_validaDocumento($documento,$tipo_documento_id,$validador=null){
        if($tipo_documento_id == 1){
            $documento = Util::numeros($documento);
        }
        $documento_len = strlen($documento);

        // tamanho
        if($documento_len < 4){
            return ["ok"=>0,"error"=>1,"message"=>"O documento informado é muito curto."];
        }

        // caracteres
        $crc1 = substr($documento,0,1);
        $all_caracts_is_same = true;
        for($i = 1; $i < $documento_len; $i++){
            if($crc1 != substr($documento,$i,1)){
                $all_caracts_is_same = false;
            }
        }

        if($all_caracts_is_same){
            return ["ok"=>0,"error"=>1,"message"=>"O documento informado é inválido."];
        }

        if(
            count(explode("NAO",strtoupper($documento))) > 1 ||
            count(explode("NÃO",strtoupper($documento))) > 1 ||
            count(explode("TENHO",strtoupper($documento))) > 1 ||
            count(explode("TEM",strtoupper($documento))) > 1 ||
            count(explode("TEM",strtoupper($documento))) > 1
        ){
            return ["ok" => 0, "error" => 1, "message" => "O documento informado é inválido."];
        }

        return ["ok"=>1,"error"=>0];
    }
}

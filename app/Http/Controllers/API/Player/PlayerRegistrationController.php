<?php

namespace App\Http\Controllers\API\Player;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Controllers\EmailController;
use App\Http\Controllers\CBXRatingController;
use App\Http\Controllers\FIDERatingController;
use App\Http\Controllers\LBXRatingController;

use App\Enum\EmailType;

use App\Http\Util\Util;

use App\Enxadrista;
use App\Documento;
use App\TipoDocumentoPais;

use Log;

class PlayerRegistrationController extends Controller
{
    public function register(Request $request)
    {
        // $user = Auth::user();

        if (
            !$request->has("accepts.policy")
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Você deve aceitar o termo de uso e política de privacidade da plataforma XadrezSuíço!"]);
        }
        if (
            $request->input("accepts.policy") == 0
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Você deve aceitar o termo de uso e política de privacidade da plataforma XadrezSuíço!"]);
        }

        if (
            !$request->has("name") ||
            !$request->has("birthday") ||
            !$request->has("sex_id") ||
            !$request->has("email") ||
            !$request->has("born_country_id") ||
            !$request->has("cellphone_country_id") ||
            !$request->has("cellphone") ||
            !$request->has("city_id")
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "registred" => 0, "ask" => 0]);
        } elseif (
            $request->input("name") == null || $request->input("name") == "" ||
            $request->input("birthday") == null || $request->input("birthday") == "" ||
            $request->input("sex_id") == null || $request->input("sex_id") == "" ||
            $request->input("email") == null || $request->input("email") == "" ||
            $request->input("born_country_id") == null || $request->input("born_country_id") == "" ||
            $request->input("cellphone_country_id") == null || $request->input("cellphone_country_id") == "" ||
            $request->input("cellphone") == null || $request->input("cellphone") == "" ||
            $request->input("city_id") == null || $request->input("city_id") == ""
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "registred" => 0, "ask" => 0]);
        }

        $validator = \Validator::make($request->all(), [
            'email' => 'required|string|email:rfc,dns|max:255',
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

        if (
            is_int($nome_corrigido)
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "O nome informado do enxadrista não é válido."]);
        }

        // print_r($request->all());

        $documentos = array();

        foreach(TipoDocumentoPais::where([["pais_id","=",$request->input("born_country_id")]])->get() as $tipo_documento_pais){
            if($tipo_documento_pais->e_requerido){
                if($request->has("documents.".$tipo_documento_pais->tipo_documento->id)){
                    return response()->json(["ok"=>0,"error"=>1,"message"=>"Há um documento que é requerido que não foi informado.", "registred" => 0, "ask" => 0]);
                }
                if(
                    $request->documents[$tipo_documento_pais->tipo_documento->id] == "" ||
                    $request->documents[$tipo_documento_pais->tipo_documento->id] == NULL
                ){
                    return response()->json(["ok"=>0,"error"=>1,"message"=>"Há um documento que é requerido que não foi informado.", "registred" => 0, "ask" => 0]);
                }
            }
            if($request->has("documents.".$tipo_documento_pais->tipo_documento->id)){
                if(
                    $request->documents[$tipo_documento_pais->tipo_documento->id] != "" &&
                    $request->documents[$tipo_documento_pais->tipo_documento->id] != NULL
                ){

                    $enxadrista_count = Enxadrista::whereHas("documentos",function($q1) use($request, $tipo_documento_pais){
                        if($tipo_documento_pais->tipo_documento->id == 1){
                            $q1->where([
                                ["tipo_documentos_id","=",$tipo_documento_pais->tipo_documento->id],
                                ["numero","=",Util::numeros($request->documents[$tipo_documento_pais->tipo_documento->id])],
                            ]);
                        }else{
                            $q1->where([
                                ["tipo_documentos_id","=",$tipo_documento_pais->tipo_documento->id],
                                ["numero","=",$request->documents[$tipo_documento_pais->tipo_documento->id]],
                            ]);
                        }
                    })
                    ->whereDoesntHave("configs", function ($q1) {
                        $q1->where([["key", "=", "united_to"]]);
                    })->count();
                    if($enxadrista_count > 0){
                        $enxadrista = Enxadrista::whereHas("documentos",function($q1) use($request, $tipo_documento_pais){
                            if($tipo_documento_pais->tipo_documento->id == 1){
                                $q1->where([
                                    ["tipo_documentos_id","=",$tipo_documento_pais->tipo_documento->id],
                                    ["numero","=",Util::numeros($request->documents[$tipo_documento_pais->tipo_documento->id])],
                                ]);
                            }else{
                                $q1->where([
                                    ["tipo_documentos_id","=",$tipo_documento_pais->tipo_documento->id],
                                    ["numero","=",$request->documents[$tipo_documento_pais->tipo_documento->id]],
                                ]);
                            }
                        })
                        ->whereDoesntHave("configs", function ($q1) {
                            $q1->where([["key", "=", "united_to"]]);
                        })->first();

                        $array = [
                            "ok"=>0,
                            "error"=>1,
                            "message" => "Já há um cadastro de Enxadrista com o Documento informado. Deseja utilizar ele?",
                            "result" => true,
                            "player" => [
                                "id" => $enxadrista->id,
                                "name" => $enxadrista->name,
                                "birthday" => $enxadrista->getBorn(),
                                "city" => $enxadrista->cidade->name,
                            ]
                        ];
                        return response()->json($array);
                    }

                    $validacao = $this->documento_validaDocumento($request->documents[$tipo_documento_pais->tipo_documento->id],$tipo_documento_pais->tipo_documento->id);
                    if($validacao["ok"] == 0){
                        return response()->json(["ok"=>0,"error"=>1,"message"=>$validacao["message"], "registred" => 0, "ask" => 0]);
                    }

                    $documento = new Documento;
                    $documento->tipo_documentos_id = $tipo_documento_pais->tipo_documento->id;
                    if($tipo_documento_pais->tipo_documento->id == 1){
                        $documento->numero = Util::numeros($request->documents[$tipo_documento_pais->tipo_documento->id]);
                    }else{
                        $documento->numero = $request->documents[$tipo_documento_pais->tipo_documento->id];
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
        if (!$enxadrista->setBorn($request->input("birthday"))) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "A data de nascimento é inválida.", "registred" => 0, "ask" => 0]);
        }
        if($enxadrista->howOld() > 130 || $enxadrista->howOld() <= 0){
            return response()->json(["ok" => 0, "error" => 1, "message" => "A data de nascimento parece inválida. Por favor, verifique e tente novamente.", "registred" => 0, "ask" => 0]);
        }

        $temEnxadrista = Enxadrista::where([
            ["name", "=", $nome_corrigido],
            ["born", "=", $enxadrista->born]
        ])
        ->whereDoesntHave("configs", function ($q1) {
            $q1->where([["key", "=", "united_to"]]);
        })->first();
        if ($temEnxadrista) {
            if ($temEnxadrista->id) {
                if ($temEnxadrista->clube) {
                    return response()->json([
                        "ok" => 0,
                        "error" => 1,
                        "message" => "Você já possui cadastro!",
                        "registred" => 1,
                        "ask" => 0,
                        "player" => [
                            "id" => $temEnxadrista->id,
                            "name" => $temEnxadrista->name,
                            "birthday" => $temEnxadrista->getBorn(),
                            "city" => $temEnxadrista->cidade->name,
                        ]
                    ]);
                } else {
                    return response()->json([
                        "ok" => 0,
                        "error" => 1,
                        "message" => "Você já possui cadastro!",
                        "registred" => 1,
                        "ask" => 0,
                        "player" => [
                            "id" => $temEnxadrista->id,
                            "name" => $temEnxadrista->name,
                            "birthday" => $temEnxadrista->getBorn(),
                            "city" => $temEnxadrista->cidade->name,
                        ]
                    ]);
                }
            }
        }

        $enxadrista->name = $nome_corrigido;
        $enxadrista->splitName();
        $enxadrista->sexos_id = $request->input("sex_id");
        $enxadrista->email = $request->input("email");
        $enxadrista->pais_id = $request->input("born_country_id");
        $enxadrista->pais_celular_id = $request->input("cellphone_country_id");
        $enxadrista->celular = $request->input("cellphone");
        $enxadrista->cidade_id = $request->input("city_id");
        if ($request->has("club_id")) {
            if ($request->input("club_id") > 0) {
                $enxadrista->clube_id = $request->input("club_id");
            }
        }
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
            EmailController::schedule(
                $enxadrista->email,
                $enxadrista,
                EmailType::CadastroEnxadrista,
                $enxadrista
            );
        }

        if ($enxadrista->id > 0) {
            $array = [
                "ok"=>1,
                "error"=>0,
                "result" => true,
                "player" => [
                    "id" => $enxadrista->id,
                    "name" => $enxadrista->name,
                    "birthday" => $enxadrista->getBorn(),
                    "city_name" => $enxadrista->cidade->getName(),
                ]
            ];
            return response()->json($array);
        } else {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um erro inesperado aconteceu. Por favor, tente novamente mais tarde.", "result" => false]);
        }
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


    public function checkExists(Request $request){
        if($request->has("name") && $request->has("birthday")){
            $enxadrista = new Enxadrista;
            if (!$enxadrista->setBorn($request->input("birthday"))) {
                return response()->json(["ok" => 0, "error" => 1, "message" => "A data de nascimento é inválida.", "registred" => 0, "ask" => 0]);
            }
            if($enxadrista->howOld() > 130 || $enxadrista->howOld() <= 0){
                return response()->json(["ok" => 0, "error" => 1, "message" => "A data de nascimento parece inválida. Por favor, verifique e tente novamente.", "registred" => 0, "ask" => 0]);
            }

            if(Enxadrista::where([
                ["name","=",$request->name],["born","=",$enxadrista->born]
            ])
            ->whereDoesntHave("configs", function ($q1) {
                $q1->where([["key", "=", "united_to"]]);
            })->count() > 0){
                $enxadrista = Enxadrista::where([
                    ["name","=",$request->name],["born","=",$enxadrista->born]
                ])
                ->whereDoesntHave("configs", function ($q1) {
                    $q1->where([["key", "=", "united_to"]]);
                })->first();
                return response()->json([
                    "ok" => 1,
                    "error" => 0,
                    "message" => "Você já possui cadastro!",
                    "result" => true,
                    "player" => [
                        "id" => $enxadrista->id,
                        "name" => $enxadrista->name,
                        "birthday" => $enxadrista->getBorn(),
                        "city_name" => $enxadrista->cidade->getName(),
                    ]
                ]);
            }else{
                return response()->json(["ok"=>1,"error"=>0,"result"=>false]);
            }
        }else if($request->has("documents")){
            foreach($request->documents as $key => $document){
                $enxadrista_count = Enxadrista::whereHas("documentos",function($q1) use($key, $document){
                    if($key == 1){
                        $q1->where([
                            ["tipo_documentos_id","=",$key],
                            ["numero","=",Util::numeros($document)],
                        ]);
                    }else{
                        $q1->where([
                            ["tipo_documentos_id","=",$key],
                            ["numero","=",$document],
                        ]);
                    }
                })
                ->whereDoesntHave("configs", function ($q1) {
                    $q1->where([["key", "=", "united_to"]]);
                })
                ->count();
                if($enxadrista_count > 0){
                    $enxadrista = Enxadrista::whereHas("documentos",function($q1) use($key, $document){
                        if($key == 1){
                            $q1->where([
                                ["tipo_documentos_id","=",$key],
                                ["numero","=",Util::numeros($document)],
                            ]);
                        }else{
                            $q1->where([
                                ["tipo_documentos_id","=",$key],
                                ["numero","=",$document],
                            ]);
                        }
                    })
                    ->whereDoesntHave("configs", function ($q1) {
                        $q1->where([["key", "=", "united_to"]]);
                    })
                    ->first();

                    $array = [
                        "ok"=>1,
                        "error"=>0,
                        "message" => "Já há um cadastro de Enxadrista com o Documento informado.",
                        "result" => true,
                        "player" => [
                            "id" => $enxadrista->id,
                            "name" => $enxadrista->name,
                            "birthday" => $enxadrista->getNascimentoPublico(),
                            "city_name" => $enxadrista->cidade->getName(),
                        ]
                    ];
                    return response()->json($array);
                }
            }
        }
        return response()->json(["ok"=>1,"error"=>0,"result"=>false]);
    }
}

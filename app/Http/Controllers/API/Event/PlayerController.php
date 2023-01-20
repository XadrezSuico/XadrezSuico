<?php

namespace App\Http\Controllers\API\Event;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Controllers\External\XadrezSuicoPagController;

use App\Evento;
use App\Enxadrista;

class PlayerController extends Controller
{
    public function search($uuid,Request $request){
        if($uuid){
            if(Evento::where([["uuid","=",$uuid]])->count() == 0){
                return response()->json(["ok"=>0,"error"=>1,"message"=>"Evento não encontrado","httpcode"=>404],404);
            }
            $evento = Evento::where([["uuid","=",$uuid]])->first();


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
            foreach($enxadristas as $enxadrista){

                $rating = $enxadrista->ratingParaEvento($evento->id);
                $item = array();
                $item["id"] = $enxadrista->id;
                $item["name"] = $enxadrista->name;
                $item["birthday"] = $enxadrista->getNascimentoPublico();
                $item["fide_id"] = ($enxadrista->fide_id) ? intval($enxadrista->fide_id) : null;
                $item["cbx_id"] = ($enxadrista->cbx_id) ? intval($enxadrista->cbx_id) : null;
                $item["lbx_id"] = ($enxadrista->cbx_id) ? intval($enxadrista->lbx_id) : null;
                $item["chesscom_username"] = $enxadrista->chess_com_username;
                $item["lichess_username"] = $enxadrista->lichess_username;
                $item["city_name"] = $enxadrista->cidade->getName();
                $item["club_name"] = ($enxadrista->clube) ? $enxadrista->clube->getFullName() : "Sem Clube";

                if($enxadrista->estaInscrito($evento->id)){
                    $item["can_register"] = false;
                    $item["register_status"] = "Inscrito no Evento";
                }else{
                    $item["can_register"] = true;
                    $item["register_status"] = "Não está inscrito no Evento";
                }

                $results[] = $item;
            }


            $total = Enxadrista::where([
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
            })->orderBy("name", "ASC")
            ->count();


            return response()->json(["ok"=>1, "error"=>0, "players" => $results, "result" => ($total > 30) ? true : false]);
        }
        return response()->json(["ok"=>0,"error"=>1,"message"=>"Evento não encontrado","httpcode"=>404],404);
    }
    public function get($uuid,$id,Request $request){
        if($uuid){
            if(Evento::where([["uuid","=",$uuid]])->count() == 0){
                return response()->json(["ok"=>0,"error"=>1,"message"=>"Evento não encontrado.","httpcode"=>404],404);
            }
            $evento = Evento::where([["uuid","=",$uuid]])->first();


            $count = Enxadrista::where([
                ["id", "=", $id],
            ])->count();

            if($count == 0){
                return response()->json(["ok"=>0,"error"=>1,"message"=>"Enxadrista não encontrado.","httpcode"=>404],404);
            }
            $enxadrista = Enxadrista::where([
                ["id", "=", $id],
            ])->first();

            if($enxadrista->estaInscrito($evento->id)){
                return response()->json(["ok"=>0,"error"=>1,"message"=>"O enxadrista já está inscrito neste evento.","httpcode"=>400],400);
            }


            $rating = $enxadrista->ratingParaEvento($evento->id);
            $player = array();
            $player["id"] = $enxadrista->id;
            $player["name"] = $enxadrista->name;
            $player["birthday"] = $enxadrista->getNascimentoPublico();
            $player["fide_id"] = ($enxadrista->fide_id) ? intval($enxadrista->fide_id) : null;
            $player["cbx_id"] = ($enxadrista->cbx_id) ? intval($enxadrista->cbx_id) : null;
            $player["lbx_id"] = ($enxadrista->cbx_id) ? intval($enxadrista->lbx_id) : null;
            $player["chesscom_username"] = $enxadrista->chess_com_username;
            $player["lichess_username"] = $enxadrista->lichess_username;
            $player["city_name"] = $enxadrista->cidade->getName();
            $player["club_name"] = ($enxadrista->clube) ? $enxadrista->clube->getFullName() : "Sem Clube";

            $player["city"] = $enxadrista->cidade->toAPIObject(true);
            if($enxadrista->clube) $player["club"] = $enxadrista->clube->toAPIObject(true);

            $fields = $this->getNeedUpdateFields($evento,$enxadrista);

            $categories = array();
            if(count($fields) == 0){
                $xadrezsuicopag_controller = XadrezSuicoPagController::getInstance();
                foreach($this->categoriesPlayer($evento,$enxadrista) as $categoria){
                    if($evento->isPaid()){
                        if($categoria->xadrezsuicopag_uuid != ""){
                            $xadrezsuicopag_category_request = $xadrezsuicopag_controller
                                                                            ->factory("categories")
                                                                            ->get(
                                                                                $evento->xadrezsuicopag_uuid,
                                                                                $categoria->xadrezsuicopag_uuid
                                                                            );

                            if($xadrezsuicopag_category_request->ok){
                                if($xadrezsuicopag_category_request->category->actual_price){
                                    $categories[] = array(
                                        "id"=>$categoria->categoria->id,
                                        "name"=>$categoria->categoria->name,
                                        "price"=>$xadrezsuicopag_category_request->category->actual_price->price
                                    );
                                }
                            }
                        }else{
                            $categories[] = array("id"=>$categoria->categoria->id,"name"=>$categoria->categoria->name,"price"=>0);
                        }
                    }else{
                        $categories[] = array("id"=>$categoria->categoria->id,"name"=>$categoria->categoria->name,"price"=>0);
                    }
                }
            }


            return response()->json(["ok"=>1, "error"=>0, "player" => $player, "fields" => $fields, "categories" => $categories]);
        }
        return response()->json(["ok"=>0,"error"=>1,"message"=>"Evento não encontrado","httpcode"=>404],404);
    }

    public function complete($uuid,$id,Request $request){
        if($uuid){
            if(Evento::where([["uuid","=",$uuid]])->count() == 0){
                return response()->json(["ok"=>0,"error"=>1,"message"=>"Evento não encontrado.","httpcode"=>404],404);
            }
            $evento = Evento::where([["uuid","=",$uuid]])->first();


            $count = Enxadrista::where([
                ["id", "=", $id],
            ])->count();

            if($count == 0){
                return response()->json(["ok"=>0,"error"=>1,"message"=>"Enxadrista não encontrado.","httpcode"=>404],404);
            }
            $enxadrista = Enxadrista::where([
                ["id", "=", $id],
            ])->first();

            $need_update_fields = $this->getNeedUpdateFields($evento,$enxadrista);
            if(count($need_update_fields) == 0){
                return response()->json(["ok"=>0,"error"=>1,"message"=>"Não há campos para atualização deste enxadrista.","httpcode"=>404],404);
            }

            foreach($need_update_fields as $need_update_field){
                switch($need_update_field){
                    case "name":
                        if(isset($request->name)){
                            if($request->name == ""){
                                return response()->json(["ok"=>0,"error"=>1,"message"=>"O campo 'Nome' é obrigatório ser atualizado também."]);
                            }

                            // Algoritmo para eliminar os problemas com espaçamentos duplos ou até triplos.
                            $nome_corrigido = "";

                            $part_names = explode(" ", mb_strtoupper($request->name));
                            foreach ($part_names as $part_name) {
                                if ($part_name != ' ') {
                                    $trim = trim($part_name);
                                    if (strlen($trim) > 0) {
                                        $nome_corrigido .= $trim;
                                        $nome_corrigido .= " ";
                                    }
                                }
                            }

                            $enxadrista->name = $nome_corrigido;
                        }else{
                            return response()->json(["ok"=>0,"error"=>1,"message"=>"O campo 'Nome' é obrigatório ser atualizado também."]);
                        }
                        break;
                    case "born":
                        if(isset($request->born)){
                            if($request->born == ""){
                                return response()->json(["ok"=>0,"error"=>1,"message"=>"O campo 'Data de Nascimento' é obrigatório ser atualizado também."]);
                            }
                            $enxadrista->setBorn($request->born);
                        }else{
                            return response()->json(["ok"=>0,"error"=>1,"message"=>"O campo 'Data de Nascimento' é obrigatório ser atualizado também."]);
                        }
                        break;
                    case "country_id":
                        if($request->born_country_id){
                            $enxadrista->pais_id = $request->born_country_id;
                        }else{
                            return response()->json(["ok"=>0,"error"=>1,"message"=>"O campo 'País de Nascimento' é obrigatório ser atualizado também."]);
                        }
                        break;
                    case "city_id":
                        if($request->city_id){
                            $enxadrista->cidade_id = $request->city_id;
                        }else{
                            return response()->json(["ok"=>0,"error"=>1,"message"=>"O campo 'Cidade' é obrigatório ser atualizado também."]);
                        }
                        break;
                    case "email":
                        if($request->email){
                            $enxadrista->email = $request->email;
                        }else{
                            return response()->json(["ok"=>0,"error"=>1,"message"=>"O campo 'E-mail' é obrigatório ser atualizado também."]);
                        }
                        break;
                    case "sex_id":
                        if($request->sex_id){
                            $enxadrista->sexos_id = $request->sex_id;
                        }else{
                            return response()->json(["ok"=>0,"error"=>1,"message"=>"O campo 'Sexo' é obrigatório ser atualizado também."]);
                        }
                        break;
                    case "country_cellphone_id":
                        if(isset($request->cellphone_country_id)){
                            if($request->cellphone_country_id == ""){
                                return response()->json(["ok"=>0,"error"=>1,"message"=>"O campo 'País do Telefone' é obrigatório ser atualizado também."]);
                            }
                            $enxadrista->pais_celular_id = $request->cellphone_country_id;
                        }else{
                            return response()->json(["ok"=>0,"error"=>1,"message"=>"O campo 'País do Telefone' é obrigatório ser atualizado também."]);
                        }
                        break;
                    case "cellphone":
                        if($request->cellphone){
                            $enxadrista->celular = $request->cellphone;
                        }else{
                            return response()->json(["ok"=>0,"error"=>1,"message"=>"O campo 'Telefone Celular' é obrigatório ser atualizado também."]);
                        }
                        break;
                    case "lichess_username":
                        if($request->lichess_username){
                            $enxadrista->lichess_username = $request->lichess_username;
                        }else{
                            return response()->json(["ok"=>0,"error"=>1,"message"=>"O campo 'Usuário do Lichess.org' é obrigatório ser atualizado também."]);
                        }
                        break;
                    case "chess_com_username":
                        if($request->chess_com_username){
                            $enxadrista->chess_com_username = $request->chess_com_username;
                        }else{
                            return response()->json(["ok"=>0,"error"=>1,"message"=>"O campo 'Usuário do Chess.com' é obrigatório ser atualizado também."]);
                        }
                        break;
                    case "calculate_cbx":
                        if(in_array("cbx_id",$need_update_fields)){
                            if($request->cbx_id){
                                $enxadrista->cbx_id = $request->cbx_id;
                            }else{
                                return response()->json(["ok"=>0,"error"=>1,"message"=>"O campo 'ID CBX' é obrigatório ser atualizado também."]);
                            }
                        }
                        break;
                    case "cbx_id":
                        if($request->cbx_id){
                            $enxadrista->cbx_id = $request->cbx_id;
                        }
                        break;
                    case "calculate_fide":
                        if($enxadrista->pais_id == 33){
                            if(in_array("cbx_id",$need_update_fields)){
                                if($request->cbx_id){
                                    $enxadrista->cbx_id = $request->cbx_id;
                                }else{
                                    return response()->json(["ok"=>0,"error"=>1,"message"=>"O campo 'ID CBX' é obrigatório ser atualizado também."]);
                                }
                            }
                        }else{
                            if(in_array("fide_id",$need_update_fields)){
                                if($request->fide_id){
                                    $enxadrista->fide_id = $request->fide_id;
                                }else{
                                    return response()->json(["ok"=>0,"error"=>1,"message"=>"O campo 'ID FIDE' é obrigatório ser atualizado também."]);
                                }
                            }
                        }
                        break;
                    case "fide_id":
                        if($request->fide_id){
                            $enxadrista->fide_id = $request->fide_id;
                        }
                        break;
                    case "documents":
                        $has_document = false;
                        if($request->documents){
                            $documents_to_save = array();
                            foreach(Pais::where([["pais_id","=",$enxadrista->pais_id]])->first()->tipo_documentos->all() as $tipo_documento_pais){
                                if($tipo_documento_pais->e_requerido){
                                    if(!$request->documents[$tipo_documento_pais->tipo_documento->id]){
                                        return response()->json(["ok"=>0,"error"=>1,"message"=>"O documento '".$tipo_documento_pais->tipo_documento->nome."' é obrigatório para a atualização."]);
                                    }
                                }
                                if($request->documents[$tipo_documento_pais->tipo_documento->id]){
                                    if($enxadrista->documentos()->where([
                                        ["tipo_documentos_id","=",$tipo_documento_pais->tipo_documento->id],
                                        ["numero","=",$request->documents[$tipo_documento_pais->tipo_documento->id]],
                                        ["enxadrista_id","!=",$enxadrista->id]
                                    ])->count()){
                                        return response()->json(["ok"=>0,"error"=>1,"message"=>"O documento '".$tipo_documento_pais->tipo_documento->nome."' informado já está vinculado a outro(a) enxadrista."]);
                                    }

                                    if($enxadrista->documentos()->where([["tipo_documentos_id","=",$tipo_documento_pais->tipo_documento->id]])->count() > 0){
                                        $documento = $enxadrista->documentos()->where([["tipo_documentos_id","=",$tipo_documento_pais->tipo_documento->id]])->first();
                                    }else{
                                        $documento = new Documento;
                                        $documento->tipo_documentos_id = $tipo_documento_pais->tipo_documento->id;
                                        $documento->enxadrista_id = $enxadrista->id;
                                    }
                                    $documento->numero = $request->documents[$tipo_documento_pais->tipo_documento->id];
                                    $documents_to_save[] = $documento;

                                    $has_document = true;
                                }
                            }
                        }

                        if(!$has_document){
                            return response()->json(["ok"=>0,"error"=>1,"message"=>"É necessário informar ao menos um Documento para o cadastro ser atualizado."]);
                        }else{
                            foreach($documents_to_save as $document_to_save){
                                $document_to_save->save();
                            }
                        }

                        break;
                }
            }

            $enxadrista->save();

            return response()->json(["ok"=>1,"error"=>0]);
        }
        return response()->json(["ok"=>0,"error"=>1,"message"=>"Evento não encontrado","httpcode"=>404],404);
    }


    public function categoriesPlayer($evento, $enxadrista, $is_user_with_permission = false)
    {
        // if(Auth::check()){
        //     if(
        //         Auth::user()->hasPermissionGlobal() ||
        //         Auth::user()->hasPermissionEventByPerfil($evento->id,[3,4]) ||
        //         Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[6])
        //     ){
        //         $is_user_with_permission = true;
        //     }
        // }
        $categorias = $evento->categorias()->whereHas("categoria", function ($q1) use ($enxadrista, $evento, $is_user_with_permission) {
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
            // if(!$is_user_with_permission){
            //     $q1->where([["is_private","=",false]]);
            // }
        })
        ->get();
        // echo ($categorias); exit();
        $results = array();
        foreach ($categorias as $categoria) {
            $results[] = $categoria;
        }
        return $results;
    }

    public function getNeedUpdateFields($evento, $enxadrista){
        $fields = [];

        if($enxadrista->name == NULL) $fields[] = "name";
        if($enxadrista->born == NULL) $fields[] = "born";
        if($enxadrista->pais_id == NULL) $fields[] = "country_id"; //
        if($enxadrista->cidade_id == NULL) $fields[] = "city_id"; //
        if($enxadrista->email == NULL) $fields[] = "email";
        if($enxadrista->sexos_id == NULL) $fields[] = "sex_id";  //
        if($enxadrista->pais_celular_id == NULL) $fields[] = "country_cellphone_id"; //
        if($enxadrista->celular == NULL) $fields[] = "cellphone";
        if($enxadrista->documentos()->count() == 0) $fields[] = "documents"; //
        if($enxadrista->howOldForEvento($evento->getYear()) >= 130) $fields[] = "born";

        if($evento->calcula_cbx){
            if(!$enxadrista->cbx_id || $enxadrista->cbx_id == 0) $fields[] = "calculate_cbx";
            if(!$enxadrista->cbx_id || $enxadrista->cbx_id == 0) $fields[] = "cbx_id";
        }
        if($evento->calcula_fide){
            if(!$enxadrista->pais_id){
                $fields[] = "calculate_fide";
                $fields[] = "fide_id";
            }elseif($enxadrista->pais_id == 33){
                if(!$enxadrista->cbx_id || $enxadrista->cbx_id == 0) $fields[] = "calculate_fide";
                if(!$enxadrista->cbx_id || $enxadrista->cbx_id == 0) $fields[] = "cbx_id";
            }elseif($enxadrista->pais_id != 33){
                if(!$enxadrista->cbx_id || $enxadrista->cbx_id == 0) $fields[] = "calculate_fide";
                if(!$enxadrista->cbx_id || $enxadrista->cbx_id == 0) $fields[] = "fide_id";
            }
        }

        if(($evento->is_lichess && ($enxadrista->lichess_username == NULL || $enxadrista->lichess_username == ""))) $fields[] = "lichess_username";

        if(($evento->is_chess_com && ($enxadrista->chess_com_username == NULL || $enxadrista->chess_com_username == ""))) $fields[] = "chess_com_username";


        return $fields;
    }
}

<?php

namespace App\Http\Controllers\API\Event;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
                $item["chesscom_username"] = $enxadrista->chesscom_username;
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
            $player["chesscom_username"] = $enxadrista->chesscom_username;
            $player["lichess_username"] = $enxadrista->lichess_username;
            $player["city_name"] = $enxadrista->cidade->getName();
            $player["club_name"] = ($enxadrista->clube) ? $enxadrista->clube->getFullName() : "Sem Clube";

            $player["city"] = $enxadrista->cidade->toAPIObject(true);
            if($enxadrista->clube) $player["club"] = $enxadrista->clube->toAPIObject(true);



            $fields = [];
            if($enxadrista->name == NULL) $fields[] = "name";
            if($enxadrista->born == NULL) $fields[] = "born";
            if($enxadrista->pais_id == NULL) $fields[] = "pais_id"; //
            if($enxadrista->cidade_id == NULL) $fields[] = "cidade_id"; //
            if($enxadrista->email == NULL) $fields[] = "email";
            if($enxadrista->sexos_id == NULL) $fields[] = "sexos_id";  //
            if($enxadrista->pais_celular_id == NULL) $fields[] = "pais_celular_id"; //
            if($enxadrista->celular == NULL) $fields[] = "celular";
            if($enxadrista->documentos()->count() == 0) $fields[] = "documentos"; //
            if($enxadrista->howOldForEvento($evento->getYear()) >= 130) $fields[] = "born";

            if(($evento->calcula_cbx && (!$enxadrista->cbx_id || $enxadrista->cbx_id == 0))) $fields[] = "cbx_id";
            if(($evento->calcula_fide &&
                (
                    (
                        $enxadrista->pais_id == 33 && (!$enxadrista->cbx_id || $enxadrista->cbx_id == 0)
                    )||(
                        $enxadrista->pais_id != 33 && (!$enxadrista->fide_id || $enxadrista->fide_id == 0)
                    )
                )
            )) $fields[] = "fide_id";

            if(($evento->is_lichess && ($enxadrista->lichess_username == NULL || $enxadrista->lichess_username == ""))) $fields[] = "lichess_username";

            if(($evento->is_chess_com && ($enxadrista->chess_com_username == NULL || $enxadrista->chess_com_username == ""))) $fields[] = "chess_com_username";


            $categories = array();
            if(count($fields) == 0){
                foreach($this->categoriesPlayer($evento,$enxadrista) as $categoria){
                    $categories[] = array("id"=>$categoria->categoria->id,"name"=>$categoria->categoria->name,"price"=>0);
                }
            }


            return response()->json(["ok"=>1, "error"=>0, "player" => $player, "fields" => $fields, "categories" => $categories]);
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
}

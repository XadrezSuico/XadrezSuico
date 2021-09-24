<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Evento;
use App\Torneio;
use App\Inscricao;
use App\Rodada;
use App\Emparceiramento;

use Auth;

use Log;


class TorneioChaveSemifinalController extends Controller
{
    public function __construct()
    {
        return $this->middleware("auth");
    }

    public function index($evento_id,$torneio_id){
        $evento = Evento::find($evento_id);
        if($evento){
            $torneio = Torneio::find($torneio_id);
            if($torneio){
                if($torneio->tipo_torneio->id == 3){
                    if($torneio->evento_id == $evento->id){
                        $user = Auth::user();
                        if (
                            !$user->hasPermissionGlobal() &&
                            !$user->hasPermissionEventByPerfil($id, [3, 4]) &&
                            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [6,7])
                        ) {
                            return redirect("/");
                        }
                        $tab = null;

                        return view("evento.torneio.gerenciamento.3_chave_semifinal.index",compact("torneio","tab"));
                    }
                }
            }
            return redirect("/evento/dashboard/".$evento->id."?tab=torneio");
        }
        return redirect("/");
    }


    public function importClassificados($evento_id){
        $evento = Evento::find($evento_id);
        if($evento){
            $user = Auth::user();
            if (
                !$user->hasPermissionGlobal() &&
                !$user->hasPermissionEventByPerfil($evento_id, [3, 4]) &&
                !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [6,7])
            ) {
                return redirect("/");
            }
            // PERCORRE OS TORNEIOS
            foreach($evento->torneios->all() as $torneio){
                Log::debug("Importando torneio ".$torneio->id);
                $i = 0;
                $total_inscricoes_permitidas = 4;

                if($torneio->tipo_torneio->id == 3){
                    foreach($torneio->rodadas->all() as $rodada){
                        foreach($rodada->emparceiramentos->all() as $emparceiramento){
                            $emparceiramento->inscricao_a = NULL;
                            $emparceiramento->inscricao_b = NULL;
                            $emparceiramento->save();
                        }
                    }
                    foreach($torneio->inscricoes->all() as $inscricao){
                        $inscricao->delete();
                    }
                    // PERCORRE AS CATEGORIAS DOS TORNEIOS
                    foreach($torneio->categorias->all() as $categoria){
                        Log::debug("Importando categoria ".$categoria->id);
                        $inscricoes = Inscricao::whereHas("torneio",function($q1) use ($torneio, $categoria){
                            $q1->whereHas("evento",function($q2) use ($torneio, $categoria){
                                $q2->where([
                                    ["id","=",$torneio->evento->classificador->id]
                                ]);
                            });
                            $q1->where([
                                ["categoria_id","=",$categoria->categoria->classificadora->id],
                            ]);
                        })
                        ->where([
                            ["confirmado","=",true],
                            ["desconsiderar_classificado","=",false]
                        ])
                        ->whereNotNull("posicao")
                        ->orderBy("posicao","asc")
                        ->get();
                        foreach($inscricoes as $inscricao){
                            if($i+1 <= $total_inscricoes_permitidas){
                            Log::debug("Importando inscrição ".$inscricao->id."(Enxadrista ".$inscricao->enxadrista->id.")");
                                // VALIDA SE O ENXADRISTA JÁ SE ENCONTRA INSCRITO EM OUTRO EVENTO DO GRUPO DE EVENTO NO MESMO DIA
                                $inscricao_mesmo_dia_count = Inscricao::whereHas("torneio",function($q1) use ($torneio,$inscricao){
                                    $q1->whereHas("evento", function($q2) use ($torneio,$inscricao){
                                        $q2->whereHas("grupo_evento",function($q3) use ($torneio,$inscricao){
                                            $q3->where([
                                                ["id","=",$torneio->evento->grupo_evento->id]
                                            ]);
                                        })
                                        ->where(function($q3) use ($torneio,$inscricao){
                                            $q3->where([
                                                ["data_inicio","=",$torneio->evento->data_inicio]
                                            ]);
                                            $q3->orWhere([
                                                ["data_fim","=",$torneio->evento->data_fim]
                                            ]);
                                            $q3->orWhere([
                                                ["data_inicio",">=",$torneio->evento->data_fim],
                                                ["data_fim","=<",$torneio->evento->data_fim]
                                            ]);
                                        });
                                    });
                                })
                                ->where([
                                    ["enxadrista_id","=",$inscricao->enxadrista->id],
                                    ["id","!=",$inscricao->id],
                                ])
                                ->count();
                                if($inscricao_mesmo_dia_count == 0){
                                    if($torneio->evento->grupo_evento->evento_classifica){
                                        // VALIDA SE O ENXADRISTA JÁ SE ENCONTRA INSCRITO NO EVENTO QUE O GRUPO DE EVENTO CLASSIFICADOR CLASSIFICA
                                        $inscricao_grupo_evento_count = Inscricao::whereHas("torneio",function($q1) use ($torneio,$inscricao){
                                            $q1->whereHas("evento", function($q2) use ($torneio,$inscricao){
                                                $q2->where([
                                                    ["id","=",$torneio->evento->grupo_evento->evento_classifica->id]
                                                ]);
                                            });
                                        })
                                        ->where([
                                            ["enxadrista_id","=",$inscricao->enxadrista->id],
                                            ["id","!=",$inscricao->id],
                                        ])
                                        ->count();
                                    }else{
                                        $inscricao_grupo_evento_count = 0;
                                    }
                                    if($inscricao_grupo_evento_count == 0){
                                        Log::debug("Efetuando inscrição...");
                                        $inscricao_nova = new Inscricao;
                                        $inscricao_nova->enxadrista_id = $inscricao->enxadrista_id;
                                        $inscricao_nova->categoria_id = $categoria->categoria->id;
                                        $inscricao_nova->cidade_id = $inscricao->cidade_id;
                                        $inscricao_nova->clube_id = $inscricao->clube_id;
                                        $inscricao_nova->torneio_id = $torneio->id;
                                        $inscricao_nova->regulamento_aceito = $inscricao->regulamento_aceito;
                                        $inscricao_nova->confirmado = $inscricao->confirmado;
                                        $inscricao_nova->xadrezsuico_aceito = $inscricao->xadrezsuico_aceito;
                                        $inscricao_nova->inscricao_from = $inscricao->id;
                                        $inscricao_nova->start_position = $i+1;
                                        $inscricao_nova->save();


                                        foreach($torneio->rodadas->all() as $rodada){
                                            foreach($rodada->emparceiramentos->all() as $emparceiramento){
                                                if($emparceiramento->numero_a == $i+1){
                                                    $emparceiramento->inscricao_a = $inscricao_nova->id;
                                                }elseif($emparceiramento->numero_b == $i+1){
                                                    $emparceiramento->inscricao_b = $inscricao_nova->id;
                                                }
                                                $emparceiramento->save();
                                            }
                                        }

                                        $i++;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return redirect("/evento/dashboard/".$evento->id."?tab=torneio");
        }
        return redirect("/");
    }
    public function zerarInscricoes($evento_id){
        $evento = Evento::find($evento_id);
        if($evento){
            $user = Auth::user();
            if (
                !$user->hasPermissionGlobal() &&
                !$user->hasPermissionEventByPerfil($evento_id, [3, 4]) &&
                !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [6,7])
            ) {
                return redirect("/");
            }
            // PERCORRE OS TORNEIOS
            foreach($evento->torneios->all() as $torneio){
                Log::debug("Importando torneio ".$torneio->id);
                $i = 0;
                $total_inscricoes_permitidas = 4;

                if($torneio->tipo_torneio->id == 3){
                    foreach($torneio->rodadas->all() as $rodada){
                        foreach($rodada->emparceiramentos->all() as $emparceiramento){
                            $emparceiramento->inscricao_a = NULL;
                            $emparceiramento->inscricao_b = NULL;
                            $emparceiramento->save();
                        }
                    }
                    foreach($torneio->inscricoes->all() as $inscricao){
                        $inscricao->delete();
                    }
                }
            }
            return redirect("/evento/dashboard/".$evento->id."?tab=torneio");
        }
        return redirect("/");
    }

    public function gerenateArmageddon($evento_id,$torneio_id,$emparceiramento_id){
        Log::debug("gerenateArmageddon");
        $evento = Evento::find($evento_id);
        if ($evento) {
            $torneio = Torneio::find($torneio_id);
            if ($torneio) {
                if ($torneio->tipo_torneio->id == 3) {
                    if ($torneio->evento_id == $evento->id) {
                        $user = Auth::user();
                        if (
                            !$user->hasPermissionGlobal() &&
                            !$user->hasPermissionEventByPerfil($id, [3, 4]) &&
                            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [6, 7])
                        ) {
                            // return response()->json(["ok"=>0,"error"=>1,"message"=>"Você não possui permissão para gerenciar este torneio."]);
                        }
                        $emparceiramento = Emparceiramento::find($emparceiramento_id);
                        if($emparceiramento){
                            if($emparceiramento->rodada->torneio->id != $torneio->id){
                                // return response()->json(["ok"=>0,"error"=>1,"message"=>"Este emparceiramento não pertence à este torneio."]);
                            }
                            if($emparceiramento->rodada->torneio->evento->id != $evento->id){
                                // return response()->json(["ok"=>0,"error"=>1,"message"=>"Este emparceiramento não pertence à este evento."]);
                            }

                            if($emparceiramento->getResultadoA() == $emparceiramento->getResultadoB() && $emparceiramento->getResultadoA() != 0){
                                $emparceiramento->resultado = 0;
                                $emparceiramento->save();

                                $emparceiramento_armageddon = new Emparceiramento;
                                $emparceiramento_armageddon->numero_a = $emparceiramento->numero_b;
                                $emparceiramento_armageddon->numero_b = $emparceiramento->numero_a;
                                $emparceiramento_armageddon->inscricao_a = $emparceiramento->inscricao_b;
                                $emparceiramento_armageddon->inscricao_b = $emparceiramento->inscricao_a;
                                $emparceiramento_armageddon->armageddon_emparceiramentos_id = $emparceiramento->id;
                                $emparceiramento_armageddon->armageddon_rodadas_id = $emparceiramento->rodada->id;
                                $emparceiramento_armageddon->is_armageddon = true;
                                $emparceiramento_armageddon->save();
                            }
                        }

                    }
                }
            }
        }
        return redirect("/evento/".$evento_id."/torneios/".$torneio_id."/gerenciamento/torneio_3");

    }
    public function api_homologateEmparceiramento($evento_id,$torneio_id,$emparceiramento_id){
        Log::debug("api_homologateEmparceiramento");
        $evento = Evento::find($evento_id);
        if ($evento) {
            $torneio = Torneio::find($torneio_id);
            if ($torneio) {
                if ($torneio->tipo_torneio->id == 3) {
                    if ($torneio->evento_id == $evento->id) {
                        $user = Auth::user();
                        if (
                            !$user->hasPermissionGlobal() &&
                            !$user->hasPermissionEventByPerfil($id, [3, 4]) &&
                            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [6, 7])
                        ) {
                            return response()->json(["ok"=>0,"error"=>1,"message"=>"Você não possui permissão para gerenciar este torneio."]);
                        }
                        $emparceiramento = Emparceiramento::find($emparceiramento_id);
                        $emparceiramento_validacao = $emparceiramento;
                        if($emparceiramento->is_armageddon){
                            $emparceiramento_validacao = $emparceiramento->armageddon_emparceiramento;
                        }
                        if($emparceiramento_validacao){
                            if($emparceiramento_validacao->rodada->torneio->id != $torneio->id){
                                return response()->json(["ok"=>0,"error"=>1,"message"=>"Este emparceiramento não pertence à este torneio."]);
                            }
                            if($emparceiramento_validacao->rodada->torneio->evento->id != $evento->id){
                                return response()->json(["ok"=>0,"error"=>1,"message"=>"Este emparceiramento não pertence à este evento."]);
                            }

                            if($emparceiramento->getResultadoA() == $emparceiramento->getResultadoB() && $emparceiramento->getResultadoA() != 0){
                                return response()->json(["ok"=>0,"error"=>1,"message"=>"O resultado está empatado, com isto, não é possível homologar o resultado."]);
                            }

                            if($emparceiramento_validacao->rodada->numero == 1){
                                $rodada_2 = $emparceiramento_validacao->rodada->torneio->rodadas()->where([["numero","=",2]])->first();
                                $emparceiramento_2_1 = $rodada_2->emparceiramentos()->first();
                            }

                            if($emparceiramento->getResultadoA() > $emparceiramento->getResultadoB()){
                                $emparceiramento->resultado = -1;

                                if($emparceiramento_validacao->rodada->numero == 1){
                                    if($emparceiramento->numero_a == 1 || $emparceiramento->numero_a == 4){
                                        $emparceiramento_2_1->inscricao_a = $emparceiramento->inscricao_a;
                                    }else{
                                        $emparceiramento_2_1->inscricao_b = $emparceiramento->inscricao_a;
                                    }
                                }
                            }else{
                                $emparceiramento->resultado = 1;

                                if($emparceiramento_validacao->rodada->numero == 1){
                                    if($emparceiramento->numero_a == 1 || $emparceiramento->numero_a == 4){
                                        $emparceiramento_2_1->inscricao_a = $emparceiramento->inscricao_b;
                                    }else{
                                        $emparceiramento_2_1->inscricao_b = $emparceiramento->inscricao_b;
                                    }
                                }
                            }
                            if($emparceiramento_validacao->rodada->numero == 1) $emparceiramento_2_1->save();
                            $emparceiramento->save();

                            if($emparceiramento_validacao->rodada->numero == 2){
                                foreach($emparceiramento_validacao->rodada->torneio->inscricoes->all() as $inscricao){
                                    $inscricao->pontos = 0;
                                    $inscricao->save();
                                    Log::debug("Inscrição ".$inscricao->id." - Resultado inserido");
                                }
                                return response()->json(["ok" => 1, "error" => 0,"finished" => 1]);
                            }

                            return response()->json(["ok" => 1, "error" => 0]);
                        }

                    }else{
                        return response()->json(["ok" => 0, "error" => 1, "message" => "Este torneio não está vinculado a este evento."]);
                    }
                }else{
                    return response()->json(["ok" => 0, "error" => 1, "message" => "Este torneio não é do tipo 'Chave Semifinal'."]);
                }
            }else{
                return response()->json(["ok" => 0, "error" => 1, "message" => "Torneio não encontrado."]);
            }
        }else{
            return response()->json(["ok" => 0, "error" => 1, "message" => "Evento não encontrado."]);
        }
    }
    public function api_desaprovarEmparceiramento($evento_id,$torneio_id,$emparceiramento_id){
        Log::debug("api_desaprovarEmparceiramento");
        $evento = Evento::find($evento_id);
        if ($evento) {
            $torneio = Torneio::find($torneio_id);
            if ($torneio) {
                if ($torneio->tipo_torneio->id == 3) {
                    if ($torneio->evento_id == $evento->id) {
                        $user = Auth::user();
                        if (
                            !$user->hasPermissionGlobal() &&
                            !$user->hasPermissionEventByPerfil($id, [3, 4]) &&
                            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [6, 7])
                        ) {
                            return response()->json(["ok"=>0,"error"=>1,"message"=>"Você não possui permissão para gerenciar este torneio."]);
                        }
                        $emparceiramento = Emparceiramento::find($emparceiramento_id);
                        $emparceiramento_validacao = $emparceiramento;
                        if($emparceiramento->is_armageddon){
                            $emparceiramento_validacao = $emparceiramento->armageddon_emparceiramento;
                        }
                        if($emparceiramento_validacao){
                            if($emparceiramento_validacao->rodada->torneio->id != $torneio->id){
                                return response()->json(["ok"=>0,"error"=>1,"message"=>"Este emparceiramento não pertence à este torneio."]);
                            }
                            if($emparceiramento_validacao->rodada->torneio->evento->id != $evento->id){
                                return response()->json(["ok"=>0,"error"=>1,"message"=>"Este emparceiramento não pertence à este evento."]);
                            }

                            if($emparceiramento->resultado == NULL && $emparceiramento->resultado != 0){
                                return response()->json(["ok"=>0,"error"=>1,"message"=>"Não é possível desaprovar um emparceiramento não aprovado."]);
                            }

                            if($emparceiramento_validacao->rodada->numero == 1){
                                $rodada_2 = $emparceiramento_validacao->rodada->torneio->rodadas()->where([["numero","=",2]])->first();
                                $emparceiramento_2_1 = $rodada_2->emparceiramentos()->first();

                                if($emparceiramento->numero_a == 1 || $emparceiramento->numero_a == 4){
                                    $emparceiramento_2_1->inscricao_a = NULL;
                                }else{
                                    $emparceiramento_2_1->inscricao_b = NULL;
                                }
                                foreach($emparceiramento_2_1->armageddons->all() as $armageddon){
                                    $armageddon->delete();
                                }
                                $emparceiramento_2_1->resultado_a = 0;
                                $emparceiramento_2_1->resultado_b = 0;
                                $emparceiramento_2_1->cor_a = NULL;
                                $emparceiramento_2_1->cor_b = NULL;
                                $emparceiramento_2_1->resultado = NULL;

                                $emparceiramento_2_1->save();
                            }

                            $emparceiramento->resultado = NULL;

                            foreach($emparceiramento->armageddons->all() as $armageddon){
                                $armageddon->delete();
                            }

                            $emparceiramento->save();


                            // if($emparceiramento_validacao->rodada->numero == 2){
                                foreach($emparceiramento_validacao->rodada->torneio->inscricoes->all() as $inscricao){
                                    $inscricao->pontos = NULL;
                                    $inscricao->save();
                                }
                            // }

                            return response()->json(["ok" => 1, "error" => 0]);
                        }

                    }else{
                        return response()->json(["ok" => 0, "error" => 1, "message" => "Este torneio não está vinculado a este evento."]);
                    }
                }else{
                    return response()->json(["ok" => 0, "error" => 1, "message" => "Este torneio não é do tipo 'Chave Semifinal'."]);
                }
            }else{
                return response()->json(["ok" => 0, "error" => 1, "message" => "Torneio não encontrado."]);
            }
        }else{
            return response()->json(["ok" => 0, "error" => 1, "message" => "Evento não encontrado."]);
        }
    }


    public function api_setEmparceiramentoData($evento_id,$torneio_id,Request $request){
        Log::debug("api_setEmparceiramentoData");
        $evento = Evento::find($evento_id);
        if ($evento) {
            $torneio = Torneio::find($torneio_id);
            if ($torneio) {
                if ($torneio->tipo_torneio->id == 3) {
                    if ($torneio->evento_id == $evento->id) {
                        $user = Auth::user();
                        if (
                            !$user->hasPermissionGlobal() &&
                            !$user->hasPermissionEventByPerfil($id, [3, 4]) &&
                            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [6, 7])
                        ) {
                            return response()->json(["ok"=>0,"error"=>1,"message"=>"Você não possui permissão para gerenciar este torneio."]);
                        }
                        $emparceiramento = Emparceiramento::find($request->input("emparceiramento_id"));
                        if($emparceiramento){
                            if($emparceiramento->rodada){
                                if ($emparceiramento->rodada->torneio->id != $torneio->id) {
                                    return response()->json(["ok" => 0, "error" => 1, "message" => "Este emparceiramento não pertence à este torneio."]);
                                }
                                if ($emparceiramento->rodada->torneio->evento->id != $evento->id) {
                                    return response()->json(["ok" => 0, "error" => 1, "message" => "Este emparceiramento não pertence à este evento."]);
                                }
                            }else{
                                if ($emparceiramento->armageddon_emparceiramento->rodada->torneio->id != $torneio->id) {
                                    return response()->json(["ok" => 0, "error" => 1, "message" => "Este emparceiramento não pertence à este torneio."]);
                                }
                                if ($emparceiramento->armageddon_emparceiramento->rodada->torneio->evento->id != $evento->id) {
                                    return response()->json(["ok" => 0, "error" => 1, "message" => "Este emparceiramento não pertence à este evento."]);
                                }
                            }

                            if($request->has("cor_a")){
                                if($request->input("cor_a") == 1 || $request->input("cor_a") == 2){
                                    $emparceiramento->cor_a = $request->input("cor_a");
                                    Log::debug("Cor A: Ok");
                                }else{
                                    Log::debug("Cor A != 1 ou 2");
                                    $emparceiramento->cor_a = NULL;
                                }
                            }else{
                                Log::debug("Cor A não chegou");
                                $emparceiramento->cor_a = NULL;
                            }

                            if($request->has("cor_b")){
                                if($request->input("cor_b") == 1 || $request->input("cor_b") == 2){
                                    $emparceiramento->cor_b = $request->input("cor_b");
                                    Log::debug("Cor B: Ok");
                                }else{
                                    Log::debug("Cor B != 1 ou 2");
                                }
                            }else{
                                Log::debug("Cor B não chegou");
                                $emparceiramento->cor_b = NULL;
                            }

                            if($request->has("resultado_a")){
                                if($request->input("resultado_a") >= 0){
                                    $emparceiramento->resultado_a = number_format($request->input("resultado_a"),1);
                                    Log::debug("Resultado A: Ok");
                                }else{
                                    Log::debug("Resultado de A não é float");
                                }
                            }else{
                                Log::debug("Resultado de A não chegou");
                                $emparceiramento->resultado_a = 0;
                            }

                            if($request->has("resultado_b")){
                                if($request->input("resultado_b") >= 0){
                                    $emparceiramento->resultado_b = number_format($request->input("resultado_b"),1);
                                    Log::debug("Resultado B: Ok");
                                }else{
                                    Log::debug("Resultado de B não é float");
                                }
                            }else{
                                $emparceiramento->resultado_b = 0;
                                Log::debug("Resultado de B não chegou");
                            }

                            $emparceiramento->save();
                            return response()->json(["ok"=>1,"error"=>0,"data"=>array("resultado_a"=>$emparceiramento->resultado_a,"resultado_b"=>$emparceiramento->resultado_b)]);

                        }
                        return response()->json(["ok"=>0,"error"=>1,"message"=>"Emparceiramento não encontrado."]);
                    }
                }
            }
            return response()->json(["ok"=>0,"error"=>1,"message"=>"Torneio não encontrado."]);
        }
        return response()->json(["ok"=>0,"error"=>1,"message"=>"Evento não encontrado."]);
    }
}

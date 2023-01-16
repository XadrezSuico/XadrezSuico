<?php

namespace App\Http\Controllers\External;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Inscricao;

class XadrezSuicoPagNotificationController extends Controller
{
    public function notification($uuid, Request $request){
        if(!$request->header("system_id")){
            activity("xadrezsuicopag_notification")
            ->withProperties(['request' => $request->all()])
            ->log("Erro: Motivo Externo (XadrezSuicoPAG): Dados da notificação não encontrados (system_id).");

            return response()->json(["ok"=>0, "error"=>1, "message"=>"Motivo Externo (XadrezSuicoPAG): Dados da notificação não encontrados.","httpcode"=>400],400);
        }else{
            $system_id = $request->header("system_id");
        }

        if(!$request->registration_uuid){
            activity("xadrezsuicopag_notification")
            ->withProperties(['request' => $request->all()])
            ->log("Erro: Motivo Externo (XadrezSuicoPAG): Dados da notificação não encontrados (registration_uuid).");

            return response()->json(["ok"=>0, "error"=>1, "message"=>"Motivo Externo (XadrezSuicoPAG): Dados da notificação não encontrados.","httpcode"=>400],400);
        }else{
            $registration_uuid = $request->registration_uuid;
        }

        if(env("XADREZSUICOPAG_SYSTEM_ID",NULL) != $system_id){
            activity("xadrezsuicopag_notification")
            ->withProperties([
                                'request' => $request->all(),
                                "XADREZSUICOPAG_SYSTEM_ID" => env("XADREZSUICOPAG_SYSTEM_ID",NULL),
                                "system_id" => $system_id
            ])
            ->log("Erro: O system_id informado não é válido.");

            return response()->json(["ok"=>0, "error"=>1, "message"=>"Motivo Externo (XadrezSuicoPAG): O system_id informado não é válido.","httpcode"=>400],400);
        }

        if(Inscricao::where([["uuid","=",$uuid]])->whereJsonContains("payment_info->uuid",$registration_uuid)->count() == 0){
            activity("xadrezsuicopag_notification")
            ->withProperties([
                'request' => $request->all()
            ])
            ->log("Erro: O registration_uuid não é válido para a inscrição informada.");

            return response()->json(["ok"=>0, "error"=>1, "message"=>"O registration_uuid não é válido para a inscrição informada.","httpcode"=>400],400);
        }

        $xadrezsuicopag_controller = XadrezSuicoPagController::getInstance();

        $registration_request = $xadrezsuicopag_controller->factory("registration")->get($registration_uuid);

        if($registration_request->ok == 1){
            if($registration_request->registration->status == "paid"){
                $inscricao = Inscricao::where([["uuid","=",$uuid]])->whereJsonContains("payment_info->uuid",$registration_uuid)->first();
                $inscricao->paid = true;
                $inscricao->save();
            }
        }

        return response()->json(["ok"=>1,"error"=>0]);
    }
}

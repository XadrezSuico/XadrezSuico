<?php

namespace App\Http\Controllers\Exports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\MessageBag;

use App\Evento;

use Auth;


class XadrezSuicoEmparceiradorController extends Controller
{
    public function __construct(){
        $this->middleware("auth");
    }
    public function export($evento_id){
        $user = Auth::user();
        if(Evento::where([["id","=",$evento_id]])->count() > 0){
            $evento = Evento::find($evento_id);
            if (
                !$user->hasPermissionGlobal() &&
                !$user->hasPermissionEventByPerfil($evento_id, [3, 4]) &&
                !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [6,7])
            ) {
                $messageBag = new MessageBag;
                $messageBag->add("type","danger");
                $messageBag->add("alerta","Sem permissão para acesso a esta funcionalidade.");

                return redirect("/evento/dashboard/".$evento_id)->withErrors($messageBag);
            }

            $evento_obj = $evento->export("xadrezsuico");

            $evento_json = json_encode($evento_obj);
            // $evento_obj = json_encode($evento_json);
            // $evento_json = json_encode($evento_obj);

            $hash = sha1($evento_json);

            $json = array("event"=>$evento_obj,"hash"=>$hash);

            $texto = json_encode($json);


            // file name that will be used in the download
            $fileName = "Xadrezsuico_evento_" . $evento->id . "_todas_inscricoes___" . date("Ymd-His") . "___.xadrezsuico-json";

            // use headers in order to generate the download
            $headers = [
                'Content-type' => 'application/json',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $fileName),
                'Content-Length' => strlen($texto),
            ];

            // make a response, with the content, a 200 response code and the headers
            return response(utf8_encode($texto))->withHeaders([
                'Content-Type' => 'application/json; charset=utf-8',
                'Cache-Control' => 'no-store, no-cache',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]);
        }

        $messageBag = new MessageBag;
        $messageBag->add("type","danger");
        $messageBag->add("alerta","Evento não encontrado.");

        return redirect("/grupoevento")->withErrors($messageBag);
    }
}

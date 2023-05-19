<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\MessageBag;

use App\Imports\SportAppIngaDigitalImport;

use App\Evento;
use Auth;
use Excel;

class ImportController extends Controller
{
    /*
     *
     * IMPORTADOR - INGÁDIGITAL MANUAL
     *
     */


    public function importIngaForm($id)
    {
        $evento = Evento::find($id);
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/" . $evento->id);
        }

        return view("evento.import.ingadigital.resultados_send", compact("evento"));
    }

    public function importInga($id, Request $request)
    {
        $evento = Evento::find($id);
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/" . $evento->id);
        }
        if ($request->hasFile('arquivo')) {
            if ($request->file('arquivo')->isValid()) {
                foreach($evento->torneios->all() as $torneio){
                    foreach($torneio->inscricoes->all() as $inscricao){
                        foreach($inscricao->configs->all() as $config){
                            $config->delete();
                        }
                        $inscricao->delete();
                    }
                }

                Excel::import(new SportAppIngaDigitalImport($evento->id), $request->file('arquivo'));

                $messageBag = new MessageBag;
                $messageBag->add("type","success");
                $messageBag->add("alerta","Importação SportApp - Realizada.<br/>Favor conferir na lista de inscritos se as informações estão corretas.");


                return redirect("/evento/dashboard/".$evento->id)->withErrors($messageBag);
            }
        }



        $messageBag = new MessageBag;
        $messageBag->add("type","danger");
        $messageBag->add("alerta","Importação SportApp - Houve um erro.<br/>Confire se o arquivo foi enviado corretamente.");


        return redirect()->back()->withErrors($messageBag);
    }
}

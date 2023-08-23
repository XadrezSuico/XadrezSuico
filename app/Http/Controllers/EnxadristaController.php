<?php

namespace App\Http\Controllers;

use App\Cidade;
use App\Clube;
use App\Enxadrista;
use App\Sexo;
use App\TipoDocumentoPais;
use App\Documento;
use App\Exports\EnxadristasCompletoFromView;
use App\Http\Util\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\MessageBag;
use GuzzleHttp\Client;

class EnxadristaController extends Controller
{
    public function __construct()
    {
        return $this->middleware("auth");
    }
    public function index()
    {
        // $enxadristas = Enxadrista::all();
        $user = Auth::user();
        if (!($user->hasPermissionGlobal() || $user->hasPermissionGlobalbyPerfil([9]))) {
            return redirect("/");
        }

        $enxadristas = Enxadrista::where([["id", "=", 1]])->get();
        return view('enxadrista.index', compact("enxadristas"));
    }
    function new () {
        $user = Auth::user();
        if (!($user->hasPermissionGlobal() || $user->hasPermissionGlobalbyPerfil([9]))) {
            return redirect("/");
        }

        $cidades = Cidade::all();
        $clubes = Clube::all();
        $sexos = Sexo::all();
        return view('enxadrista.new', compact("cidades", "clubes", "sexos"));
    }
    public function new_post(Request $request)
    {
        $user = Auth::user();
        if (!($user->hasPermissionGlobal() || $user->hasPermissionGlobalbyPerfil([9]))) {
            return redirect("/");
        }

        $enx = new Enxadrista;
        $enx->setBorn($request->input("born"));

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

        $validator = \Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|max:255',
            'born' => 'required|string',
            'cidade_id' => 'required|string',
            'sexos_id' => 'required|string',
            'pais_nascimento_id' => 'required|string',
            'pais_celular_id' => 'required|string',
            'celular' => 'required|string',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $documentos = array();

        foreach(TipoDocumentoPais::where([["pais_id","=",$request->input("pais_nascimento_id")]])->get() as $tipo_documento_pais){
            if($tipo_documento_pais->e_requerido){
                if(!$request->has("tipo_documento_".$tipo_documento_pais->tipo_documento->id)){
                    $messageBag = new MessageBag;
                    $messageBag->add("type","danger");
                    $messageBag->add("alerta","Há um documento que é requerido que não foi informado.");

                    return redirect()->back()->withErrors($messageBag);
                }
                if(
                    $request->input("tipo_documento_".$tipo_documento_pais->tipo_documento->id) == "" ||
                    $request->input("tipo_documento_".$tipo_documento_pais->tipo_documento->id) == NULL
                ){
                    $messageBag = new MessageBag;
                    $messageBag->add("type","danger");
                    $messageBag->add("alerta","Há um documento que é requerido que não foi informado.");

                    return redirect()->back()->withErrors($messageBag);
                }
            }
            if($request->has("tipo_documento_".$tipo_documento_pais->tipo_documento->id)){
                if(
                    $request->input("tipo_documento_".$tipo_documento_pais->tipo_documento->id) != "" &&
                    $request->input("tipo_documento_".$tipo_documento_pais->tipo_documento->id) != NULL
                ){

                    $temEnxadrista_count = Enxadrista::whereHas("documentos",function($q1) use($request, $tipo_documento_pais){
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
                    })->count();
                    if($temEnxadrista_count > 0){
                        $messageBag = new MessageBag;
                        $messageBag->add("type","danger");
                        $messageBag->add("alerta","Já há um cadastro de Enxadrista com o Documento informado.");

                        return redirect()->back()->withErrors($messageBag);
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
            $messageBag = new MessageBag;
            $messageBag->add("type","danger");
            $messageBag->add("alerta","É obrigatório a inserção de ao menos UM DOCUMENTO.");

            return redirect()->back()->withErrors($messageBag);
        }

        $temEnxadrista_count = Enxadrista::where([
            ["name", "=", $nome_corrigido],
            ["born", "=", $enx->born],
        ])->count();
        if ($temEnxadrista_count > 0) {
            $messageBag = new MessageBag;
            $messageBag->add("type","danger");
            $messageBag->add("alerta","O enxadrista informado já possui cadastro.");

            return redirect()->back()->withErrors($messageBag);
            exit();
        } else {
            $enxadrista = new Enxadrista;
            $enxadrista->name = $nome_corrigido;
            $enxadrista->splitName();
            $enxadrista->setBorn($request->input("born"));
            $enxadrista->cidade_id = $request->input("cidade_id");
            $enxadrista->sexos_id = $request->input("sexos_id");
            $enxadrista->pais_id = $request->input("pais_nascimento_id");
            $enxadrista->pais_celular_id = $request->input("pais_celular_id");
            $enxadrista->celular = $request->input("celular");
            $enxadrista->email = $request->input("email");
            if ($request->has("clube_id")) {
                if ($request->input("clube_id")) {
                    $enxadrista->clube_id = $request->input("clube_id");
                }
            }

            if ($request->has("cbx_id")) {
                if ($request->input("cbx_id")) {
                    $enxadrista->cbx_id = $request->input("cbx_id");
                }
            }

            if ($request->has("fide_id")) {
                if ($request->input("fide_id")) {
                    if($enxadrista->fide_id){
                        if($enxadrista->fide_id != $request->input("fide_id")){
                            $enxadrista->fide_last_update = null;
                        }
                    }
                    $enxadrista->fide_id = $request->input("fide_id");
                }
            }

            if ($request->has("lbx_id")) {
                if ($request->input("lbx_id")) {
                    $enxadrista->lbx_id = $request->input("lbx_id");
                }
            }
            if ($request->has("lichess_username")) {
                if ($request->input("lichess_username")) {
                    $enxadrista->lichess_username = $request->input("lichess_username");
                }
            }
            if ($request->has("chess_com_username")) {
                if ($request->input("chess_com_username")) {
                    $enxadrista->chess_com_username = $request->input("chess_com_username");
                }
            }

            $enxadrista->save();

            foreach($documentos as $documento){
                $documento->enxadrista_id = $enxadrista->id;
                $documento->save();
            }

            return redirect("/enxadrista/edit/" . $enxadrista->id);
        }
    }
    public function edit($id)
    {
        $user = Auth::user();
        if (!($user->hasPermissionGlobal() || $user->hasPermissionGlobalbyPerfil([9]))) {
            return redirect("/");
        }

        $enxadrista = Enxadrista::find($id);
        $cidades = Cidade::all();
        $clubes = Clube::all();
        $sexos = Sexo::all();

        if(!$enxadrista->lbx_id){
            $client = new Client;
            $response = $client->get(env("LBX_RATING_SERVER")."//rating/search/byName?search=" . $enxadrista->name);
            $html = (string) $response->getBody();
            $json_lbx = json_decode($html);
        }else{
            $json_lbx = false;
        }

        return view('enxadrista.edit', compact("enxadrista", "cidades", "clubes", "sexos", "json_lbx"));
    }
    public function edit_post($id, Request $request)
    {
        $user = Auth::user();
        if (!($user->hasPermissionGlobal() || $user->hasPermissionGlobalbyPerfil([9]))) {
            return redirect("/");
        }

        $validator = \Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|max:255',
            'born' => 'required|string',
            'cidade_id' => 'required|string',
            'sexos_id' => 'required|string',
            'pais_nascimento_id' => 'required|string',
            'pais_celular_id' => 'required|string',
            'celular' => 'required|string',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $enxadrista = Enxadrista::find($id);

        $documentos = array();

        foreach(TipoDocumentoPais::where([["pais_id","=",$request->input("pais_nascimento_id")]])->get() as $tipo_documento_pais){
            if($tipo_documento_pais->e_requerido){
                if(!$request->has("tipo_documento_".$tipo_documento_pais->tipo_documento->id)){
                    $messageBag = new MessageBag;
                    $messageBag->add("type","danger");
                    $messageBag->add("alerta","Há um documento que é requerido que não foi informado.");

                    return redirect()->back()->withErrors($messageBag);
                }
                if(
                    $request->input("tipo_documento_".$tipo_documento_pais->tipo_documento->id) == "" ||
                    $request->input("tipo_documento_".$tipo_documento_pais->tipo_documento->id) == NULL
                ){
                    $messageBag = new MessageBag;
                    $messageBag->add("type","danger");
                    $messageBag->add("alerta","Há um documento que é requerido que não foi informado.");

                    return redirect()->back()->withErrors($messageBag);
                }
            }
            if($request->has("tipo_documento_".$tipo_documento_pais->tipo_documento->id)){
                if(
                    $request->input("tipo_documento_".$tipo_documento_pais->tipo_documento->id) != "" &&
                    $request->input("tipo_documento_".$tipo_documento_pais->tipo_documento->id) != NULL
                ){

                    $temEnxadrista_count = Enxadrista::where([
                        ["id","!=",$enxadrista->id]
                    ])
                    ->whereHas("documentos",function($q1) use($request, $tipo_documento_pais){
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
                    })->count();

                    if($temEnxadrista_count > 0){
                        $messageBag = new MessageBag;
                        $messageBag->add("type","danger");
                        $messageBag->add("alerta","Já há um cadastro de Enxadrista com o Documento informado.");

                        return redirect()->back()->withErrors($messageBag);
                    }

                    $documento = new Documento;
                    $documento->tipo_documentos_id = $tipo_documento_pais->tipo_documento->id;
                    if($tipo_documento_pais->tipo_documento->id == 1){
                        $documento->numero = Util::numeros($request->input("tipo_documento_".$tipo_documento_pais->tipo_documento->id));
                    }else{
                        $documento->numero = $request->input("tipo_documento_".$tipo_documento_pais->tipo_documento->id);
                    }
                    $documento->enxadrista_id = $enxadrista->id;

                    $documentos[] = $documento;
                }
            }
        }

        if(count($documentos) == 0){
            $messageBag = new MessageBag;
            $messageBag->add("type","danger");
            $messageBag->add("alerta","É obrigatório a inserção de ao menos UM DOCUMENTO.");

            return redirect()->back()->withErrors($messageBag);
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
        $enxadrista->name = $nome_corrigido;

        $split_names = false;

        if(!$request->has("firstname") && !$request->has("lastname")){
            $enxadrista->splitName();
        }else{
            if($request->input("firstname") == "" && $request->input("lastname") == ""){
                $enxadrista->splitName();
            }else{
                $split_names = true;
            }
        }



        if($split_names){
            // Algoritmo para eliminar os problemas com espaçamentos duplos ou até triplos.
            $first_nome_corrigido = "";

            $part_first_names = explode(" ", mb_strtoupper($request->input("firstname")));
            foreach ($part_first_names as $part_name) {
                if ($part_name != ' ') {
                    $trim = trim($part_name);
                    if (strlen($trim) > 0) {
                        $first_nome_corrigido .= $trim;
                        $first_nome_corrigido .= " ";
                    }
                }
            }
            $first_nome_corrigido = trim($first_nome_corrigido);

            // Algoritmo para eliminar os problemas com espaçamentos duplos ou até triplos.
            $last_nome_corrigido = "";

            $part_last_names = explode(" ", mb_strtoupper($request->input("lastname")));
            foreach ($part_last_names as $part_name) {
                if ($part_name != ' ') {
                    $trim = trim($part_name);
                    if (strlen($trim) > 0) {
                        $last_nome_corrigido .= $trim;
                        $last_nome_corrigido .= " ";
                    }
                }
            }
            $last_nome_corrigido = trim($last_nome_corrigido);

            $enxadrista->firstname = mb_convert_case($first_nome_corrigido,MB_CASE_TITLE,"UTF-8");
            $enxadrista->lastname = mb_convert_case($last_nome_corrigido,MB_CASE_TITLE,"UTF-8");
        }

        $enxadrista->setBorn($request->input("born"));
        $enxadrista->cidade_id = $request->input("cidade_id");
        $enxadrista->sexos_id = $request->input("sexos_id");
        $enxadrista->pais_id = $request->input("pais_nascimento_id");
        $enxadrista->pais_celular_id = $request->input("pais_celular_id");
        $enxadrista->celular = $request->input("celular");
        if ($request->has("clube_id")) {
            if ($request->input("clube_id")) {
                $enxadrista->clube_id = $request->input("clube_id");
            } else {
                $enxadrista->clube_id = null;
            }
        } else {
            $enxadrista->clube_id = null;
        }
        $enxadrista->email = $request->input("email");
        if ($request->has("lichess_username")) {
            if ($request->input("lichess_username")) {
                $enxadrista->lichess_username = mb_strtolower($request->input("lichess_username"));
            } else {
                $enxadrista->lichess_username = null;
            }
        } else {
            $enxadrista->lichess_username = null;
        }
        if ($request->has("chess_com_username")) {
            if ($request->input("chess_com_username")) {
                $enxadrista->chess_com_username = mb_strtolower($request->input("chess_com_username"));
            } else {
                $enxadrista->chess_com_username = null;
            }
        } else {
            $enxadrista->chess_com_username = null;
        }
        $enxadrista->save();


        if ($request->has("cbx_id")) {
            if ($request->input("cbx_id")) {
                $enxadrista->setCBXID($request->input("cbx_id"));
            } else {
                $enxadrista->setCBXID();
            }
        } else {
            $enxadrista->setCBXID();
        }
        if ($request->has("fide_id")) {
            if ($request->input("fide_id")) {
                $enxadrista->setFIDEID($request->input("fide_id"));
            } else {
                $enxadrista->setFIDEID();
            }
        } else {
            $enxadrista->setFIDEID();
        }
        if ($request->has("lbx_id")) {
            if ($request->input("lbx_id")) {
                $enxadrista->setLBXID($request->input("lbx_id"));
            } else {
                $enxadrista->setLBXID(null);
            }
        } else {
            $enxadrista->setLBXID(null);
        }

        foreach($enxadrista->documentos->all() as $documento){
            $documento->delete();
        }

        foreach($documentos as $documento){
            $documento->save();
        }


        return redirect("/enxadrista/edit/" . $enxadrista->id);
    }
    public function delete($id)
    {
        $user = Auth::user();
        if (!($user->hasPermissionGlobal() || $user->hasPermissionGlobalbyPerfil([9]))) {
            return redirect("/");
        }

        $enxadrista = Enxadrista::find($id);

        if ($enxadrista->isDeletavel()) {
            foreach($enxadrista->documentos->all() as $documento){
                $documento->delete();
            }
            $enxadrista->delete();
        }
        return redirect("/enxadrista");
    }

    public function downloadBaseCompleta()
    {
        if (
            !Auth::user()->hasPermissionGlobal()
        ) {
            return redirect("/");
        }
        $enxadristasView = new EnxadristasCompletoFromView();
        return Excel::download($enxadristasView, 'xadrezSuico_exportacao_lista_enxadristas_' . date('d-m-Y--H-i-s') . '.xlsx');
    }

    /*
     *
     *
     * API
     *
     *
     */
    public function searchEnxadristasList($type = 0, Request $request)
    {
        $user = Auth::user();
        if (!($user->hasPermissionGlobal() || $user->hasPermissionGlobalbyPerfil([9]))) {
            return redirect("/");
        }

        $permitido_edicao = false;
        if (
            $user->hasPermissionGlobal() ||
            $user->hasPermissionEventsByPerfil([4])
        ) {
            $permitido_edicao = true;
        }

        $requisicao = $request->all();

        $enxadristaBorn = new Enxadrista();

        $recordsTotal = Enxadrista::count();
        $enxadristas = Enxadrista::where([["name", "like", "%" . $requisicao["search"]["value"] . "%"]]);
        $enxadristas->orWhere([["id", "=", $requisicao["search"]["value"]]]);
        $enxadristas->orWhere(function ($q1) use ($requisicao) {
            $q1->whereHas("sexo", function ($q2) use ($requisicao) {
                $q2->where([["name", "like", "%" . $requisicao["search"]["value"] . "%"]]);
                $q2->orWhere([["abbr", "like", "%" . $requisicao["search"]["value"] . "%"]]);
            });
        });

        $enxadristaBorn->setBorn($requisicao["search"]["value"]);
        if ($enxadristaBorn->getBorn()) {
            $enxadristas->orWhere([["born", "=", $enxadristaBorn->getBorn()]]);
        }

        $enxadristas->orWhere([["fide_id", "=", $requisicao["search"]["value"]]]);
        $enxadristas->orWhere([["cbx_id", "=", $requisicao["search"]["value"]]]);
        $enxadristas->orWhere([["lbx_id", "=", $requisicao["search"]["value"]]]);
        $enxadristas->orWhere(function ($q1) use ($requisicao) {
            $q1->whereHas("cidade", function ($q2) use ($requisicao) {
                $q2->where([["name", "like", "%" . $requisicao["search"]["value"] . "%"]]);
            });
        });
        $enxadristas->orWhere(function ($q1) use ($requisicao) {
            $q1->whereHas("clube", function ($q2) use ($requisicao) {
                $q2->where([["name", "like", "%" . $requisicao["search"]["value"] . "%"]]);
            });
        });

        switch ($requisicao["order"][0]["column"]) {
            case 1:
                $enxadristas->orderBy("name", mb_strtoupper($requisicao["order"][0]["dir"]));
                break;
            case 2:
                $enxadristas->orderBy("born", mb_strtoupper($requisicao["order"][0]["dir"]));
                break;
            case 3:
                $enxadristas->orderBy("sexos_id", mb_strtoupper($requisicao["order"][0]["dir"]));
                break;
            case 4:
                $enxadristas->orderBy("fide_id", mb_strtoupper($requisicao["order"][0]["dir"]));
                $enxadristas->orderBy("cbx_id", mb_strtoupper($requisicao["order"][0]["dir"]));
                $enxadristas->orderBy("lbx_id", mb_strtoupper($requisicao["order"][0]["dir"]));
                break;
            case 5:
                $enxadristas->orderBy("cidade_id", mb_strtoupper($requisicao["order"][0]["dir"]));
                break;
            case 6:
                $enxadristas->orderBy("clube_id", mb_strtoupper($requisicao["order"][0]["dir"]));
                break;
            default:
                $enxadristas->orderBy("id", mb_strtoupper($requisicao["order"][0]["dir"]));
        }
        $total = $enxadristas->count();
        $enxadristas->limit($requisicao["length"]);
        $enxadristas->skip($requisicao["start"]);

        $retorno = array("draw" => $requisicao["draw"], "recordsTotal" => $recordsTotal, "recordsFiltered" => $total, "data" => array(), "requisicao" => $requisicao);
        foreach ($enxadristas->get() as $enxadrista) {
            $p = array();
            $p[0] = $enxadrista->id;
            $p[1] = $enxadrista->name;

            $p[2] = $enxadrista->getBorn();

            $p[3] = $enxadrista->sexo->name;

            $p[4] = "";
            if ($enxadrista->cbx_id) {
                $p[4] .= "CBX: " . $enxadrista->cbx_id . "<br/>";
            }
            if ($enxadrista->fide_id) {
                $p[4] .= "FIDE: " . $enxadrista->fide_id . "<br/>";
            }
            if ($enxadrista->lbx_id) {
                $p[4] .= "LBX: " . $enxadrista->lbx_id . "<br/>";
            }

            $p[5] = "#" . $enxadrista->cidade->id . " - " . $enxadrista->cidade->name;

            if ($enxadrista->clube) {
                $p[6] = $enxadrista->clube->getName();
            } else {
                $p[6] = "Não possui clube";
            }

            $p[7] = "";
            if ($permitido_edicao) {
                $p[7] .= '<a href="' . url("/enxadrista/edit/" . $enxadrista->id) . '" title="Editar Enxadrista: ' . $enxadrista->id . ' ' . $enxadrista->name . '" class="btn btn-success btn-sm" data-toggle="tooltip" data-original-title="Editar Enxadrista"><i class="fa fa-edit"></i></a>';
            }

            if ($enxadrista->isDeletavel() && $permitido_edicao) {
                $p[7] .= '<button type="button" class="btn btn-danger btn-sm" data-toggle="modal" title="Deletar Enxadrista: ' . $enxadrista->id . ' ' . $enxadrista->name . '" data-target="#modalDelete_' . $enxadrista->id . '"><i class="fa fa-times"></i></button>
                    <!-- Modal Exclusão -->
                    <div class="modal fade modal-danger" id="modalDelete_' . $enxadrista->id . '" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Efetuar Exclusão - Enxadrista #' . $enxadrista->id . ': ' . $enxadrista->name . '</h4>
                            </div>
                            <div class="modal-body">
                            <h2>Você tem certeza que pretende fazer isso?</h2><br>
                            O enxadrista será deletado e não será possível recuperá-lo.
                            <h4>Você deseja ainda assim efetuar a exclusão?</h4>
                            </div>
                            <div class="modal-footer">
                            <button type="button" class="btn btn-success" data-dismiss="modal">Não quero mais</button>
                            <a class="btn btn-danger" href="' . url("/enxadrista/delete/" . $enxadrista->id) . '">Efetuar a exclusão</a>
                            </div>
                        </div>
                        </div>
                    </div>';
            }

            $retorno["data"][] = $p;
        }
        return response()->json($retorno);
    }



    public function updateAllnames(){
        foreach(Enxadrista::where([
            ["firstname","=",NULL],
            ["lastname","=",NULL],
        ])
        ->orWhere([
            ["firstname","=",""],
            ["lastname","=",""],
        ])
        ->get() as $enxadrista){
            $enxadrista->splitName();
            $enxadrista->save();
        }
        return redirect("/enxadrista");
    }
}

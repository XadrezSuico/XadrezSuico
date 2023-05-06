<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Process\RatingController;

use App\Http\Controllers\External\XadrezSuicoPagController;

use App\Categoria;
use App\CategoriaEvento;
use App\Cidade;
use App\CriterioDesempate;
use App\CriterioDesempateEvento;
use App\Evento;
use App\Exports\EnxadristasFromView;
use App\Inscricao;
use App\Pagina;
use App\Software;
use App\TipoRating;
use App\TipoRatingEvento;
use App\TipoTorneio;
use App\EmailTemplate;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Maatwebsite\Excel\Facades\Excel;

use App\Enum\EmailType;
use App\Enum\ConfigType;

use Log;

class EventoGerenciarController extends Controller
{
    public function __construct()
    {
        return $this->middleware("auth");
    }

    // public function index()
    // {
    //     $user = Auth::user();
    //     if (
    //         !$user->hasPermissionGlobal() &&
    //         !$user->hasPermissionEventsByPerfil([3, 4, 5]) &&
    //         !$user->hasPermissionGroupEventsByPerfil([6])
    //     ) {
    //         return redirect("/");
    //     }
    //     $eventos = Evento::all();
    //     return view("evento.index", compact("eventos"));
    // }

    public function edit($id, Request $request)
    {
        $user = Auth::user();
        $evento = Evento::find($id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($id, [3, 4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [6,7])
        ) {
            return redirect("/");
        }

        $categorias = Categoria::where([
            ["evento_id", "=", $evento->id],
        ])
            ->orWhere([
                ["grupo_evento_id", "=", $evento->grupo_evento->id],
            ])
            ->get();
        $criterios_desempate = CriterioDesempate::criterios_evento()->get();
        $tipos_torneio = TipoTorneio::all();
        $softwares = Software::all();
        $tipos_rating = TipoRating::all();
        $cidades = Cidade::all();
        if ($request->has("tab")) {
            $tab = $request->input("tab");
        } else {
            $tab = null;
        }
        $this->importEmailTemplates($evento->id);

        $xadrezsuicopag_controller = null;
        if(
            env("XADREZSUICOPAG_URI",null) &&
            env("XADREZSUICOPAG_SYSTEM_ID",null) &&
            env("XADREZSUICOPAG_SYSTEM_TOKEN",null) &&
            $user->hasPermissionGlobalbyPerfil([1,10,11,12])
        ){
            $xadrezsuicopag_controller = XadrezSuicoPagController::getInstance();
        }

        return view('evento.edit', compact(
                                            "evento",
                                            "categorias",
                                            "criterios_desempate",
                                            "tipos_torneio",
                                            "softwares",
                                            "tipos_rating",
                                            "cidades",
                                            "tab",
                                            "xadrezsuicopag_controller"
                                    )
        );
    }

    public function edit_post($id, Request $request)
    {
        $user = Auth::user();
        $evento = Evento::find($id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/" . $evento->id);
        }


        $datetime_data_inicio = DateTime::createFromFormat('d/m/Y', $request->input("data_inicio"));
        $datetime_data_fim = DateTime::createFromFormat('d/m/Y', $request->input("data_fim"));
        $datetime_data_limite_inscricoes_abertas = DateTime::createFromFormat('d/m/Y H:i', $request->input("data_limite_inscricoes_abertas"));

        // CADASTRO DO EVENTO
        $evento->name = $request->input("name");
        $evento->data_inicio = $datetime_data_inicio->format('Y-m-d');
        $evento->data_fim = $datetime_data_fim->format('Y-m-d');
        $evento->local = $request->input("local");
        $evento->cidade_id = $request->input("cidade_id");
        $evento->tipo_modalidade = $request->input("tipo_modalidade");
        $evento->exportacao_sm_modelo = $request->input("exportacao_sm_modelo");
        $evento->layout_version = $request->input("layout_version");
        if ($request->has("link")) {
            $evento->link = $request->input("link");
        } else {
            $evento->link = null;
        }
        if ($request->has("maximo_inscricoes_evento")) {
            if (is_numeric($request->input("maximo_inscricoes_evento"))) {
                $evento->maximo_inscricoes_evento = intval($request->input("maximo_inscricoes_evento"));
            } else {
                $evento->maximo_inscricoes_evento = null;
            }
        } else {
            $evento->maximo_inscricoes_evento = null;
        }
        if ($request->has("data_limite_inscricoes_abertas") && $datetime_data_limite_inscricoes_abertas) {
            $evento->data_limite_inscricoes_abertas = $datetime_data_limite_inscricoes_abertas->format('Y-m-d H:i').":59";
        } else {
            $evento->data_limite_inscricoes_abertas = null;
        }
        if ($request->has("e_permite_visualizar_lista_inscritos_publica")) {
            $evento->e_permite_visualizar_lista_inscritos_publica = true;
        } else {
            $evento->e_permite_visualizar_lista_inscritos_publica = false;
        }

        if ($request->has("e_inscricao_apenas_com_link")) {
            $evento->e_inscricao_apenas_com_link = true;
            if ($evento->token == null) {
                $evento->gerarToken();
            }
        } else {
            $evento->e_inscricao_apenas_com_link = false;
        }
        if ($request->has("orientacao_pos_inscricao")) {
            if ($request->input("orientacao_pos_inscricao") != "") {
                $evento->orientacao_pos_inscricao = $request->input("orientacao_pos_inscricao");
            } else {
                $evento->orientacao_pos_inscricao = null;
            }
        } else {
            $evento->orientacao_pos_inscricao = null;
        }

        if ($request->has("usa_fide") && !$request->has("usa_lbx")) {
            $evento->usa_fide = true;
        } else {
            $evento->usa_fide = false;
        }
        if ($request->has("calcula_fide") && !$request->has("usa_lbx")) {
            $evento->calcula_fide = true;
        } else {
            $evento->calcula_fide = false;
        }
        if ($request->has("fide_required") && !$request->has("calcula_fide") && !$request->has("usa_lbx")) {
            $evento->fide_required = true;
        } else {
            $evento->fide_required = false;
        }


        if ($request->has("usa_cbx")) {
            $evento->usa_cbx = true;
        } else {
            $evento->usa_cbx = false;
        }
        if ($request->has("calcula_cbx")) {
            $evento->calcula_cbx = true;
        } else {
            $evento->calcula_cbx = false;
        }
        if ($request->has("cbx_required") && !$request->has("calcula_cbx")) {
            $evento->cbx_required = true;
        } else {
            $evento->cbx_required = false;
        }
        if ($request->has("fide_sequence")) {
            $evento->setConfig("fide_sequence",ConfigType::Boolean,true);
        } else {
            $evento->setConfig("fide_sequence",ConfigType::Boolean,false);
        }


        if ($request->has("usa_lbx")) {
            $evento->usa_lbx = true;
        } else {
            $evento->usa_lbx = false;
        }
        if ($request->has("is_lichess")) {
            $evento->is_lichess = true;
        } else {
            $evento->is_lichess = false;
        }
        if ($request->has("is_lichess_integration")) {
            $evento->is_lichess_integration = true;

            if($request->has("lichess_team_id")){
                if($request->input("lichess_team_id") != ""){
                    $evento->lichess_team_id = $request->input("lichess_team_id");
                }else{
                    $evento->lichess_team_id = NULL;
                }
            }else{
                $evento->lichess_team_id = NULL;
            }

            if($request->has("lichess_team_password")){
                if($request->input("lichess_team_password") != ""){
                    $evento->lichess_team_password = $request->input("lichess_team_password");
                }else{
                    $evento->lichess_team_password = NULL;
                }
            }else{
                $evento->lichess_team_password = NULL;
            }

            if($request->has("lichess_tournament_id")){
                if($request->input("lichess_tournament_id") != ""){
                    $evento->lichess_tournament_id = $request->input("lichess_tournament_id");
                }else{
                    $evento->lichess_tournament_id = NULL;
                }
            }else{
                $evento->lichess_tournament_id = NULL;
            }

            if($request->has("lichess_tournament_password")){
                if($request->input("lichess_tournament_password") != ""){
                    $evento->lichess_tournament_password = $request->input("lichess_tournament_password");
                }else{
                    $evento->lichess_tournament_password = NULL;
                }
            }else{
                $evento->lichess_tournament_password = NULL;
            }
        } else {
            $evento->is_lichess_integration = false;
            $evento->lichess_team_id = NULL;
            $evento->lichess_team_password = NULL;
            $evento->lichess_tournament_id = NULL;
            $evento->lichess_tournament_password = NULL;
        }
        if ($request->has("is_chess_com")) {
            $evento->is_chess_com = true;
        } else {
            $evento->is_chess_com = false;
        }

        if ($request->has("evento_classificador_id")) {
            if ($request->input("evento_classificador_id") != "") {
                $evento->evento_classificador_id = $request->input("evento_classificador_id");
            } else {
                $evento->evento_classificador_id = null;
            }
        } else {
            $evento->evento_classificador_id = null;
        }

        if ($request->has("grupo_evento_classificador_id")) {
            if ($request->input("grupo_evento_classificador_id") != "") {
                $evento->grupo_evento_classificador_id = $request->input("grupo_evento_classificador_id");
            } else {
                $evento->grupo_evento_classificador_id = null;
            }
        } else {
            $evento->grupo_evento_classificador_id = null;
        }


        if ($request->has("e_permite_confirmacao_publica")) {
            $evento->e_permite_confirmacao_publica = true;

            if($request->has("confirmacao_publica_inicio")){
                if($request->input("confirmacao_publica_inicio") != ""){
                    $evento->setConfirmacoesDataInicial($request->input("confirmacao_publica_inicio"));
                }else{
                    $evento->confirmacao_publica_inicio = NULL;
                }
            }else{
                $evento->confirmacao_publica_inicio = NULL;
            }

            if($request->has("confirmacao_publica_final")){
                if($request->input("confirmacao_publica_final") != ""){
                    $evento->setConfirmacoesDataFim($request->input("confirmacao_publica_final"));
                }else{
                    $evento->confirmacao_publica_final = NULL;
                }
            }else{
                $evento->confirmacao_publica_final = NULL;
            }
        }else{
            $evento->e_permite_confirmacao_publica = false;
            $evento->confirmacao_publica_inicio = NULL;
            $evento->confirmacao_publica_final = NULL;
        }

        if(
            env("XADREZSUICOPAG_URI",null) &&
            env("XADREZSUICOPAG_SYSTEM_ID",null) &&
            env("XADREZSUICOPAG_SYSTEM_TOKEN",null)
        ){
            if($user->hasPermissionGlobalbyPerfil([1,10,11])){
                if($request->has("xadrezsuicopag_uuid")){
                    if($request->input("xadrezsuicopag_uuid") != ""){
                        $evento->xadrezsuicopag_uuid = $request->input("xadrezsuicopag_uuid");
                    }else{
                        $evento->xadrezsuicopag_uuid = null;
                    }
                }else{
                    $evento->xadrezsuicopag_uuid = null;
                }
            }
        }else{
            $evento->xadrezsuicopag_uuid = null;
        }


        $evento->save();

        if ($request->has("tipo_ratings_id")) {
            if (
                ($evento->tipo_rating_interno && $evento->tipo_rating) ||
                (!$evento->tipo_rating_interno && !$evento->tipo_rating)
            ) {
                if ($request->input("tipo_ratings_id") != "") {
                    if (!$evento->tipo_rating_interno) {
                        $tipo_rating = new TipoRatingEvento;
                        $tipo_rating->evento_id = $evento->id;
                        $tipo_rating->tipo_ratings_id = $request->input("tipo_ratings_id");
                        $tipo_rating->save();
                    } else {
                        $evento->tipo_rating_interno->tipo_ratings_id = $request->input("tipo_ratings_id");
                        $evento->tipo_rating_interno->save();
                    }
                } else {
                    if ($evento->tipo_rating_interno) {
                        $evento->tipo_rating_interno->delete();
                    }
                }
            }
        }

        return redirect("/evento/dashboard/" . $id. "?tab=editar_evento");
    }

    public function edit_pagina_post($id, Request $request)
    {
        $user = Auth::user();
        $evento = Evento::find($id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/" . $evento->id. "?tab=pagina");
        }

        $evento = Evento::find($id);

        if (!$evento->pagina) {
            $pagina = new Pagina;
            $pagina->evento_id = $id;
            $pagina->save();

            $evento = Evento::find($id);
        }

        // CADASTRO DO EVENTO
        if ($request->hasFile('imagem')) {
            if ($request->file('imagem')->isValid()) {
                $evento->pagina->imagem = base64_encode(file_get_contents($request->file('imagem')));
            }
        }
        if ($request->has("texto")) {
            $evento->pagina->texto = $request->input("texto");
        } else {
            $evento->pagina->texto = null;
        }
        if ($request->has('remover_imagem')) {
            $evento->pagina->imagem = null;
        }
        $evento->pagina->save();
        return redirect("/evento/dashboard/" . $id . "?tab=pagina");
    }

    public function delete($id)
    {
        $user = Auth::user();
        $evento = Evento::find($id);
        $grupo_evento = $evento->grupo_evento;
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/grupoevento/dashboard/" . $grupo_evento->id);
        }

        if($evento->isDeletavel()){
            foreach($evento->email_templates->all() as $email_template){
                $email_template->delete();
            }
            $evento->delete();
        }
        return redirect("/grupoevento/dashboard/" . $grupo_evento->id);

    }

    public function categoria_add($id, Request $request)
    {
        $user = Auth::user();
        $evento = Evento::find($id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/" . $evento->id);
        }

        $categoria_evento = new CategoriaEvento;
        $categoria_evento->evento_id = $id;
        $categoria_evento->categoria_id = $request->input("categoria_id");

        if(
            env("XADREZSUICOPAG_URI",null) &&
            env("XADREZSUICOPAG_SYSTEM_ID",null) &&
            env("XADREZSUICOPAG_SYSTEM_TOKEN",null) &&
            $user->hasPermissionGlobalbyPerfil([1,10,11]) &&
            $evento->xadrezsuicopag_uuid != ""
        ){
            if($request->has("xadrezsuicopag_uuid")){
                if($request->input("xadrezsuicopag_uuid") != ""){
                    $xadrezsuicopag_controller = XadrezSuicoPagController::getInstance();

                    $xadrezsuicopag_category_request = $xadrezsuicopag_controller::factory("category")->get($evento->xadrezsuicopag_uuid,$request->input("xadrezsuicopag_uuid"));

                    if($xadrezsuicopag_category_request->ok){
                        $categoria_evento->xadrezsuicopag_uuid = $request->input("xadrezsuicopag_uuid");
                    }
                }else{
                    $categoria_evento->xadrezsuicopag_uuid = null;
                }
            }else{
                $categoria_evento->xadrezsuicopag_uuid = null;
            }
        }else{
            $categoria_evento->xadrezsuicopag_uuid = null;
        }

        $categoria_evento->save();
        return redirect("/evento/dashboard/" . $id . "?tab=categorias_relacionadas");
    }

    public function categoria_edit($evento_id, $id)
    {
        $evento = Evento::find($evento_id);
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/" . $evento->id);
        }
        $categoria = CategoriaEvento::find($id);

        $xadrezsuicopag_controller = XadrezSuicoPagController::getInstance();
        return view('evento.categoria_evento.edit', compact("categoria", "xadrezsuicopag_controller", "evento"));
    }
    public function categoria_edit_post($evento_id, $id, Request $request)
    {
        $user = Auth::user();
        $evento = Evento::find($evento_id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/" . $evento->id);
        }

        $categoria_evento = CategoriaEvento::find($id);

        if(
            env("XADREZSUICOPAG_URI",null) &&
            env("XADREZSUICOPAG_SYSTEM_ID",null) &&
            env("XADREZSUICOPAG_SYSTEM_TOKEN",null) &&
            $user->hasPermissionGlobalbyPerfil([1,10,11]) &&
            $evento->xadrezsuicopag_uuid != ""
        ){
            if($request->has("xadrezsuicopag_uuid")){
                if($request->input("xadrezsuicopag_uuid") != ""){
                    $xadrezsuicopag_controller = XadrezSuicoPagController::getInstance();

                    $xadrezsuicopag_category_request = $xadrezsuicopag_controller::factory("category")->get($evento->xadrezsuicopag_uuid,$request->input("xadrezsuicopag_uuid"));

                    if($xadrezsuicopag_category_request->ok){
                        $categoria_evento->xadrezsuicopag_uuid = $request->input("xadrezsuicopag_uuid");
                    }
                }else{
                    $categoria_evento->xadrezsuicopag_uuid = null;
                }
            }else{
                $categoria_evento->xadrezsuicopag_uuid = null;
            }
        }else{
            $categoria_evento->xadrezsuicopag_uuid = null;
        }

        $categoria_evento->save();
        return redirect("/evento/dashboard/" . $evento_id . "?tab=categorias_relacionadas");
    }
    public function categoria_remove($id, $categoria_evento_id)
    {
        $user = Auth::user();
        $evento = Evento::find($id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/" . $evento->id);
        }

        $categoria_evento = CategoriaEvento::find($categoria_evento_id);
        $categoria_evento->delete();
        return redirect("/evento/dashboard/" . $id . "?tab=categorias_relacionadas");
    }

    public function classificar($evento_id)
    {
        $user = Auth::user();
        $evento = Evento::find($evento_id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard".$evento->id);
        }
        if ($evento) {
            foreach($evento->torneios->all() as $torneio){
                TorneioController::gerarCriteriosDesempate($torneio->id);
            }
            foreach ($evento->categorias->all() as $categoria) {
                CategoriaController::classificar($evento->id, $categoria->categoria->id);
            }

            $messageBag = new MessageBag;
            $messageBag->add("alerta", "O evento foi classificado com sucesso!");
            $messageBag->add("type", "success");

            return redirect("/evento/dashboard/" . $evento->id)->withErrors($messageBag);
        }
    }

    public function calcular_rating($evento_id)
    {
        $user = Auth::user();
        $evento = Evento::find($evento_id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard".$evento->id);
        }
        if ($evento) {
            RatingController::calculate($evento->id);

            $messageBag = new MessageBag;
            $messageBag->add("alerta", "O rating foi calculado com sucesso!");
            $messageBag->add("type", "success");

            return redirect("/evento/dashboard/" . $evento->id)->withErrors($messageBag);
        }
    }

    public function toggleInscricoes($evento_id)
    {
        $user = Auth::user();
        $evento = Evento::find($evento_id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/".$evento->id);
        }
        if ($evento) {
            if ($evento->is_inscricoes_bloqueadas) {
                $evento->is_inscricoes_bloqueadas = false;
            } else {
                $evento->is_inscricoes_bloqueadas = true;
            }
            $evento->save();
            return redirect("/evento/dashboard/" . $evento->id);
        }
    }
    public function toggleMostrarClassificacao($evento_id)
    {
        $user = Auth::user();
        $evento = Evento::find($evento_id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/".$evento->id);
        }
        if ($evento) {
            if ($evento->mostrar_resultados) {
                $evento->mostrar_resultados = false;
            } else {
                $evento->mostrar_resultados = true;
            }
            $evento->save();
            return redirect("/evento/dashboard/" . $evento->id);
        }
    }

    public function toggleEventoClassificavel($evento_id)
    {
        $user = Auth::user();
        $evento = Evento::find($evento_id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/".$evento->id);
        }
        if ($evento) {
            if ($evento->classificavel) {
                $evento->classificavel = false;
            } else {
                $evento->classificavel = true;
            }
            $evento->save();
            return redirect("/evento/dashboard/" . $evento->id);
        }
    }
    public function toggleClassificacaoManual($evento_id)
    {
        $user = Auth::user();
        $evento = Evento::find($evento_id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard".$evento->id);
        }
        if ($evento) {
            if ($evento->e_resultados_manuais) {
                $evento->e_resultados_manuais = false;
            } else {
                $evento->e_resultados_manuais = true;
            }
            $evento->save();
            return redirect("/evento/dashboard/" . $evento->id);
        }
    }
    public function toggleRating($evento_id)
    {
        $user = Auth::user();
        $evento = Evento::find($evento_id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/".$evento->id);
        }
        if ($evento) {
            if ($evento->is_rating_calculate_enabled) {
                $evento->is_rating_calculate_enabled = false;
            } else {
                $evento->is_rating_calculate_enabled = true;
            }
            $evento->save();
            return redirect("/evento/dashboard/" . $evento->id);
        }
    }
    public function toggleEdicaoInscricao($evento_id)
    {
        $user = Auth::user();
        $evento = Evento::find($evento_id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/".$evento->id);
        }
        if ($evento) {
            if ($evento->permite_edicao_inscricao) {
                $evento->permite_edicao_inscricao = false;
            } else {
                $evento->permite_edicao_inscricao = true;
            }
            $evento->save();
            return redirect("/evento/dashboard/" . $evento->id);
        }
    }

    public function toggleRegistrationPaidConfirmed($evento_id)
    {
        $user = Auth::user();
        $evento = Evento::find($evento_id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/".$evento->id);
        }
        if ($evento) {
            $is_true = false;
            if(!$evento->hasConfig("flag__registration_paid_confirmed")){
                Log::debug("config_not_exists = true");
                $evento->setConfig("flag__registration_paid_confirmed",ConfigType::Boolean,true);
                $is_true = true;
            }else{
                $config = $evento->getConfig("flag__registration_paid_confirmed");
                if($config["config"]->boolean){
                    Log::debug("!config = false");
                    $result = $evento->setConfig("flag__registration_paid_confirmed",ConfigType::Boolean,false);
                    Log::debug("result: ".json_encode($result));
                }else{
                    Log::debug("!config = true");
                    $result = $evento->setConfig("flag__registration_paid_confirmed",ConfigType::Boolean,true);
                    Log::debug("result: ".json_encode($result));
                    $is_true = true;
                }
            }

            if($is_true){
                $this->confirmPaidRegistrations($evento);
            }

            return redirect("/evento/dashboard/" . $evento->id);
        }
    }


    public function confirmAllRegistrations($evento_id)
    {
        $user = Auth::user();
        $evento = Evento::find($evento_id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/".$evento->id);
        }
        if ($evento) {
            foreach($evento->torneios->all() as $torneio){
                foreach($torneio->inscricoes->all() as $inscricao){
                    $inscricao->confirmado = true;
                    $inscricao->save();
                }
            }
            return redirect("/evento/dashboard/" . $evento->id);
        }
    }
    public function unconfirmAllRegistrations($evento_id)
    {
        $user = Auth::user();
        $evento = Evento::find($evento_id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/".$evento->id);
        }
        if ($evento) {
            foreach($evento->torneios->all() as $torneio){
                foreach($torneio->inscricoes->all() as $inscricao){
                    $inscricao->confirmado = false;
                    $inscricao->save();
                }
            }
            return redirect("/evento/dashboard/" . $evento->id);
        }
    }

    public function confirmPaidRegistrations($evento){
        if($evento->isPaid()){
            if($evento->hasConfig("flag__registration_paid_confirmed")){
                if($evento->getConfig("flag__registration_paid_confirmed",true)){
                    foreach($evento->torneios->all() as $torneio){
                        foreach($torneio->inscricoes()->where([
                            ["paid","=",true],
                            ["confirmado","=",false],
                        ])->get() as $inscricao){
                            $inscricao->confirmado = true;
                            $inscricao->save();

                            EmailController::schedule(
                                $inscricao->enxadrista->email,
                                $inscricao,
                                EmailType::InscricaoConfirmadaComPagamentoAutomatico,
                                $inscricao->enxadrista
                            );
                        }
                    }
                }
            }
        }
    }

    public function classificacao($evento_id)
    {
        $user = Auth::user();
        $evento = Evento::find($evento_id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id,[3, 4, 5]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[6,7])
        ) {
            return redirect("/evento/dashboard/".$evento->id);
        }
        return view("evento.publico.classificacao", compact("evento"));
    }
    public function resultados($evento_id, $categoria_id)
    {
        $user = Auth::user();
        $evento = Evento::find($evento_id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/" . $evento->id);
        }

        $categoria = Categoria::find($categoria_id);
        $torneio = $categoria->getTorneioByEvento($evento);
        if($evento->is_lichess_integration){
            $inscricoes = Inscricao::where([
                ["categoria_id", "=", $categoria->id],
            ])
                ->whereHas("torneio", function ($q1) use ($evento) {
                    $q1->where([
                        ["evento_id", "=", $evento->id],
                    ]);
                })
                ->orderBy("confirmado", "DESC")
                ->orderBy("posicao", "ASC")
                ->get();
        }else{
            $inscricoes = Inscricao::where([
                ["categoria_id", "=", $categoria->id],
                ["confirmado", "=", true],
            ])
                ->whereHas("torneio", function ($q1) use ($evento) {
                    $q1->where([
                        ["evento_id", "=", $evento->id],
                    ]);
                })
                ->orderBy("posicao", "ASC")
                ->get();
        }
        $criterios = $torneio->getCriteriosTotal();
        return view("evento.publico.list", compact("evento", "torneio", "categoria", "inscricoes", "criterios"));
    }

    public function visualizar_inscricoes($id)
    {
        $user = Auth::user();
        $evento = Evento::find($id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [3,4,5]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [6,7])
        ) {
            return redirect("/evento/dashboard/" . $evento->id);
        }

        $evento = Evento::find($id);
        if ($evento) {
            return view("evento.inscricoes", compact("evento"));
        }
        return redirect("/evento");
    }

    public function downloadListaManagerParaEvento($id)
    {
        $user = Auth::user();
        $evento = Evento::find($id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/" . $evento->id);
        }

        $enxadristasView = new EnxadristasFromView();
        $enxadristasView->setEvento($id);
        return Excel::download($enxadristasView, 'xadrezSuico_evento_' . $id . '_lista_enxadristas_' . date('d-m-Y--H-i-s') . '.xlsx');
    }

    public function criterio_desempate_add($id, Request $request)
    {
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/" . $evento->id);
        }

        $criterio_desempate_grupo_evento = new CriterioDesempateEvento;
        $criterio_desempate_grupo_evento->evento_id = $id;
        $criterio_desempate_grupo_evento->criterio_desempate_id = $request->input("criterio_desempate_id");
        $criterio_desempate_grupo_evento->tipo_torneio_id = $request->input("tipo_torneio_id");
        $criterio_desempate_grupo_evento->softwares_id = $request->input("softwares_id");
        $criterio_desempate_grupo_evento->prioridade = $request->input("prioridade");
        $criterio_desempate_grupo_evento->save();
        return redirect("/evento/dashboard/" . $id . "?tab=criterio_desempate");
    }
    public function criterio_desempate_remove($id, $cd_grupo_evento_id)
    {
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/" . $evento->id);
        }

        $criterio_desempate_grupo_evento = CriterioDesempateEvento::find($cd_grupo_evento_id);
        $criterio_desempate_grupo_evento->delete();
        return redirect("/evento/dashboard/" . $id . "?tab=criterio_desempate");
    }




    private function importEmailTemplates($evento_id){
        $evento = Evento::find($evento_id);
        $email_type = new EmailType;
        if($evento){
            foreach(EmailTemplate::where([["grupo_evento_id","=",$evento->grupo_evento->id]])->get() as $template){
                if($email_type->get($template->email_type)["is_general"] == 0){
                    if($evento->email_templates()->where([
                        ["email_type","=",$template->email_type]
                    ])->count() == 0){
                        $email_template = new EmailTemplate;
                        $email_template->name = $template->name;
                        $email_template->subject = $template->subject;
                        $email_template->message = $template->message;
                        $email_template->email_type = $template->email_type;
                        $email_template->evento_id = $evento->id;
                        $email_template->save();
                    }
                }
            }
        }
    }



    /*
     *
     * RELATÓRIOS
     *
     */
    public function relatorio_premiados($id)
    {
        $user = Auth::user();
        $evento = Evento::find($id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [3,4,5]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [6,7])
        ) {
            return redirect("/evento/dashboard/" . $evento->id);
        }

        $evento = Evento::find($id);
        if ($evento) {
            return view("evento.relatorios.premiados", compact("evento"));
        }
        return redirect("/evento");
    }
}

<?php

namespace App\Http\Controllers;

use App\Categoria;
use App\CategoriaEvento;
use App\CategoriaGrupoEvento;
use App\CategoriaTorneio;
use App\Cidade;
use App\CriterioDesempate;
use App\CriterioDesempateGrupoEvento;
use App\CriterioDesempateEvento;
use App\CriterioDesempateGrupoEventoGeral;
use App\Evento;
use App\GrupoEvento;
use App\Pontuacao;
use App\Software;
use App\TipoRating;
use App\TipoRatingGrupoEvento;
use App\TipoTorneio;
use App\Torneio;
use App\TorneioTemplate;
use App\TorneioTemplateGrupoEvento;
use App\EmailTemplate;
use Illuminate\Support\Str;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Enum\EmailType;
use Illuminate\Support\MessageBag;

class GrupoEventoController extends Controller
{
    public function __construct()
    {
        return $this->middleware("auth");
    }
    public function index()
    {
        $user = Auth::user();
        $grupos_evento = GrupoEvento::all();
        return view('grupoevento.index', compact("grupos_evento"));
    }
    function new () {
        $user = Auth::user();
        if (!$user->hasPermissionGlobal()) {
            return redirect("/grupoevento");
        }

        return view('grupoevento.new');
    }
    public function new_post(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobal()) {
            return redirect("/grupoevento");
        }

        $grupo_evento = new GrupoEvento;
        $grupo_evento->name = $request->input("name");
        $grupo_evento->save();

        if ($request->has("tipo_ratings_id")) {
            if ($request->input("tipo_ratings_id")) {
                $tipo_rating_grupo_evento = new TipoRatingGrupoEvento;
                $tipo_rating_grupo_evento->grupo_evento_id = $grupo_evento->id;
                $tipo_rating_grupo_evento->tipo_ratings_id = $request->input("tipo_ratings_id");
                $tipo_rating_grupo_evento->save();
            }
        }

        return redirect("/grupoevento/dashboard/" . $grupo_evento->id);
    }
    public function edit($id, Request $request)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobal() && !$user->hasPermissionGroupEventByPerfil($id,[6,7]) && !$user->hasPermissionEventByPerfilByGroupEvent($id,[3,4,5])) {
            return redirect("/grupoevento");
        }

        $grupo_evento = GrupoEvento::find($id);
        $torneio_templates = TorneioTemplate::all();
        $categorias = Categoria::all();
        $criterios_desempate = CriterioDesempate::criterios_evento()->get();
        $criterios_desempate_geral = CriterioDesempate::criterios_grupo_evento()->get();
        $tipos_torneio = TipoTorneio::all();
        $softwares = Software::all();
        $tipos_rating = TipoRating::all();
        if ($request->has("tab")) {
            $tab = $request->input("tab");
        } else {
            $tab = null;
        }

        $this->importEmailTemplates($grupo_evento->id);

        return view('grupoevento.edit', compact("grupo_evento", "torneio_templates", "categorias", "criterios_desempate", "tipos_torneio", "softwares", "criterios_desempate_geral", "tipos_rating", "tab", "user"));
    }
    public function clone($id)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobal() && !$user->hasPermissionGroupEventByPerfil($id, [6, 7])) {
            return redirect("/grupoevento");
        }

        $grupo_evento = GrupoEvento::find($id);

        $novo_grupo_evento = $grupo_evento->replicate();
        $novo_grupo_evento->name .= " (CÓPIA)";
        $novo_grupo_evento->save();

        foreach ($grupo_evento->criterios->all() as $criterio) {
            $criterio_novo_grupo = $criterio->replicate();
            $criterio_novo_grupo->grupo_evento_id = $novo_grupo_evento->id;
            $criterio_novo_grupo->save();
        }

        foreach ($grupo_evento->criterios_gerais->all() as $criterio) {
            $criterio_novo_grupo = $criterio->replicate();
            $criterio_novo_grupo->grupo_evento_id = $novo_grupo_evento->id;
            $criterio_novo_grupo->save();
        }

        foreach ($grupo_evento->pontuacoes->all() as $pontuacao) {
            $pontuacao_novo_grupo = $pontuacao->replicate();
            $pontuacao_novo_grupo->grupo_evento_id = $novo_grupo_evento->id;
            $pontuacao_novo_grupo->save();
        }

        $categories_relationship = array();
        foreach ($grupo_evento->categorias->all() as $categoria) {
            $categoria_novo_grupo = $categoria->replicate();
            $categoria_novo_grupo->grupo_evento_id = $novo_grupo_evento->id;
            $categoria_novo_grupo->save();

            foreach($categoria->sexos->all() as $categoria_sexo){
                $categoria_sexo_novo_grupo = $categoria_sexo->replicate();
                $categoria_sexo_novo_grupo->categoria_id = $categoria_novo_grupo->id;
                $categoria_sexo_novo_grupo->save();
            }

            $categories_relationship[$categoria->id] = $categoria_novo_grupo->id;
        }

        /* todo: CAMPO PERSONALIZADO */


        foreach ($grupo_evento->torneios_template->all() as $torneio_template) {
            $torneio_template_novo_grupo = $torneio_template->replicate();
            $torneio_template_novo_grupo->grupo_evento_id = $novo_grupo_evento->id;
            $torneio_template_novo_grupo->save();

            foreach($torneio_template->categorias->all() as $template_categoria) {
                $template_categoria_novo_grupo = $template_categoria->replicate();
                $template_categoria_novo_grupo->categoria_id = $categories_relationship[$template_categoria_novo_grupo->categoria_id];
                $template_categoria_novo_grupo->torneio_template_id = $torneio_template_novo_grupo->id;
                $template_categoria_novo_grupo->save();
            }
        }

        $this->importEmailTemplates($novo_grupo_evento->id);

        if(!$user->hasPermissionGlobal()){
            foreach($user->perfis()->where([["grupo_evento_id","=",$grupo_evento->id]])->get() as $user_profile){
                $new_user_profile = $user_profile->replicate();
                $new_user_profile->grupo_evento_id = $novo_grupo_evento->id;
                $new_user_profile->save();
            }
        }

        $messageBag = new MessageBag;
        $messageBag->add("type", "success");
        $messageBag->add("alerta", "O Grupo de Evento #{$grupo_evento->id} - {$grupo_evento->name} foi clonado com sucesso!");

        return redirect("/grupoevento/dashboard/".$novo_grupo_evento->id)->withErrors($messageBag);
    }
    public function edit_post($id, Request $request)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobal() && !$user->hasPermissionGroupEventByPerfil($id,[7])) {
            return redirect("/grupoevento");
        }

        $grupo_evento = GrupoEvento::find($id);
        $grupo_evento->name = $request->input("name");
        if ($request->has("limite_calculo_geral")) {
            if ($request->input("limite_calculo_geral") != "") {
                $grupo_evento->limite_calculo_geral = $request->input("limite_calculo_geral");
            } else {
                $grupo_evento->limite_calculo_geral = null;
            }
        } else {
            $grupo_evento->limite_calculo_geral = null;
        }

        if ($request->has("regulamento_link")) {
            if ($request->input("regulamento_link") != "") {
                $grupo_evento->regulamento_link = $request->input("regulamento_link");
            } else {
                $grupo_evento->regulamento_link = null;
            }
        } else {
            $grupo_evento->regulamento_link = null;
        }


        if(
            env("XADREZSUICOPAG_URI",null) &&
            env("XADREZSUICOPAG_SYSTEM_ID",null) &&
            env("XADREZSUICOPAG_SYSTEM_TOKEN",null)
        ){
            if($user->hasPermissionGlobalbyPerfil([1,10,11])){
                if($request->has("xadrezsuicopag_uuid")){
                    if($request->input("xadrezsuicopag_uuid") != ""){
                        $grupo_evento->xadrezsuicopag_uuid = $request->input("xadrezsuicopag_uuid");
                    }else{
                        $grupo_evento->xadrezsuicopag_uuid = null;
                    }
                }else{
                    $grupo_evento->xadrezsuicopag_uuid = null;
                }
            }
        }else{
            $grupo_evento->xadrezsuicopag_uuid = null;
        }


        if ($request->has("grupo_evento_classificador_id")) {
            if ($request->input("grupo_evento_classificador_id") != "") {
                $grupo_evento->grupo_evento_classificador_id = $request->input("grupo_evento_classificador_id");
            } else {
                $grupo_evento->grupo_evento_classificador_id = null;
            }
        } else {
            $grupo_evento->grupo_evento_classificador_id = null;
        }

        if ($request->has("e_pontuacao_resultado_para_geral")) {
            $grupo_evento->e_pontuacao_resultado_para_geral = true;
        } else {
            $grupo_evento->e_pontuacao_resultado_para_geral = false;
        }
        $grupo_evento->save();

        if ($request->has("tipo_ratings_id")) {
            if ($request->input("tipo_ratings_id")) {
                if (!$grupo_evento->tipo_rating) {
                    $tipo_rating_grupo_evento = new TipoRatingGrupoEvento;
                    $tipo_rating_grupo_evento->grupo_evento_id = $grupo_evento->id;
                    $tipo_rating_grupo_evento->tipo_ratings_id = $request->input("tipo_ratings_id");
                    $tipo_rating_grupo_evento->save();
                } else {
                    $grupo_evento->tipo_rating->tipo_ratings_id = $request->input("tipo_ratings_id");
                    $grupo_evento->tipo_rating->save();
                }
            } else {
                if ($grupo_evento->tipo_rating) {
                    $grupo_evento->tipo_rating->delete();
                }
            }
        } else {
            if ($grupo_evento->tipo_rating) {
                $grupo_evento->tipo_rating->delete();
            }
        }
        return redirect("/grupoevento/dashboard/" . $grupo_evento->id);
    }
    public function delete($id)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobal()) {
            return redirect("/grupoevento");
        }

        $grupo_evento = GrupoEvento::find($id);

        if ($grupo_evento->isDeletavel()) {
            $grupo_evento->delete();
        }
        return redirect("/grupoevento");
    }

    public function torneio_template_add($id, Request $request)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobal() && !$user->hasPermissionGroupEventByPerfil($id,[7])) {
            return redirect("/grupoevento");
        }

        $torneio_template_grupo_evento = new TorneioTemplateGrupoEvento;
        $torneio_template_grupo_evento->grupo_evento_id = $id;
        $torneio_template_grupo_evento->torneio_template_id = $request->input("torneio_template_id");
        $torneio_template_grupo_evento->save();
        return redirect("/grupoevento/dashboard/" . $id . "?tab=torneio_template");
    }
    public function torneio_template_remove($id, $torneio_template_grupo_evento_id)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobal() && !$user->hasPermissionGroupEventByPerfil($id,[7])) {
            return redirect("/grupoevento");
        }

        $torneio_template_grupo_evento = TorneioTemplateGrupoEvento::find($torneio_template_grupo_evento_id);
        $torneio_template_grupo_evento->delete();
        return redirect("/grupoevento/dashboard/" . $id . "?tab=torneio_template");
    }

    public function categoria_add($id, Request $request)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobal() && !$user->hasPermissionGroupEventByPerfil($id,[7])) {
            return redirect("/grupoevento");
        }

        $categoria_grupo_evento = new CategoriaGrupoEvento;
        $categoria_grupo_evento->grupo_evento_id = $id;
        $categoria_grupo_evento->categoria_id = $request->input("categoria_id");
        if ($request->has("nao_classificar")) {
            $categoria_grupo_evento->nao_classificar = true;
        }

        $categoria_grupo_evento->save();
        return redirect("/grupoevento/dashboard/" . $id . "?tab=categoria");
    }
    public function categoria_remove($id, $categoria_grupo_evento_id)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobal() && !$user->hasPermissionGroupEventByPerfil($id,[7])) {
            return redirect("/grupoevento");
        }

        $categoria_grupo_evento = CategoriaGrupoEvento::find($categoria_grupo_evento_id);
        $categoria_grupo_evento->delete();
        return redirect("/grupoevento/dashboard/" . $id . "?tab=categoria");
    }

    public function criterio_desempate_add($id, Request $request)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobal() && !$user->hasPermissionGroupEventByPerfil($id,[7])) {
            return redirect("/grupoevento");
        }

        $criterio_desempate_grupo_evento = new CriterioDesempateGrupoEvento;
        $criterio_desempate_grupo_evento->grupo_evento_id = $id;
        $criterio_desempate_grupo_evento->criterio_desempate_id = $request->input("criterio_desempate_id");
        $criterio_desempate_grupo_evento->tipo_torneio_id = $request->input("tipo_torneio_id");
        $criterio_desempate_grupo_evento->softwares_id = $request->input("softwares_id");
        $criterio_desempate_grupo_evento->prioridade = $request->input("prioridade");
        $criterio_desempate_grupo_evento->save();
        return redirect("/grupoevento/dashboard/" . $id . "?tab=criterio_desempate");
    }
    public function criterio_desempate_remove($id, $cd_grupo_evento_id)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobal() && !$user->hasPermissionGroupEventByPerfil($id,[7])) {
            return redirect("/grupoevento");
        }

        $criterio_desempate_grupo_evento = CriterioDesempateGrupoEvento::find($cd_grupo_evento_id);
        $criterio_desempate_grupo_evento->delete();
        return redirect("/grupoevento/dashboard/" . $id . "?tab=criterio_desempate");
    }

    public function criterio_desempate_geral_add($id, Request $request)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobal() && !$user->hasPermissionGroupEventByPerfil($id,[7])) {
            return redirect("/grupoevento");
        }

        $criterio_desempate_grupo_evento_geral = new CriterioDesempateGrupoEventoGeral;
        $criterio_desempate_grupo_evento_geral->grupo_evento_id = $id;
        $criterio_desempate_grupo_evento_geral->criterio_desempate_id = $request->input("criterio_desempate_id");
        $criterio_desempate_grupo_evento_geral->prioridade = $request->input("prioridade");
        $criterio_desempate_grupo_evento_geral->save();
        return redirect("/grupoevento/dashboard/" . $id . "?tab=criterio_desempate_geral");
    }
    public function criterio_desempate_geral_remove($id, $cd_grupo_evento_geral_id)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobal() && !$user->hasPermissionGroupEventByPerfil($id,[7])) {
            return redirect("/grupoevento");
        }

        $criterio_desempate_grupo_evento_geral = CriterioDesempateGrupoEventoGeral::find($cd_grupo_evento_geral_id);
        $criterio_desempate_grupo_evento_geral->delete();
        return redirect("/grupoevento/dashboard/" . $id . "?tab=criterio_desempate_geral");
    }

    public function pontuacao_add($id, Request $request)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobal() && !$user->hasPermissionGroupEventByPerfil($id,[7])) {
            return redirect("/grupoevento");
        }

        $pontuacao = new Pontuacao;
        $pontuacao->grupo_evento_id = $id;
        $pontuacao->posicao = $request->input("posicao");
        $pontuacao->pontuacao = $request->input("pontuacao");
        $pontuacao->save();
        return redirect("/grupoevento/dashboard/" . $id . "?tab=pontuacao");
    }
    public function pontuacao_remove($id, $pontuacao_id)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobal() && !$user->hasPermissionGroupEventByPerfil($id,[7])) {
            return redirect("/grupoevento");
        }

        $pontuacao = Pontuacao::find($pontuacao_id);
        $pontuacao->delete();
        return redirect("/grupoevento/dashboard/" . $id . "?tab=pontuacao");
    }

    public function classificar($grupo_evento_id)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobal() && !$user->hasPermissionGroupEventByPerfil($id,[7])) {
            return redirect("/grupoevento");
        }

        $retornos = array();
        $grupo_evento = GrupoEvento::find($grupo_evento_id);
        if ($grupo_evento) {
            foreach ($grupo_evento->categorias->all() as $categoria) {
                $retornos = array_merge($retornos, CategoriaController::classificar_geral($grupo_evento->id, $categoria->categoria->id));
            }
            return view("grupoevento.retornos", compact("grupo_evento", "retornos"));
        }
    }

    public function classificar_page($grupo_evento_id)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobal() && !$user->hasPermissionGroupEventByPerfil($grupo_evento_id,[7])) {
            return redirect("/grupoevento");
        }

        $retornos = array();
        $grupo_evento = GrupoEvento::find($grupo_evento_id);
        return view("grupoevento.classificar", compact("grupo_evento"));
    }

    public function classificar_call($grupo_evento_id, $categoria_id, $action)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobal() && !$user->hasPermissionGroupEventByPerfil($grupo_evento_id,[7])) {
            return redirect("/grupoevento");
        }

        $retornos = array();
        $grupo_evento = GrupoEvento::find($grupo_evento_id);
        $categoria = Categoria::find($categoria_id);
        if ($grupo_evento && $categoria) {
            try {
                switch ($action) {
                    case 1:
                        CategoriaController::somar_pontos_geral($grupo_evento, $categoria);
                        break;
                    case 2:
                        CategoriaController::gerar_criterios_desempate($grupo_evento, $categoria);
                        break;
                    case 3:
                        CategoriaController::classificar_enxadristas_geral($grupo_evento, $categoria);
                }
                return response()->json(["ok" => 1, "error" => 0]);
            } catch (Exception $e) {
                return response()->json(["ok" => 0, "error" => 1]);
            }
        }
    }

    public function evento_new($id, Request $request)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobal() && !$user->hasPermissionGroupEventByPerfil($id,[7])) {
            return redirect("/grupoevento");
        }

        $grupo_evento = GrupoEvento::find($id);

        $datetime_data_inicio = DateTime::createFromFormat('d/m/Y', $request->input("data_inicio"));
        $datetime_data_fim = DateTime::createFromFormat('d/m/Y', $request->input("data_fim"));
        $datetime_data_limite_inscricoes_abertas = DateTime::createFromFormat('d/m/Y H:i', $request->input("data_limite_inscricoes_abertas"));

        // CADASTRO DO EVENTO
        $evento = new Evento;
        $evento->name = $request->input("name");
        $evento->data_inicio = $datetime_data_inicio->format('Y-m-d');
        $evento->data_fim = $datetime_data_fim->format('Y-m-d');
        $evento->local = $request->input("local");
        $evento->cidade_id = $request->input("cidade_id");
        if ($request->has("link")) {
            $evento->link = $request->input("link");
        }

        if ($request->has("data_limite_inscricoes_abertas") && $datetime_data_limite_inscricoes_abertas) {
            $evento->data_limite_inscricoes_abertas = $datetime_data_limite_inscricoes_abertas->format('Y-m-d H:i');
        }

        if ($request->has("usa_fide")) {
            $evento->usa_fide = true;
        }

        if ($request->has("usa_cbx")) {
            $evento->usa_cbx = true;
        }

        if ($request->has("usa_lbx")) {
            $evento->usa_lbx = true;
        }

        $evento->grupo_evento_id = $grupo_evento->id;
        $evento->save();

        // IMPORTAÇÃO DAS CATEGORIAS
        foreach ($grupo_evento->categorias->all() as $categoria) {
            $categoria_evento = new CategoriaEvento;
            $categoria_evento->categoria_id = $categoria->id;
            $categoria_evento->evento_id = $evento->id;
            $categoria_evento->save();
        }

        // IMPORTAÇÃO DOS TORNEIOS A PARTIR DOS TEMPLATES
        foreach ($grupo_evento->torneios_template->all() as $torneio_template) {
            $torneio = new Torneio;
            $torneio->name = ($torneio_template->torneio_name) ? $torneio_template->torneio_name : $torneio_template->name;
            $torneio->evento_id = $evento->id;
            $torneio->tipo_torneio_id = $torneio_template->tipo_torneio_id;
            $torneio->torneio_template_id = $torneio_template->id;
            $torneio->save();

            // IMPORTAÇÃO DAS CATEGORIAS PARA O TORNEIO A PARTIR DO TEMPLATE
            foreach ($torneio_template->categorias->all() as $categoria) {
                $categoria_torneio = new CategoriaTorneio;
                $categoria_torneio->categoria_id = $categoria->categoria->id;
                $categoria_torneio->torneio_id = $torneio->id;
                $categoria_torneio->save();
            }

            TorneioController::generateRodadasDefault($torneio->id);
        }



        // IMPORTAÇÃO DOS CRITÉRIOS DE DESEMPATE
        foreach ($grupo_evento->criterios->all() as $criterio_desempate_grupo_evento) {
            $criterio_desempate_evento = new CriterioDesempateEvento;
            $criterio_desempate_evento->evento_id = $evento->id;
            $criterio_desempate_evento->softwares_id = $criterio_desempate_grupo_evento->softwares_id;
            $criterio_desempate_evento->tipo_torneio_id = $criterio_desempate_grupo_evento->tipo_torneio_id;
            $criterio_desempate_evento->prioridade = $criterio_desempate_grupo_evento->prioridade;
            $criterio_desempate_evento->criterio_desempate_id = $criterio_desempate_grupo_evento->criterio_desempate_id;
            $criterio_desempate_evento->save();
        }

        return redirect("/grupoevento/dashboard/" . $grupo_evento->id . "?tab=evento");
    }

    public function evento_clone($id, $evento_id){

        $user = Auth::user();
        if (!$user->hasPermissionGlobal() && !$user->hasPermissionGroupEventByPerfil($id, [7])) {
            return redirect("/grupoevento");
        }

        $grupo_evento = GrupoEvento::find($id);

        if($grupo_evento->eventos()->where([["id","=",$evento_id]])->count() == 0){
            return redirect()->back();
        }

        $evento = $grupo_evento->eventos()->where([["id", "=", $evento_id]])->first();
        $novo_evento = $evento->replicate();
        $novo_evento->uuid = Str::uuid();
        $novo_evento->name .= " (CÓPIA)";

        $novo_evento->save();


        $categorias_ids = array();

        // IMPORTAÇÃO DAS CATEGORIAS
        foreach ($evento->categorias->all() as $categoria) {
            $categoria_evento = $categoria->replicate();
            $categoria_evento->evento_id = $novo_evento->id;
            $categoria_evento->save();
        }


        // IMPORTAÇÃO DOS TORNEIOS
        foreach ($evento->torneios->all() as $torneio) {
            $novo_torneio = $torneio->replicate();
            $novo_torneio->evento_id = $novo_evento->id;
            $novo_torneio->save();


            // IMPORTAÇÃO DAS CATEGORIAS DO TORNEIO
            foreach ($torneio->categorias->all() as $categoria) {
                $categoria_torneio = $categoria->replicate();
                $categoria_torneio->torneio_id = $torneio->id;
                $categoria_torneio->save();
            }
            // IMPORTAÇÃO DAS CONFIGURAÇÕES DO TORNEIO
            foreach ($torneio->configs->all() as $config) {
                $config_torneio = $config->replicate();
                $config_torneio->torneio_id = $torneio->id;
                $config_torneio->save();
            }
            TorneioController::generateRodadasDefault($novo_torneio->id);
        }
        // IMPORTAÇÃO DOS CRITÉRIOS DE DESEMPATE
        foreach ($evento->criterios->all() as $criterio_desempate) {
            $criterio_desempate_evento = $criterio_desempate->replicate();
            $criterio_desempate_evento->evento_id = $novo_evento->id;
            $criterio_desempate_evento->save();
        }

        if ($evento->tipo_rating_interno) {
            $novo_tipo_rating_interno = $evento->tipo_rating_interno->replicate();
            $novo_tipo_rating_interno->evento_id = $novo_evento->id;
            $novo_tipo_rating_interno->save();
        }
        if ($evento->pagina) {
            $nova_pagina = $evento->pagina->replicate();
            $nova_pagina->evento_id = $novo_evento->id;
            $nova_pagina->save();
        }
        // IMPORTAÇÃO DOS TEMPLATES DE E-MAIL
        foreach ($evento->email_templates->all() as $email_template) {
            $email_template_evento = $email_template->replicate();
            $email_template_evento->evento_id = $novo_evento->id;
            $email_template_evento->save();
        }
        // IMPORTAÇÃO DOS ITENS DE TIMELINE
        foreach ($evento->timeline_items->all() as $timeline_item) {
            $timeline_item_evento = $timeline_item->replicate();
            $timeline_item_evento->event_id = $novo_evento->id;
            $timeline_item_evento->save();
        }


        $messageBag = new MessageBag;
        $messageBag->add("type", "success");
        $messageBag->add("alerta", "Evento duplicado com sucesso!");
        return redirect("/evento/dashboard/" . $novo_evento->id)->withErrors($messageBag);
    }



    private function importEmailTemplates($grupo_evento_id){
        $grupo_evento = GrupoEvento::find($grupo_evento_id);
        $email_type = new EmailType;
        if($grupo_evento){
            foreach(EmailTemplate::whereNull("grupo_evento_id")->whereNull("evento_id")->get() as $template){
                if($email_type->get($template->email_type)["is_general"] == 0){
                    if($grupo_evento->email_templates()->where([
                        ["email_type","=",$template->email_type]
                    ])->count() == 0){
                        $email_template = new EmailTemplate;
                        $email_template->name = $template->name;
                        $email_template->subject = $template->subject;
                        $email_template->message = $template->message;
                        $email_template->email_type = $template->email_type;
                        $email_template->grupo_evento_id = $grupo_evento->id;
                        $email_template->save();
                    }
                }
            }
        }
    }

    public function visualizar_inscricoes($id)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobal() && !$user->hasPermissionGroupEventByPerfil($id,[6,7]) && !$user->hasPermissionEventByPerfilByGroupEvent($id,[3,4,5])) {
            return redirect("/grupoevento");
        }

        $grupo_evento = GrupoEvento::find($id);

        return view('grupoevento.inscricoes', compact("grupo_evento"));
    }


    public function visualizar_premiados($id)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobal() && !$user->hasPermissionGroupEventByPerfil($id,[6,7]) && !$user->hasPermissionEventByPerfilByGroupEvent($id,[3,4,5])) {
            return redirect("/grupoevento");
        }
        $grupo_evento = GrupoEvento::find($id);
        if ($grupo_evento) {
            return view("grupoevento.premiados", compact("grupo_evento"));
        }
        return redirect("/grupoevento/dashboard/" . $id);
    }
}

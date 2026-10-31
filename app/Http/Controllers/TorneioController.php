<?php

namespace App\Http\Controllers;

use App\Categoria;
use App\CategoriaTorneio;
use App\CriterioDesempate;
use App\Enxadrista;
use App\Evento;
use App\Inscricao;
use App\InscricaoCriterioDesempate;
use App\MovimentacaoRating;
use App\Rating;
use App\TipoTorneio;
use App\Torneio;
use App\Rodada;
use App\Emparceiramento;
use App\Software;



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;

use App\Enum\EmailType;
use App\Enum\ConfigType;

use App\Http\Controllers\Integration\Online\ChessComIntegrationController;

use App\Http\Controllers\LichessIntegrationController;
use App\Http\Controllers\CriterioDesempateController;

use App\Imports\ChessComResultsImport;
use App\PerfilUser;
use App\Services\Integration\ChessComApiErrorPreviewService;
use App\Services\Integration\ChessComResultsCsvValidator;
use App\Services\Integration\ChessComTournamentJsonValidator;
use Illuminate\Support\Facades\DB;
use Log;
use Storage;
use Excel;

class TorneioController extends Controller
{
    public function __construct()
    {
        return $this->middleware("auth");
    }

    public function index($id)
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

        $torneios = $evento->torneios->all();
        return view("evento.torneio.index", compact("evento", "torneios"));
    }

    function new ($id) {
        $evento = Evento::find($id);
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/" . $evento->id);
        }

        $tipos_torneio = TipoTorneio::all();
        $softwares = Software::all();
        return view('evento.torneio.new', compact("evento", "tipos_torneio","softwares"));
    }
    public function new_post($id, Request $request)
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

        $torneio = new Torneio;
        $torneio->name = $request->input("name");
        $torneio->tipo_torneio_id = $request->input("tipo_torneio_id");
        $torneio->softwares_id = $request->input("softwares_id");
        $torneio->evento_id = $evento->id;
        $torneio->save();

        $torneio->checkIfIsChessCom();

        return redirect("/evento/" . $evento->id . "/torneios/edit/" . $torneio->id);
    }
    public function edit($id, $torneio_id)
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

        $categorias_id = $evento->categorias()->pluck("categoria_id")->toArray();

        $torneio = Torneio::find($torneio_id);
        $tipos_torneio = TipoTorneio::all();
        $softwares = Software::all();
        $categorias = Categoria::whereIn("id", $categorias_id)->get();
        return view('evento.torneio.edit', compact("torneio", "tipos_torneio", "categorias","softwares"));
    }
    public function edit_post($id, $torneio_id, Request $request)
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

        $torneio = Torneio::find($torneio_id);
        $torneio->name = $request->input("name");
        $torneio->tipo_torneio_id = $request->input("tipo_torneio_id");
        $torneio->softwares_id = $request->input("softwares_id");

        $torneio->save();

        $torneio = Torneio::find($torneio_id);

        if($torneio->software->isChessCom()){
            if($request->has("chesscom_tournament_slug")){
                if($request->input("chesscom_tournament_slug") != ""){
                    $torneio->setConfig("chesscom_tournament_slug",ConfigType::String,$request->input("chesscom_tournament_slug"));

                    foreach($torneio->inscricoes->all() as $inscricao){
                        $inscricao->needToReplicateInfo();
                    }
                }else{
                    $torneio->removeConfig("chesscom_tournament_slug");
                }
            }else{
                $torneio->removeConfig("chesscom_tournament_slug");
            }
        }else{
            $torneio->removeConfig("chesscom_tournament_slug");
        }

        $torneio->checkIfIsChessCom();

        return redirect("/evento/" . $evento->id . "/torneios/edit/" . $torneio->id);
    }
    public function delete($id, $torneio_id)
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

        $torneio = Torneio::find($torneio_id);

        if ($torneio->isDeletavel()) {
            $torneio->delete();
        }
        return redirect("/evento/dashboard/" . $evento->id . "/?tab=torneio");
    }
    public function union($id, $torneio_id)
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

        $torneio = Torneio::find($torneio_id);
        $torneios = $torneio->evento->torneios()->where([["id", "!=", $torneio->id]])->get();
        return view('evento.torneio.union', compact("torneio", "torneios", "evento"));
    }
    public function union_post($id, $torneio_id, Request $request)
    {
        if (!$request->has("torneio_a_ser_unido")) {
            return redirect()->back();
        } elseif ($request->input("torneio_a_ser_unido") == "") {
            return redirect()->back();
        }

        $evento = Evento::find($id);
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/" . $id);
        }

        $torneio_base = Torneio::find($torneio_id);
        $torneio_a_ser_unido = Torneio::find($request->input("torneio_a_ser_unido"));

        if ($torneio_base && $torneio_a_ser_unido) {
            if ($torneio_base->evento->id == $torneio_a_ser_unido->evento->id) {
                foreach ($torneio_a_ser_unido->inscricoes->all() as $inscricao) {
                    $inscricao->torneio_id = $torneio_base->id;
                    $inscricao->save();
                }
                foreach ($torneio_a_ser_unido->categorias->all() as $categoria) {
                    $categoria_torneio = new CategoriaTorneio;
                    $categoria_torneio->torneio_id = $torneio_base->id;
                    $categoria_torneio->categoria_id = $categoria->categoria->id;
                    $categoria_torneio->save();

                    $categoria->delete();
                }

                $torneio_base->torneio_template_id = null;
                $torneio_base->save();

                $torneio_a_ser_unido->delete();

                return redirect("/evento/dashboard/" . $evento->id . "/?tab=torneio");
            }
        }

        return redirect()->back();
    }
    public function migrate_to_new_event($id, $torneio_id)
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

        $torneio = Torneio::find($torneio_id);
        return view('evento.torneio.migrate_to_new_event', compact("torneio", "evento"));
    }
    public function migrate_to_new_event__execute($id, $torneio_id, Request $request)
    {
        $evento = Evento::find($id);
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/" . $id);
        }

        $torneio = Torneio::find($torneio_id);


        $new_event = $evento->replicate();
        $new_event->uuid = null;
        $new_event->name .= " - " . $torneio->name;
        $new_event->save();

        if ($torneio) {
            $torneio->evento_id = $new_event->id;
            $torneio->save();

            foreach($torneio->categorias->all() as $categoria_torneio){
                if($categoria_torneio->categoria->evento){
                    $categoria_torneio->categoria->evento_id = $new_event->id;
                    $categoria_torneio->categoria->save();
                }
                $evento_categoria = $evento->categorias()->where([["categoria_id", "=",$categoria_torneio->categoria_id]])->first();
                $evento_categoria->evento_id = $new_event->id;
                $evento_categoria->save();
            }

            foreach ($evento->criterios->all() as $criterio_desempate_evento) {
                $new_criterio_desempate_evento = $criterio_desempate_evento->replicate();
                $new_criterio_desempate_evento->evento_id = $new_event->id;
                $new_criterio_desempate_evento->save();
            }
            foreach ($evento->campos_adicionais->all() as $campo_adicional_evento) {
                $new_campo_adicional_evento = $campo_adicional_evento->replicate();
                $new_campo_adicional_evento->evento_id = $new_event->id;
                $new_campo_adicional_evento->save();

                // @todo
            }
            foreach ($evento->email_templates->all() as $email_template) {
                $new_email_template = $email_template->replicate();
                $new_email_template->evento_id = $new_event->id;
                $new_email_template->save();
            }
            foreach ($evento->timeline_items->all() as $timeline_item) {
                $new_timeline_item = $timeline_item->replicate();
                $new_timeline_item->event_id = $new_event->id;
                $new_timeline_item->save();
            }
            foreach ($evento->configs->all() as $config) {
                $new_config = $config->replicate();
                $new_config->evento_id = $new_event->id;
                $new_config->save();
            }
            foreach ($evento->event_team_awards->all() as $event_team_award) {
                // @todo
            }

            foreach(PerfilUser::where([["evento_id","=",$evento->id]])->get() as $perfil_user){
                $new_perfil_user = $perfil_user->replicate();
                $new_perfil_user->evento_id = $new_event->id;
                $new_perfil_user->save();
            }

            return redirect("/evento/dashboard/".$new_event->id);
        }

        return redirect()->back();
    }
    public function categoria_add($id, $torneio_id, Request $request)
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

        $torneio = Torneio::find($torneio_id);
        $categoria_torneio = new CategoriaTorneio;
        $categoria_torneio->torneio_id = $torneio_id;
        $categoria_torneio->categoria_id = $request->input("categoria_id");
        $categoria_torneio->save();

        $torneio->torneio_template_id = null;
        $torneio->save();

        return redirect("/evento/" . $evento->id . "/torneios/edit/" . $torneio->id);
    }
    public function categoria_remove($id, $torneio_id, $categoria_torneio_id)
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

        $torneio = Torneio::find($torneio_id);
        $categoria_torneio = CategoriaTorneio::find($categoria_torneio_id);
        $categoria_torneio->delete();

        $torneio->torneio_template_id = null;
        $torneio->save();

        return redirect("/evento/" . $evento->id . "/torneios/edit/" . $torneio->id);
    }


    public function categoria_transfer($id, $torneio_id, $categoria_id)
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

        $torneio = Torneio::find($torneio_id);
        $categoria = $torneio->categorias()->where([["id","=",$categoria_id]])->first();
        return view('evento.torneio.categoria.transfer', compact("torneio", "categoria", "evento"));
    }
    public function categoria_transfer_post($id, $torneio_id, $categoria_id, Request $request)
    {
        if (!$request->has("tournament_id")) {
            return redirect()->back();
        } elseif ($request->input("tournament_id") == "") {
            return redirect()->back();
        }

        $evento = Evento::find($id);
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/" . $id);
        }

        $torneio = Torneio::find($torneio_id);
        $categoria = $torneio->categorias()->where([["id","=",$categoria_id]])->first();

        $outro_torneio = Torneio::find($request->input("tournament_id"));

        if ($torneio && $outro_torneio && $categoria) {
            if ($torneio->evento->id == $outro_torneio->evento->id) {
                foreach ($torneio->inscricoes()->where([["categoria_id","=",$categoria->categoria->id]])->get() as $inscricao) {
                    $inscricao->torneio_id = $outro_torneio->id;
                    $inscricao->save();
                }

                $torneio->torneio_template_id = null;
                $torneio->save();

                $outro_torneio->torneio_template_id = null;
                $outro_torneio->save();

                $categoria->torneio_id = $outro_torneio->id;
                $categoria->save();

                $messageBag = new MessageBag;
                $messageBag->add("type","success");
                $messageBag->add("alerta","A categoria foi transferida com sucesso do Torneio #".$torneio->id." (".$torneio->name.") para o Torneio #".$outro_torneio->id." (".$outro_torneio->name.").");

                return redirect("/evento/" . $evento->id . "/torneios/edit/".$torneio->id)->withErrors($messageBag);
            }
        }

        $messageBag = new MessageBag;
        $messageBag->add("type","danger");
        $messageBag->add("alerta","Houve um erro na transferência da categoria. Por favor, verifique e tente novamente.");

        return redirect()->back()->withErrors($messageBag);
    }



    /*
     *
     * LICHESS INTEGRATION
     *
     */
    public function check_players_in($id, $torneio_id)
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

        $torneio = Torneio::find($torneio_id);
        if($torneio->evento->is_lichess_integration){
            if($torneio->evento->lichess_tournament_id){
                $players_not_found = array();

                $lichess_integration_controller = new LichessIntegrationController;
                $retorno = $lichess_integration_controller->getSwissResults($torneio->evento->lichess_tournament_id);
                if($retorno["ok"] == 1){
                    $torneio->setAllInscricoesNotFound();
                    foreach(explode("\n",$retorno["data"]) as $lichess_player_raw){
                        $lichess_player = json_decode(trim($lichess_player_raw));
                        if($lichess_player){
                            $inscricao_count = $torneio->inscricoes()->whereHas("enxadrista",function($q1) use ($lichess_player){
                                $q1->where([
                                    ["lichess_username","=",mb_strtolower($lichess_player->username)]
                                ]);
                            })->count();
                            if($inscricao_count > 0){
                                $inscricao = $torneio->inscricoes()->whereHas("enxadrista",function($q1) use ($lichess_player){
                                    $q1->where([
                                        ["lichess_username","=",mb_strtolower($lichess_player->username)]
                                    ]);
                                })->first();

                                if(!$inscricao->is_lichess_found && (!$inscricao->is_last_lichess_found)){
                                    // EMAIL PARA O ENXADRISTA SOLICITANTE
                                    EmailController::schedule(
                                        $inscricao->enxadrista->email,
                                        $inscricao,
                                        EmailType::ConfirmacaoInscricaoLichess,
                                        $inscricao->enxadrista
                                    );
                                }

                                $inscricao->lichess_rating = $lichess_player->rating;
                                $inscricao->start_position = $lichess_player->rank;
                                $inscricao->is_lichess_found = true;
                                $inscricao->is_last_lichess_found = true;
                                $inscricao->save();
                            }else{
                                $inscricao->is_lichess_found = false;
                                $inscricao->save();
                                $players_not_found[] = $lichess_player->username;
                            }
                        }
                    }
                }
                $torneio->lichess_last_update = date("Y-m-d H:i:s");
                $torneio->save();

                activity()
                    ->performedOn($torneio)
                    ->causedBy(Auth::user())
                    ->log("Lichess.org - Não encontrados como inscritos: ".json_encode($players_not_found));


                $players_not_found = array();
                $retorno_team = $lichess_integration_controller->getTeamMembers($torneio->evento->lichess_team_id);
                if($retorno_team["ok"] == 1){
                    $token_user = $lichess_integration_controller->getUserData(env("LICHESS_TOKEN",""));
                    foreach(explode("\n",$retorno_team["data"]) as $lichess_player_raw){
                        $lichess_player = json_decode(trim($lichess_player_raw));
                        if($lichess_player){
                            $inscricao_count = $torneio->inscricoes()->whereHas("enxadrista",function($q1) use ($lichess_player){
                                $q1->where([
                                    ["lichess_username","=",mb_strtolower($lichess_player->username)]
                                ]);
                            })->count();
                            if($inscricao_count > 0){
                                $inscricao = $torneio->inscricoes()->whereHas("enxadrista",function($q1) use ($lichess_player){
                                    $q1->where([
                                        ["lichess_username","=",mb_strtolower($lichess_player->username)]
                                    ]);
                                })->first();


                                $inscricao->is_lichess_team_found = true;
                                $inscricao->save();
                            }else{
                                if($token_user["data"]->username != $lichess_player->username){
                                    $players_not_found[] = $lichess_player->username;
                                }
                            }
                        }
                    }
                }

                activity()
                    ->performedOn($torneio)
                    ->causedBy(Auth::user())
                    ->log("Lichess.org - Usuários Não Encontrados no Time: ".json_encode($players_not_found));

            }
        }

        return redirect("/evento/dashboard/" . $evento->id . "/?tab=torneio");
    }


    public function lichess_get_results($id, $torneio_id)
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

        $torneio = Torneio::find($torneio_id);
        if($torneio->evento->is_lichess_integration){
            if($torneio->evento->lichess_tournament_id){
                $criterio_lichess_tiebreak = CriterioDesempate::where([["is_lichess","=",true]])->first();

                $lichess_integration_controller = new LichessIntegrationController;
                $retorno = $lichess_integration_controller->getSwissResults($torneio->evento->lichess_tournament_id);
                if($retorno["ok"] == 1){
                $torneio->setAllInscricoesNotFound();
                    $k = 1;
                    foreach(explode("\n",$retorno["data"]) as $lichess_player_raw){
                        $lichess_player = json_decode(trim($lichess_player_raw));
                        if($lichess_player){
                            Log::debug("Import Username ".$k++.": ".$lichess_player->username);
                            $inscricao_count = $torneio->inscricoes()->whereHas("enxadrista",function($q1) use ($lichess_player){
                                $q1->where([
                                    ["lichess_username","=",trim(mb_strtolower($lichess_player->username))]
                                ]);
                            })->count();
                            if($inscricao_count > 0){
                                $inscricao = $torneio->inscricoes()->whereHas("enxadrista",function($q1) use ($lichess_player){
                                    $q1->where([
                                        ["lichess_username","=",trim(mb_strtolower($lichess_player->username))]
                                    ]);
                                })->first();

                                $inscricao->pontos = $lichess_player->points;

                                $inscricao->is_lichess_found = true;
                                $inscricao->confirmado = true;
                                $inscricao->save();

                                $inscricao_criterio = $inscricao->criterios_desempate()->where([
                                    ["criterio_desempate_id","=",$criterio_lichess_tiebreak->id]
                                ])->first();
                                if(!$inscricao_criterio){
                                    $inscricao_criterio = new InscricaoCriterioDesempate;
                                    $inscricao_criterio->inscricao_id = $inscricao->id;
                                    $inscricao_criterio->criterio_desempate_id = $criterio_lichess_tiebreak->id;
                                }
                                $inscricao_criterio->valor = $lichess_player->tieBreak;
                                $inscricao_criterio->save();

                            }
                        }
                    }
                }

                $torneio->lichess_last_update = date("Y-m-d H:i:s");
                $torneio->save();

                activity()
                    ->performedOn($torneio)
                    ->causedBy(Auth::user())
                    ->log("Resultados importados para o torneio.");

            }
        }

        return redirect("/evento/dashboard/" . $evento->id . "/?tab=torneio");
    }

    public function remove_lichess_players_not_found($id, $torneio_id)
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

        $torneio = Torneio::find($torneio_id);
        if($torneio->evento->is_lichess_integration){
            if($torneio->evento->lichess_tournament_id){
                $players_not_found = array();

                $lichess_integration_controller = new LichessIntegrationController;

                $token_user = $lichess_integration_controller->getUserData(env("LICHESS_TOKEN",""));

                if($token_user){
                    if($token_user["ok"]){
                        $retorno = $lichess_integration_controller->getTeamMembers($torneio->evento->lichess_team_id);
                        if($retorno["ok"] == 1){
                            foreach(explode("\n",$retorno["data"]) as $lichess_player_raw){
                                $lichess_player = json_decode(trim($lichess_player_raw));
                                if($lichess_player){
                                    $inscricao_count = $torneio->inscricoes()->whereHas("enxadrista",function($q1) use ($lichess_player){
                                        $q1->where([
                                            ["lichess_username","=",mb_strtolower($lichess_player->username)]
                                        ]);
                                    })->count();
                                    if($inscricao_count == 0){
                                        if($token_user["data"]->username != $lichess_player->username){
                                            $players_not_found[] = $lichess_player->username;
                                            $lichess_integration_controller->removeMemberFromTeam($torneio->evento->lichess_team_id, $lichess_player->username);
                                        }
                                    }
                                }
                            }
                        }
                        $torneio->save();

                        activity()
                            ->performedOn($torneio)
                            ->causedBy(Auth::user())
                            ->log("Lichess.org - Usuários Removidos do Time: ".json_encode($players_not_found));

                    }
                }
            }
        }

        return redirect("/evento/dashboard/" . $evento->id . "/?tab=torneio");
    }
    public function remove_lichess_players_not_found_on_team($id, $torneio_id)
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

        $torneio = Torneio::find($torneio_id);
        if($torneio->evento->is_lichess_integration){
            if($torneio->evento->lichess_tournament_id){
                $players_not_found = array();

                $lichess_integration_controller = new LichessIntegrationController;

                $token_user = $lichess_integration_controller->getUserData(env("LICHESS_TOKEN",""));

                if($token_user){
                    if($token_user["ok"]){
                        $retorno = $lichess_integration_controller->getTeamMembers($torneio->evento->lichess_team_id);
                        if($retorno["ok"] == 1){
                            foreach(explode("\n",$retorno["data"]) as $lichess_player_raw){
                                $lichess_player = json_decode(trim($lichess_player_raw));
                                if($lichess_player){
                                    $inscricao_count = $torneio->inscricoes()->whereHas("enxadrista",function($q1) use ($lichess_player){
                                        $q1->where([
                                            ["lichess_username","=",mb_strtolower($lichess_player->username)]
                                        ]);
                                    })->count();
                                    if($inscricao_count == 0){
                                        if($token_user["data"]->username != $lichess_player->username){
                                            $players_not_found[] = $lichess_player->username;
                                            $lichess_integration_controller->removeMemberFromTeam($torneio->evento->lichess_team_id, $lichess_player->username);
                                        }
                                    }
                                }
                            }
                        }
                        $torneio->save();

                        activity()
                            ->performedOn($torneio)
                            ->causedBy(Auth::user())
                            ->log("Lichess.org - Usuários Removidos do Time: ".json_encode($players_not_found));

                    }
                }
            }
        }

        return redirect("/evento/dashboard/" . $evento->id . "/?tab=torneio");
    }



    /*
     *
     * CHESS.COM INTEGRATION
     *
     */
    private function chesscomRedirectDashboard(Evento $evento, string $type, string $alerta)
    {
        $messageBag = new MessageBag;
        $messageBag->add("type", $type);
        $messageBag->add("alerta", $alerta);

        return redirect("/evento/dashboard/" . $evento->id . "/?tab=torneio")->withErrors($messageBag);
    }

    /**
     * @param array<string, string|int> $replace
     */
    private function chesscomTrans(string $key, array $replace = []): string
    {
        $fullKey = "chesscom." . $key;
        $locales = array_unique([
            config("app.locale"),
            "pt-br",
            "pt-BR",
            config("app.fallback_locale"),
            "en",
        ]);

        foreach ($locales as $locale) {
            if ($locale === null || $locale === "") {
                continue;
            }
            $translated = trans($fullKey, $replace, $locale);
            if ($translated !== $fullKey) {
                return $translated;
            }
        }

        return $fullKey;
    }

    private function chesscomLastErrorUrl(Evento $evento, Torneio $torneio): string
    {
        return url("/evento/" . $evento->id . "/torneios/" . $torneio->id . "/chesscom/last_error");
    }

    /**
     * @param array<string, mixed> $retorno
     */
    private function chesscomHandleApiError(Evento $evento, Torneio $torneio, $user, string $action, array $retorno)
    {
        $torneio->chesscom_saveLastApiError($action, $retorno, $user->id);

        $messageKey = isset($retorno["message_key"]) ? (string) $retorno["message_key"] : "tournament_not_found";
        $message = $this->chesscomTrans($messageKey);

        if ($user->hasPermissionGlobal() && isset($retorno["status_code"], $retorno["request_url"], $retorno["slug"])) {
            $responseBody = isset($retorno["response_body"]) ? (string) $retorno["response_body"] : "";
            $message .= " " . $this->chesscomTrans("admin_details.suffix", [
                "code" => $retorno["status_code"],
                "slug" => $retorno["slug"],
                "url" => $retorno["request_url"],
                "body" => mb_substr($responseBody !== "" ? $responseBody : "-", 0, 300),
            ]);
        }

        $flashMessage = $this->chesscomTrans("last_error.flash_with_link", [
            "message" => $message,
            "url" => $this->chesscomLastErrorUrl($evento, $torneio),
        ]);

        return $this->chesscomRedirectDashboard($evento, "danger", $flashMessage);
    }

    public function chesscom__check_players_in($id, $torneio_id)
    {
        $resolved = $this->chesscomAuthorizedEventAndTorneio($id, $torneio_id);
        if (!is_array($resolved)) {
            return $resolved;
        }
        list($evento, $torneio) = $resolved;
        $user = Auth::user();

        if (!$torneio->hasConfig("chesscom_tournament_slug")) {
            return $this->chesscomRedirectDashboard($evento, "danger", $this->chesscomTrans("slug_not_configured"));
        }

        $chesscom_integration_controller = new ChessComIntegrationController;
        $retorno = $chesscom_integration_controller->getTournament($torneio->getConfig("chesscom_tournament_slug", true));
        if ($retorno["ok"] != 1) {
            try {
                $torneio->chesscom_saveLastApiError("check_players_in", $retorno, $user->id);
            } catch (\Throwable $exception) {
                Log::error("Chess.com: falha ao registrar erro de consulta de inscrições", ["exception" => $exception]);
            }
            return redirect($this->chesscomPlayersUploadUrl($evento, $torneio));
        }
        return $this->chesscomCheckPlayers($evento, $torneio, $retorno["data"]);
    }

    private function chesscomPlayersUploadUrl(Evento $evento, Torneio $torneio): string
    {
        return url("/evento/" . $evento->id . "/torneios/" . $torneio->id . "/chesscom/players/file");
    }

    public function chesscom__players_file($id, $torneio_id)
    {
        $resolved = $this->chesscomAuthorizedEventAndTorneio($id, $torneio_id);
        if (!is_array($resolved)) {
            return $resolved;
        }
        list($evento, $torneio) = $resolved;
        if (!$torneio->hasConfig("chesscom_tournament_slug")) {
            return $this->chesscomRedirectDashboard($evento, "danger", $this->chesscomTrans("slug_not_configured"));
        }
        $downloadUrl = (new ChessComIntegrationController)->tournamentDownloadUrl($torneio->getConfig("chesscom_tournament_slug", true));
        $uploadUrl = $this->chesscomPlayersUploadUrl($evento, $torneio);
        return view("evento.torneio.chesscom_players_upload", compact("evento", "torneio", "downloadUrl", "uploadUrl"));
    }

    public function chesscom__upload_players(Request $request, $id, $torneio_id)
    {
        $resolved = $this->chesscomAuthorizedEventAndTorneio($id, $torneio_id);
        if (!is_array($resolved)) {
            return $resolved;
        }
        list($evento, $torneio) = $resolved;
        if (!$torneio->hasConfig("chesscom_tournament_slug")) {
            return $this->chesscomRedirectDashboard($evento, "danger", $this->chesscomTrans("slug_not_configured"));
        }
        $validator = \Validator::make($request->all(), ["arquivo" => "required|file|max:10240"]);
        if ($validator->fails()) {
            return redirect($this->chesscomPlayersUploadUrl($evento, $torneio))->withErrors($validator);
        }
        $json = file_get_contents($request->file("arquivo")->getRealPath());
        if ($json === false) {
            return redirect($this->chesscomPlayersUploadUrl($evento, $torneio))->withErrors(["arquivo" => "Não foi possível ler o arquivo enviado. Tente novamente."]);
        }
        return $this->chesscomCheckPlayers($evento, $torneio, $json);
    }

    private function chesscomCheckPlayers(Evento $evento, Torneio $torneio, string $json)
    {
        try {
            $chesscom_tournament_data = (new ChessComTournamentJsonValidator)->parse($json, $torneio->getConfig("chesscom_tournament_slug", true));
        } catch (\InvalidArgumentException $exception) {
            return redirect($this->chesscomPlayersUploadUrl($evento, $torneio))->withErrors(["arquivo" => $exception->getMessage()]);
        }
        if ($chesscom_tournament_data->status != "finished") {
            return $this->chesscomRedirectDashboard($evento, "warning", $this->chesscomTrans("tournament_not_finished"));
        }
        $players_not_found = [];
        try {
            DB::transaction(function () use ($torneio, $chesscom_tournament_data, &$players_not_found) {
                $torneio->chesscom_setAllRegistrationsNotFound();
                foreach ($chesscom_tournament_data->players as $chesscom_tournament_player) {
                    if ($chesscom_tournament_player->username) {
                        $inscricao_count = $torneio->inscricoes()->whereHas("configs", function ($q1) use ($chesscom_tournament_player) {
                            $q1->where([
                                ["key", "=", "chesscom_username"],
                                ["value_type", "=", ConfigType::String],
                                ["string", "=", mb_strtolower($chesscom_tournament_player->username)],
                            ]);
                        })->count();
                        if ($inscricao_count > 0) {
                            $inscricao = $torneio->inscricoes()->whereHas("configs", function ($q1) use ($chesscom_tournament_player) {
                                $q1->where([
                                    ["key", "=", "chesscom_username"],
                                    ["value_type", "=", ConfigType::String],
                                    ["string", "=", mb_strtolower($chesscom_tournament_player->username)],
                                ]);
                            })->first();

                            $inscricao->setConfig("chesscom_registration_found", ConfigType::Boolean, true);
                        } else {
                            $players_not_found[] = $chesscom_tournament_player->username;
                        }
                    }
                }

                $torneio->setConfig("chesscom_last_players_list_update", ConfigType::Integer, time());
                $torneio->save();

                activity()
                    ->performedOn($torneio)
                    ->causedBy(Auth::user())
                    ->log("Chess.com - Não encontrados como inscritos: " . json_encode($players_not_found));

            });
        } catch (\Throwable $exception) {
            Log::error("Chess.com: falha ao conferir inscrições do torneio " . $torneio->id, ["exception" => $exception]);
            return redirect($this->chesscomPlayersUploadUrl($evento, $torneio))->withErrors(["arquivo" => "Não foi possível conferir as inscrições. Tente novamente."]);
        }
        $torneio->chesscom_clearLastApiError();

        if (count($players_not_found) > 0) {
            return $this->chesscomRedirectDashboard(
                $evento,
                "warning",
                $this->chesscomTrans("check_players_in.success_with_not_found", ["users" => implode(", ", $players_not_found)])
            );
        }

        return $this->chesscomRedirectDashboard($evento, "success", $this->chesscomTrans("check_players_in.success"));
    }


    public function chesscom__get_results($id, $torneio_id)
    {
        $resolved = $this->chesscomAuthorizedEventAndTorneio($id, $torneio_id);
        if (!is_array($resolved)) {
            return $resolved;
        }
        list($evento, $torneio) = $resolved;
        $user = Auth::user();

        if (!$torneio->hasConfig("chesscom_tournament_slug")) {
            return $this->chesscomRedirectDashboard($evento, "danger", $this->chesscomTrans("slug_not_configured"));
        }

        $chesscom_integration_controller = new ChessComIntegrationController;
        $retorno = $chesscom_integration_controller->getResults($torneio->getConfig("chesscom_tournament_slug", true));
        if ($retorno["ok"] == 1) {
            try {
                (new ChessComResultsCsvValidator)->validate($retorno["data"]);
            } catch (\InvalidArgumentException $exception) {
                $retorno = [
                    "ok" => 0,
                    "message_key" => "invalid_results_csv",
                    "status_code" => 200,
                    "slug" => $torneio->getConfig("chesscom_tournament_slug", true),
                    "request_url" => $chesscom_integration_controller->resultsDownloadUrl($torneio->getConfig("chesscom_tournament_slug", true)),
                    "response_body" => mb_substr($retorno["data"], 0, 100000),
                ];
            }
        }
        if ($retorno["ok"] != 1) {
            try {
                $torneio->chesscom_saveLastApiError("get_results", $retorno, $user->id);
            } catch (\Throwable $exception) {
                Log::error("Chess.com: falha ao registrar erro de download", ["exception" => $exception]);
            }
            return redirect($this->chesscomUploadUrl($evento, $torneio));
        }

        return $this->chesscomImportResults($evento, $torneio, $retorno["data"]);
    }

    private function chesscomUploadUrl(Evento $evento, Torneio $torneio): string
    {
        return url("/evento/" . $evento->id . "/torneios/" . $torneio->id . "/chesscom/results/file");
    }

    public function chesscom__results_file($id, $torneio_id)
    {
        $resolved = $this->chesscomAuthorizedEventAndTorneio($id, $torneio_id);
        if (!is_array($resolved)) {
            return $resolved;
        }
        list($evento, $torneio) = $resolved;
        if (!$torneio->hasConfig("chesscom_tournament_slug")) {
            return $this->chesscomRedirectDashboard($evento, "danger", $this->chesscomTrans("slug_not_configured"));
        }
        $downloadUrl = (new ChessComIntegrationController)->resultsDownloadUrl($torneio->getConfig("chesscom_tournament_slug", true));
        $uploadUrl = $this->chesscomUploadUrl($evento, $torneio);
        return view("evento.torneio.chesscom_results_upload", compact("evento", "torneio", "downloadUrl", "uploadUrl"));
    }

    public function chesscom__upload_results(Request $request, $id, $torneio_id)
    {
        $resolved = $this->chesscomAuthorizedEventAndTorneio($id, $torneio_id);
        if (!is_array($resolved)) {
            return $resolved;
        }
        list($evento, $torneio) = $resolved;
        if (!$torneio->hasConfig("chesscom_tournament_slug")) {
            return $this->chesscomRedirectDashboard($evento, "danger", $this->chesscomTrans("slug_not_configured"));
        }
        $validator = \Validator::make($request->all(), ["arquivo" => "required|file|max:10240"]);
        if ($validator->fails()) {
            return redirect($this->chesscomUploadUrl($evento, $torneio))->withErrors($validator);
        }
        $csv = file_get_contents($request->file("arquivo")->getRealPath());
        if ($csv === false) {
            return redirect($this->chesscomUploadUrl($evento, $torneio))->withErrors(["arquivo" => "Não foi possível ler o arquivo enviado. Tente novamente."]);
        }
        try {
            (new ChessComResultsCsvValidator)->validate($csv);
        } catch (\InvalidArgumentException $exception) {
            return redirect($this->chesscomUploadUrl($evento, $torneio))->withErrors(["arquivo" => $exception->getMessage()]);
        }
        return $this->chesscomImportResults($evento, $torneio, $csv);
    }

    private function chesscomImportResults(Evento $evento, Torneio $torneio, string $csv)
    {
        $filename = "chesscom_results_csv_" . $torneio->id . "_" . uniqid() . ".csv";
        try {
            if (!Storage::disk("imports_chess_com")->put($filename, $csv)) {
                throw new \RuntimeException("Não foi possível armazenar o arquivo de resultados.");
            }
            DB::transaction(function () use ($evento, $torneio, $filename) {
                Excel::import(
                    new ChessComResultsImport($evento->id, $torneio->id),
                    Storage::disk("imports_chess_com")->path($filename),
                    null,
                    \Maatwebsite\Excel\Excel::CSV
                );
                $torneio->setConfig("chesscom_last_players_list_update", ConfigType::Integer, time());
                $torneio->setConfig("chesscom_last_result_import_update", ConfigType::Integer, time());
                $torneio->save();
            });
        } catch (\Throwable $exception) {
            Log::error("Chess.com: falha ao importar resultados do torneio " . $torneio->id, ["exception" => $exception]);
            return redirect($this->chesscomUploadUrl($evento, $torneio))->withErrors(["arquivo" => "Não foi possível processar o CSV. Confira o arquivo de resultados e tente novamente."]);
        }
        $torneio->chesscom_clearLastApiError();
        return $this->chesscomRedirectDashboard($evento, "success", $this->chesscomTrans("get_results.success"));
    }

    private function chesscomAuthorizedEventAndTorneio($id, $torneio_id)
    {
        $evento = Evento::findOrFail($id);
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/" . $evento->id);
        }

        $torneio = Torneio::find($torneio_id);
        if (!$torneio || (int) $torneio->evento_id !== (int) $evento->id || !$torneio->software || !$torneio->software->isChessCom()) {
            return redirect("/evento/dashboard/" . $evento->id . "/?tab=torneio");
        }

        return [$evento, $torneio];
    }

    public function chesscom__last_error($id, $torneio_id)
    {
        $resolved = $this->chesscomAuthorizedEventAndTorneio($id, $torneio_id);
        if ($resolved instanceof \Illuminate\Http\RedirectResponse) {
            return $resolved;
        }

        /** @var Evento $evento */
        /** @var Torneio $torneio */
        list($evento, $torneio) = $resolved;

        $error = $torneio->chesscom_getLastApiError();
        if ($error === null) {
            return $this->chesscomRedirectDashboard($evento, "warning", $this->chesscomTrans("last_error.none_saved"));
        }

        $torneio->chesscom_ensureLastApiErrorPreview();

        $previewImageUrl = null;
        $previewHtmlUrl = null;
        $jsonPath = $torneio->chesscom_getLastApiErrorStoragePath();
        if ($jsonPath !== null) {
            $previewService = new ChessComApiErrorPreviewService();
            if ($previewService->previewImageExists($jsonPath)) {
                $previewImageUrl = route("evento.torneios.chesscom.chesscom__last_error_preview_image", [
                    "id" => $evento->id,
                    "torneio_id" => $torneio->id,
                ]);
            }
            if ($previewService->previewHtmlExists($jsonPath)) {
                $previewHtmlUrl = route("evento.torneios.chesscom.chesscom__last_error_preview_html", [
                    "id" => $evento->id,
                    "torneio_id" => $torneio->id,
                ]);
            }
        }

        $messageKey = isset($error["message_key"]) ? (string) $error["message_key"] : "tournament_not_found";
        $errorMessage = $this->chesscomTrans($messageKey);

        $actionKey = isset($error["action"]) ? (string) $error["action"] : "check_players_in";
        $actionLabel = $this->chesscomTrans("last_error.action_" . $actionKey);
        if ($actionLabel === "chesscom.last_error.action_" . $actionKey) {
            $actionLabel = $actionKey;
        }

        return view("evento.torneio.chesscom.last_error", [
            "evento" => $evento,
            "torneio" => $torneio,
            "error" => $error,
            "errorMessage" => $errorMessage,
            "actionLabel" => $actionLabel,
            "previewImageUrl" => $previewImageUrl,
            "previewHtmlUrl" => $previewHtmlUrl,
            "fieldLabels" => [
                "action" => $this->chesscomTrans("last_error.field_action"),
                "occurred_at" => $this->chesscomTrans("last_error.field_occurred_at"),
                "user_id" => $this->chesscomTrans("last_error.field_user_id"),
                "message" => $this->chesscomTrans("last_error.field_message"),
                "http_status" => $this->chesscomTrans("last_error.field_http_status"),
                "slug" => $this->chesscomTrans("last_error.field_slug"),
                "request_url" => $this->chesscomTrans("last_error.field_request_url"),
                "response_body" => $this->chesscomTrans("last_error.field_response_body"),
                "response_preview" => $this->chesscomTrans("last_error.field_response_preview"),
            ],
            "previewImageUnavailable" => $this->chesscomTrans("last_error.preview_image_unavailable"),
            "pageTitle" => $this->chesscomTrans("last_error.page_title"),
        ]);
    }

    public function chesscom__last_error_preview_image($id, $torneio_id)
    {
        $resolved = $this->chesscomAuthorizedEventAndTorneio($id, $torneio_id);
        if ($resolved instanceof \Illuminate\Http\RedirectResponse) {
            return $resolved;
        }

        /** @var Torneio $torneio */
        list($evento, $torneio) = $resolved;

        $jsonPath = $torneio->chesscom_getLastApiErrorStoragePath();
        if ($jsonPath === null) {
            abort(404);
        }

        $torneio->chesscom_ensureLastApiErrorPreview();

        $previewService = new ChessComApiErrorPreviewService();
        $pngPath = $previewService->jsonPathToPngPath($jsonPath);
        if (!Storage::disk("chesscom_api_errors")->exists($pngPath)) {
            abort(404);
        }

        return response(Storage::disk("chesscom_api_errors")->get($pngPath), 200, [
            "Content-Type" => "image/png",
            "Content-Disposition" => "inline; filename=\"chesscom-error-preview.png\"",
        ]);
    }

    public function chesscom__last_error_preview_html($id, $torneio_id)
    {
        $resolved = $this->chesscomAuthorizedEventAndTorneio($id, $torneio_id);
        if ($resolved instanceof \Illuminate\Http\RedirectResponse) {
            return $resolved;
        }

        /** @var Torneio $torneio */
        list($evento, $torneio) = $resolved;

        $jsonPath = $torneio->chesscom_getLastApiErrorStoragePath();
        if ($jsonPath === null) {
            abort(404);
        }

        $torneio->chesscom_ensureLastApiErrorPreview();

        $previewService = new ChessComApiErrorPreviewService();
        $htmlPath = $previewService->jsonPathToHtmlPath($jsonPath);
        if (!Storage::disk("chesscom_api_errors")->exists($htmlPath)) {
            abort(404);
        }

        return response(Storage::disk("chesscom_api_errors")->get($htmlPath), 200, [
            "Content-Type" => "text/html; charset=UTF-8",
            "Content-Disposition" => "inline; filename=\"chesscom-error-preview.html\"",
            "X-Frame-Options" => "SAMEORIGIN",
        ]);
    }


    public static function generateRodadasDefault($torneio_id){
        $torneio = Torneio::find($torneio_id);
        if($torneio){
            if($torneio->tipo_torneio->id == 3){
                // GERANDO RODADAS PARA A ESTRUTURA DE CHAVE DE SEMI FINAL E FINAL (SEM DISPUTA DE 3o)
                /*
                 *       J1 --|                                               |-- J2
                 *            |----(VENCEDOR J1 X J4) X (VENCEDOR J2 X J3)----|
                 *       J4 --|                                               |-- J3
                 *
                 */

                $rodada_1 = new Rodada;
                $rodada_1->torneio_id = $torneio->id;
                $rodada_1->numero = 1;
                $rodada_1->save();

                $emparceiramento_1 = new Emparceiramento;
                $emparceiramento_1->rodadas_id = $rodada_1->id;
                $emparceiramento_1->numero_a = 1;
                $emparceiramento_1->numero_b = 4;
                $emparceiramento_1->save();

                $emparceiramento_2 = new Emparceiramento;
                $emparceiramento_2->rodadas_id = $rodada_1->id;
                $emparceiramento_2->numero_a = 2;
                $emparceiramento_2->numero_b = 3;
                $emparceiramento_2->save();

                $rodada_2 = new Rodada;
                $rodada_2->torneio_id = $torneio->id;
                $rodada_2->numero = 2;
                $rodada_2->save();

                $emparceiramento_3 = new Emparceiramento;
                $emparceiramento_3->rodadas_id = $rodada_2->id;
                $emparceiramento_3->numero_a = "J1xJ4";
                $emparceiramento_3->numero_b = "J2xJ3";
                $emparceiramento_3->save();
            }
        }
    }


    public static function gerarCriteriosDesempate($torneio_id){
        $torneio = Torneio::find($torneio_id);
        if($torneio){
            $criterio_desempate_controller = new CriterioDesempateController;
            $criterios_desempate = $torneio->evento->getCriterios();
            if($torneio->tipo_torneio->id == 3){
                foreach($torneio->inscricoes->all() as $inscricao){
                    foreach($inscricao->criterios_desempate->all() as $criterios_inscricao){
                        $criterios_inscricao->delete();
                    }
                }
                foreach($criterios_desempate as $criterio_desempate){
                    foreach($torneio->inscricoes->all() as $inscricao){
                        if(is_numeric($inscricao->pontos)){
                            $valor_criterio = $criterio_desempate_controller->generate($torneio->evento, $inscricao->enxadrista, $criterio_desempate->criterio);
                            if(!is_bool($valor_criterio)){
                                $criterio_desempate_inscricao = new InscricaoCriterioDesempate;
                                $criterio_desempate_inscricao->inscricao_id = $inscricao->id;
                                $criterio_desempate_inscricao->criterio_desempate_id = $criterio_desempate->criterio->id;
                                $criterio_desempate_inscricao->valor = (!$inscricao->desconsiderar_pontuacao_geral) ? $valor_criterio : 0;
                                $criterio_desempate_inscricao->save();
                            }
                        }
                    }
                }
            }
        }
    }

}

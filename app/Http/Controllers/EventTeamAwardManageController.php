<?php

namespace App\Http\Controllers;

use App\CriterioDesempate;
use App\Enum\ConfigType;
use App\EventTeamAward;
use App\EventTeamAwardCategory;
use App\EventTeamAwardScore;
use App\Evento;
use App\GrupoEvento;
use App\TiebreakTeamAward;
use Auth;
use Illuminate\Http\Request;

class EventTeamAwardManageController extends Controller
{
    public function grupo_add($id, Request $request)
    {
        $user = Auth::user();
        if (!$this->canEditGrupo($user, $id)) {
            return redirect("/grupoevento");
        }

        $award = new EventTeamAward;
        $award->event_groups_id = $id;
        $award->events_id = null;
        $award->name = $request->input("name");
        $award->is_public = $request->has("is_public");
        $award->is_can_calculate = $request->has("is_can_calculate");
        $award->save();

        return redirect("/grupoevento/{$id}/premiacao_time/edit/{$award->id}");
    }

    public function grupo_remove($id, $award_id)
    {
        $user = Auth::user();
        if (!$this->canEditGrupo($user, $id)) {
            return redirect("/grupoevento");
        }

        $award = $this->findGrupoAward($id, $award_id);
        if ($award) {
            $this->deleteAward($award);
        }

        return redirect("/grupoevento/dashboard/{$id}?tab=premiacao_equipe");
    }

    public function grupo_edit($id, $award_id)
    {
        $user = Auth::user();
        if (!$this->canEditGrupo($user, $id)) {
            return redirect("/grupoevento");
        }

        $grupo_evento = GrupoEvento::find($id);
        $team_award = $this->findGrupoAward($id, $award_id);
        if (!$grupo_evento || !$team_award) {
            return redirect("/grupoevento/dashboard/{$id}?tab=premiacao_equipe");
        }

        return view("team_award.edit", $this->editViewData(
            $team_award,
            "grupo",
            $grupo_evento,
            $id,
            $award_id,
            $user,
            $grupo_evento->categorias()->orderBy("name", "ASC")->get()
        ));
    }

    public function grupo_edit_post($id, $award_id, Request $request)
    {
        $user = Auth::user();
        if (!$this->canEditGrupo($user, $id)) {
            return redirect("/grupoevento");
        }

        $team_award = $this->findGrupoAward($id, $award_id);
        if (!$team_award) {
            return redirect("/grupoevento/dashboard/{$id}?tab=premiacao_equipe");
        }

        $this->applyAwardSettings($team_award, $request);

        return $this->redirectEdit($id, $award_id, "grupo", "Configurações gerais salvas.", "geral");
    }

    public function grupo_category_add($id, $award_id, Request $request)
    {
        return $this->category_add($id, $award_id, $request, "grupo");
    }

    public function grupo_category_remove($id, $award_id, $link_id)
    {
        return $this->category_remove($id, $award_id, $link_id, "grupo");
    }

    public function grupo_score_add($id, $award_id, Request $request)
    {
        return $this->score_add($id, $award_id, $request, "grupo");
    }

    public function grupo_score_remove($id, $award_id, $score_id)
    {
        return $this->score_remove($id, $award_id, $score_id, "grupo");
    }

    public function grupo_tiebreak_add($id, $award_id, Request $request)
    {
        return $this->tiebreak_add($id, $award_id, $request, "grupo");
    }

    public function grupo_tiebreak_remove($id, $award_id, $tiebreak_id)
    {
        return $this->tiebreak_remove($id, $award_id, $tiebreak_id, "grupo");
    }

    public function grupo_category_points_post($id, $award_id, Request $request)
    {
        return $this->category_points_post($id, $award_id, $request, "grupo");
    }

    public function grupo_category_add_all($id, $award_id)
    {
        return $this->category_add_all($id, $award_id, "grupo");
    }

    public function grupo_import_pontuacoes($id, $award_id)
    {
        return $this->import_pontuacoes_grupo($id, $award_id, "grupo");
    }

    public function event_add($id, Request $request)
    {
        $evento = Evento::find($id);
        $user = Auth::user();
        if (!$evento || !$this->canEditEvento($user, $evento)) {
            return redirect("/");
        }

        $award = new EventTeamAward;
        $award->events_id = $evento->id;
        $award->event_groups_id = null;
        $award->name = $request->input("name");
        $award->is_public = $request->has("is_public");
        $award->is_can_calculate = $request->has("is_can_calculate");
        $award->save();

        return redirect("/evento/{$id}/premiacao_time/edit/{$award->id}");
    }

    public function event_remove($id, $award_id)
    {
        $evento = Evento::find($id);
        $user = Auth::user();
        if (!$evento || !$this->canEditEvento($user, $evento)) {
            return redirect("/");
        }

        $award = $this->findEventAward($id, $award_id);
        if ($award) {
            $this->deleteAward($award);
        }

        return redirect("/evento/dashboard/{$id}?tab=premiacao_equipe");
    }

    public function event_edit($id, $award_id)
    {
        $evento = Evento::find($id);
        $user = Auth::user();
        if (!$evento || !$this->canEditEvento($user, $evento)) {
            return redirect("/");
        }

        $team_award = $this->findEventAward($id, $award_id);
        if (!$team_award) {
            return redirect("/evento/dashboard/{$id}?tab=premiacao_equipe");
        }

        return view("evento.v2.team_award.edit", array_merge(
            $this->editViewData(
            $team_award,
            "evento",
            $evento,
            $id,
            $award_id,
            $user,
            $evento->grupo_evento->categorias()->orderBy("name", "ASC")->get()
        ), ['evento' => $evento]));
    }

    public function event_edit_post($id, $award_id, Request $request)
    {
        $evento = Evento::find($id);
        $user = Auth::user();
        if (!$evento || !$this->canEditEvento($user, $evento)) {
            return redirect("/");
        }

        $team_award = $this->findEventAward($id, $award_id);
        if (!$team_award) {
            return redirect("/evento/dashboard/{$id}?tab=premiacao_equipe");
        }

        $this->applyAwardSettings($team_award, $request);

        return $this->redirectEdit($id, $award_id, "evento", "Configurações gerais salvas.", "geral");
    }

    public function event_category_add($id, $award_id, Request $request)
    {
        return $this->category_add($id, $award_id, $request, "evento");
    }

    public function event_category_remove($id, $award_id, $link_id)
    {
        return $this->category_remove($id, $award_id, $link_id, "evento");
    }

    public function event_score_add($id, $award_id, Request $request)
    {
        return $this->score_add($id, $award_id, $request, "evento");
    }

    public function event_score_remove($id, $award_id, $score_id)
    {
        return $this->score_remove($id, $award_id, $score_id, "evento");
    }

    public function event_tiebreak_add($id, $award_id, Request $request)
    {
        return $this->tiebreak_add($id, $award_id, $request, "evento");
    }

    public function event_tiebreak_remove($id, $award_id, $tiebreak_id)
    {
        return $this->tiebreak_remove($id, $award_id, $tiebreak_id, "evento");
    }

    public function event_category_points_post($id, $award_id, Request $request)
    {
        return $this->category_points_post($id, $award_id, $request, "evento");
    }

    public function event_category_add_all($id, $award_id)
    {
        return $this->category_add_all($id, $award_id, "evento");
    }

    public function event_import_pontuacoes($id, $award_id)
    {
        return $this->import_pontuacoes_grupo($id, $award_id, "evento");
    }

    private function editViewData(EventTeamAward $team_award, $context, $parent, $parent_id, $award_id, $user, $categorias)
    {
        $team_award->load(["categories.category", "scores", "tiebreaks.tiebreak"]);

        $grupo = $context === "grupo" ? $parent : $parent->grupo_evento;
        $grupo_pontuacoes = $grupo->pontuacoes()->orderBy("posicao", "ASC")->get();
        $criterios_team_award = CriterioDesempate::where([["is_team_award", "=", true]])->orderBy("name", "ASC")->get();
        $next_tiebreak_priority = (int) $team_award->tiebreaks()->max("priority") + 1;
        if ($next_tiebreak_priority < 1) {
            $next_tiebreak_priority = 1;
        }

        $linked_category_ids = $team_award->categories->pluck("categories_id")->all();
        $available_categorias = $categorias->filter(function ($categoria) use ($linked_category_ids) {
            return !in_array($categoria->id, $linked_category_ids, true);
        });

        $is_points_mode = $team_award->hasConfig("is_points") && $team_award->getConfig("is_points", true);
        $url_base = $context === "grupo"
            ? "/grupoevento/{$parent_id}/premiacao_time/edit/{$award_id}"
            : "/evento/{$parent_id}/premiacao_time/edit/{$award_id}";
        $dashboard_url = $context === "grupo"
            ? "/grupoevento/dashboard/{$parent_id}?tab=premiacao_equipe"
            : "/evento/dashboard/{$parent_id}?tab=premiacao_equipe";
        $classificar_url = $context === "grupo"
            ? url("/grupoevento/premiacao_time/classificar/{$parent_id}")
            : url("/evento/premiacao_time/classificar/{$parent_id}");
        $public_standings_url = $context === "grupo"
            ? url("/grupoevento/{$parent_id}/team_awards/standings")
            : url("/evento/{$parent_id}/team_awards/standings");

        return compact(
            "context",
            "parent",
            "parent_id",
            "award_id",
            "team_award",
            "categorias",
            "available_categorias",
            "criterios_team_award",
            "grupo_pontuacoes",
            "next_tiebreak_priority",
            "is_points_mode",
            "user",
            "url_base",
            "dashboard_url",
            "classificar_url",
            "public_standings_url"
        );
    }

    private function category_add_all($parent_id, $award_id, $context)
    {
        $team_award = $this->resolveAward($parent_id, $award_id, $context);
        if (!$team_award) {
            return $this->redirectDashboard($parent_id, $context);
        }

        $categorias = $context === "grupo"
            ? GrupoEvento::find($parent_id)->categorias
            : Evento::find($parent_id)->grupo_evento->categorias;

        $added = 0;
        foreach ($categorias as $categoria) {
            if ($team_award->categories()->where([["categories_id", "=", $categoria->id]])->count() == 0) {
                $link = new EventTeamAwardCategory;
                $link->event_team_awards_id = $team_award->id;
                $link->categories_id = $categoria->id;
                $link->save();
                $added++;
            }
        }

        $message = $added > 0
            ? "{$added} categoria(s) vinculada(s)."
            : "Todas as categorias já estavam vinculadas.";

        return $this->redirectEdit($parent_id, $award_id, $context, $message, "pontos");
    }

    private function import_pontuacoes_grupo($parent_id, $award_id, $context)
    {
        $team_award = $this->resolveAward($parent_id, $award_id, $context);
        if (!$team_award) {
            return $this->redirectDashboard($parent_id, $context);
        }

        $grupo = $context === "grupo"
            ? GrupoEvento::find($parent_id)
            : Evento::find($parent_id)->grupo_evento;

        $imported = 0;
        foreach ($grupo->pontuacoes()->orderBy("posicao", "ASC")->get() as $pontuacao) {
            if ($pontuacao->posicao > 0) {
                $existing = $team_award->scores()->where([["place", "=", $pontuacao->posicao]])->first();
                if ($existing) {
                    $existing->score = (int) $pontuacao->pontuacao;
                    $existing->save();
                } else {
                    $row = new EventTeamAwardScore;
                    $row->event_team_awards_id = $team_award->id;
                    $row->place = (int) $pontuacao->posicao;
                    $row->score = (int) $pontuacao->pontuacao;
                    $row->save();
                }
                $imported++;
            }
        }

        if ($imported === 0) {
            return $this->redirectEdit(
                $parent_id,
                $award_id,
                $context,
                "Não há pontuação por posição cadastrada na aba Pontuação do grupo de evento.",
                "pontos"
            );
        }

        return $this->redirectEdit(
            $parent_id,
            $award_id,
            $context,
            "Importadas {$imported} colocação(ões) a partir da pontuação geral do grupo.",
            "pontos"
        );
    }

    private function category_add($parent_id, $award_id, Request $request, $context)
    {
        $team_award = $this->resolveAward($parent_id, $award_id, $context);
        if (!$team_award) {
            return $this->redirectDashboard($parent_id, $context);
        }

        $categories_id = $request->input("categories_id");
        if ($categories_id && $team_award->categories()->where([["categories_id", "=", $categories_id]])->count() == 0) {
            $link = new EventTeamAwardCategory;
            $link->event_team_awards_id = $team_award->id;
            $link->categories_id = $categories_id;
            $link->save();
        }

        return $this->redirectEdit($parent_id, $award_id, $context, "Categoria adicionada.", "pontos");
    }

    private function category_remove($parent_id, $award_id, $link_id, $context)
    {
        $team_award = $this->resolveAward($parent_id, $award_id, $context);
        if ($team_award) {
            $link = $team_award->categories()->where([["id", "=", $link_id]])->first();
            if ($link) {
                $link->delete();
            }
        }

        return $this->redirectEdit($parent_id, $award_id, $context, "Categoria removida.", "pontos");
    }

    private function score_add($parent_id, $award_id, Request $request, $context)
    {
        $team_award = $this->resolveAward($parent_id, $award_id, $context);
        if (!$team_award) {
            return $this->redirectDashboard($parent_id, $context);
        }

        $place = (int) $request->input("place");
        $score = (int) $request->input("score");
        if ($place > 0) {
            $existing = $team_award->scores()->where([["place", "=", $place]])->first();
            if ($existing) {
                $existing->score = $score;
                $existing->save();
            } else {
                $row = new EventTeamAwardScore;
                $row->event_team_awards_id = $team_award->id;
                $row->place = $place;
                $row->score = $score;
                $row->save();
            }
        }

        return $this->redirectEdit($parent_id, $award_id, $context, "Pontuação da colocação salva.", "pontos");
    }

    private function score_remove($parent_id, $award_id, $score_id, $context)
    {
        $team_award = $this->resolveAward($parent_id, $award_id, $context);
        if ($team_award) {
            $row = $team_award->scores()->where([["id", "=", $score_id]])->first();
            if ($row) {
                $row->delete();
            }
        }

        return $this->redirectEdit($parent_id, $award_id, $context, "Colocação removida da tabela.", "pontos");
    }

    private function tiebreak_add($parent_id, $award_id, Request $request, $context)
    {
        $team_award = $this->resolveAward($parent_id, $award_id, $context);
        if (!$team_award) {
            return $this->redirectDashboard($parent_id, $context);
        }

        $tiebreaks_id = $request->input("tiebreaks_id");
        $priority = (int) $request->input("priority");
        if ($tiebreaks_id) {
            $row = new TiebreakTeamAward;
            $row->event_team_awards_id = $team_award->id;
            $row->tiebreaks_id = $tiebreaks_id;
            $row->priority = $priority;
            $row->save();
        }

        return $this->redirectEdit($parent_id, $award_id, $context, "Critério de desempate adicionado.", "desempate");
    }

    private function tiebreak_remove($parent_id, $award_id, $tiebreak_id, $context)
    {
        $team_award = $this->resolveAward($parent_id, $award_id, $context);
        if ($team_award) {
            $row = $team_award->tiebreaks()->where([["id", "=", $tiebreak_id]])->first();
            if ($row) {
                $row->delete();
            }
        }

        return $this->redirectEdit($parent_id, $award_id, $context, "Critério de desempate removido.", "desempate");
    }

    private function category_points_post($parent_id, $award_id, Request $request, $context)
    {
        $team_award = $this->resolveAward($parent_id, $award_id, $context);
        if (!$team_award) {
            return $this->redirectDashboard($parent_id, $context);
        }

        foreach ($request->input("category_points", []) as $categoria_id => $value) {
            $key = "category_" . $categoria_id . "_default_points";
            if ($value !== null && $value !== "") {
                $team_award->setConfig($key, ConfigType::Integer, (int) $value);
            } else {
                $team_award->removeConfig($key);
            }
        }

        return $this->redirectEdit($parent_id, $award_id, $context, "Pontuação fixa por categoria salva.", "pontos");
    }

    private function applyAwardSettings(EventTeamAward $team_award, Request $request)
    {
        $team_award->name = $request->input("name");
        $team_award->is_public = $request->has("is_public");
        $team_award->is_can_calculate = $request->has("is_can_calculate");
        $team_award->save();

        $this->syncBooleanConfig($team_award, "is_points", $request->has("is_points"));
        $this->syncBooleanConfig($team_award, "no_classificate", $request->has("no_classificate"));

        if ($request->input("limit_places") !== null && $request->input("limit_places") !== "") {
            $team_award->setConfig("limit_places", ConfigType::Integer, (int) $request->input("limit_places"));
        } else {
            $team_award->removeConfig("limit_places");
        }

        if ($request->input("limit_total_places") !== null && $request->input("limit_total_places") !== "") {
            $team_award->setConfig("limit_total_places", ConfigType::Integer, (int) $request->input("limit_total_places"));
        } else {
            $team_award->removeConfig("limit_total_places");
        }
    }

    private function syncBooleanConfig(EventTeamAward $team_award, $key, $enabled)
    {
        if ($enabled) {
            $team_award->setConfig($key, ConfigType::Boolean, true);
        } else {
            $team_award->removeConfig($key);
        }
    }

    private function deleteAward(EventTeamAward $award)
    {
        foreach ($award->team_scores as $score) {
            foreach ($score->configs as $config) {
                $config->delete();
            }
            foreach ($score->tiebreaks as $tiebreak) {
                $tiebreak->delete();
            }
            $score->delete();
        }
        foreach ($award->configs as $config) {
            $config->delete();
        }
        foreach ($award->scores as $score) {
            $score->delete();
        }
        foreach ($award->categories as $category) {
            $category->delete();
        }
        foreach ($award->tiebreaks as $tiebreak) {
            $tiebreak->delete();
        }
        $award->delete();
    }

    private function resolveAward($parent_id, $award_id, $context)
    {
        $user = Auth::user();
        if ($context === "grupo") {
            if (!$this->canEditGrupo($user, $parent_id)) {
                return null;
            }
            return $this->findGrupoAward($parent_id, $award_id);
        }

        $evento = Evento::find($parent_id);
        if (!$evento || !$this->canEditEvento($user, $evento)) {
            return null;
        }
        return $this->findEventAward($parent_id, $award_id);
    }

    private function findGrupoAward($grupo_id, $award_id)
    {
        $grupo = GrupoEvento::find($grupo_id);
        if (!$grupo) {
            return null;
        }
        return $grupo->event_team_awards()->where([["id", "=", $award_id]])->first();
    }

    private function findEventAward($evento_id, $award_id)
    {
        $evento = Evento::find($evento_id);
        if (!$evento) {
            return null;
        }
        return $evento->event_team_awards()->where([["id", "=", $award_id]])->first();
    }

    private function canEditGrupo($user, $grupo_id)
    {
        return $user->hasPermissionGlobal() || $user->hasPermissionGroupEventByPerfil($grupo_id, [7]);
    }

    private function canEditEvento($user, Evento $evento)
    {
        return $user->hasPermissionGlobal()
            || $user->hasPermissionEventByPerfil($evento->id, [4])
            || $user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7]);
    }

    private function editUrl($parent_id, $award_id, $context)
    {
        if ($context === "grupo") {
            return "/grupoevento/{$parent_id}/premiacao_time/edit/{$award_id}";
        }
        return "/evento/{$parent_id}/premiacao_time/edit/{$award_id}";
    }

    private function redirectEdit($parent_id, $award_id, $context, $message = null, $tab = null)
    {
        $url = $this->editUrl($parent_id, $award_id, $context);
        if ($tab) {
            $url .= "?tab=" . urlencode($tab);
        }
        if ($message) {
            return redirect($url)->with("status", $message);
        }
        return redirect($url);
    }

    private function redirectDashboard($parent_id, $context)
    {
        if ($context === "grupo") {
            return redirect("/grupoevento/dashboard/{$parent_id}?tab=premiacao_equipe");
        }
        return redirect("/evento/dashboard/{$parent_id}?tab=premiacao_equipe");
    }
}

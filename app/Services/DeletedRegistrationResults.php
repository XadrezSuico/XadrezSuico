<?php

namespace App\Services;

use App\EnxadristaCriterioDesempateGeral;
use App\Inscricao;
use App\MovimentacaoRating;
use App\PontuacaoEnxadrista;
use App\Rating;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\Process\TeamAwardCalculatorController;

class DeletedRegistrationResults
{
    public static function refresh(Inscricao $registration)
    {
        $event = $registration->torneio->evento;
        $group = $event->grupo_evento;

        // Rebuild existing materialized circuit standings without this registration.
        if ($group && PontuacaoEnxadrista::where('grupo_evento_id', $group->id)
                ->where('categoria_id', $registration->categoria_id)->exists()) {
            EnxadristaCriterioDesempateGeral::where('grupo_evento_id', $group->id)
                ->where('categoria_id', $registration->categoria_id)->delete();
            CategoriaController::classificar_geral($group->id, $registration->categoria_id);
        }

        if ($group && PontuacaoEnxadrista::where('grupo_evento_id', $group->id)
                ->whereNull('categoria_id')->exists()) {
            EnxadristaCriterioDesempateGeral::where('grupo_evento_id', $group->id)
                ->whereNull('categoria_id')->delete();
            CategoriaController::classificar_geral_unico($group->id);
        }

        foreach ([$event, $group] as $parent) {
            if (!$parent) continue;
            foreach ($parent->event_team_awards()->whereHas('team_scores')->get() as $award) {
                $eventArg = $parent->isEvent() ? $parent : null;
                $groupArg = $parent->isEventGroup() ? $parent : null;
                TeamAwardCalculatorController::sum_scores($eventArg, $groupArg, $award);
                TeamAwardCalculatorController::generate_tiebreaks($eventArg, $groupArg, $award);
                $award->unsetRelation('team_scores');
                TeamAwardCalculatorController::classificate_teams($eventArg, $groupArg, $award);
            }
        }

        // Preserve historical movements but remove their contribution to current ratings.
        $ratingIds = MovimentacaoRating::withoutGlobalScope('not_deleted_registrations')
            ->where('inscricao_id', $registration->id)->pluck('ratings_id');
        foreach (Rating::whereIn('id', $ratingIds)->get() as $rating) {
            $rating->calcular();
            $rating->dias()->delete();
        }
    }
}

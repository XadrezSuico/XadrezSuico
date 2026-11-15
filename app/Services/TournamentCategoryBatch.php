<?php

namespace App\Services;

use App\Torneio;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TournamentCategoryBatch
{
    public function execute(Torneio $source, array $linkIds, string $action, ?int $targetId = null): int
    {
        return DB::transaction(function () use ($source, $linkIds, $action, $targetId) {
            $ids = array_unique(array_merge([$source->id], $targetId ? [$targetId] : []));
            $tournaments = Torneio::whereIn('id', $ids)->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            $source = $tournaments->get($source->id);
            abort_unless($source, 404);
            $links = $source->categorias()->whereIn('id', $linkIds)->lockForUpdate()->get();
            if (!$links->count() || $links->count() !== count(array_unique($linkIds))) {
                throw ValidationException::withMessages(['category_ids' => 'Selecione apenas categorias que pertencem a este torneio.']);
            }
            if (!in_array($action, ['delete', 'transfer'], true)) {
                throw ValidationException::withMessages(['action' => 'Ação inválida.']);
            }
            if ($action === 'transfer') {
                $target = $tournaments->get($targetId);
                if (!$target || $target->id === $source->id || $target->evento_id !== $source->evento_id) {
                    throw ValidationException::withMessages(['tournament_id' => 'Escolha outro torneio deste mesmo evento.']);
                }
                $categoryIds = $links->pluck('categoria_id')->unique();
                $existing = $target->categorias()->pluck('categoria_id');
                if ((int) $target->tipo_torneio_id === 3 && $existing->merge($categoryIds)->unique()->count() > 1) {
                    throw ValidationException::withMessages(['tournament_id' => 'O torneio de destino permite apenas uma categoria.']);
                }
                foreach ($source->inscricoes()->whereIn('categoria_id', $categoryIds)->lockForUpdate()->get() as $registration) {
                    $registration->torneio_id = $target->id;
                    $registration->save();
                }
                foreach ($links as $link) {
                    if ($existing->contains($link->categoria_id)) {
                        $link->delete();
                    } else {
                        $link->torneio_id = $target->id;
                        $link->save();
                        $existing->push($link->categoria_id);
                    }
                }
                $target->torneio_template_id = null;
                $target->save();
            } else {
                foreach ($links as $link) {
                    $link->delete();
                }
            }
            $source->torneio_template_id = null;
            $source->save();
            return $links->count();
        });
    }
}

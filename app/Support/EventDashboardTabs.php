<?php

namespace App\Support;

use App\Evento;
use Illuminate\Support\Facades\Auth;

class EventDashboardTabs
{
    /**
     * @return array<int, array{id: string, label: string, url: string, active: bool}>
     */
    public static function forEvent(Evento $evento, ?string $activeTab = null): array
    {
        $user = Auth::user();
        $baseUrl = url('/evento/dashboard/' . $evento->id);
        $activeTab = $activeTab ?: 'funcoes';

        $tabs = [
            ['id' => 'funcoes', 'label' => 'Funções'],
            ['id' => 'resume', 'label' => 'Resumo'],
            ['id' => 'editar_evento', 'label' => 'Editar Evento'],
            ['id' => 'pagina', 'label' => 'Página'],
            ['id' => 'timeline', 'label' => 'Timeline'],
            ['id' => 'criterio_desempate', 'label' => 'Critério de Desempate'],
        ];

        if (
            $user->hasPermissionGlobal() ||
            $user->hasPermissionEventByPerfil($evento->id, [4]) ||
            $user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            $tabs[] = ['id' => 'premiacao_equipe', 'label' => 'Premiação por Equipes'];
        }

        $tabs[] = ['id' => 'categoria', 'label' => 'Categorias'];
        $tabs[] = ['id' => 'categorias_relacionadas', 'label' => 'Categorias Relacionadas'];

        if ($evento->event_children()->count() > 0) {
            $tabs[] = ['id' => 'evento_filho', 'label' => 'Eventos Filhos'];
        }

        $tabs[] = ['id' => 'torneio', 'label' => 'Torneios'];
        $tabs[] = ['id' => 'campo_personalizado', 'label' => 'Campos'];
        $tabs[] = ['id' => 'email_template', 'label' => 'E-mail'];

        if (
            $user->hasPermissionGlobal() ||
            $user->hasPermissionEventsByPerfil([14, 15, 16])
        ) {
            $tabs[] = ['id' => 'classificator', 'label' => 'Classificador'];
        }

        return array_map(function ($tab) use ($baseUrl, $activeTab) {
            return [
                'id' => $tab['id'],
                'label' => $tab['label'],
                'url' => $baseUrl . '?tab=' . $tab['id'],
                'active' => $tab['id'] === $activeTab,
            ];
        }, $tabs);
    }
}

<?php

namespace App\Support;

use App\Evento;
use Illuminate\Support\Facades\Auth;

class EventDashboardTabs
{
    /**
     * Abas da dashboard que ainda usam markup AdminLTE/Bootstrap legado.
     *
     * @return array<int, string>
     */
    public static function legacyTabIds(): array
    {
        return [
            'editar_evento',
            'pagina',
            'timeline',
            'criterio_desempate',
            'premiacao_equipe',
            'categoria',
            'categorias_relacionadas',
            'evento_filho',
            'torneio',
            'campo_personalizado',
            'email_template',
            'classificator',
        ];
    }

    /**
     * @return array<int, array{id: string, label: string}>
     */
    private static function baseTabDefinitions(): array
    {
        return [
            ['id' => 'funcoes', 'label' => 'Funções'],
            ['id' => 'resume', 'label' => 'Resumo'],
            ['id' => 'editar_evento', 'label' => 'Editar Evento'],
            ['id' => 'pagina', 'label' => 'Página'],
            ['id' => 'timeline', 'label' => 'Timeline'],
            ['id' => 'criterio_desempate', 'label' => 'Critério de Desempate'],
            ['id' => 'premiacao_equipe', 'label' => 'Premiação por Equipes'],
            ['id' => 'categoria', 'label' => 'Categorias'],
            ['id' => 'categorias_relacionadas', 'label' => 'Categorias Relacionadas'],
            ['id' => 'evento_filho', 'label' => 'Eventos Filhos'],
            ['id' => 'torneio', 'label' => 'Torneios'],
            ['id' => 'campo_personalizado', 'label' => 'Campos'],
            ['id' => 'email_template', 'label' => 'E-mail'],
            ['id' => 'classificator', 'label' => 'Classificador'],
        ];
    }

    public static function labelFor(string $tabId): string
    {
        foreach (self::baseTabDefinitions() as $tab) {
            if ($tab['id'] === $tabId) {
                return $tab['label'];
            }
        }

        return ucfirst(str_replace('_', ' ', $tabId));
    }

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

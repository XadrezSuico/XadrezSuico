<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;

class NavigationMenuBuilder
{
    /**
     * @return array<int, array<string, mixed>|string>
     */
    public function build(): array
    {
        $items = [];

        $items[] = ['type' => 'header', 'label' => 'ACESSO PÚBLICO'];

        if (env('SHOW_RATING', false)) {
            $items[] = [
                'type' => 'link',
                'label' => 'Ratings',
                'url' => '/rating',
                'icon' => 'star',
            ];
        }

        if (Auth::check()) {
            $user = Auth::user();

            $items[] = ['type' => 'header', 'label' => 'ACESSO RESTRITO'];

            $items[] = [
                'type' => 'link',
                'label' => 'Dashboard',
                'url' => '/home',
                'icon' => 'dashboard',
            ];

            if ($user->hasPermissionGlobal() || $user->hasPermissionGlobalbyPerfil([9])) {
                $items[] = [
                    'type' => 'link',
                    'label' => 'Enxadristas',
                    'url' => '/enxadrista',
                    'icon' => 'user',
                ];
            }

            if (
                $user->hasPermissionGlobal() ||
                $user->hasPermissionEventsByPerfil([3, 4, 5]) ||
                $user->hasPermissionGroupEventsByPerfil([6, 7])
            ) {
                $items[] = [
                    'type' => 'link',
                    'label' => 'Grupos de Evento',
                    'url' => '/grupoevento',
                    'icon' => 'th-large',
                ];
            }

            $items[] = ['type' => 'header', 'label' => 'ADMINSTRAÇÃO'];

            if ($user->hasPermissionGlobal() || $user->hasPermissionGlobalbyPerfil([8])) {
                $items[] = [
                    'type' => 'link',
                    'label' => 'Cidades',
                    'url' => '/cidade',
                    'icon' => 'map-marker',
                ];
                $items[] = [
                    'type' => 'link',
                    'label' => 'Clubes',
                    'url' => '/clube',
                    'icon' => 'building',
                ];
            }

            if ($user->hasPermissionGlobal()) {
                $items[] = [
                    'type' => 'link',
                    'label' => 'Sexos',
                    'url' => '/sexo',
                    'icon' => 'user',
                ];
                $items[] = [
                    'type' => 'link',
                    'label' => 'Tipo de Rating',
                    'url' => '/tiporating',
                    'icon' => 'star',
                ];
                $items[] = [
                    'type' => 'link',
                    'label' => 'Template de E-mail',
                    'url' => '/emailtemplate',
                    'icon' => 'envelope',
                ];
            }

            if ($user->hasPermissionGlobal() || $user->hasPermissionGroupEventsByPerfil([7])) {
                $items[] = [
                    'type' => 'link',
                    'label' => 'Usuários',
                    'url' => '/usuario',
                    'icon' => 'users',
                ];
            }
        }

        if (env('ENTITY_DOMAIN', null) == 'fexpar.com.br') {
            $items[] = ['type' => 'header', 'label' => 'FEXPAR'];

            if (Auth::check()) {
                $user = Auth::user();
                if ($user->hasPermissionGlobalByPerfil([10])) {
                    $items[] = [
                        'type' => 'link',
                        'label' => 'Gerenciar Vínculos',
                        'url' => '/fexpar/vinculos',
                        'icon' => 'id-card',
                    ];
                }
            }

            $items[] = [
                'type' => 'link',
                'label' => 'Enxadristas',
                'url' => '/especiais/fexpar/todos_enxadristas',
                'icon' => 'users',
            ];
            $items[] = [
                'type' => 'link',
                'label' => 'Vínculos Federativos',
                'url' => '/especiais/fexpar/vinculos',
                'icon' => 'id-card',
            ];
        }

        if (Auth::check()) {
            $items[] = ['type' => 'header', 'label' => mb_strtoupper(config('xadrezsuico.name', 'XadrezSuíço'))];
            $items[] = [
                'type' => 'link',
                'label' => 'O que há de novo?',
                'url' => '/whatsnew',
                'icon' => 'certificate',
            ];
        }

        return $items;
    }
}

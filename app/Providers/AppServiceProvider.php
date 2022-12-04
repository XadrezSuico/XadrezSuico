<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Events\Dispatcher;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Dispatcher $events)
    {
        if(env("IS_HTTPS",false)) {
            \URL::forceScheme('https');
        }
		$events->listen(BuildingMenu::class, function (BuildingMenu $event) {
            $event->menu->add("ACESSO PÚBLICO");
            if(env("SHOW_RATING",false)){
                $event->menu->add([
                    'text' => 'Ratings',
                    'url'  => '/rating',
                    'icon' => 'star'
                ]);
            }
            if(Auth::check()){
                $user = Auth::user();
                $event->menu->add("ACESSO RESTRITO");
                if(
                    $user->hasPermissionGlobal() ||
                    $user->hasPermissionGlobalbyPerfil([9])
                ){
                    $event->menu->add([
                        'text' => 'Enxadristas',
                        'url'  => '/enxadrista',
                        'icon' => 'user'
                    ]);
                }

                if(
                    $user->hasPermissionGlobal() ||
                    $user->hasPermissionEventsByPerfil([3,4,5]) ||
                    $user->hasPermissionGroupEventsByPerfil([6,7])
                ){
                    $event->menu->add([
                        'text' => 'Grupos de Evento',
                        'url'  => '/grupoevento',
                        'icon' => 'th-large'
                    ]);
                }
                $event->menu->add("ADMINSTRAÇÃO");
                if(
                    $user->hasPermissionGlobal() ||
                    $user->hasPermissionGlobalbyPerfil([8])
                ){
                    $event->menu->add([
                        'text' => 'Cidades',
                        'url'  => '/cidade',
                        'icon' => 'map-marker'
                    ]);
                    $event->menu->add([
                        'text' => 'Clubes',
                        'url'  => '/clube',
                        'icon' => 'building'
                    ]);
                }
                if(
                    $user->hasPermissionGlobal()
                ){
                    $event->menu->add([
                        'text' => 'Sexos',
                        'url'  => '/sexo',
                        'icon' => 'user'
                    ]);
                    $event->menu->add([
                        'text' => 'Tipo de Rating',
                        'url'  => '/tiporating',
                        'icon' => 'star'
                    ]);
                    $event->menu->add([
                        'text' => 'Template de E-mail',
                        'url'  => '/emailtemplate',
                        'icon' => 'envelope'
                    ]);
                }
                if(
                    $user->hasPermissionGlobal() ||
                    $user->hasPermissionGroupEventsByPerfil([7])
                ){
                    $event->menu->add([
                        'text' => 'Usuários',
                        'url'  => '/usuario',
                        'icon' => 'users'
                    ]);
                };
            }
            if(env("ENTITY_DOMAIN",NULL) == "fexpar.com.br"){
                $event->menu->add("FEXPAR");
                if(Auth::check()){
                    if(
                        $user->hasPermissionGlobalByPerfil([10])
                    ){
                        $event->menu->add([
                            'text' => 'Gerenciar Vínculos',
                            'url'  => '/fexpar/vinculos',
                            'icon' => 'id-card'
                        ]);
                    }
                }
                $event->menu->add([
                    'text' => 'Enxadristas',
                    'url'  => '/especiais/fexpar/todos_enxadristas',
                    'icon' => 'users'
                ]);
                $event->menu->add([
                    'text' => 'Vínculos Federativos',
                    'url'  => '/especiais/fexpar/vinculos',
                    'icon' => 'id-card'
                ]);
            }
            if(Auth::check()){
                $event->menu->add("XADREZSUÍÇO");
                $event->menu->add([
                    'text' => 'O que há de novo?',
                    'url'  => '/whatsnew',
                    'icon' => 'certificate'
                ]);
            }
        });
    }
}

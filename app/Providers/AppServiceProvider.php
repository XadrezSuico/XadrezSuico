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
                if($user->hasPermissionMain([1,2,3,4,5,6])){
                    $event->menu->add([
                        'text' => 'Eventos',
                        'url'  => '/evento',
                        'icon' => 'fort-awesome'
                    ]);
                }
                if($user->hasPermissionMain([1,2,3,4])){
                    $event->menu->add([
                        'text' => 'Enxadristas',
                        'url'  => '/enxadrista',
                        'icon' => 'user'
                    ]);
                }
                $event->menu->add("ADMINSTRAÇÃO");
                
                if($user->hasPermissionMain([1,2,6])){
                    $event->menu->add([
                        'text' => 'Grupos de Evento',
                        'url'  => '/grupoevento',
                        'icon' => 'th-large'
                    ]);
                }
                if($user->hasPermissionMain([1,2])){
                    $event->menu->add([
                        'text' => 'Categorias',
                        'url'  => '/categoria',
                        'icon' => 'certificate'
                    ]);
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
                    $event->menu->add([
                        'text' => 'Template de Torneio',
                        'url'  => '/torneiotemplate',
                        'icon' => 'file-text'
                    ]);
                    $event->menu->add([
                        'text' => 'Sexos',
                        'url'  => '/sexo',
                        'icon' => 'user'
                    ]);
                }
                if($user->hasPermissionMain([1,2])){
                    $event->menu->add([
                        'text' => 'Usuários',
                        'url'  => '/usuario',
                        'icon' => 'users'
                    ]);
                };
            }
        });
    }
}

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
            $event->menu->add([
                'text' => 'Ratings',
                'url'  => '/rating',
                'icon' => 'star'
            ]);
            if(Auth::check()){
                $event->menu->add([
                    'text' => 'Eventos',
                    'url'  => '/evento',
                    'icon' => 'fort-awesome'
                ]);
                $event->menu->add("ADMINSTRAÇÃO");
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
                    'text' => 'Usuários',
                    'url'  => '/usuario',
                    'icon' => 'users'
                ]);
            }
        });
    }
}

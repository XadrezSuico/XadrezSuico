<?php

namespace App\Providers;

use App\Support\NavigationMenuBuilder;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;

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
        if (env('IS_HTTPS', false)) {
            \URL::forceScheme('https');
        }

        $events->listen(BuildingMenu::class, function (BuildingMenu $event) {
            $builder = new NavigationMenuBuilder();

            foreach ($builder->build() as $item) {
                if (is_array($item)) {
                    if (($item['type'] ?? null) === 'header') {
                        $event->menu->add($item['label']);
                        continue;
                    }

                    if (($item['type'] ?? null) === 'link') {
                        $event->menu->add([
                            'text' => $item['label'],
                            'url' => $item['url'],
                            'icon' => $item['icon'],
                        ]);
                    }
                }
            }
        });
    }
}

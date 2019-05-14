<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->resource('config/categoria', CategoriaController::class);
    $router->resource('config/cidade', CidadeController::class);
    $router->resource('config/clube', ClubeController::class);
    $router->resource('config/tipotorneio', TipoTorneioController::class);
    $router->resource('config/torneio/template', TorneioTemplateController::class);
    $router->resource('config/criteriodesempate', CriterioDesempateController::class);
    $router->resource('config/perfil', PerfilController::class);
    $router->resource('pontuacao', PontuacaoController::class);
    $router->resource('evento/grupo', GrupoEventoController::class);
    $router->resource('evento/categoria', CategoriaEventoController::class);
    $router->resource('evento/torneio/categoria', CategoriaTorneioController::class);
    $router->resource('evento/torneio', TorneioController::class);
    $router->resource('evento/inscricao', InscricaoController::class);
    $router->resource('evento', EventoController::class);
    $router->resource('enxadrista', EnxadristaController::class);
    $router->resource('usuario/perfil', UserPerfilController::class);
    $router->resource('usuario', UserController::class);

});


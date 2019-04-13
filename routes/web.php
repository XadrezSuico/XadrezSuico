<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if(\Illuminate\Support\Facades\Auth::check()){
        return redirect("/home");
    }
    return redirect("http://crx.xadrezsuico.info");
});

Auth::routes();
if(!\App\User::canRegisterWithoutLogin()){
    Route::get('/register', function(){
        return redirect("/login");
    })->name('register');
    Route::post('/register', function(){
        return redirect("/login");
    })->name('register.post');
}
Route::get('/wat', function(){
    echo \App\User::count();
    foreach(\App\User::All() as $user){
        echo $user->id;
    }
})->name('register.post');


Route::get('/home', 'HomeController@index')->name('home');



// Novas Inscrições
Route::group(["prefix"=>"inscricao"],function(){
    Route::get('/{id}', 'InscricaoController@inscricao')->name('inscricao.inscricao');
    Route::get('/{id}/busca/enxadrista', 'InscricaoController@buscaEnxadrista')->name('inscricao.busca.enxadrista');
    Route::get('/{id}/busca/categoria', 'InscricaoController@buscaCategoria')->name('inscricao.busca.categoria');
    Route::get('/{id}/busca/cidade', 'InscricaoController@buscaCidade')->name('inscricao.busca.cidade');
    Route::get('/{id}/busca/clube', 'InscricaoController@buscaClube')->name('inscricao.busca.clube');
    Route::post('/{id}/enxadrista/novo', 'InscricaoController@adicionarNovoEnxadrista')->name('inscricao.enxadrista.novo');
    Route::post('/{id}/cidade/nova', 'InscricaoController@adicionarNovaCidade')->name('inscricao.cidade.nova');
    Route::post('/{id}/clube/novo', 'InscricaoController@adicionarNovoClube')->name('inscricao.clube.novo');
    Route::post('/{id}/inscricao', 'InscricaoController@adicionarNovaInscricao')->name('inscricao.enviar');
    Route::get('/{id}/enxadrista/getCidadeClube/{enxadrista_id}', 'InscricaoController@getCidadeClube')->name('inscricao.getCidadeClube');
});

Route::group(["prefix"=>"rating"],function(){
    Route::get('/', 'RatingController@index')->name('rating.index');
    Route::get('/list/{tipo_rating_id}', 'RatingController@list')->name('rating.list');
    Route::get('/{tipo_rating_id}/view/{rating_id}', 'RatingController@view')->name('rating.view');
});





Route::group(["prefix"=>"usuario"],function(){
	Route::get('/', 'UserController@index')->name('usuario.index');
	Route::get('/new', 'UserController@new')->name('usuario.new');
	Route::post('/new', 'UserController@newPost')->name('usuario.new.post');
	Route::get('/edit/{id}', 'UserController@edit')->name('usuario.edit');
	Route::post('/edit/{id}', 'UserController@editPost')->name('usuario.edit.post');
	Route::get('/password/{id}', 'UserController@password')->name('usuario.password');
	Route::post('/password/{id}', 'UserController@passwordPost')->name('usuario.password.post');
	Route::get('/delete/{id}', 'UserController@delete')->name('usuario.delete');
});

Route::group(["prefix"=>"evento"],function(){
	Route::get('/', 'EventoGerenciarController@index')->name('evento.index');
	Route::get('/new', 'UserController@new')->name('evento.new');
	Route::post('/new', 'UserController@newPost')->name('evento.new.post');
	Route::get('/edit/{id}', 'UserController@edit')->name('evento.edit');
	Route::post('/edit/{id}', 'UserController@editPost')->name('evento.edit.post');
    Route::get('/delete/{id}', 'UserController@delete')->name('evento.delete');
    Route::get('/classificar/{id}', 'EventoGerenciarController@classificar')->name('evento.classificar');
    Route::get('/classificacao/{id}', 'EventoController@classificacao')->name('evento.classificacao');
    Route::get('/{id}/resultados/{categoria_id}', 'EventoController@resultados')->name('evento.resultados');
    Route::get('/{id}/toggleresultados', 'EventoGerenciarController@toggleMostrarClassificacao')->name('evento.toggleMostrarClassificacao');
    Route::get('/classificacao/{id}/interno', 'EventoGerenciarController@classificacao')->name('evento.classificacao.interno');
    Route::get('/{id}/resultados/{categoria_id}/interno', 'EventoGerenciarController@resultados')->name('evento.resultados.interno');
    


    // Novas Inscrições
    Route::group(["prefix"=>"inscricao"],function(){
        Route::get('/{id}', 'InscricaoGerenciarController@inscricao')->name('evento.inscricao.inscricao');
        Route::get('/{id}/busca/enxadrista', 'InscricaoGerenciarController@buscaEnxadrista')->name('evento.inscricao.busca.enxadrista');
        Route::get('/{id}/busca/categoria', 'InscricaoGerenciarController@buscaCategoria')->name('evento.inscricao.busca.categoria');
        Route::get('/{id}/busca/cidade', 'InscricaoGerenciarController@buscaCidade')->name('evento.inscricao.busca.cidade');
        Route::get('/{id}/busca/clube', 'InscricaoGerenciarController@buscaClube')->name('evento.inscricao.busca.clube');
        Route::post('/{id}/enxadrista/novo', 'InscricaoGerenciarController@adicionarNovoEnxadrista')->name('evento.inscricao.enxadrista.novo');
        Route::post('/{id}/cidade/nova', 'InscricaoGerenciarController@adicionarNovaCidade')->name('evento.inscricao.cidade.nova');
        Route::post('/{id}/clube/novo', 'InscricaoGerenciarController@adicionarNovoClube')->name('evento.inscricao.clube.novo');
        Route::post('/{id}/inscricao', 'InscricaoGerenciarController@adicionarNovaInscricao')->name('evento.inscricao.enviar');
        Route::get('/{id}/enxadrista/getCidadeClube/{enxadrista_id}', 'InscricaoGerenciarController@getCidadeClube')->name('evento.inscricao.getCidadeClube');

        Route::get('/{id}/confirmacao', 'InscricaoGerenciarController@confirmacao')->name('evento.inscricao.confirmacao');
        Route::get('/{id}/confirmacao/busca/enxadrista', 'InscricaoGerenciarController@buscaEnxadristaParaConfirmacao')->name('evento.inscricao.confirmacao.busca.enxadrista');
        Route::get('/{id}/confirmacao/getInfo/{inscricao_id}', 'InscricaoGerenciarController@getInscricaoDados')->name('evento.inscricao.confirmacao.getInfo');
        Route::post('/{id}/confirmacao/confirmar', 'InscricaoGerenciarController@confirmarInscricao')->name('evento.inscricao.confirmacao.confirmarInscricao');
    });

    Route::group(["prefix"=>"{id}/torneios"],function(){
	    Route::get('/', 'TorneioController@index')->name('evento.torneios.index');
	    Route::get('/{torneio_id}/resultados', 'TorneioController@formResults')->name('evento.torneios.resultados');
	    Route::post('/{torneio_id}/resultados', 'TorneioController@sendResultsTxt')->name('evento.torneios.resultados.post');
        Route::group(["prefix"=>"{torneio_id}/inscricoes"],function(){
	        Route::get('/', 'InscricaoGerenciarController@index')->name('evento.torneios.inscricoes.index');
            Route::get('/edit/{inscricao_id}', 'InscricaoGerenciarController@edit')->name('evento.torneios.inscricao.edit');
            Route::post('/edit/{inscricao_id}', 'InscricaoGerenciarController@edit_post')->name('evento.torneios.inscricao.edit.post');
	        Route::get('/sm', 'InscricaoGerenciarController@list_to_manager')->name('evento.torneios.inscricoes.sm');
	        Route::get('/relatorio/inscricoes', 'InscricaoGerenciarController@report_list_subscriptions')->name('evento.torneios.inscricoes.relatorio.inscritos');
	        Route::get('/relatorio/inscricoes/alfabetico', 'InscricaoGerenciarController@report_list_subscriptions_alf')->name('evento.torneios.inscricoes.relatorio.inscritos.alf');
	        Route::get('/relatorio/inscricoes/alfabetico/cidade', 'InscricaoGerenciarController@report_list_subscriptions_cidade_alf')->name('evento.torneios.inscricoes.relatorio.inscritos.alf.cidade');
	        Route::get('/sm/all', 'InscricaoGerenciarController@list_to_manager_all')->name('evento.torneios.inscricoes.sm.all');
        });
    });
});
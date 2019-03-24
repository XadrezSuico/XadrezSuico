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
	Route::get('/', 'EventoController@index')->name('evento.index');
	Route::get('/new', 'UserController@new')->name('evento.new');
	Route::post('/new', 'UserController@newPost')->name('evento.new.post');
	Route::get('/edit/{id}', 'UserController@edit')->name('evento.edit');
	Route::post('/edit/{id}', 'UserController@editPost')->name('evento.edit.post');
    Route::get('/delete/{id}', 'UserController@delete')->name('evento.delete');
    


    // Novas Inscrições
    Route::get('/inscricao/{id}', 'InscricaoGerenciarController@inscricao')->name('evento.inscricao.inscricao');
    Route::get('/inscricao/{id}/busca/enxadrista', 'InscricaoGerenciarController@buscaEnxadrista')->name('evento.inscricao.busca.enxadrista');
    Route::get('/inscricao/{id}/busca/categoria', 'InscricaoGerenciarController@buscaCategoria')->name('evento.inscricao.busca.categoria');
    Route::get('/inscricao/{id}/busca/cidade', 'InscricaoGerenciarController@buscaCidade')->name('evento.inscricao.busca.cidade');
    Route::get('/inscricao/{id}/busca/clube', 'InscricaoGerenciarController@buscaClube')->name('evento.inscricao.busca.clube');
    Route::post('/inscricao/{id}/enxadrista/novo', 'InscricaoGerenciarController@adicionarNovoEnxadrista')->name('evento.inscricao.enxadrista.novo');
    Route::post('/inscricao/{id}/cidade/nova', 'InscricaoGerenciarController@adicionarNovaCidade')->name('evento.inscricao.cidade.nova');
    Route::post('/inscricao/{id}/clube/novo', 'InscricaoGerenciarController@adicionarNovoClube')->name('evento.inscricao.clube.novo');
    Route::post('/inscricao/{id}/inscricao', 'InscricaoGerenciarController@adicionarNovaInscricao')->name('evento.inscricao.enviar');
    Route::get('/inscricao/{id}/enxadrista/getCidadeClube/{enxadrista_id}', 'InscricaoGerenciarController@getCidadeClube')->name('evento.inscricao.getCidadeClube');

    Route::get('/inscricao/{id}/confirmacao', 'InscricaoGerenciarController@confirmacao')->name('evento.inscricao.confirmacao');
    Route::get('/inscricao/{id}/confirmacao/busca/enxadrista', 'InscricaoGerenciarController@buscaEnxadristaParaConfirmacao')->name('evento.inscricao.confirmacao.busca.enxadrista');
    Route::get('/inscricao/{id}/confirmacao/getInfo/{inscricao_id}', 'InscricaoGerenciarController@getInscricaoDados')->name('evento.inscricao.confirmacao.getInfo');
});
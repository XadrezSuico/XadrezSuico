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
if(\App\User::canRegisterWithoutLogin()){
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
Route::get('/inscricao/{id}', 'InscricaoController@inscricao')->name('inscricao.inscricao');
Route::get('/inscricao/{id}/busca/enxadrista', 'InscricaoController@buscaEnxadrista')->name('inscricao.busca.enxadrista');
Route::get('/inscricao/{id}/busca/categoria', 'InscricaoController@buscaCategoria')->name('inscricao.busca.categoria');
Route::get('/inscricao/{id}/busca/cidade', 'InscricaoController@buscaCidade')->name('inscricao.busca.cidade');
Route::get('/inscricao/{id}/busca/clube', 'InscricaoController@buscaClube')->name('inscricao.busca.clube');
Route::post('/inscricao/{id}/enxadrista/novo', 'InscricaoController@adicionarNovoEnxadrista')->name('inscricao.enxadrista.novo');
Route::post('/inscricao/{id}/cidade/nova', 'InscricaoController@adicionarNovaCidade')->name('inscricao.cidade.nova');
Route::post('/inscricao/{id}/clube/novo', 'InscricaoController@adicionarNovoClube')->name('inscricao.clube.novo');
Route::post('/inscricao/{id}/inscricao', 'InscricaoController@adicionarNovaInscricao')->name('inscricao.enviar');



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
});
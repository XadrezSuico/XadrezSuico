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
    return view('welcome');
});

Auth::routes();
Route::get('/register', function () {
    return redirect("/login");
});

Route::post('/register', function () {
    return redirect("/login");
});

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


Route::get('/import/csv', 'ImportController@importCSVEnxadristas')->name('import.csv');

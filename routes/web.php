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
// if(!\App\User::canRegisterWithoutLogin()){
//     Route::get('/register', function(){
//         return redirect("/login");
//     })->name('register');
//     Route::post('/register', function(){
//         return redirect("/login");
//     })->name('register.post');
// }
// Route::get('/wat', function(){
//     echo \App\User::count();
//     foreach(\App\User::All() as $user){
//         echo $user->id;
//     }
// })->name('register.post');


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

Route::group(["prefix"=>"categoria"],function(){
	Route::get('/', 'CategoriaController@index')->name('categoria.index');
	Route::get('/new', 'CategoriaController@new')->name('categoria.new');
	Route::post('/new', 'CategoriaController@new_post')->name('categoria.new.post');
	Route::get('/dashboard/{id}', 'CategoriaController@edit')->name('categoria.dashboard');
	Route::post('/dashboard/{id}', 'CategoriaController@edit_post')->name('categoria.dashboard.post');
	Route::get('/delete/{id}', 'CategoriaController@delete')->name('categoria.delete');
    Route::group(["prefix"=>"{id}/sexo"],function(){
        Route::post('/add', 'CategoriaController@sexo_add')->name('categoria.sexo.add');
        Route::get('/remove/{categoria_sexo_id}', 'CategoriaController@sexo_remove')->name('categoria.sexo.remove');
    });
});

Route::group(["prefix"=>"cidade"],function(){
	Route::get('/', 'CidadeController@index')->name('cidade.index');
	Route::get('/new', 'CidadeController@new')->name('cidade.new');
	Route::post('/new', 'CidadeController@new_post')->name('cidade.new.post');
	Route::get('/edit/{id}', 'CidadeController@edit')->name('cidade.edit');
	Route::post('/edit/{id}', 'CidadeController@edit_post')->name('cidade.edit.post');
	Route::get('/delete/{id}', 'CidadeController@delete')->name('cidade.delete');
});

Route::group(["prefix"=>"clube"],function(){
	Route::get('/', 'ClubeController@index')->name('clube.index');
	Route::get('/new', 'ClubeController@new')->name('clube.new');
	Route::post('/new', 'ClubeController@new_post')->name('clube.new.post');
	Route::get('/edit/{id}', 'ClubeController@edit')->name('clube.edit');
	Route::post('/edit/{id}', 'ClubeController@edit_post')->name('clube.edit.post');
	Route::get('/delete/{id}', 'ClubeController@delete')->name('clube.delete');
});

Route::group(["prefix"=>"enxadrista"],function(){
	Route::get('/', 'EnxadristaController@index')->name('enxadrista.index');
	Route::get('/new', 'EnxadristaController@new')->name('enxadrista.new');
	Route::post('/new', 'EnxadristaController@new_post')->name('enxadrista.new.post');
	Route::get('/edit/{id}', 'EnxadristaController@edit')->name('enxadrista.edit');
	Route::post('/edit/{id}', 'EnxadristaController@edit_post')->name('enxadrista.edit.post');
	Route::get('/delete/{id}', 'EnxadristaController@delete')->name('enxadrista.delete');
});

Route::group(["prefix"=>"sexo"],function(){
	Route::get('/', 'SexoController@index')->name('sexo.index');
	Route::get('/new', 'SexoController@new')->name('sexo.new');
	Route::post('/new', 'SexoController@new_post')->name('sexo.new.post');
	Route::get('/edit/{id}', 'SexoController@edit')->name('sexo.edit');
	Route::post('/edit/{id}', 'SexoController@edit_post')->name('sexo.edit.post');
	Route::get('/delete/{id}', 'SexoController@delete')->name('sexo.delete');
});

Route::group(["prefix"=>"grupoevento"],function(){
	Route::get('/', 'GrupoEventoController@index')->name('grupoevento.index');
	Route::get('/new', 'GrupoEventoController@new')->name('grupoevento.new');
	Route::post('/new', 'GrupoEventoController@new_post')->name('grupoevento.new.post');
	Route::get('/dashboard/{id}', 'GrupoEventoController@edit')->name('grupoevento.dashboard');
	Route::post('/dashboard/{id}', 'GrupoEventoController@edit_post')->name('grupoevento.dashboard.post');
	Route::get('/delete/{id}', 'GrupoEventoController@delete')->name('grupoevento.delete');
    Route::get('/classificar/{id}', 'GrupoEventoController@classificar')->name('grupoevento.classificar');
    Route::get('/classificacao/{id}', 'GrupoEventoPublicoController@classificacao')->name('grupoevento.publico.classificacao');
    Route::get('/{id}/resultados/{categoria_id}', 'GrupoEventoPublicoController@resultados')->name('grupoevento.publico.resultados');
    Route::group(["prefix"=>"{id}/categoria"],function(){
        Route::post('/add', 'GrupoEventoController@categoria_add')->name('grupoevento.categoria.add');
        Route::get('/remove/{categoria_grupo_evento_id}', 'GrupoEventoController@categoria_remove')->name('grupoevento.categoria.remove');
    });
    Route::group(["prefix"=>"{id}/torneiotemplate"],function(){
        Route::post('/add', 'GrupoEventoController@torneio_template_add')->name('grupoevento.torneiotemplate.add');
        Route::get('/remove/{torneio_template_grupo_evento_id}', 'GrupoEventoController@torneio_template_remove')->name('grupoevento.torneiotemplate.remove');
    });
    Route::group(["prefix"=>"{id}/criteriodesempate"],function(){
        Route::post('/add', 'GrupoEventoController@criterio_desempate_add')->name('grupoevento.criteriodesempate.add');
        Route::get('/remove/{cd_grupo_evento_id}', 'GrupoEventoController@criterio_desempate_remove')->name('grupoevento.criteriodesempate.remove');
    });
    Route::group(["prefix"=>"{id}/criteriodesempategeral"],function(){
        Route::post('/add', 'GrupoEventoController@criterio_desempate_geral_add')->name('grupoevento.criteriodesempategeral.add');
        Route::get('/remove/{cd_grupo_evento_geral_id}', 'GrupoEventoController@criterio_desempate_geral_remove')->name('grupoevento.criteriodesempategeral.remove');
    });
    Route::group(["prefix"=>"{id}/pontuacao"],function(){
        Route::post('/add', 'GrupoEventoController@pontuacao_add')->name('grupoevento.pontuacao.add');
        Route::get('/remove/{pontuacao_id}', 'GrupoEventoController@pontuacao_remove')->name('grupoevento.pontuacao.remove');
    });
    Route::group(["prefix"=>"{id}/evento"],function(){
        Route::post('/new', 'GrupoEventoController@evento_new')->name('grupoevento.evento.new');
    });
});

Route::group(["prefix"=>"torneiotemplate"],function(){
	Route::get('/', 'TorneioTemplateController@index')->name('torneiotemplate.index');
	Route::get('/new', 'TorneioTemplateController@new')->name('torneiotemplate.new');
	Route::post('/new', 'TorneioTemplateController@new_post')->name('torneiotemplate.new.post');
	Route::get('/dashboard/{id}', 'TorneioTemplateController@edit')->name('torneiotemplate.dashboard');
	Route::post('/dashboard/{id}', 'TorneioTemplateController@edit_post')->name('torneiotemplate.dashboard.post');
	Route::get('/delete/{id}', 'TorneioTemplateController@delete')->name('torneiotemplate.delete');
    Route::group(["prefix"=>"{id}/categoria"],function(){
        Route::post('/add', 'TorneioTemplateController@categoria_add')->name('torneiotemplate.categoria.add');
        Route::get('/remove/{categoria_torneio_id}', 'TorneioTemplateController@categoria_remove')->name('torneiotemplate.categoria.remove');
    });
});
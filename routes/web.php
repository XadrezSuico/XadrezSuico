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
    return redirect("/login");
});

Auth::routes();
Route::get('/register', function(){
    return redirect("/login");
})->name('register');
Route::post('/register', function(){
    return redirect("/login");
})->name('register.post');

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
Route::get('/cron', 'CronController@index')->name('cron');



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
    Route::get('/visualizar/{id}', 'InscricaoController@visualizar_inscricoes')->name('inscricao.visualizar.inscricao');
    Route::group(["prefix"=>"v2"],function(){
        Route::get('/{id}/busca/enxadrista', 'InscricaoController@telav2_buscaEnxadrista')->name('inscricao.v2.busca.enxadrista');
        Route::get('/{id}/busca/estado/{pais_id}', 'InscricaoController@telav2_buscaEstado')->name('inscricao.v2.busca.estado');
        Route::get('/{id}/busca/cidade/{estados_id}', 'InscricaoController@telav2_buscaCidade')->name('inscricao.v2.busca.cidade');
        Route::get('/{id}/busca/clube', 'InscricaoController@telav2_buscaClube')->name('inscricao.v2.busca.clube');
        Route::get('/{id}/enxadrista/{enxadrista_id}', 'InscricaoController@telav2_buscarDadosEnxadrista')->name('inscricao.v2.enxadrista');
        Route::post('/{id}/inscricao', 'InscricaoController@telav2_adicionarNovaInscricao')->name('inscricao.v2.enviar');
        Route::get('/{id}/inscricao/get/{inscricao_id}', 'InscricaoController@telav2_getInscricaoDados')->name('inscricao.v2.get');
        Route::post('/{id}/inscricao/confirmar', 'InscricaoController@telav2_confirmarInscricao')->name('inscricao.v2.confirmar');
        Route::get('/{id}/inscricao/desconfirmar/{inscricao_id}', 'InscricaoController@telav2_desconfirmarInscricao')->name('inscricao.v2.desconfirmar');
        Route::post('/{id}/enxadrista/novo', 'InscricaoController@telav2_adicionarNovoEnxadrista')->name('inscricao.v2.enxadrista.novo');
        Route::get('/{id}/enxadrista/conferencia/{enxadrista_id}', 'InscricaoController@telav2_conferenciaDados')->name('inscricao.v2.enxadrista.conferencia');
        Route::post('/{id}/enxadrista/atualizacao/{enxadrista_id}', 'InscricaoController@telav2_atualizarEnxadrista')->name('inscricao.v2.enxadrista.atualizacao');
        Route::post('/{id}/cidade/nova', 'InscricaoController@telav2_adicionarNovaCidade')->name('inscricao.v2.cidade.nova');
        Route::post('/{id}/estado/novo', 'InscricaoController@telav2_adicionarNovoEstado')->name('inscricao.v2.estado.novo');
        Route::post('/{id}/clube/novo', 'InscricaoController@telav2_adicionarNovoClube')->name('inscricao.v2.clube.novo');
    });
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
    Route::group(["prefix"=>"{id}/perfis"],function(){
        Route::post('/add', 'UserController@perfil_add')->name('usuario.perfil.add');
        Route::get('/remove/{perfil_users_id}', 'UserController@perfil_remove')->name('usuario.perfil.remove');
    });
});

Route::group(["prefix"=>"evento"],function(){
	Route::get('/dashboard/{id}', 'EventoGerenciarController@edit')->name('evento.dashboard');
	Route::post('/dashboard/{id}', 'EventoGerenciarController@edit_post')->name('evento.dashboard.post');
	Route::post('/{id}/pagina', 'EventoGerenciarController@edit_pagina_post')->name('evento.dashboard.pagina.post');
    Route::get('/delete/{id}', 'EventoGerenciarController@delete')->name('evento.delete');
    Route::get('/classificar/{id}', 'EventoGerenciarController@classificar')->name('evento.classificar');
    Route::get('/classificacao/{id}', 'EventoController@classificacao')->name('evento.classificacao');
    Route::get('/{id}/resultados/{categoria_id}', 'EventoController@resultados')->name('evento.resultados');
    Route::get('/{id}/toggleresultados', 'EventoGerenciarController@toggleMostrarClassificacao')->name('evento.toggleMostrarClassificacao');
    Route::get('/{id}/toggleclassificavel', 'EventoGerenciarController@toggleEventoClassificavel')->name('evento.toggleEventoClassificavel');
    Route::get('/{id}/togglemanual', 'EventoGerenciarController@toggleClassificacaoManual')->name('evento.toggleClassificacaoManual');
    Route::get('/classificacao/{id}/interno', 'EventoGerenciarController@classificacao')->name('evento.classificacao.interno');
    Route::get('/{id}/resultados/{categoria_id}/interno', 'EventoGerenciarController@resultados')->name('evento.resultados.interno');
	Route::get('/{id}/inscricoes/list', 'EventoGerenciarController@visualizar_inscricoes')->name('evento.inscricoes.list');
	Route::get('/{id}/enxadristas/sm', 'EventoGerenciarController@downloadListaManagerParaEvento')->name('evento.enxadristas.sm');
    


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
	    Route::get('/new', 'TorneioController@new')->name('evento.torneios.new');
	    Route::post('/new', 'TorneioController@new_post')->name('evento.torneios.new.post');
	    Route::get('/edit/{torneio_id}', 'TorneioController@edit')->name('evento.torneios.edit');
	    Route::post('/edit/{torneio_id}', 'TorneioController@edit_post')->name('evento.torneios.edit.post');
	    Route::get('/union/{torneio_id}', 'TorneioController@union')->name('evento.torneios.union');
	    Route::post('/union/{torneio_id}', 'TorneioController@union_post')->name('evento.torneios.union.post');
	    Route::get('/delete/{torneio_id}', 'TorneioController@delete')->name('evento.torneios.delete');
	    Route::get('/{torneio_id}/resultados', 'TorneioController@formResults')->name('evento.torneios.resultados');
	    Route::post('/{torneio_id}/resultados', 'TorneioController@sendResultsTxt')->name('evento.torneios.resultados.post');
        
        Route::group(["prefix"=>"{torneio_id}/categoria"],function(){
            Route::post('/add', 'TorneioController@categoria_add')->name('evento.torneios.categoria.add');
            Route::get('/remove/{categoria_torneio_id}', 'TorneioController@categoria_remove')->name('evento.torneios.categoria.remove');
        });
        Route::group(["prefix"=>"{torneio_id}/inscricoes"],function(){
	        Route::get('/', 'InscricaoGerenciarController@index')->name('evento.torneios.inscricoes.index');
            Route::get('/edit/{inscricao_id}', 'InscricaoGerenciarController@edit')->name('evento.torneios.inscricao.edit');
            Route::post('/edit/{inscricao_id}', 'InscricaoGerenciarController@edit_post')->name('evento.torneios.inscricao.edit.post');
            Route::get('/unconfirm/{inscricao_id}', 'InscricaoGerenciarController@unconfirm')->name('evento.torneios.inscricao.unconfirm');
            Route::get('/delete/{inscricao_id}', 'InscricaoGerenciarController@delete')->name('evento.torneios.inscricao.delete');
	        Route::get('/sm', 'InscricaoGerenciarController@list_to_manager')->name('evento.torneios.inscricoes.sm');
	        Route::get('/relatorio/inscricoes', 'InscricaoGerenciarController@report_list_subscriptions')->name('evento.torneios.inscricoes.relatorio.inscritos');
	        Route::get('/relatorio/inscricoes/alfabetico', 'InscricaoGerenciarController@report_list_subscriptions_alf')->name('evento.torneios.inscricoes.relatorio.inscritos.alf');
	        Route::get('/relatorio/inscricoes/alfabetico/cidade', 'InscricaoGerenciarController@report_list_subscriptions_cidade_alf')->name('evento.torneios.inscricoes.relatorio.inscritos.alf.cidade');
	        Route::get('/sm/all', 'InscricaoGerenciarController@list_to_manager_all')->name('evento.torneios.inscricoes.sm.all');
        });
    });


    Route::group(["prefix"=>"{id}/categoria"],function(){
        Route::post('/add', 'EventoGerenciarController@categoria_add')->name('evento.categoria.add');
        Route::get('/remove/{categoria_evento_id}', 'EventoGerenciarController@categoria_remove')->name('evento.categoria.remove');
    });
    
    Route::group(["prefix"=>"{evento_id}/categorias"],function(){
        Route::get('/', 'CategoriaEventoController@index')->name('evento.categorias.index');
        Route::get('/new', 'CategoriaEventoController@new')->name('evento.categorias.new');
        Route::post('/new', 'CategoriaEventoController@new_post')->name('evento.categorias.new.post');
        Route::get('/dashboard/{id}', 'CategoriaEventoController@edit')->name('evento.categorias.dashboard');
        Route::post('/dashboard/{id}', 'CategoriaEventoController@edit_post')->name('evento.categorias.dashboard.post');
        Route::get('/delete/{id}', 'CategoriaEventoController@delete')->name('evento.categorias.delete');
        Route::group(["prefix"=>"{id}/sexo"],function(){
            Route::post('/add', 'CategoriaEventoController@sexo_add')->name('evento.categorias.sexo.add');
            Route::get('/remove/{categoria_sexo_id}', 'CategoriaEventoController@sexo_remove')->name('evento.categorias.sexo.remove');
        });
    });
    
    Route::group(["prefix"=>"{evento_id}/campos"],function(){
        Route::post('/new', 'CampoPersonalizadoEventoController@new_post')->name('evento.campos.new.post');
        Route::get('/dashboard/{id}', 'CampoPersonalizadoEventoController@dashboard')->name('evento.campos.dashboard');
        Route::post('/dashboard/{id}', 'CampoPersonalizadoEventoController@edit_post')->name('evento.campos.dashboard.post');
        Route::get('/delete/{id}', 'CampoPersonalizadoEventoController@delete')->name('evento.campos.delete');
        Route::group(["prefix"=>"{id}/opcao"],function(){
            Route::post('/add', 'CampoPersonalizadoEventoController@opcao_add')->name('evento.campos.opcao.add');
            Route::get('/remove/{opcaos_id}', 'CampoPersonalizadoEventoController@opcao_remove')->name('evento.campos.opcao.remove');
        });
    });
});


Route::group(["prefix"=>"estado"],function(){
	Route::get('/search/{pais_id}', 'EstadoController@buscaEstado')->name('estado.search');
});

Route::group(["prefix"=>"cidade"],function(){
	Route::get('/', 'CidadeController@index')->name('cidade.index');
	Route::get('/new', 'CidadeController@new')->name('cidade.new');
	Route::post('/new', 'CidadeController@new_post')->name('cidade.new.post');
	Route::get('/edit/{id}', 'CidadeController@edit')->name('cidade.edit');
	Route::post('/edit/{id}', 'CidadeController@edit_post')->name('cidade.edit.post');
	Route::get('/delete/{id}', 'CidadeController@delete')->name('cidade.delete');
	Route::get('/search/{estados_id}', 'CidadeController@buscaCidade')->name('cidade.search');
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
	Route::get('/download', 'EnxadristaController@downloadBaseCompleta')->name('enxadrista.download');
	Route::get('/splits', 'EnxadristaController@updateAllnames')->name('enxadrista.splits');
	Route::get('/api/searchList', 'EnxadristaController@searchEnxadristasList')->name('enxadrista.api.list');
    Route::group(["prefix"=>"{id}/documentos"],function(){
	    Route::get('/getDocumento/{tipo_documento_id}', 'DocumentoController@getDocumento')->name('enxadrista.documentos.getDocumento');
    });
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
    Route::get('/classificar/{id}', 'GrupoEventoController@classificar_page')->name('grupoevento.classificar');
    Route::get('/classificar/{id}/call/{categoria_id}/{action}', 'GrupoEventoController@classificar_call')->name('grupoevento.classificar.call');
    Route::get('/classificacao/{id}', 'GrupoEventoPublicoController@classificacao')->name('grupoevento.publico.classificacao');
    Route::get('/{id}/resultados/enxadrista/{enxadrista_id}', 'GrupoEventoPublicoController@verPontuacaoEnxadrista')->name('grupoevento.publico.verPontuacaoEnxadrista');
    Route::get('/{id}/resultados/{categoria_id}', 'GrupoEventoPublicoController@resultados')->name('grupoevento.publico.resultados');
    Route::group(["prefix"=>"{id}/categoria"],function(){
        Route::post('/add', 'GrupoEventoController@categoria_add')->name('grupoevento.categoria.add');
        Route::get('/remove/{categoria_grupo_evento_id}', 'GrupoEventoController@categoria_remove')->name('grupoevento.categoria.remove');
    });
    Route::group(["prefix"=>"{grupo_evento_id}/categorias"],function(){
        Route::get('/', 'CategoriaGrupoEventoController@index')->name('grupoevento.categorias.index');
        Route::get('/new', 'CategoriaGrupoEventoController@new')->name('grupoevento.categorias.new');
        Route::post('/new', 'CategoriaGrupoEventoController@new_post')->name('grupoevento.categorias.new.post');
        Route::get('/dashboard/{id}', 'CategoriaGrupoEventoController@edit')->name('grupoevento.categorias.dashboard');
        Route::post('/dashboard/{id}', 'CategoriaGrupoEventoController@edit_post')->name('grupoevento.categorias.dashboard.post');
        Route::get('/delete/{id}', 'CategoriaGrupoEventoController@delete')->name('grupoevento.categorias.delete');
        Route::group(["prefix"=>"{id}/sexo"],function(){
            Route::post('/add', 'CategoriaGrupoEventoController@sexo_add')->name('grupoevento.categorias.sexo.add');
            Route::get('/remove/{categoria_sexo_id}', 'CategoriaGrupoEventoController@sexo_remove')->name('grupoevento.categorias.sexo.remove');
        });
    });
    Route::group(["prefix"=>"{grupo_evento_id}/campos"],function(){
        Route::post('/new', 'CampoPersonalizadoGrupoEventoController@new_post')->name('grupoevento.campos.new.post');
        Route::get('/dashboard/{id}', 'CampoPersonalizadoGrupoEventoController@dashboard')->name('grupoevento.campos.dashboard');
        Route::post('/dashboard/{id}', 'CampoPersonalizadoGrupoEventoController@edit_post')->name('grupoevento.campos.dashboard.post');
        Route::get('/delete/{id}', 'CampoPersonalizadoGrupoEventoController@delete')->name('grupoevento.campos.delete');
        Route::group(["prefix"=>"{id}/opcao"],function(){
            Route::post('/add', 'CampoPersonalizadoGrupoEventoController@opcao_add')->name('grupoevento.campos.opcao.add');
            Route::get('/remove/{opcaos_id}', 'CampoPersonalizadoGrupoEventoController@opcao_remove')->name('grupoevento.campos.opcao.remove');
        });
    });
    Route::group(["prefix"=>"{id}/torneiotemplate"],function(){
        Route::post('/add', 'GrupoEventoController@torneio_template_add')->name('grupoevento.torneiotemplate.add');
        Route::get('/remove/{torneio_template_grupo_evento_id}', 'GrupoEventoController@torneio_template_remove')->name('grupoevento.torneiotemplate.remove');
    });
    Route::group(["prefix"=>"{grupo_evento_id}/torneiotemplates"],function(){
        Route::post('/new', 'TorneioTemplateController@new_post')->name('torneiotemplate.new.post');
        Route::get('/dashboard/{id}', 'TorneioTemplateController@edit')->name('torneiotemplate.dashboard');
        Route::post('/dashboard/{id}', 'TorneioTemplateController@edit_post')->name('torneiotemplate.dashboard.post');
        Route::get('/delete/{id}', 'TorneioTemplateController@delete')->name('torneiotemplate.delete');
        Route::group(["prefix"=>"{id}/categoria"],function(){
            Route::post('/add', 'TorneioTemplateController@categoria_add')->name('torneiotemplate.categoria.add');
            Route::get('/remove/{categoria_torneio_id}', 'TorneioTemplateController@categoria_remove')->name('torneiotemplate.categoria.remove');
        });
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


Route::group(["prefix"=>"tiporating"],function(){
	Route::get('/', 'TipoRatingController@index')->name('tiporating.index');
	Route::get('/new', 'TipoRatingController@new')->name('tiporating.new');
	Route::post('/new', 'TipoRatingController@new_post')->name('tiporating.new.post');
	Route::get('/dashboard/{id}', 'TipoRatingController@dashboard')->name('tiporating.dashboard');
	Route::post('/dashboard/{id}', 'TipoRatingController@dashboard_post')->name('tiporating.dashboard.post');
	Route::get('/delete/{id}', 'TipoRatingController@delete')->name('tiporating.delete');
    Route::group(["prefix"=>"{id}/regra"],function(){
        Route::post('/add', 'TipoRatingController@regra_add')->name('tiporating.regra.add');
        Route::get('/remove/{tipo_rating_regra_id}', 'TipoRatingController@regra_remove')->name('tiporating.regra.remove');
    });
});

Route::group(["prefix"=>"tipodocumento"],function(){
    Route::get('/searchByPais/{pais_id}', 'TipoDocumentoPaisController@getTiposDocumento')->name('tipodocumento.buscaPorPais');
});



Route::group(["prefix"=>"update"],function(){
    Route::get('/cbx/rating', 'CBXRatingController@updateRatings')->name('update.cbx.rating');
    Route::get('/fide/rating', 'FIDERatingController@updateRatings')->name('update.fide.rating');
    Route::get('/lbx/rating', 'LBXRatingController@updateRatings')->name('update.lbx.rating');
});



Route::get('/whatsnew', 'WhatsNewController@index')->name('whatsnew');


Route::get('/politicadeprivacidade', function () {
    return view("politicadeprivacidade");
})->name('politicadeprivacidade');

Route::get('/termosdeuso', function () {
    return view("termosdeuso");
})->name('termosdeuso');



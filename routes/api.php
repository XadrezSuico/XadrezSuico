<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(["prefix"=>"v1"],function(){
    Route::group(["prefix"=>"xadrezsuicopag"],function(){
        Route::post('/callback/{uuid}', 'External\XadrezSuicoPagController@notification')->name('api.v1.xadrezsuicopag.callback');
    });

    Route::group(["prefix"=>"event"],function(){
        Route::get('/get/{uuid}', 'API\Event\EventController@get')->name('api.v1.event.get');
        Route::post('/register/{uuid}', 'API\Event\RegisterController@register')->name('api.v1.event.register');
        Route::get('/register/list/{uuid}', 'API\Event\RegisterController@list')->name('api.v1.event.list');
        Route::get('/banner/{uuid}', 'API\Event\BannerController@get')->name('api.v1.event.banner');
        Route::group(["prefix"=>"{uuid}/players"],function(){
            Route::get('/search', 'API\Event\PlayerController@search')->name('api.v1.event.players.search');
            Route::get('/get/{id}', 'API\Event\PlayerController@get')->name('api.v1.event.players.get');
            Route::post('/complete/{id}', 'API\Event\PlayerController@complete')->name('api.v1.event.players.complete');
        });
    });


    Route::group(["prefix"=>"page"],function(){
        Route::get('/get/{uuid}', 'API\Page\PageController@get')->name('api.v1.page.get');
    });

    Route::group(["prefix"=>"player"],function(){
        Route::get('/list', 'API\Player\PlayerController@list')->name('api.v1.player.search');
        Route::group(["prefix"=>"registration"],function(){
            Route::post('/check', 'API\Player\PlayerRegistrationController@checkExists')->name('api.v1.player.registration.check');
            Route::post('/register', 'API\Player\PlayerRegistrationController@register')->name('api.v1.player.registration.register');
        });
    });


    Route::group(["prefix"=>"sexes"],function(){
        Route::get('/list', 'API\Sex\SexController@list')->name('api.v1.sex.sex.list');
    });

    Route::group(["prefix"=>"location"],function(){
        Route::group(["prefix"=>"country"],function(){
            Route::get('/list', 'API\Location\CountryController@list')->name('api.v1.location.country.list');
            Route::get('/get/{id}', 'API\Location\CountryController@get')->name('api.v1.location.country.get');
        });
        Route::group(["prefix"=>"state"],function(){
            Route::get('/list/{country_id}', 'API\Location\StateController@list')->name('api.v1.location.state.list');
            Route::get('/get/{id}', 'API\Location\StateController@get')->name('api.v1.location.state.get');
        });
        Route::group(["prefix"=>"city"],function(){
            Route::get('/list/{state_id}', 'API\Location\CityController@list')->name('api.v1.location.city.list');
            Route::get('/get/{id}', 'API\Location\CityController@get')->name('api.v1.location.city.get');
        });
    });


    Route::group(["prefix"=>"document"],function(){
        Route::group(["prefix"=>"document_types"],function(){
            Route::get('/list/{country_id}', 'API\Document\DocumentTypeController@list')->name('api.v1.document.document_type.list');
        });
    });

    Route::group(["prefix"=>"club"],function(){
        Route::get('/search', 'API\ClubController@search')->name('api.v1.clubs.search');
        Route::get('/get/{id}', 'API\ClubController@get')->name('api.v1.clubs.get');
    });
    Route::get('/defaults', 'API\DefaultController@default')->name('api.v1.defaults.defaults');
});

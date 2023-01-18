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
        Route::group(["prefix"=>"clubs"],function(){
            Route::get('/search', 'API\Event\ClubController@search')->name('api.v1.event.clubs.search');
            Route::get('/get/{id}', 'API\Event\ClubController@get')->name('api.v1.event.clubs.get');
        });
        Route::group(["prefix"=>"cities"],function(){
            Route::group(["prefix"=>"country"],function(){
                Route::get('/list', 'API\Event\City\CountryController@list')->name('api.v1.event.cities.country.list');
                Route::get('/get/{id}', 'API\Event\City\CountryController@get')->name('api.v1.event.cities.country.get');
            });
            Route::group(["prefix"=>"state"],function(){
                Route::get('/list/{country_id}', 'API\Event\City\StateController@list')->name('api.v1.event.cities.state.list');
                Route::get('/get/{id}', 'API\Event\City\StateController@get')->name('api.v1.event.cities.state.get');
            });
            Route::group(["prefix"=>"city"],function(){
                Route::get('/list/{state_id}', 'API\Event\City\CityController@list')->name('api.v1.event.cities.city.list');
                Route::get('/get/{id}', 'API\Event\City\CityController@get')->name('api.v1.event.cities.city.get');
            });
        });
        Route::group(["prefix"=>"sexes"],function(){
            Route::get('/list', 'API\Event\SexController@list')->name('api.v1.event.sex.list');
        });
        Route::group(["prefix"=>"document_types"],function(){
            Route::get('/list/{country_id}', 'API\Event\DocumentTypeController@list')->name('api.v1.event.document.list');
        });
    });
});

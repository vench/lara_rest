<?php
/**
 * Created by PhpStorm.
 * User: vench
 * Date: 21.08.17
 * Time: 11:12
 */

namespace LpRest;

use Illuminate\Support\ServiceProvider;

class RestServiceProvider extends ServiceProvider
{


    public function boot()
    {

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->registerRoute();


    }


    /**
     *
     */
    protected function registerRoute() {

        $optionGroup = [];

        Route::group($optionGroup, function($router){

            Route::post('rest/multi', '\LpRest\Controllers\CommonController@multi');

            Route::get('rest/{modelName}/{relations?}', '\LpRest\Controllers\CommonController@all')->where([
                'relations' => '^[A-Za-z][A-Za-z0-9\/]+',
                'modelName' => '^[A-Za-z][A-Za-z0-9]+',
            ]);
            Route::get('rest/{modelName}/{id}/{relations?}', '\LpRest\Controllers\\CommonController@one')->where([
                'id'        => '[0-9]+',
                'relations' => '^[A-Za-z][A-Za-z0-9\/]+',
                'modelName' => '^[A-Za-z][A-Za-z0-9]+',
            ]);
            Route::delete('rest/{modelName}/{id}', '\LpRest\Controllers\\CommonController@delete')->where([
                'id'        => '[0-9]+',
                'modelName' => '^[A-Za-z][A-Za-z0-9]+',
            ]);
            Route::post('rest/{modelName}', '\LpRest\Controllers\\CommonController@create');
            Route::put('rest/{modelName}/{id}', '\LpRest\Controllers\\CommonController@update')->where([
                'id'        => '[0-9]+',
                'modelName' => '^[A-Za-z][A-Za-z0-9]+',
            ]);

            Route::post('rest/{modelName}/{id}/{methodName}',  '\LpRest\Controllers\\CommonController@call')->where([
                'id'        => '[0-9]+',
                'modelName' => '^[A-Za-z][A-Za-z0-9]+',
                'methodName' => '^[A-Za-z][A-Za-z0-9]+',
            ]);
        });
    }
}
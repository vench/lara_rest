<?php
/**
 * Created by PhpStorm.
 * User: vench
 * Date: 21.08.17
 * Time: 12:57
 */

namespace LpRest;

use Illuminate\Support\ServiceProvider;
use LpRest\Repositories\CommonRepositoryModelProvider;
use LpRest\Repositories\CommonRepositoryModelProviderBase;
use LpRest\Repositories\CommonRepositoryAccessProvider;
use LpRest\Repositories\CommonRepositoryAccessProviderBase;
use LpRest\Controllers\CommonResponse;
use LpRest\Controllers\CommonResponseBase;
use LpRest\Repositories\RepositoryProvider;
use LpRest\Repositories\RepositoryProviderBase;
use Route;

/**
 * Class RestServiceProviderLaravel
 * @package LpRest
 */
class RestServiceProviderLaravel extends ServiceProvider
{
    /**
     * @var RestServiceHelper
     */
    private $restServiceHelper;

    /**
     *
     */
    public function boot()
    {

        $this->restServiceHelper = $this->app->make(RestServiceHelper::class);

        $this->registerRoute();

        $this->handleMigrations();

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->app->bindIf(CommonRepositoryModelProvider::class,
            CommonRepositoryModelProviderBase::class, true);

        $this->app->bindIf(CommonRepositoryAccessProvider::class,
            CommonRepositoryAccessProviderBase::class, true);

        $this->app->bindIf(CommonResponse::class,
            CommonResponseBase::class, true);

        $this->app->bindIf(RepositoryProvider::class,
            RepositoryProviderBase::class, true);

    }

    /**
     *
     */
    public function registerRoute() {

        $optionGroup = array_merge([
            'prefix'        => 'rest',
            'middleware'    => [],
        ], $this->restServiceHelper->getRouteGroupOptions() );


        Route::group($optionGroup, function () {

            //rest api
            Route::post('multi', '\LpRest\Controllers\CommonController@multi');
            Route::get('{modelName}/{relations?}', '\LpRest\Controllers\CommonController@all')->where([
                'relations' => '^[A-Za-z][A-Za-z0-9\/]+',
                'modelName' => '^[A-Za-z][A-Za-z0-9]+',
            ]);
            Route::get('{modelName}/{id}/{relations?}', '\LpRest\Controllers\CommonController@one')->where([
                'id'        => '[0-9]+',
                'relations' => '^[A-Za-z][A-Za-z0-9\/]+',
                'modelName' => '^[A-Za-z][A-Za-z0-9]+',
            ]);
            Route::delete('{modelName}/{id}', '\LpRest\Controllers\CommonController@delete')->where([
                'id'        => '[0-9]+',
                'modelName' => '^[A-Za-z][A-Za-z0-9]+',
            ]);
            Route::post('{modelName}', '\LpRest\Controllers\CommonController@create');
            Route::put('{modelName}/{id}', '\LpRest\Controllers\CommonController@update')->where([
                'id'        => '[0-9]+',
                'modelName' => '^[A-Za-z][A-Za-z0-9]+',
            ]);
            Route::post('{modelName}/{id}/{methodName}',  '\LpRest\Controllers\CommonController@call')->where([
                'id'        => '[0-9]+',
                'modelName' => '^[A-Za-z][A-Za-z0-9]+',
                'methodName' => '^[A-Za-z][A-Za-z0-9]+',
            ]);
        });
    }


    /**
     *
     */
    private function handleMigrations() {
        $this->loadMigrationsFrom([
            __DIR__ . '/migrations',
        ]);
    }

}
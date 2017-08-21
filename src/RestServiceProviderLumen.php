<?php
/**
 * Created by PhpStorm.
 * User: vench
 * Date: 21.08.17
 * Time: 11:12
 */

namespace LpRest;

use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application;
use LpRest\Repositories\CommonRepositoryModelProvider;
use LpRest\Repositories\CommonRepositoryModelProviderBase;
use LpRest\Repositories\CommonRepositoryAccessProvider;
use LpRest\Repositories\CommonRepositoryAccessProviderBase;
use LpRest\Controllers\CommonResponse;
use LpRest\Controllers\CommonResponseBase;


class RestServiceProviderLumen extends ServiceProvider
{

    /**
     * @var RestServiceHelper
     */
    private $restServiceHelper;

    public function boot()
    {

        $this->restServiceHelper = $this->app->make(RestServiceHelper::class);

        $this->registerRoute();

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
    }





    /**
     * For Lumen
     */
    protected function registerRoute() {
        /* @var $app \Laravel\Lumen\Application */
        $app = $this->app;

        $optionGroup = array_merge([
            'prefix'    => 'rest',
        ], $this->restServiceHelper->getRouteGroupOptions() );

        $app->group($optionGroup, function(Application $router){

            //all
            $router->get('{modelName:[A-Za-z][A-Za-z0-9]+}',
                '\LpRest\Controllers\CommonController@all');
            $router->get('{modelName:[A-Za-z][A-Za-z0-9]+}/{relations:[A-Za-z][A-Za-z0-9\/]+}',
                '\LpRest\Controllers\CommonController@all');

            //multi
            $router->post('multi', '\LpRest\Controllers\CommonController@multi');

            //one
            $router->get('{modelName:[A-Za-z][A-Za-z0-9]+}/{id:[0-9]+}',
                '\LpRest\Controllers\\CommonController@one');
            $router->get('{modelName:[A-Za-z][A-Za-z0-9]+}/{id:[0-9]+}/{relations:[A-Za-z][A-Za-z0-9\/]+}',
                '\LpRest\Controllers\\CommonController@one');

            //delete
            $router->delete('{modelName:[A-Za-z][A-Za-z0-9]+}/{id:[0-9]+}',
                '\LpRest\Controllers\\CommonController@delete');

            //create
            $router->post('{modelName:^[A-Za-z][A-Za-z0-9]+}',
                '\LpRest\Controllers\\CommonController@create');

            //
            $router->put('{modelName:^[A-Za-z][A-Za-z0-9]+}/{id:[0-9]+}',
                '\LpRest\Controllers\\CommonController@update');

            //call
            $router->post('{modelName:^[A-Za-z][A-Za-z0-9]+}/{id:[0-9]+}/{methodName:^[A-Za-z][A-Za-z0-9]+}',
                '\LpRest\Controllers\\CommonController@call');
        });
    }
}
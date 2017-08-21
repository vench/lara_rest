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


class RestServiceProviderLumen extends ServiceProvider
{


    public function boot()
    {
        $this->registerRoute();

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton(CommonRepositoryModelProvider::class, CommonRepositoryModelProviderBase::class);
        $this->app->singleton(CommonRepositoryAccessProvider::class, CommonRepositoryAccessProviderBase::class);
    }


    public function setRouteGroupOptions($optionGroup) {
        var_dump($optionGroup); exit();
    }


    /**
     * For Lumen
     */
    protected function registerRoute() {
        /* @var $app \Laravel\Lumen\Application */
        $app = $this->app;

        $optionGroup = [];

        $app->group($optionGroup, function(Application $router){

            //all
            $router->get('rest/{modelName:[A-Za-z][A-Za-z0-9]+}',
                '\LpRest\Controllers\CommonController@all');
            $router->get('rest/{modelName:[A-Za-z][A-Za-z0-9]+}/{relations:[A-Za-z][A-Za-z0-9\/]+}',
                '\LpRest\Controllers\CommonController@all');

            //multi
            $router->post('rest/multi', '\LpRest\Controllers\CommonController@multi');

            //one
            $router->get('rest/{modelName:[A-Za-z][A-Za-z0-9]+}/{id:[0-9]+}',
                '\LpRest\Controllers\\CommonController@one');
            $router->get('rest/{modelName:[A-Za-z][A-Za-z0-9]+}/{id:[0-9]+}/{relations:[A-Za-z][A-Za-z0-9\/]+}',
                '\LpRest\Controllers\\CommonController@one');

            //delete
            $router->delete('rest/{modelName:[A-Za-z][A-Za-z0-9]+}/{id:[0-9]+}',
                '\LpRest\Controllers\\CommonController@delete');

            //create
            $router->post('rest/{modelName:^[A-Za-z][A-Za-z0-9]+}',
                '\LpRest\Controllers\\CommonController@create');

            //
            $router->put('rest/{modelName:^[A-Za-z][A-Za-z0-9]+}/{id:[0-9]+}',
                '\LpRest\Controllers\\CommonController@update');

            //call
            $router->post('rest/{modelName:^[A-Za-z][A-Za-z0-9]+}/{id:[0-9]+}/{methodName:^[A-Za-z][A-Za-z0-9]+}',
                '\LpRest\Controllers\\CommonController@call');
        });
    }
}
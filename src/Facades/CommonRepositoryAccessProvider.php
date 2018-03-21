<?php

namespace LpRest\Facades;

use Illuminate\Support\Facades\Facade;
use LpRest\Repositories\CommonRepositoryAccessProvider as AccessProviderContract;

/**
 * Class CommonRepositoryAccessProvider
 * @package LpRest\Facades
 */
class CommonRepositoryAccessProvider extends Facade
{

    /**
     * @return string
     */
    protected static function getFacadeAccessor() {
        return AccessProviderContract::class;
    }
}
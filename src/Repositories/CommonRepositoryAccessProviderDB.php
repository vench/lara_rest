<?php
/**
 * Created by PhpStorm.
 * User: vench
 * Date: 13.03.18
 * Time: 11:59
 */

namespace LpRest\Repositories;

use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\Access\Authorizable;
use LpRest\Models\RestAccessUser;

/**
 * Class CommonRepositoryAccessProviderDB
 * @package LpRest\Repositories
 */
class CommonRepositoryAccessProviderDB implements CommonRepositoryAccessProvider
{



    /**
     * @param Authorizable $authorizable
     * @return bool
     */
    public function checkAccess(string $accessName): bool
    {
        if(Auth::guest()) {
            return false;
        }

        $id = Auth::id();

        $model = RestAccessUser::query()
            ->where('user_outer', $id)->first();
        if(is_null($model)) {
            return false;
        }

        return true;
    }
}
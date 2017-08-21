<?php
/**
 * Created by PhpStorm.
 * User: vench
 * Date: 21.08.17
 * Time: 14:58
 */

namespace LpRest\Repositories;


class CommonRepositoryAccessProviderBase implements CommonRepositoryAccessProvider
{

    /**
     * @param string $accessName
     * @return bool
     */
    public function checkAccess(string $accessName): bool
    {
        $user = \Auth::user();
        return !is_null($user) ? $user->can($accessName) : false;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: vench
 * Date: 21.08.17
 * Time: 14:58
 */

namespace LpRest\Repositories;

use Illuminate\Contracts\Auth\Access\Authorizable;


interface CommonRepositoryAccessProvider
{

    /**
     * @param Authorizable $authorizable
     * @return bool
     */
    public function checkAccess(string $accessName):bool;
}
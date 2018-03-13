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
     * @var array
     */
    protected $stored = [];

    /**
     * @param string $accessName
     * @return bool
     */
    public function checkAccess(string $accessName): bool
    {
        if(Auth::guest()) {
            return false;
        }

        $id = Auth::id();

        if(!isset($this->stored[$id][$accessName])) {
            $this->stored[$id][$accessName] = $this->checkAccessByUserId($accessName, $id);
        }

        return $this->stored[$id][$accessName];
    }


    /**
     * @param string $accessName
     * @param int $id
     * @return bool
     */
    public function checkAccessByUserId(string $accessName, int $id): bool {
        $models = RestAccessUser::query()
            ->where('user_outer', $id)->get();
        if(!empty($models)) {

            foreach ($models as $model) {
                if($model->checkAccess($accessName)) {
                    return true;
                }
            }
        }

        return false;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: vench
 * Date: 21.08.17
 * Time: 11:06
 */

namespace LpRest\Repositories;

/**
 * Interface CommonRepositoryModel
 * @package LpRest\Repositories
 */
interface CommonRepositoryModel
{

    const ACTION_ALL = 'all';

    const ACTION_ONE = 'one';

    const ACTION_DELETE = 'delete';

    const ACTION_CREATE = 'create';

    const ACTION_UPDATE = 'update';

    const ACTION_CALL = 'call';


    /**
     * @param array $merge
     * @return array
     */
    public function getRestRules($merge = []);

    /**
     * @return array
     */
    public function getRestCustomAttributeNames();

    /**
     * Use ACTION_*
     * @return array
     */
    public function getRestAccessPermissionAliases();

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    public function getRestQuery();
}
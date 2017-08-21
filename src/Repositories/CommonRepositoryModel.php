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
     * @return array
     */
    public function getRestAccessPermissionAliases();

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    public function getRestQuery();
}
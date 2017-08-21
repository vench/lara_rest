<?php
/**
 * Created by PhpStorm.
 * User: vench
 * Date: 21.08.17
 * Time: 11:06
 */

namespace LpRest\Repositories;


interface CommonRepositoryModel
{

    /**
     * @param array $merge
     * @return array
     */
    public static function rules($merge = []);

    /**
     * @return array
     */
    public static function getCustomAttributeNames();

    /**
     * @return array
     */
    public static function getAccessPermissionAliases();

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    public static function query();
}
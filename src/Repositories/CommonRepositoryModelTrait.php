<?php
/**
 * Created by PhpStorm.
 * User: vench
 * Date: 12.03.18
 * Time: 15:37
 */

namespace LpRest\Repositories;


/**
 * Trait CommonRepositoryModelTrait
 * @package LpRest\Repositories
 */
trait CommonRepositoryModelTrait
{



    /**
     * @param array $merge
     * @return array
     */
    public function getRestRules($merge = [])
    {
        return $merge;
    }

    /**
     * @return array
     */
    public function getRestCustomErrorMessages()
    {
        return [];
    }

    /**
     * Use ACTION_*
     * @return array
     */
    public function getRestAccessPermissionAliases()
    {
        $className = $this->getBaseActionNameByClass(__CLASS__);
        return [
            CommonRepositoryModel::ACTION_ALL    => $className . '@' . CommonRepositoryModel::ACTION_ALL,
            CommonRepositoryModel::ACTION_ONE    => $className . '@' . CommonRepositoryModel::ACTION_ONE,
            CommonRepositoryModel::ACTION_DELETE => $className . '@' . CommonRepositoryModel::ACTION_DELETE,
            CommonRepositoryModel::ACTION_UPDATE => $className . '@' . CommonRepositoryModel::ACTION_UPDATE,
            CommonRepositoryModel::ACTION_CREATE => $className . '@' . CommonRepositoryModel::ACTION_CREATE,
            CommonRepositoryModel::ACTION_CALL   => $className . '@' . CommonRepositoryModel::ACTION_CALL,
        ];

    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    public function getRestQuery()
    {
        return static::query();
    }

    /**
     * @param string|object $klass
     * @return string
     */
    protected  function getBaseActionNameByClass($klass)
    {
        $klass = is_object($klass) ? get_class($klass) : $klass;
        return strtolower(basename(str_replace('\\', '/', $klass)));
    }

}
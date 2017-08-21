<?php
/**
 * Created by PhpStorm.
 * User: vench
 * Date: 21.08.17
 * Time: 12:42
 */

namespace LpRest\Repositories;


interface CommonRepositoryModelProvider
{

    /**
     * @param string $name
     * @return CommonRepositoryModel
     */
    public function getModelByName(string $name):CommonRepositoryModel;

    /**
     * @param string $alias
     * @param string $modelName
     * @return void
     */
    public function addModelAliases(string $alias, string $modelName);
}
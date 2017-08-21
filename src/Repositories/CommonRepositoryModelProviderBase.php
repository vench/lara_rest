<?php
/**
 * Created by PhpStorm.
 * User: vench
 * Date: 21.08.17
 * Time: 12:43
 */

namespace LpRest\Repositories;


class CommonRepositoryModelProviderBase implements CommonRepositoryModelProvider
{
    /**
     * @var array
     */
    protected static $listModelAliases = [];


    /**
     * @param string $alias
     * @param string $modelName
     */
    public function addModelAliases(string $alias, string $modelName) {
        self::$listModelAliases[$alias] = $modelName;
    }


    /**
     * @param string $modelName
     * @return CommonRepositoryModel
     * @throws \Exception
     */
    public function getModelByName(string $modelName):CommonRepositoryModel {
        $modelNameLower = strtolower($modelName);

        if (!empty(self::$listModelAliases[$modelNameLower])) {
            $reflectionName = self::$listModelAliases[$modelNameLower];
        } else {
            $reflectionName = '\App\Models\\' . ucfirst($modelName);
        }

        if (!class_exists($reflectionName)) {
            throw new \Exception("Class {$modelName} not found");
        }

        $ref = new \ReflectionClass($reflectionName);

        if(!$ref->implementsInterface(CommonRepositoryModel::class)) {
            throw new \Exception("Class {$modelName} not implements App\Rest\Repositories\CommonRepositoryModel");
        }

        return $ref->newInstance();
    }
}
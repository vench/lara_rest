<?php
/**
 * Created by PhpStorm.
 * User: vench
 * Date: 24.08.17
 * Time: 13:36
 */

namespace LpRest\Repositories;


class RepositoryProviderBase implements RepositoryProvider
{

    /**
     * @var Repository[]
     */
    private $repositoriesMap = [];


    /**
     * @param string $modelName
     * @param Repository $repository
     */
    public function registerRepository(string $modelName, Repository $repository) {
        $this->repositoriesMap[$modelName] = $repository;
    }

    /**
     * @param string $modelName
     * @return Repository
     */
    public function getRepository(string $modelName): Repository
    {

        if(isset($this->repositoriesMap[$modelName])) {
            return $this->repositoriesMap[$modelName];
        }

        return CommonRepository::createByModelName($modelName);
    }
}
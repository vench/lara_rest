<?php
/**
 * Created by PhpStorm.
 * User: vench
 * Date: 24.08.17
 * Time: 13:34
 */

namespace LpRest\Repositories;


interface RepositoryProvider
{

    /**
     * @param string $modelName
     * @param Repository $repository
     * @return void
     */
    public function registerRepository(string $modelName, Repository $repository);


    /**
     * @param string $modelName
     * @return Repository
     */
    public function getRepository(string $modelName):Repository;
}
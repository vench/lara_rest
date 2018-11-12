<?php

namespace LpRest\Repositories;

/**
 * Created by PhpStorm.
 * User: vench
 * Date: 21.08.17
 * Time: 11:02
 */
interface Repository
{
    /**
     *
     */
    const DEFAULT_LIMIT = 25;

    /**
     * @param int $offset
     * @param array|null $orders
     * @param array|null $filters
     * @param array|null $relations
     * @param int $limit
     * @param string $select
     * @return array [ list: [], total: int ]
     */
    public function all($offset = 0, array $orders = null, array $filters = null, array $relations = null, $limit = self::DEFAULT_LIMIT, $select = '*');

    /**
     * @param int $id
     * @param array|null $relations
     * @return mixed
     */
    public function one(int $id, array $relations = null);

    /**
     * @param int $id
     * @return boolean
     */
    public function delete(int $id);


    /**
     * @param string $modelName
     * @param array $dataFields
     * @return int new ID
     */
    public function create(array $dataFields = []);

    /**
     * @param int $id
     * @param array $dataFields
     * @return boolean
     */
    public function update(int $id, array $dataFields = []);

    /**
     * @param array $dataFields
     * @param array $messages
     * @return true|array - array list errors
     */
    public function validate(array $dataFields, $messages = []);

    /**
     * Получить название Permission.action
     * @param  string $method
     * @return string
     */
    public function getAccessPermissionName(string $method);
}
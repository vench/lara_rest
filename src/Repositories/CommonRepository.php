<?php
/**
 * Created by PhpStorm.
 * User: vench
 * Date: 21.08.17
 * Time: 11:04
 */

namespace LpRest\Repositories;

use Symfony\Component\Debug\Exception\ClassNotFoundException;
use Illuminate\Contracts\Validation\Factory;

class CommonRepository implements Repository
{
    /**
     * @var array
     */
    private static $listModelAliases = [

        //TODO
    ];


    /**
     * @var \Illuminate\Database\Eloquent\Builder
     */
    private $queryBuilder;

    /**
     * @var array
     */
    private $rules = [];

    /**
     * @var array
     */
    private $customAttributes = [];

    /**
     * @var array
     */
    private $accessPermissionNames = [];


    private function __construct()  {  }


    /**
     * @param $offset
     * @param array|null $orders [[]]
     * @param array|null $relations
     * @param int $limit
     * @return array [ list: [], total: int ]
     */
    public function all($offset = 0, array $orders = null, array $filters = null, array $relations = null, $limit = self::DEFAULT_LIMIT)
    {
        $this->applyFilter($filters);
        $total = $this->queryBuilder->count();

        $this->applyRelations($relations);
        $this->applyOrder($orders);
        if ($limit != -1) {
            $this->queryBuilder->take($limit);
            $this->queryBuilder->offset($offset);
        }

        return ['list' => $this->queryBuilder->get(), 'total' => $total];
    }

    /**
     * @param int $id
     * @param array|null $relations
     * @return mixed
     */
    public function one(int $id, array $relations = null)
    {
        $this->applyRelations($relations);
        return $this->queryBuilder->find($id);
    }

    /**
     * @param int $id
     * @return boolean
     */
    public function delete(int $id)
    {
        return $this->queryBuilder->find($id)->delete() > 0;
    }

    /**
     * @param array $dataFields
     * @return int new ID
     */
    public function create(array $dataFields = [])
    {
        $instance = $this->queryBuilder->getModel()->newInstance($dataFields);

        $instance->save();

        return $instance->save() ? $instance->getKey() : false;
    }

    /**
     * @param int $id
     * @param array $dataFields
     * @return boolean
     */
    public function update(int $id, array $dataFields = [])
    {
        return $this->queryBuilder->find($id)->update($dataFields);
    }


    /**
     * @param string $method
     * @return string|null
     */
    public function getAccessPermissionName(string $method)
    {
        return isset ($this->accessPermissionNames[$method]) ? $this->accessPermissionNames[$method] : null;
    }


    /**
     * @param array|null $relations
     */
    private function applyRelations( array $relations = null) {
        if(!empty($relations)) {
            $model = $this->queryBuilder->getModel();
            $this->queryBuilder->with(array_filter($relations, function($name) use(&$model) { return method_exists($model, $name);}));
        }
    }


    /**
     * @param array|null $orders
     */
    private function applyOrder(array $orders = null) {
        if(!empty($orders)) {
            foreach ($orders as $order) {
                list($column, $direction ) = $order;
                $this->queryBuilder->orderBy($column, $direction);
            }
        }
    }


    /**
     * @param array|null $filters
     */
    private function applyFilter(array $filters = null) {
        if(!empty($filters)) {
            foreach ($filters as $filter) {
                if(count($filter) == 2) {
                    list($column, $value) = $filter;
                    $this->queryBuilder->where($column, '=', $value);
                } else if(count($filter) == 3){
                    list($column, $operator, $value) = $filter;
                    $this->queryBuilder->where($column, $operator, $value);
                } else if(count($filter) == 4){
                    list($column, $operator, $value, $boolean) = $filter;
                    $boolean = strtolower($boolean);
                    if(!in_array($boolean, ['and', 'or'])) {
                        $boolean = 'and';
                    }
                    $this->queryBuilder->where($column, $operator, $value, $boolean);
                }
            }
        }
    }



    /**
     * @param array $dataFields
     * @param array $messages
     * @return array|bool
     */
    public function validate(array $dataFields, $messages = []) {

        $defaultMessages = [
            'required'  => 'Поле :attribute должно быть заполнено.',
            'min'       => 'Поле :attribute должно содержать минимум :min символов.',
            'unique'    => 'Такой :attribute уже был добавлен.'
        ];

        $rules = $this->makeRules(  $dataFields);

        $validator = app(Factory::class)
            ->make($dataFields, $rules, array_merge($defaultMessages, $messages), $this->customAttributes);

        if ($validator->fails()) {
            return $validator->getMessageBag()->toArray();
        }

        return true;
    }

    /**
     * @param array $entity
     * @return array
     */
    private function makeRules(array $entity = []) {
        $rules = $this->rules;
        if(!empty($entity)) {
            $entityMath = array_filter($entity, function($value) { return !is_array($value); });

            foreach ($rules as $field => $rule) {
                $rules[$field] = str_replace(
                    array_map(function($key){ return ':' . $key; }, array_keys($entityMath))
                    ,
                    array_values($entityMath), $rule);
            }
        }

        return $rules;
    }


    /**
     * Допустима только модель которая реализует App\Rest\Repositories\CommonRepositoryModel
     *
     * @param $modelName
     * @return CommonRepository
     * @throws \Exception
     */
    public static function createByModelName($modelName)
    {
        $reflectionName = self::getModelNameByAlias($modelName);

        if (empty($reflectionName)) {
            $reflectionName = '\App\Models\\' . ucfirst($modelName);
            if (!class_exists($reflectionName)) {
                throw new \Exception("Class {$modelName} not found");
            }
        }

        $ref = new \ReflectionClass($reflectionName);

        if(!$ref->implementsInterface('App\Rest\Repositories\CommonRepositoryModel')) {
            throw new \Exception("Class {$modelName} not implements App\Rest\Repositories\CommonRepositoryModel");
        }

        $instance = new CommonRepository();
        $instance->queryBuilder = call_user_func_array([$ref->getName(), 'query'], []);
        $instance->rules = call_user_func_array([$ref->getName(), 'rules'], []);
        $instance->customAttributes = call_user_func_array([$ref->getName(), 'getCustomAttributeNames'], []);
        $instance->accessPermissionNames = call_user_func_array([$ref->getName(), 'getAccessPermissionAliases'], []);

        return $instance;
    }


    /**
     * @param string $alias
     * @return string
     */
    private static function getModelNameByAlias($alias) {
        $aliasLower = strtolower($alias);
        return self::$listModelAliases[$aliasLower] ?? null;
    }
}
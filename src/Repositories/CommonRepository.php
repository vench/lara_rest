<?php
/**
 * Created by PhpStorm.
 * User: vench
 * Date: 21.08.17
 * Time: 11:04
 */

namespace LpRest\Repositories;

use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Factory;

/**
 * Class CommonRepository
 * @package LpRest\Repositories
 * TODO check 404 if applyFilterOwner
 */
class CommonRepository implements Repository
{



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
    private $customErrorMessages = [];

    /**
     * @var array
     */
    private $accessPermissionNames = [];

    /**
     * @var CommonRepositoryModelProvider
     */
    private $commonRepositoryModelProvider;

    /**
     * @var string
     */
    private $accessPermissionOwnedField = null;


    /**
     * CommonRepository constructor.
     * @param CommonRepositoryModelProvider $commonRepositoryModelProvider
     */
    public function __construct(CommonRepositoryModelProvider $commonRepositoryModelProvider)  {
        $this->commonRepositoryModelProvider = $commonRepositoryModelProvider;
    }


    /**
     * @param $modelName
     */
    public function setModelName($modelName) {
        $model = $this->commonRepositoryModelProvider->getModelByName($modelName);

        $this->queryBuilder = $model->getRestQuery();
        $this->rules = $model->getRestRules();
        $this->customErrorMessages = $model->getRestCustomErrorMessages();
        $this->accessPermissionNames = $model->getRestAccessPermissionAliases();
        $this->accessPermissionOwnedField = $model->getAccessPermissionOwnedField();
    }


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
        $this->applyFilterOwner();
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
        $this->applyFilterOwner();
        return $this->queryBuilder->find($id);
    }

    /**
     * @param int $id
     * @return boolean
     */
    public function delete(int $id)
    {
        $this->applyFilterOwner();
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
        $this->applyFilterOwner();
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
     *
     */
    public function getAccessPermissionOwnedField()
    {
        //TODO check model field and check exclude role list
        return 'user_id';
        return null;
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
     *
     */
    private function applyFilterOwner() {
        if(!is_null($field = $this->accessPermissionOwnedField)) {
            $this->applyFilter([
                [$field, Auth::id()],
            ]);
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

        $defaultMessages = $this->customErrorMessages;

        $rules = $this->makeRules(  $dataFields);

        $validator = app(Factory::class)
            ->make($dataFields, $rules, array_merge($defaultMessages, $messages));

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
        $instance = app()->make(CommonRepository::class, []);
        $instance->setModelName( $modelName);

        return $instance;
    }


}
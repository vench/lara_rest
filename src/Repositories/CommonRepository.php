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
 */
class CommonRepository implements Repository
{

    use TraitRepositoryFilter;


    /**
     * @var \Illuminate\Database\Eloquent\Builder
     */
    protected $queryBuilder;

    /**
     * @var array
     */
    protected $rules = [];

    /**
     * @var array
     */
    protected $customErrorMessages = [];

    /**
     * @var array
     */
    protected $accessPermissionNames = [];

    /**
     * @var CommonRepositoryModelProvider
     */
    protected $commonRepositoryModelProvider;

    /**
     * @var string
     */
    protected $accessPermissionOwnedField = null;


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
        $this->setCommonRepositoryModel($model);
    }

    /**
     * @param CommonRepositoryModel $model
     */
    public function setCommonRepositoryModel(CommonRepositoryModel $model) {
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
    public function all($offset = 0, array $orders = null, array $filters = null, array $relations = null, $limit = self::DEFAULT_LIMIT, $select = '*')
    {
        $this->applySelect($select);

        if($this->applyFilterOwner()) {
            $this->queryBuilder->where(function($subQuery) use (&$filters){
                $baseQuery = $this->queryBuilder;
                $this->queryBuilder = $subQuery;
                $this->applyFilter($filters);
                $this->queryBuilder = $baseQuery;
            });

        } else {
            $this->applyFilter($filters);
        }

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
        $model = $this->queryBuilder->find($id);
        if(is_null($model)) {
            return false;
        }
        return $model->delete() > 0;
    }

    /**
     * @param array $dataFields
     * @return int new ID
     */
    public function create(array $dataFields = [])
    {
        $instance = $this->queryBuilder->getModel()
            ->newInstance($dataFields);
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
        $model = $this->queryBuilder->find($id);
        if(is_null($model)) {
            return false;
        }
        return $model->update($dataFields);
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
    protected function applyRelations( array $relations = null) {
        if(!empty($relations)) {
            $model = $this->queryBuilder->getModel();
            $this->queryBuilder->with(array_filter($relations, function($name) use(&$model) { return method_exists($model, $name);}));
        }
    }


    /**
     * @return bool
     */
    protected function applyFilterOwner() {
        if(!is_null($field = $this->accessPermissionOwnedField)) {

            if(is_string($field)){
                $this->applyFilter([
                    [$field, Auth::id()],
                ]);
                return true;
            } else if(is_array($field)) {
                $this->applyFilter($field);
                return true;
            }
        }

        return false;
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
    protected function makeRules(array $entity = []) {
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
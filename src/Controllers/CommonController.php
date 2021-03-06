<?php

namespace LpRest\Controllers;

use LpRest\Repositories\CommonRepository;
use LpRest\Repositories\CommonRepositoryAccessProvider;
use LpRest\Repositories\Repository;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use LpRest\Repositories\RepositoryProvider;

/**
 * Created by PhpStorm.
 * User: vench
 * Date: 21.08.17
 * Time: 10:52
 */
class CommonController extends Controller
{


    /**
     * @var CommonRepositoryAccessProvider
     */
    private $accessProvider;

    /**
     * @var CommonResponse
     */
    private $commonResponse;

    /**
     * @var RepositoryProvider
     */
    private $repositoryProvider;

    /**
     * @var array
     */
    private $defaultErrorMessages = [];

    /**
     * CommonController constructor.
     * @param CommonRepositoryAccessProvider $accessProvider
     * @param CommonResponse $commonResponse
     * @param RepositoryProvider $repositoryProvider
     */
    public function __construct(CommonRepositoryAccessProvider $accessProvider,
                                CommonResponse $commonResponse,
                                RepositoryProvider $repositoryProvider)
    {
        $this->accessProvider = $accessProvider;
        $this->commonResponse = $commonResponse;
        $this->repositoryProvider = $repositoryProvider;
    }


    /**
     * @param string $modelName
     * @param Request $request
     * @param string|null $relations
     * @return \Illuminate\Http\JsonResponse
     */
    public function all(string $modelName, Request $request, string $relations = null)
    {
        $r = $this->getRepository($modelName);
        if(!is_null($response = $this->checkAccess($r, __METHOD__))) {
            return $response;
        }
        $page = $request->get('page', 1);
        $limit = $request->get('limit', Repository::DEFAULT_LIMIT);
        $select = $request->get('select', '*');

        $relationsList = !empty($relations) ? explode('/', $relations) : null;

        $sort = $request->get('sort', null);
        $sortList = [];
        if(!empty($sort)) {
            $fnApplySort = function($sort){
                return strpos($sort, ':') !== false ? explode(':', $sort): [$sort, 'asc'];
            };
            if(!is_array($sort)) {
                $sort = [$sort];
            }
            $sortList = array_map($fnApplySort, $sort);
        }

        $filter = $request->get('filter', null);
        $filterList = [];
        if(!empty($filter) && is_array($filter)) {
            foreach ($filter as $key => $value) {
                $filterList[] = strpos($value, ':') ? explode(':', $value) : [$key, $value];
            }
        }


        $offset = $limit * max($page - 1, 0);
        $data = $r->all($offset, $sortList, $filterList, $relationsList, $limit, $select);

        $result = [
            'list'       =>  $data['list'],
            'sort'       =>  $sortList,
            'filterList' =>  $filterList,
            'pagination'    => [
                'page'      => $page,
                'total'     => $data['total'],
                'pages'     => ceil($data['total'] / $limit)
            ],
        ];

        return $this->responseResult($result);
    }


    /**
     * @param string $modelName
     * @param int $id
     * @param string|null $relations
     * @return \Illuminate\Http\JsonResponse
     */
    public function one(string $modelName, int $id, string $relations = null)
    {
        $r = $this->getRepository($modelName);
        if(!is_null($response = $this->checkAccess($r, __METHOD__))) {
            return $response;
        }

        $relationsList = !empty($relations) ? explode('/', $relations) : null;
        $model = $r->one($id, $relationsList);
        return $this->responseResult($model, !is_null($model));
    }

    /**
     * @param string $modelName
     * @param int $id
     * @return array
     */
    public function delete(string $modelName, int $id)
    {
        $r = $this->getRepository($modelName);
        if(!is_null($response = $this->checkAccess($r, __METHOD__))) {
            return $response;
        }

        return $this->responseResult([
            'success' => $r->delete($id),
        ]);
    }

    /**
     * @param string $modelName
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(string $modelName, Request $request)
    {
        $r = $this->getRepository($modelName);
        if(!is_null($response = $this->checkAccess($r, __METHOD__))) {
            return $response;
        }

        $entity = $request->get('Entry', []);

        if(!is_null($response = $this->checkValidate($r, $entity))) {
            return $response;
        }

        $id = $r->create( $entity );

        return $this->responseResult([
            'id'      => $id,
        ], !empty($id));
    }

    /**
     * @param string $modelName
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(string $modelName, int $id, Request $request)
    {
        $r = $this->getRepository($modelName);
        if(!is_null($response = $this->checkAccess($r, __METHOD__))) {
            return $response;
        }

        $entity = $request->get('Entry', []);

        if(!is_null($response = $this->checkValidate($r, $entity))) {
            return $response;
        }

        $result = $r->update($id,  $entity);

        return $this->responseResult([
            'id'      => $id,
        ], $result > 0);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function multi(Request $request)
    {
        $requestData = $request->json()->all();
        $responseData   = [];

        $reflection = new \ReflectionClass(self::class);

        foreach ($requestData as $method) {

            $reflectionMethod = $reflection->getMethod($method['method']);
            $parametersReflection = $reflectionMethod->getParameters();

            $parameters     = [];
            $params = isset($method['params']) ? $method['params'] : [];

            foreach ($parametersReflection as $refPar) {

                $parameter = isset($params[$refPar->getPosition()]) ? $params[$refPar->getPosition()] : null;

                if($refPar->getClass() && $refPar->getClass()->getName() === Request::class) {
                    $requestClone = clone $request;

                    if(is_array($parameter)) {
                        foreach ($parameter as $key => $value) {
                            $requestClone->request->set($key, $value);
                        }
                    }

                    $parameters[$refPar->getPosition()] = $requestClone;
                } else if($parameter) {
                    $parameters[$refPar->getPosition()] = $parameter;
                }
            }

            $response  = $this->callAction($reflectionMethod->getName(), $parameters);
            $responseData[$method['id']] = $response->getData();
        }

        return $this->responseResult($responseData);
    }


    /**
     * @param string $modelName
     * @param int $id
     * @param string $methodName
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function call(string $modelName, int $id, string $methodName, Request $request)
    {

        $r = $this->getRepository($modelName);
        if(!is_null($response = $this->checkAccess($r, __METHOD__))) {
            return $response;
        }

        $model = $r->one($id);
        $result = null;

        if(method_exists($model, $methodName)) {
            $arguments = $request->json()->all();
            $result =  call_user_func_array([$model, $methodName], $arguments);
        }

        return $this->responseResult($result);
    }

    /**
     * @param array $defaultErrorMessages
     */
    public function setDefaultErrorMessages(array $defaultErrorMessages) {
        $this->defaultErrorMessages = $defaultErrorMessages;
    }


    /**
     * @param \LpRest\Repositories\Repository $r
     * @param array $entity
     * @param array $body
     * @return \Illuminate\Http\JsonResponse|null
     */
    private function checkValidate($r, array $entity = [], array $body = []) {
        if(($errors = $r->validate($entity, $this->defaultErrorMessages)) !== true) {
            return $this->responseResult($body, false, $errors);
        }

        return null;
    }


    /**
     * @param \LpRest\Repositories\Repository  $r
     * @param string $method
     * @return  \Illuminate\Http\JsonResponse|null
     */
    private function checkAccess($r, $method) {
        $methodShort = ($pos = strrpos($method, '::')) !== false ?
            substr($method, $pos + 2) : $method;

        $action = $r->getAccessPermissionName($methodShort);

        if($action === true) {
            return null;
        }
        if(is_string($action) && !is_null( $this->accessProvider) && $this->accessProvider->checkAccess($action)) {
            return null;
        }

        return $this->responseResult([ ], false, [
            ['Insufficient rights '.$action]
        ], 403);
    }


    /**
     * @param mixed $body
     * @param bool $success
     * @param array $errors
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    private function responseResult($body, $success = true, $errors = [], $status = 200) {
        return $this->commonResponse->responseResult($body, $success, $errors, $status);
    }

    /**
     * @param $modelName
     * @return \LpRest\Repositories\Repository;
     */
    private function getRepository($modelName)  {
        $repository = $this->repositoryProvider->getRepository($modelName);
        return $repository;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: vench
 * Date: 11.07.18
 * Time: 10:12
 */

namespace LpRest\Repositories;

/**
 * Trait TraitRepositoryFilter
 * @package LpRest\Repositories
 */
trait TraitRepositoryFilter
{

    /**
     * @var \Illuminate\Database\Eloquent\Builder
     */
    protected $queryBuilder;


    /**
     * @param array|null $filters
     */
    protected function applyFilter(array $filters = null) {
        if(!empty($filters)) {

            foreach ($filters as $filter) {
                if(count($filter) == 2) {
                    list($column, $value) = $filter;
                    $this->addWhereCondition($column, $value);
                } else if(count($filter) == 3){
                    list($column, $operator, $value) = $filter;
                    $this->addWhereCondition($column, $value, $operator);
                } else if(count($filter) == 4){
                    list($column, $operator, $value, $boolean) = $filter;
                    $boolean = strtolower($boolean);
                    if(!in_array($boolean, ['and', 'or'])) {
                        $boolean = 'and';
                    }
                    $this->addWhereCondition($column, $value, $operator, $boolean);
                }
            }
        }
    }

    /**
     * @param $column
     * @param $value
     * @param string $operator
     * @param string $boolean
     * @param null $queryBuilder
     */
    protected function addWhereCondition($column, $value, $operator = '=', $boolean = 'and', $queryBuilder = null) {

        if(is_null($queryBuilder)) {
            $queryBuilder = $this->queryBuilder;
        }

        if(strpos($column, '.') !== false) {
            list($table, $column) = explode('.', $column);
            $self = $this;
            $queryBuilder->whereHas($table, function ($query) use(&$column, &$value, &$operator, &$boolean, &$self)  {
                $self->addWhereCondition($column, $value, $operator, $boolean, $query);
            });

            return;
        }

        switch ($operator) {
            case 'range':
                $queryBuilder->whereBetween($column, explode(',', $value), $boolean);
                break;
            case 'in':
                $queryBuilder->whereIn($column, explode(',', $value), $boolean);
                break;

            default:
                $queryBuilder->where($column, $operator, $value, $boolean);
                break;
        }


    }


    /**
     * @param array|null $orders
     */
    protected function applyOrder(array $orders = null) {
        if(!empty($orders)) {
            foreach ($orders as $order) {
                list($column, $direction ) = $order;
                $this->queryBuilder->orderBy($column, $direction);
            }
        }
    }
}
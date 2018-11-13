<?php
/**
 * Created by PhpStorm.
 * User: vench
 * Date: 11.07.18
 * Time: 10:12
 */

namespace LpRest\Repositories;

use Illuminate\Support\Facades\DB;

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
     * @var array
     */
    protected $aggregate = [];

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
                $this->queryBuilder->orderBy(
                    isset($this->aggregate[$column]) ? $this->aggregate[$column] : $column,
                    $direction);
            }
        }
    }


    /**
     * @param $select
     */
    protected function applySelect($select ) {

        if(empty($select) || $select == '*') {
            return;
        }
        $selectList = explode(',',$select);

        $pure = [];
        $aggregate = [];

        foreach ($selectList as $field) {
            $components = explode(':', $field);
            if(count($components) == 2) {
                $func =  $components[1];
                $alias = join('', array_map('ucwords', $components));
                $this->aggregate[$alias] = DB::raw("{$func}({$components[0]})");
                $aggregate[] = DB::raw("{$func}({$components[0]}) as {$alias}");
            } else {
                $pure[] = $components[0];
            }
        }

        $this->queryBuilder->select(array_merge($aggregate,  $pure));
        if(!empty($this->aggregate) && !empty($pure)) {
            $this->queryBuilder->groupBy($pure);
        }
    }
}
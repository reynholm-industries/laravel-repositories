<?php

namespace Reynholm\LaravelRepositories\Repository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\QueryException;

use Reynholm\LaravelRepositories\Exception\ColumnNotFoundException;
use Reynholm\LaravelRepositories\Exception\EntityNotFoundException;
use Rodamoto\Repository\Exception\InvalidCriteriaParametersException;

/**
 * Class ArrayBasedRepository
 * @package Reynholm\LaravelRepositories\Implementation
 * This repository implementation provides methods to work with your laravel data
 * using only arrays to get data and arrays to query data
 *
 * @property Builder $builder
 * @property string  $connection
 * @property string  $tableName
 *
 */
abstract class ArrayRepository {

    protected $connection = 'default';
    protected $primaryKey = 'id';
    protected $tableName;

    protected $builder;

    function __construct()
    {
        $this->builder = \DB::connection($this->connection)->table($this->tableName);
    }

    /**     
     * @return Builder
     */
    public function getBuilder() {
        return $this->builder;
    }    

    /**
     * @param int $id
     * @param array $columns Restrict columns that you want to retrieve
     * @return array
     * @throws ColumnNotFoundException 
     * @todo test Exception
     */
    public function find($id, array $columns = array())
    {
        $searchCriteria = array($this->primaryKey => $id);

        return $this->findOne($searchCriteria, $columns);
    }

    /**
     * @param int $id
     * @param array $columns Restrict columns that you want to retrieve
     * @return array
     * @throws ColumnNotFoundException
     * @throws EntityNotFoundException
     * @todo test Column not found exception
     */
    public function findOrFail($id, array $columns = array())
    {
        $entity = $this->find($id, $columns);

        if ( is_null($entity) ) {
            throw new EntityNotFoundException();
        }

        return $entity;
    }

    /**
     * @param array $criteria
     * Ex.:
     * array(
     *     array('name', '=', 'carlos'),
     *     array('age',  '>', 20),
     * )
     * @param array $columns Restrict columns that you want to retrieve
     * @return array
     * @throws ColumnNotFoundException
     * @throws InvalidCriteriaParametersException
     */
    public function findOne(array $searchCriteria, array $columns = array())
    {
        $builder = $this->builder;

        if ( ! empty($columns) ) {
            $builder = $builder->select($columns);
        }

        foreach ($searchCriteria as $search => $value) {
            $builder = $builder->where($search, '=', $value);
        }

        try {
            $result = (array)$builder->first();
        }
        catch(QueryException $queryException) {
            if ( $queryException->getCode() === '42S22' ) { //columna no encontrada
                throw new ColumnNotFoundException();
            }

            throw $queryException;
        }

        if ( empty($result) ) {
            return null;
        }

        return $result;
    }

}
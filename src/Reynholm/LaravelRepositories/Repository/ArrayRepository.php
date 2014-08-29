<?php

namespace Reynholm\LaravelRepositories\Repository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\QueryException;

use Reynholm\LaravelRepositories\Behaviour\LaravelRepositoryInterface;
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
 * @todo test Column not found exception Is not tested on any method
 *       Seems to be not working with sqlite
 */
abstract class ArrayRepository implements LaravelRepositoryInterface
{

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
     * {@inheritdoc}
     */
    public function find($id, array $columns = array())
    {
        $searchCriteria = [ [$this->primaryKey, '=', $id] ];

        return $this->findOne($searchCriteria, $columns);
    }

    /**
     * {@inheritdoc}
     */
    public function findOrFail($id, array $columns = array())
    {
        $entity = $this->find($id, $columns);

        if ( empty($entity) ) {
            throw new EntityNotFoundException();
        }

        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function findOne(array $criteria, array $columns = array())
    {
        $builder = $this->builder;

        if ( ! empty($columns) ) {
            $builder = $builder->select($columns);
        }

        foreach ($criteria as $search) {
            $builder = $builder->where($search[0], $search[1], $search[2]);
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
            return array();
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        return $this->builder->where($this->primaryKey, '=', $id)->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOrFail($id)
    {
        if ( ! $this->delete($id) ) {
            throw new EntityNotFoundException;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validate(array $data)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function validateOrFail(array $data)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function validateMany(array $data)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function validateManyOrFail(array $data)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getValidationErrors()
    {

    }

}
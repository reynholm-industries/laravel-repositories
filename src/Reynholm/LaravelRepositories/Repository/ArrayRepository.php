<?php

namespace Reynholm\LaravelRepositories\Repository;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\QueryException;

use Reynholm\LaravelRepositories\Behaviour\LaravelRepositoryInterface;
use Reynholm\LaravelRepositories\Exception\ColumnNotFoundException;
use Reynholm\LaravelRepositories\Exception\DataNotValidException;
use Reynholm\LaravelRepositories\Exception\EntityNotFoundException;

/**
 * Class ArrayBasedRepository
 * @package Reynholm\LaravelRepositories\Implementation
 * This repository implementation provides methods to work with your laravel data
 * using only arrays to get data and arrays to query data
 *
 * @property Builder $builder
 * @property string  $connection
 * @property string  $tableName
 * @property array   $validationErrors If validation fails errors will be stored here.
 *                   Is an array with 2 keys, messages (fields that failed with message), and failed (fails without message)
 *
 * @todo test Column not found exception Is not tested on any method
 *       Seems to be not working with sqlite
 */
abstract class ArrayRepository implements LaravelRepositoryInterface
{

    protected $connection = 'default';
    protected $primaryKey = 'id';
    protected $tableName;
    protected $rules = array();
    protected $validationErrors = array();

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
    public function findMany(array $criteria, array $columns = array(), $limit = 0, array $orderBy = array())
    {
        $builder = $this->builder;

        if ( ! empty($columns) ) {
            $builder = $builder->select($columns);
        }

        $builder = $builder->limit($limit);

        foreach ($criteria as $search) {
            $builder = $builder->where($search[0], $search[1], $search[2]);
        }

        if ( ! empty($orderBy) ) {
            foreach ($orderBy as $orderField) {
                $builder->orderBy($orderField[0], $orderField[1]);
            }
        }

        try {
            $result = $builder->get();
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

        return $this->objectsToArray($result);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data, $force = false)
    {
        if ($force === false) {
            $this->validateOrFail($data);
        }

        return $this->builder->insert($data);
    }

    /**
     * {@inheritdoc}
     */
    public function createMany(array $data, $force = false)
    {
        if ($force === false) {
            $this->validateManyOrFail($data);
        }

        return $this->builder->insert($data);
    }

    /**
     * @param array $criteria
     * Ex.:
     * array(
     *     array('name', '=', 'carlos'),
     *     array('age',  '>', 20),
     * )
     * @return int
     */
    public function count(array $criteria = array())
    {
        $builder = $this->builder;

        foreach ($criteria as $search) {
            $builder = $builder->where($search[0], $search[1], $search[2]);
        }

        return $this->builder->count();
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
        return $this->validateWithCustomRules($data, $this->rules);
    }

    /**
     * {@inheritdoc}
     */
    public function validateWithCustomRules(array $data, array $rules)
    {
        $validator = \Validator::make($data, $rules);

        if ( $validator->fails() )
        {
            $this->validationErrors = array(
                'messages' => $validator->messages(),
                'failed' => $validator->failed(),
            );

            return false;
        }

        $this->validationErrors = array();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function validateOrFail(array $data)
    {
        if ( ! $this->validate($data) ) {
            throw new DataNotValidException();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateWithCustomRulesOrFail(array $data, array $rules)
    {
        if ( ! $this->validateWithCustomRules($data, $rules) ) {
            throw new DataNotValidException();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateMany(array $data)
    {
        return $this->validateManyWithCustomRules($data, $this->rules);
    }

    /**
     * {@inheritdoc}
     */
    public function validateManyWithCustomRules(array $data, array $rules)
    {
        $dataIsValid = true;
        $this->validationErrors = array();

        foreach ($data as $row) {

            $validator = \Validator::make($row, $rules);

            if ( $validator->fails() )
            {
                $this->validationErrors[] = array(
                    'messages' => $validator->messages(),
                    'failed'   => $validator->failed(),
                );

                $dataIsValid = false;
            }
        }

        return $dataIsValid;
    }

    /**
     * {@inheritdoc}
     */
    public function validateManyOrFail(array $data)
    {
        if ( ! $this->validateMany($data) ) {
            throw new DataNotValidException();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateManyWithCustomRulesOrFail(array $data, array $rules)
    {
        if ( ! $this->validateManyWithCustomRules($data, $rules)) {
            throw new DataNotValidException();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

    /**
     * Used to convert the stdClass array coming from the laravel query builder
     * to an array
     * @param array $data
     * @return array
     */
    protected function objectsToArray($data)
    {
        array_walk($data, function(&$row) {
            $row = (array)$row;
        });

        return $data;
    }

}
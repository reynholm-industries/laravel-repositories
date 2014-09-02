<?php

namespace Reynholm\LaravelRepositories\Repository;

use Illuminate\Database\Query\Builder;

use Reynholm\LaravelRepositories\Behaviour\LaravelRepositoryInterface;
use Reynholm\LaravelRepositories\Exception\DataNotValidException;
use Reynholm\LaravelRepositories\Exception\EntityNotFoundException;
use Reynholm\LaravelRepositories\Support\TableNameGuesser;

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
 */
abstract class ArrayRepository implements LaravelRepositoryInterface
{
    protected $connection;
    protected $primaryKey = 'id';
    protected $tableName;
    protected $rules = array();

    private $validationErrors = array();
    private $builder;

    function __construct()
    {
        if ($this->tableName === null) {

            /** @var TableNameGuesser $guesser */
            $guesser = \App::make('Reynholm\LaravelRepositories\Support\TableNameGuesser');

            $this->tableName = $guesser->guess(get_class($this));
        }

        $this->builder = \DB::connection($this->connection)->table($this->tableName);
    }

    /**
     * Returns a new Builder instance
     * @return Builder
     */
    protected function getBuilder() {
        return $this->builder->newQuery()->getConnection()->table($this->tableName);
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
        $builder = $this->getBuilder();

        if ( ! empty($columns) ) {
            $builder = $builder->select($columns);
        }

        foreach ($criteria as $search) {
            $builder = $builder->where($search[0], $search[1], $search[2]);
        }

        $result = (array)$builder->first();

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
        $builder = $this->getBuilder();

        if ( ! empty($columns) ) {
            $builder = $builder->select($columns);
        }

        if ($limit > 0) {
            $builder = $builder->limit($limit);
        }

        foreach ($criteria as $search) {
            $builder = $builder->where($search[0], $search[1], $search[2]);
        }

        if ( ! empty($orderBy) ) {
            foreach ($orderBy as $orderName => $orderDirection) {
                $builder->orderBy($orderName, $orderDirection);
            }
        }

        $result = $builder->get();

        if ( empty($result) ) {
            return array();
        }

        return $this->objectsToArray($result);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(array $columns = array(), $limit = 0, array $orderBy = array())
    {
        return $this->findMany([], $columns, $limit, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function lists($column, $key = null)
    {
        return $this->getBuilder()->lists($column, $key);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data, $force = false)
    {
        if ($force === false) {
            $this->validateOrFail($data);
        }

        return $this->getBuilder()->insert($data);
    }

    /**
     * {@inheritdoc}
     */
    public function createMany(array $data, $force = false)
    {
        if ($force === false) {
            $this->validateManyOrFail($data);
        }

        return $this->getBuilder()->insert($data);
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
        $builder = $this->getBuilder();

        foreach ($criteria as $search) {
            $builder = $builder->where($search[0], $search[1], $search[2]);
        }

        return $builder->count();
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        return $this->getBuilder()->where($this->primaryKey, '=', $id)->update($data);
    }


    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        return $this->getBuilder()->where($this->primaryKey, '=', $id)->delete();
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
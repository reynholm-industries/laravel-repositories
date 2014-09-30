<?php

namespace Reynholm\LaravelRepositories\Repository;

use Illuminate\Database\Query\Builder;

use Reynholm\LaravelRepositories\Behaviour\LaravelRepositoryInterface;
use Reynholm\LaravelRepositories\Exception\DataNotValidException;
use Reynholm\LaravelRepositories\Exception\EntityNotFoundException;
use Reynholm\LaravelRepositories\Support\Fetcher\ArrayObjectFetcher;
use Reynholm\LaravelRepositories\Support\Fetcher\CollectionArrayFetcher;
use Reynholm\LaravelRepositories\Support\Fetcher\CollectionObjectFetcher;
use Reynholm\LaravelRepositories\Support\Fetcher\FetcherInterface;
use Reynholm\LaravelRepositories\Support\Fetcher\MultidimensionalArrayFetcher;
use Reynholm\LaravelRepositories\Support\TableNameGuesser;
use Reynholm\LaravelRepositories\Support\Timestamper;

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
 * @property Timestamper $timestamper
 */
abstract class LaravelRepository implements LaravelRepositoryInterface
{
    protected $connection;
    protected $primaryKey = 'id';
    protected $tableName;
    protected $rules = array();

    protected $timestamps   = false;
    protected $stamp_create = 'created_at';
    protected $stamp_update = 'updated_at';

    /**
     * @var int Choose the fetch mode.
     * @see LaravelRepositoryInterface for to check available fetch constants
     */
    protected $fetchMode = LaravelRepositoryInterface::FETCH_AS_MULTIDIMENSIONAL_ARRAY;

    private $validationErrors = array();
    private $builder;
    private $timestamper;
    private $fetcher;

    function __construct(TableNameGuesser $tableNameGuesser, Timestamper $timestamper)
    {
        if ($this->tableName === null) {
            $this->tableName = $tableNameGuesser->guess(get_class($this));
        }

        $this->builder = \DB::connection($this->connection)->table($this->tableName);
        $this->timestamper = $timestamper;
        $this->fetcher = $this->resolveFetcher();
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

        return $this->getFetcher()->fetch( $this->findOne($searchCriteria, $columns) );
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

        return $this->getFetcher()->fetch($entity);
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

        return $this->getFetcher()->fetch($result);
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

        return $this->getFetcher()->fetchMany($result);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(array $columns = array(), $limit = 0, array $orderBy = array())
    {
        return $this->getFetcher()->fetchMany(
            $this->findMany([], $columns, $limit, $orderBy)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function lists($column, $key = null)
    {
        return $this->getFetcher()->fetch(
            $this->getBuilder()->lists($column, $key)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data, $force = false)
    {
        return $this->createMany([$data], $force);
    }

    /**
     * {@inheritdoc}
     */
    public function createMany(array $data, $force = false)
    {
        if ($this->timestamps === true) {
            $stampFields = [$this->stamp_create, $this->stamp_update];
            $data = $this->timestamper->stampCollection($data, $stampFields);
        }

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
        return $this->updateMany([[$this->primaryKey, '=', $id]], $data);
    }

    /**
     * {@inheritdoc}
     */
    public function updateMany(array $criteria, array $data)
    {
        $builder = $this->getBuilder();

        if ($this->timestamps === true) {                        
            $data = $this->timestamper->stamp($data, [$this->stamp_update]);
        }

        foreach ($criteria as $search) {
            $builder = $builder->where($search[0], $search[1], $search[2]);
        }

        return $builder->update($data);
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
     * Delete all the rows
     * @return boolean
     */
    public function deleteAll()
    {
        return $this->getBuilder()->delete();
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
                'messages' => $validator->messages()->getMessages(),
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
        $this->validationErrors = array();

        foreach ($data as $row) {

            $validator = \Validator::make($row, $rules);

            if ( $validator->fails() )
            {
                $this->validationErrors[] = array(
                    'messages' => $validator->messages()->getMessages(),
                    'failed'   => $validator->failed(),
                );

                $dataIsValid = false;
            }
        }

        return !(bool)count($this->validationErrors);
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
     * {@inheritdoc}
     */
    public function getValidationMessages()
    {
        if (empty($this->validationErrors)) {
            return [];
        }

        $isMultidimensional = ! empty($this->validationErrors[0]);

        if ($isMultidimensional) {
            return array_pluck($this->validationErrors, 'messages');
        }        

        return $this->validationErrors['messages'];
    }

    /**
     * {@inheritdoc}
     */
    public function getValidationFailures()
    {                
        if (empty($this->validationErrors)) {
            return [];
        }

        $isMultidimensional = ! empty($this->validationErrors[0]);

        if ($isMultidimensional) {
            return array_pluck($this->validationErrors, 'failed');
        }        

        return $this->validationErrors['failed'];
    }

    /**
     * You can override this if you want to use a custom fetcher
     * @throws \Exception
     * @return FetcherInterface
     */
    protected function resolveFetcher()
    {
        switch ($this->fetchMode) {
            case LaravelRepositoryInterface::FETCH_AS_MULTIDIMENSIONAL_ARRAY:
                return new MultidimensionalArrayFetcher();
            case LaravelRepositoryInterface::FETCH_AS_ARRAY_OF_OBJECTS:
                return new ArrayObjectFetcher();
            case LaravelRepositoryInterface::FETCH_AS_LARAVEL_COLLECTION_OBJECTS:
                return new CollectionObjectFetcher();
            case LaravelRepositoryInterface::FETCH_AS_LARAVEL_COLLECTION_ARRAY:
                return new CollectionArrayFetcher();
        }

        throw new \Exception('Fetcher not supported: ' . $this->fetchMode);
    }

    /**
     * Use this to change the fetcher at runtime
     * @param FetcherInterface $fetcherInterface
     */
    public function setFetcher(FetcherInterface $fetcherInterface)
    {
        $this->fetcher = $fetcherInterface;
    }

    /**
     * @return FetcherInterface
     */
    protected function getFetcher()
    {
        return $this->fetcher;
    }

    /**
     * @{@inheritdoc
     */
    public function fetch($data)
    {
        return $this->getFetcher()->fetch($data);
    }

    /**
     * @{@inheritdoc
     */
    public function fetchMany($data)
    {
        return $this->getFetcher()->fetchMany($data);
    }


}
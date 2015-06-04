<?php

namespace Reynholm\LaravelRepositories\Behaviour;

use Reynholm\LaravelRepositories\Exception\DataNotValidException;
use Reynholm\LaravelRepositories\Exception\EntityNotFoundException;
use Reynholm\LaravelRepositories\Support\Fetcher\FetcherInterface;

interface LaravelRepositoryInterface
{
    const FETCH_AS_MULTIDIMENSIONAL_ARRAY = 1;
    const FETCH_AS_LARAVEL_COLLECTION_OBJECTS = 2;
    const FETCH_AS_ARRAY_OF_OBJECTS = 3;
    const FETCH_AS_LARAVEL_COLLECTION_ARRAY = 4;

    /**
     * @param int $id
     * @param array $columns Restrict columns that you want to retrieve
     * @return array
     */
    public function find($id, array $columns = array());

    /**
     * @param int $id
     * @param array $columns Restrict columns that you want to retrieve
     * @return array
     * @throws EntityNotFoundException
     */
    public function findOrFail($id, array $columns = array());

    /**
     * @param array $criteria
     * Ex.:
     * array(
     *     array('name', '=', 'carlos'),
     *     array('age',  '>', 20),
     * )
     * @param array $columns Restrict columns that you want to retrieve
     * @return array
     */
    public function findOne(array $criteria, array $columns = array());

    /**
     * @param array $criteria
     * Ex.:
     * array(
     *     array('name', '=', 'carlos'),
     *     array('age',  '>', 20),
     * )
     * @param array $columns Restrict columns that you want to retrieve
     * @param integer $limit
     * @param array $orderBy
     * Ex.: ['name' => 'asc', 'age' => 'desc']
     * @return array
     */
    public function findMany(array $criteria, array $columns = array(), $limit = 0, array $orderBy = array());

    /**
     * @param array $columns Restrict columns that you want to retrieve
     * @param int $limit
     * @param array $orderBy
     * Ex.: ['name' => 'asc', 'age' => 'desc']
     * @return array
     */
    public function findAll(array $columns = array(), $limit = 0, array $orderBy = array());

    /**
     * Get an array with the values of a given column.
     *
     * @param  string  $column
     * @param  string  $key
     * @return array
     */
    public function lists($column, $key = null);

    /**
     * @param array $data The resource that you want to create
     * @param bool $force If force is false and data is not valid error will be thrown
     * @return boolean
     * @throws DataNotValidException
     */
    public function create(array $data, $force = false);

    /**
     * @param array $data The resources that you want to create
     * @param bool $force If force is false and data is not valid error will be thrown
     * @return boolean
     * @throws DataNotValidException
     */
    public function createMany(array $data, $force = false);

    /**
     * Update a resource by its id
     * @param int $id
     * @param array $data
     * @return int Number of affected rows
     */
    public function update($id, array $data);

    /**
     * Update one or more resources
     * @param array $criteria
     * @param array $data
     * @return int Number of affected rows
     */
    public function updateMany(array $criteria, array $data);

    /**
     * @param array $criteria
     * Ex.:
     * array(
     *     array('name', '=', 'carlos'),
     *     array('age',  '>', 20),
     * )
     * @return int
     */
    public function count(array $criteria = array());

    /**
     * Validates the input array and stores all the errors,
     * them, you can get them with the getValidationErrors() method
     * @param array $data
     * @return boolean
     */
    public function validate(array $data);

    /**
     * Validates the input array and stores all the errors,
     * them, you can get them with the getValidationErrors() method
     * Same as validate but specify the rules, instead of using the repository rules
     * @param array $data
     * @param array $rules
     * @return boolean
     */
    public function validateWithCustomRules(array $data, array $rules);

    /**
     * Validates the input array or throws exception
     * It also stores all the errors. Then you can retrieve them with the
     * getValidationErrors() method
     * @param array $data
     * @throws DataNotValidException
     * @return void
     */
    public function validateOrFail(array $data);

    /**
     * Validates the input array or throws exception
     * It also stores all the errors. Then you can retrieve them with the
     * getValidationErrors() method
     * @param array $data
     * @param array $rules
     * @throws DataNotValidException
     * @return void
     */
    public function validateWithCustomRulesOrFail(array $data, array $rules);

    /**
     * Validates a multidimensional
     * It also stores all the errors. Then you can retrieve them with the
     * getValidationErrors() method
     * @param array $data
     * @return boolean
     */
    public function validateMany(array $data);

    /**
     * Validates a multidimensional
     * It also stores all the errors. Then you can retrieve them with the
     * getValidationErrors() method
     * Same as validate but specify the rules, instead of using the repository rules
     * @param array $data
     * @param array $rules
     * @return boolean
     */
    public function validateManyWithCustomRules(array $data, array $rules);

    /**
     * Validates a multidimensional or throws exception
     * It also stores all the errors. Then you can retrieve them with the
     * getValidationErrors() method
     * @param array $data
     * @throws DataNotValidException
     */
    public function validateManyOrFail(array $data);

    /**
     * Validates a multidimensional or throws exception
     * It also stores all the errors. Then you can retrieve them with the
     * getValidationErrors() method
     * @param array $data
     * @param array $rules
     * @throws DataNotValidException
     */
    public function validateManyWithCustomRulesOrFail(array $data, array $rules);

    /**
     * Returns the errors generated by the validate methods
     * with the keys "messages" and "failed"
     * If you used validateMany it will be a multidimensional array
     * @return array
     */
    public function getValidationErrors();

    /**
    * Return the messages key from the getValidationErrors method
    * If used after validateMany it will be a multidimensional array
    * @return array
    */
    public function getValidationMessages();

    /**
    * Return the failed key from the getValidationErrors method
    * If used after validateMany it will be a multidimensional array
    * @return array
    */
    public function getValidationFailures();

    /**
     * @param int $id
     * @return boolean
     */
    public function delete($id);

    /**
     * @param int $id
     * @throw EntityNotFoundException
     */
    public function deleteOrFail($id);

    /**
     * Delete all the rows
     * @return int Number of deleted rows
     */
    public function deleteAll();

    /**
     * Change fetch mode at runtime
     * @param FetcherInterface $fetcherInterface
     * @return void
     */
    public function setFetcher(FetcherInterface $fetcherInterface);

    /**
     * Returns one row of data depending on the used fetcher
     * It is a shortcut for getFetcher()->fetch()
     * @param $data
     * @return mixed
     */
    public function fetch($data);

    /**
     * Returns multiple rows of data depending on the used fetcher
     * It is a shortcut for getFetcher()->fetchMany()
     * @param $data
     * @return mixed
     */
    public function fetchMany($data);
}

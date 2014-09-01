<?php

namespace Reynholm\LaravelRepositories\Behaviour;

use Reynholm\LaravelRepositories\Exception\ColumnNotFoundException;
use Reynholm\LaravelRepositories\Exception\DataNotValidException;
use Reynholm\LaravelRepositories\Exception\EntityNotFoundException;
use Rodamoto\Repository\Exception\InvalidCriteriaParametersException;

interface LaravelRepositoryInterface
{

    /**
     * @param int $id
     * @param array $columns Restrict columns that you want to retrieve
     * @return array
     * @throws ColumnNotFoundException
     */
    public function find($id, array $columns = array());

    /**
     * @param int $id
     * @param array $columns Restrict columns that you want to retrieve
     * @return array
     * @throws ColumnNotFoundException
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
     * @throws ColumnNotFoundException
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
     * Ex.:
     * array(
     *     array('name', 'asc'),
     *     array('age', 'desc'),
     * )
     * @return array
     * @throws ColumnNotFoundException
     */
    public function findMany(array $criteria, array $columns = array(), $limit = 0, array $orderBy = array());

    /**
     * @param array $data The resource that you want to create
     * @param bool $force If force is false and data is not valid error will be throwed
     * @return boolean
     * @throws DataNotValidException
     */
    public function create(array $data, $force = false);

    /**
     * @param array $data The resources that you want to create
     * @param bool $force If force is false and data is not valid error will be throwed
     * @return boolean
     * @throws DataNotValidException
     */
    public function createMany(array $data, $force = false);

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
     * them, you can get them with the getErrors() method
     * @param array $data
     * @return boolean
     */
    public function validate(array $data);

    /**
     * Validates the input array and stores all the errors,
     * them, you can get them with the getErrors() method
     * Same af validate but specify the rules, instead of using the repository rules
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
     * Same af validate but specify the rules, instead of using the repository rules
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
     * @return array
     */
    public function getValidationErrors();

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
} 
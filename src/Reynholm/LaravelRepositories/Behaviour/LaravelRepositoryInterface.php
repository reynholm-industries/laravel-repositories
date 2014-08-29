<?php

namespace Reynholm\LaravelRepositories\Behaviour;

use Reynholm\LaravelRepositories\Exception\ColumnNotFoundException;
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
     * @throws InvalidCriteriaParametersException
     */
    public function findOne(array $criteria, array $columns = array());

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
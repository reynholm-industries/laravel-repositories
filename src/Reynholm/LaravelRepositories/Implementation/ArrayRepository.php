<?php

namespace Reynholm\LaravelRepositories\Implementation;

use Illuminate\Database\Query\Builder;
use Reynholm\LaravelRepositories\Behaviour\BaseRepository;

/**
 * Class ArrayBasedRepository
 * @package Reynholm\LaravelRepositories\Implementation
 * This repository implementation provides methods to work with your laravel data
 * using only arrays to get data and arrays to query data
 */
abstract class ArrayRepository extends BaseRepository {

    protected $builder;

    function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * {@inheritdoc}
     */
    public function find($id, array $columns = array())
    {
        if ( ! empty($columns) ) {
            $this->builder->select($columns);
        }

        return $this->builder->find($id);
    }

}
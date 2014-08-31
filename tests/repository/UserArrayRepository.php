<?php

namespace tests\repository;

use Reynholm\LaravelRepositories\Repository\ArrayRepository;

class UserArrayRepository extends ArrayRepository {

    protected $connection = 'testbench';
    protected $tableName  = 'users';
    protected $rules      = array(
        'name' => 'required|min:5|unique:users',
        'age'  => 'required|integer|between:0,120',
    );
}
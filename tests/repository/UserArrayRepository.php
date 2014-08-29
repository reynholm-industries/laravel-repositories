<?php

namespace tests\repository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Reynholm\LaravelRepositories\Repository\ArrayRepository;
use tests\model\User;

class UserArrayRepository extends ArrayRepository {

	protected $connection = 'testbench';
    protected $tableName  = 'users';

}
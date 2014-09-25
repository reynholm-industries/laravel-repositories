<?php

namespace tests\repository;

use Reynholm\LaravelRepositories\Repository\LaravelRepository;

class UserRepository extends LaravelRepository {

    protected $rules      = array(
        'name' => 'required|min:5|unique:users',
        'age'  => 'required|integer|between:0,120',
    );

}
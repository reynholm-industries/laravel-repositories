Laravel Repositories
====================

[![Build Status](https://travis-ci.org/reynholm-industries/laravel-repositories.svg)](https://travis-ci.org/reynholm-industries/laravel-repositories)
[![Coverage Status](https://coveralls.io/repos/reynholm-industries/laravel-repositories/badge.png?branch=master)](https://coveralls.io/r/reynholm-industries/laravel-repositories?branch=master)

Repository pattern for Laravel

# Warning
This package is currently on development. Is not ready for use yet.
Interfaces may change.

# Installation and Configuration
to do: how to install with composer

# Usage
Simply extend one of the currently Reynholm\LaravelRepositories\Repository implementations
and provide connection and table name.

Currently available:
· Reynholm\LaravelRepositories\Repository\ArrayRepository
  Allows you to query and retrieve data only with arrays so there is no
  tight coupling with laravel or eloquent

## Example
```php
class UserArrayRepository extends ArrayRepository {
	//protected $connection = 'default';
    protected $tableName  = 'users';
}
```

Specify a connection string if is not the laravel's default connection.
Specify the database table.

Currently implemented methods:
```php

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
      * Ex.:
      * array(
      *     array('name', 'asc'),
      *     array('age', 'desc'),
      * )
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

```

## Validation
You can validate your data with the validate methods.
Specify the rules of your repository in the rules property:
```php
class UserArrayRepository extends ArrayRepository {

    protected $connection = 'default';
    protected $tableName  = 'users';
    protected $rules      = array(
        'name' => 'required|min:5|unique:users',
        'age'  => 'required|integer|between:0,120',
    );
}
```

Examples:
```php 
if ($this->arrayRepository->validate($validData)) {
    //The data is valid
}
else {
    //The data is not valid
    //You can get the validation failed or messages this way:
    $errors = $this->arrayRepository->getValidationErrors()
    //$errors['messages'] Contains the LaravelViewBag with the description of the errors
    //$errors['failed'] Contains the failed validation rules without messages
}
```

## Extending the repository with your custom methods
You can grab the builder instance with
```php
$this->getBuilder()
```
to create your custom repository methods.

### Example
```php
class MyUsersRepository extends ArrayRepository
{
    public function getActiveUsers()
    {
        return (array)$this->getBuilder()->whereActive(true)->get();
    }
}
```

# Future
More features coming soon like Spring-Like annotations for cache and transactions,
entity based repositories with entity generator based on your schema, etc.

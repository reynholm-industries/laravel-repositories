Laravel Repositories
====================

[![Latest Stable Version](https://poser.pugx.org/reynholm/laravel-repositories/v/stable.svg)](https://packagist.org/packages/reynholm/laravel-repositories)
[![Build Status](https://travis-ci.org/reynholm-industries/laravel-repositories.svg)](https://travis-ci.org/reynholm-industries/laravel-repositories)
[![Coverage Status](https://coveralls.io/repos/reynholm-industries/laravel-repositories/badge.png?branch=master)](https://coveralls.io/r/reynholm-industries/laravel-repositories?branch=master)
[![Total Downloads](https://poser.pugx.org/reynholm/laravel-repositories/downloads.svg)](https://packagist.org/packages/reynholm/laravel-repositories)
[![License](https://poser.pugx.org/reynholm/laravel-repositories/license.svg)](https://packagist.org/packages/reynholm/laravel-repositories)

Repository pattern for Laravel

# Warning
This package is currently on development. Is not ready for use yet.
Interfaces may change.

# Installation and Configuration
to do: how to install with composer

# Usage
Simply extend one of the currently Reynholm\LaravelRepositories\Repository implementations.

Currently available:

· Reynholm\LaravelRepositories\Repository\ArrayRepository

Lightway implementation that allows you retrieve data as arrays so there is no
tight coupling with laravel or eloquent

## Example
```php
class UserArrayRepository extends ArrayRepository
{
    //defaults to laravel's default connection
	//protected $connection = 'mysql';

	//If no tableName is specified it will be guessed based on a snake_case version of the CamelCase
	//class name without the repository part and pluralized.
	//Examples: UserRepository => users, CustomerHistoryLog => customer_history_logs, etc...
    //protected $tableName  = 'users';
}
```

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
     * Update a resource by its id
     * @param int $id
     * @param array $data
     * @return boolean
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
     * If you used validateMany it will be a multidimensional array
     * @return array
     */
    public function getValidationErrors();

    /*
    * Return the messages key from the getValidationErrors method
    * If used after validateMany it will be a multidimensional array
    * @return array
    */
    public function getValidationMessages();

    /*
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

```

## Timestamps
You can add timestamps adding the $timestamps = true property:
```php
class DownloadRepository extends ArrayRepository
{
    protected $timestamps = true;
}
```

By default it will manage created_at and updated_at fields.
You can override the created and updated fields using the following properties:

```php
protected $stamp_create = 'created_at';
protected $stamp_update = 'updated_at';
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
class MyUserRepository extends ArrayRepository
{
    public function getActiveUsers()
    {
        $result = $this->getBuilder()->whereActive(true)->get();

        //Builder returns an objects array so you can use the following method
        //to convert an array of objets to array
        return $this->objectsToArray($result);
    }
}
```

A best practice would be to create a new interface for MyUsersRepository with
all of the new methods that you are going to add.
```php
interface MyUserRepositoryInterface
{
    /**
    * @return array
    */
    public function getActiveUsers();
}
```
And then implement it on your repository:
```php
class MyUserRepository extends ArrayRepository implements MyUserRepositoryInterface
{
    /**
    * {@inheritdoc}
    */
    public function getActiveUsers()
    {
        $result = $this->getBuilder()->whereActive(true)->get();
        return $this->objectsToArray($result);
    }
}
```

So if you want to change the implementation you would need to implement the MyUsersRepositoryInterface
 and LaravelRepositoryInterface.

# Future
More features coming soon like Spring-Like annotations for cache and transactions,
entity based repositories with entity generator based on your schema, etc.

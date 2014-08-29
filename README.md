laravel-repositories
====================

[![Build Status](https://travis-ci.org/reynholm-industries/laravel-repositories.svg)](https://travis-ci.org/reynholm-industries/laravel-repositories)

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

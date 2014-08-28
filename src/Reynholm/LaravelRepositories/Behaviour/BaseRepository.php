<?php

namespace Reynholm\LaravelRepositories\Behaviour;

use Reynholm\LaravelRepositories\Exception\ColumnNotFoundException;

abstract class BaseRepository {

    /**
     * @param int $id
     * @param array $columns Restrict columns that you want to retrieve
     * @return array
     * @throws ColumnNotFoundException
     */
    abstract public function find($id, array $columns = array());

} 
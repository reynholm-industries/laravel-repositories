<?php

namespace Reynholm\LaravelRepositories\Support\Fetcher;

use Illuminate\Database\Eloquent\Collection;

/**
 * Class CollectionObjectFetcher
 * @package Reynholm\LaravelRepositories\Support\Fetcher
 *
 * returns data as a Laravel's collection with objects
 */
class CollectionObjectFetcher implements FetcherInterface {

    public function fetch($data)
    {
        return (object)$data;
    }

    public function fetchMany($data)
    {
        $result = [];

        foreach ($data as $row) {
            $result[] = (object)$row;
        }

        return new Collection($result);
    }
}
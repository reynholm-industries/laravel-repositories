<?php

namespace Reynholm\LaravelRepositories\Support\Fetcher;

use Illuminate\Support\Collection;

/**
 * Class CollectionArrayFetcher
 * @package src\Reynholm\LaravelRepositories\Support\Fetcher
 *
 * Returns a Laravel Collection with arrays
 */
class CollectionArrayFetcher implements FetcherInterface {

    public function fetch($data)
    {
        return (array)$data;
    }

    public function fetchMany($data)
    {
        $result = [];

        foreach ($data as $row) {
            $result[] = (array)$row;
        }

        return new Collection($result);
    }
}
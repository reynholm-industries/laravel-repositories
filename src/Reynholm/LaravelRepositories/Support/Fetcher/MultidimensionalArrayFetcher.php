<?php

namespace Reynholm\LaravelRepositories\Support\Fetcher;

/**
 * Class ArrayFetcher
 * @package Reynholm\LaravelRepositories\Support\Fetcher
 * Returns data as a multidimensional array
 */
class MultidimensionalArrayFetcher implements FetcherInterface {

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

        return $result;
    }
}